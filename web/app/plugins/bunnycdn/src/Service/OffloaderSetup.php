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
use Bunny\Wordpress\Api\Pullzone;
use Bunny\Wordpress\Api\Storagezone;
use Bunny\Wordpress\Config\Offloader as OffloaderConfig;
use Bunny\Wordpress\Utils\Offloader as OffloaderUtils;

class OffloaderSetup
{
    private const PULLZONE_EDGERULE_DESCRIPTION = 'WordPress Content Offloading';
    private Client $api;
    private CdnAcceleration $cdnAcceleration;
    private OffloaderUtils $offloaderUtils;

    public function __construct(Client $api, CdnAcceleration $cdnAcceleration, OffloaderUtils $offloaderUtils)
    {
        $this->api = $api;
        $this->cdnAcceleration = $cdnAcceleration;
        $this->offloaderUtils = $offloaderUtils;
    }

    /**
     * @param array<string, mixed> $postData
     */
    public function perform(array $postData): void
    {
        $postData['storage_replication'] = $postData['storage_replication'] ?? [];
        $postData['sync_existing'] = $postData['sync_existing'] ?? '';
        $this->validatePost($postData);
        $host = $this->cdnAcceleration->getRequestHost();
        $record = $this->cdnAcceleration->getDNSRecord();
        $pullzoneId = $record->getAcceleratedPullzoneId();
        if (null === $pullzoneId) {
            throw new \Exception('We could not find a pullzone for this domain.');
        }
        $pullzone = $this->api->getPullzoneDetails($pullzoneId);
        $pathPrefix = $this->offloaderUtils->getPathPrefix();
        [$syncToken, $syncTokenHash] = $this->offloaderUtils->generateSyncToken();
        // setup storage zone
        $storageZoneId = $this->offloaderUtils->checkForExistingEdgeRule($pullzone, $pathPrefix);
        if (null === $storageZoneId) {
            $storageZone = $this->createStorageZone($postData['storage_replication']);
            $this->createEdgeRules($host, $pullzone, $storageZone, $pathPrefix);
        } else {
            $storageZone = $this->api->getStorageZone($storageZoneId);
        }
        $this->api->updateStorageZoneForOffloader($storageZone->getId(), $record->getZone()->getId(), $record->getId(), $pathPrefix, $syncToken);
        // save configuration
        update_option('bunnycdn_offloader_enabled', true);
        update_option('bunnycdn_offloader_storage_zone', $storageZone->getName());
        update_option('bunnycdn_offloader_storage_zoneid', $storageZone->getId());
        update_option('bunnycdn_offloader_storage_password', $storageZone->getPassword());
        update_option('bunnycdn_offloader_sync_existing', '1' === $postData['sync_existing']);
        update_option('bunnycdn_offloader_sync_path_prefix', $pathPrefix);
        update_option('bunnycdn_offloader_sync_token_hash', $syncTokenHash);
        update_option('_bunnycdn_offloader_last_sync', time());
    }

    /**
     * @param array<string, mixed> $postData
     */
    private function validatePost(array $postData): void
    {
        if (!isset($postData['enable_confirmed']) || '1' !== $postData['enable_confirmed']) {
            throw new \Exception('Needs confirmation');
        }
        foreach ($postData['storage_replication'] as $replicationRegion) {
            if (empty($replicationRegion) || !isset(OffloaderConfig::STORAGE_REGIONS_SSD[$replicationRegion])) {
                throw new \Exception('Invalid replication region: '.$replicationRegion);
            }
            if (OffloaderConfig::STORAGE_REGION_SSD_MAIN === $replicationRegion) {
                throw new \Exception('Do not repeat the main region in the replication regions.');
            }
        }
    }

    /**
     * @param string[] $replicationRegions
     */
    private function createStorageZone(array $replicationRegions): Storagezone\Details
    {
        for ($i = 0; $i < 5; ++$i) {
            try {
                $name = 'wp-offloader-'.strtolower(wp_generate_password(16, false));

                return $this->api->createStorageZone($name, OffloaderConfig::STORAGE_REGION_SSD_MAIN, $replicationRegions);
            } catch (\Exception $e) {
                if ('The storage zone name is already taken.' === $e->getMessage()) {
                    continue;
                }
                error_log('bunnycdn: offloader: '.$e->getMessage(), \E_USER_WARNING);
                throw $e;
            }
        }
        throw new \Exception('Could not create storage zone.');
    }

    private function createEdgeRules(string $hostname, Pullzone\Details $pullzone, Storagezone\Details $storageZone, string $pathPrefix): void
    {
        $urls = ['http://'.$hostname.$pathPrefix.'/wp-content/uploads/*', 'https://'.$hostname.$pathPrefix.'/wp-content/uploads/*'];
        $this->api->addEdgeRuleToPullzone($pullzone->getId(), ['Enabled' => true, 'Description' => self::PULLZONE_EDGERULE_DESCRIPTION, 'ActionType' => 17, 'ActionParameter1' => $storageZone->getId(), 'ActionParameter2' => $storageZone->getName(), 'TriggerMatchingType' => 1, 'Triggers' => [['Type' => 0, 'PatternMatchingType' => 0, 'PatternMatches' => $urls]]]);
    }
}
