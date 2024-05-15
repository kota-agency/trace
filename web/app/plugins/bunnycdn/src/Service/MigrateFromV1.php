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

class MigrateFromV1
{
    private \Closure $newApiClient;
    private \Closure $saveConfigCallback;

    public function __construct(\Closure $newApiClient, \Closure $saveConfigCallback)
    {
        $this->newApiClient = $newApiClient;
        $this->saveConfigCallback = $saveConfigCallback;
    }

    public function perform(): void
    {
        /** @var false|array<string, string|int>|null $v1Config */
        $v1Config = get_option('bunnycdn');
        // there is no V1 configuration to migrate from
        if (false === $v1Config || !is_array($v1Config)) {
            return;
        }
        // check for minimum requirements
        $siteUrl = rtrim((string) ($v1Config['site_url'] ?? ''), '/');
        $pullZoneName = (string) ($v1Config['pull_zone'] ?? '');
        if ('' === $siteUrl || '' === $pullZoneName) {
            update_option('_bunnycdn_migration_warning', 'bunny.net: settings migration failed. Please configure the bunny.net plugin by clicking <a href="%url%">here</a>.');

            return;
        }
        $this->migrateRequired($v1Config, $pullZoneName, $siteUrl);
        $this->migrateOptional($v1Config);
        $this->migrateApiKey($v1Config, $pullZoneName);
        // remove v1 configuration
        delete_option('bunnycdn');
    }

    /**
     * @param array<string, string|int> $v1Config
     */
    public function migrateRequired(array $v1Config, string $pullZoneName, string $siteUrl): void
    {
        $hostname = sprintf('%s.b-cdn.net', $pullZoneName);
        if (!empty($v1Config['cdn_domain_name'])) {
            $hostname = (string) $v1Config['cdn_domain_name'];
        }
        // migrate in agency mode
        update_option('bunnycdn_cdn_enabled', '1');
        update_option('bunnycdn_cdn_pullzone', ['name' => $pullZoneName]);
        update_option('bunnycdn_cdn_hostname', $hostname);
        update_option('bunnycdn_cdn_url', $siteUrl);
        update_option('bunnycdn_wizard_mode', 'agency');
        update_option('bunnycdn_wizard_finished', '1', true);
    }

    /**
     * @param array<string, string|int> $v1Config
     */
    public function migrateOptional(array $v1Config): void
    {
        if (!empty($v1Config['excluded'])) {
            update_option('bunnycdn_cdn_excluded', explode(',', (string) $v1Config['excluded']));
        }
        if (!empty($v1Config['directories'])) {
            update_option('bunnycdn_cdn_included', explode(',', (string) $v1Config['directories']));
        }
        if (!empty($v1Config['disable_admin'])) {
            update_option('bunnycdn_cdn_disable_admin', '1' === $v1Config['disable_admin'] || 1 === $v1Config['disable_admin']);
        }
        ($this->saveConfigCallback)();
    }

    /**
     * @param array<string, string|int> $v1Config
     */
    public function migrateApiKey(array $v1Config, string $pullZoneName): void
    {
        if (!empty($v1Config['api_key'])) {
            $apiKey = (string) $v1Config['api_key'];
            $api = ($this->newApiClient)($apiKey);
            try {
                $user = $api->getUser();
                /** @var \Bunny\Wordpress\Api\Pullzone\Info|null $pullzone */
                $pullzone = null;
                foreach ($api->listPullzones() as $info) {
                    if ($info->getName() === $pullZoneName) {
                        $pullzone = $info;
                        break;
                    }
                }
                if (null !== $pullzone) {
                    update_option('bunnycdn_api_key', $apiKey);
                    update_option('bunnycdn_api_user', $user);
                    update_option('bunnycdn_cdn_pullzone', ['id' => $pullzone->getId(), 'name' => $pullzone->getName()]);
                    update_option('bunnycdn_wizard_mode', 'standalone');
                }
            } catch (\Exception $e) {
                // invalid API key, keep the plugin in agency mode
            }
        }
    }
}
