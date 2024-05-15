<?php

// bunny.net WordPress Plugin
// Copyright (C) 2024  BunnyWay d.o.o.
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
declare(strict_types=1);

namespace Bunny\Wordpress\Service;

use Bunny\Wordpress\Api\Client;
use Bunny\Wordpress\Api\Dnszone;
use Bunny\Wordpress\Api\Exception\AccountNotActivatedException;
use Bunny\Wordpress\Api\Exception\PullzoneLocalUrlException;
use Bunny\Wordpress\Api\Pullzone\Info;

class CdnAcceleration
{
    private Client $api;
    /** @var array<string, mixed> */
    private array $serverVars;
    private AttachmentCounter $attachmentCounter;
    private bool $isAgencyMode;
    private bool $isOffloaderEnabled;
    private bool $isOffloaderConfigured;
    private ?int $pullzoneId;

    /**
     * @param array<string, mixed> $serverVars
     */
    public function __construct(Client $api, array $serverVars, AttachmentCounter $attachmentCounter, bool $isAgencyMode, bool $isOffloaderEnabled, bool $isOffloaderConfigured, ?int $pullzoneId)
    {
        $this->api = $api;
        $this->serverVars = $serverVars;
        $this->attachmentCounter = $attachmentCounter;
        $this->isAgencyMode = $isAgencyMode;
        $this->isOffloaderEnabled = $isOffloaderEnabled;
        $this->isOffloaderConfigured = $isOffloaderConfigured;
        $this->pullzoneId = $pullzoneId;
    }

    public function getRequestHost(): string
    {
        return $this->serverVars['HTTP_HOST'];
    }

    public function isRequestAccelerated(): bool
    {
        $via = $this->serverVars['HTTP_VIA'] ?? null;
        $cdnRequest = $this->serverVars['HTTP_CDN_REQUESTID'] ?? null;
        if ('BunnyCDN' !== $via || empty($cdnRequest)) {
            return false;
        }
        if ($this->isAgencyMode) {
            return false;
        }
        $record = $this->api->findDnsRecordForHostname($this->getRequestHost());

        return null !== $record && $record->isAccelerated();
    }

    public function enable(): void
    {
        $record = $this->api->findDnsRecordForHostname($this->getRequestHost());
        if (null === $record) {
            throw new \Exception('CDN acceleration is not enabled on Bunny DNS. Please contact Bunny Support.');
        }
        $pullzoneId = $record->getAcceleratedPullzoneId();
        if (null === $pullzoneId) {
            throw new \Exception('CDN acceleration is not enabled on Bunny DNS. Please contact Bunny Support.');
        }
        $this->swapOptimizerConfiguration($pullzoneId);
        $pullzone = $this->api->getPullzoneById($pullzoneId);
        update_option('bunnycdn_cdn_pullzone', ['id' => $pullzone->getId(), 'name' => $pullzone->getName()]);
        update_option('bunnycdn_cdn_status', 2);
    }

    public function disable(string $url, int $pullzoneId): void
    {
        if ($this->isOffloaderEnabled) {
            throw new \Exception('You cannot disable the CDN acceleration while the Content Offloader feature is in use.');
        }
        if (0 === $pullzoneId) {
            $pullzone = $this->createPullzone($url);
        } else {
            $pullzone = $this->api->getPullzoneById($pullzoneId);
        }
        $this->swapOptimizerConfiguration($pullzoneId);
        update_option('bunnycdn_cdn_pullzone', ['id' => $pullzone->getId(), 'name' => $pullzone->getName()]);
        update_option('bunnycdn_cdn_status', 1);
    }

    private function createPullzone(string $originUrl): Info
    {
        for ($i = 0; $i < 5; ++$i) {
            try {
                $name = 'bunny-wp-pullzone-'.strtolower(wp_generate_password(10, false));

                return $this->api->createPullzoneForCdn($name, $originUrl);
            } catch (PullzoneLocalUrlException $e) {
                throw $e;
            } catch (\Exception $e) {
                if ('Your account is not currently allowed to add new zones' === $e->getMessage()) {
                    throw new AccountNotActivatedException();
                }
                if ('The pull zone name is already taken.' === $e->getMessage()) {
                    continue;
                }
                throw $e;
            }
        }
        throw new \Exception('Could not create a new pullzone.');
    }

    public function swapOptimizerConfiguration(int $newPullzoneId): void
    {
        if (null === $this->pullzoneId) {
            return;
        }
        try {
            $oldPullzone = $this->api->getPullzoneDetails($this->pullzoneId);
        } catch (\Exception $e) {
            // old pullzone is not reachable anymore
            return;
        }
        if ($oldPullzone->getConfig()->isEnabled()) {
            $newPullzone = $this->api->getPullzoneDetails($newPullzoneId);
            if (!$newPullzone->getConfig()->isEnabled()) {
                // copy optimizer config to new pullzone
                $this->api->saveOptimizerConfig($oldPullzone->getConfig(), $newPullzoneId);
            }
            // disable optimizer in old pullzone
            $oldPullzone->getConfig()->handlePost(['enabled' => '0']);
            $this->api->saveOptimizerConfig($oldPullzone->getConfig(), $this->pullzoneId);
        }
    }

    public function shouldShowAlert(): bool
    {
        if ($this->isAgencyMode) {
            return false;
        }
        $attachments = $this->attachmentCounter->count();

        return !$this->isRequestAccelerated() && $this->isOffloaderConfigured && $attachments[AttachmentCounter::BUNNY] > 0;
    }

    public function getDNSRecord(): Dnszone\Record
    {
        $host = $this->getRequestHost();
        if (!$this->isRequestAccelerated()) {
            throw new \Exception('Your website is not using CDN acceleration in Bunny DNS.');
        }
        $record = $this->api->findDnsRecordForHostname($host);
        if (null === $record) {
            throw new \Exception('Could not find the Bunny DNS entry.');
        }
        if (!$record->isAccelerated()) {
            throw new \Exception('Your website is not using CDN acceleration in Bunny DNS.');
        }

        return $record;
    }
}
