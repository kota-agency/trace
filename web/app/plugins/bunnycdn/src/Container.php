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

namespace Bunny\Wordpress;

use Bunny\Wordpress\Api\Client as ApiClient;
use Bunny\Wordpress\Api\Config as ApiConfig;
use Bunny\Wordpress\Config\Cdn as CdnConfig;
use Bunny\Wordpress\Config\Fonts as FontsConfig;
use Bunny\Wordpress\Config\Offloader as OffloaderConfig;
use Bunny\Wordpress\Service\AttachmentCounter;
use Bunny\Wordpress\Service\AttachmentMover;
use Bunny\Wordpress\Service\MigrateFromV1;
use Bunny\Wordpress\Service\MigrateToWP65;
use Bunny\Wordpress\Utils\Offloader as OffloaderUtils;
use Bunny\Wordpress\Utils\StorageClientFactory;

class Container
{
    private ?CdnConfig $cdnConfig = null;
    private ?OffloaderConfig $offloaderConfig = null;

    public function newApiClient(ApiConfig $config): ApiClient
    {
        $httpClient = new \Bunny_WP_Plugin\GuzzleHttp\Client(['base_uri' => ApiClient::BASE_URL, 'timeout' => 20, 'allow_redirects' => false, 'http_errors' => false, 'proxy' => bunnycdn_http_proxy(), 'headers' => ['Accept' => 'application/json', 'User-Agent' => 'bunny-wp-plugin/'.BUNNYCDN_WP_VERSION, 'AccessKey' => $config->getApiKey()]]);

        return new ApiClient($httpClient);
    }

    public function getApiClient(): ApiClient
    {
        static $instance;
        if (null !== $instance) {
            return $instance;
        }
        $instance = $this->newApiClient($this->getApiConfig());

        return $instance;
    }

    private function newApiConfig(string $apiKey): ApiConfig
    {
        return new ApiConfig($apiKey);
    }

    private function getApiConfig(): ApiConfig
    {
        static $instance;
        if (null !== $instance) {
            return $instance;
        }
        $instance = ApiConfig::fromWpOptions();

        return $instance;
    }

    public function getCdnConfig(): CdnConfig
    {
        if (null !== $this->cdnConfig) {
            return $this->cdnConfig;
        }
        $this->cdnConfig = CdnConfig::fromWpOptions();

        return $this->cdnConfig;
    }

    private function reloadCdnConfig(): CdnConfig
    {
        $this->cdnConfig = null;

        return $this->getCdnConfig();
    }

    public function getFontsConfig(): FontsConfig
    {
        static $instance;
        if (null !== $instance) {
            return $instance;
        }
        $instance = FontsConfig::fromWpOptions();

        return $instance;
    }

    public function getOffloaderConfig(): OffloaderConfig
    {
        if (null !== $this->offloaderConfig) {
            return $this->offloaderConfig;
        }
        $this->offloaderConfig = OffloaderConfig::fromWpOptions();

        return $this->offloaderConfig;
    }

    public function reloadOffloaderConfig(): OffloaderConfig
    {
        $this->offloaderConfig = null;

        return $this->getOffloaderConfig();
    }

    public function newMigrateFromV1(): MigrateFromV1
    {
        return new MigrateFromV1(function (string $apiKey) {
            return $this->newApiClient($this->newApiConfig($apiKey));
        }, function () {
            $this->reloadCdnConfig()->saveToWpOptions();
        });
    }

    public function newMigrateToWP65(): MigrateToWP65
    {
        return new MigrateToWP65($this->getApiClient(), $this->getCdnConfig());
    }

    public function getAttachmentCounter(): AttachmentCounter
    {
        static $instance;
        if (null !== $instance) {
            return $instance;
        }
        $instance = new AttachmentCounter($this->getDb());

        return $instance;
    }

    public function newAttachmentMover(): AttachmentMover
    {
        $storageClient = $this->getStorageClientFactory()->new($this->getOffloaderConfig()->getStorageZone(), $this->getOffloaderConfig()->getStoragePassword());

        return new AttachmentMover($this->getDb(), $storageClient, $this->getOffloaderConfig(), $this->getOffloaderUtils());
    }

    public function getOffloaderUtils(): OffloaderUtils
    {
        static $instance;
        if (null !== $instance) {
            return $instance;
        }
        $instance = new OffloaderUtils($this->getApiClient(), $this->getAttachmentCounter(), $this->getOffloaderConfig(), $this->getDb(), $this->getStorageClientFactory());

        return $instance;
    }

    public function getStorageClientFactory(): StorageClientFactory
    {
        static $instance;
        if (null !== $instance) {
            return $instance;
        }
        $instance = new StorageClientFactory();

        return $instance;
    }

    private function getDb(): \wpdb
    {
        global $wpdb;

        return $wpdb;
    }
}
