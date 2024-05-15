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

namespace Bunny\Wordpress\Utils;

use Bunny\Storage\Client as StorageClient;
use Bunny\Storage\Region;
use Bunny\Wordpress\Config\Offloader as OffloaderConfig;

class StorageClientFactory
{
    public static function createFromConfig(OffloaderConfig $config): StorageClient
    {
        return self::create($config->getStorageZone(), $config->getStoragePassword());
    }

    public function newWithConfig(OffloaderConfig $config): StorageClient
    {
        return self::create($config->getStorageZone(), $config->getStoragePassword());
    }

    public function new(string $name, string $password): StorageClient
    {
        return self::create($name, $password);
    }

    private static function create(string $name, string $password): StorageClient
    {
        $httpClient = new \Bunny_WP_Plugin\GuzzleHttp\Client(['allow_redirects' => false, 'http_errors' => false, 'base_uri' => Region::getBaseUrl(OffloaderConfig::STORAGE_REGION_SSD_MAIN), 'proxy' => bunnycdn_http_proxy(), 'headers' => ['AccessKey' => $password]]);

        return new StorageClient($password, $name, OffloaderConfig::STORAGE_REGION_SSD_MAIN, $httpClient);
    }
}
