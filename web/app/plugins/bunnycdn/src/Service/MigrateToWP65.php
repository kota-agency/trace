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
use Bunny\Wordpress\Config\Cdn as CdnConfig;

class MigrateToWP65
{
    private Client $api;
    private CdnConfig $cdnConfig;

    public function __construct(Client $api, CdnConfig $cdnConfig)
    {
        $this->api = $api;
        $this->cdnConfig = $cdnConfig;
    }

    public function perform(): void
    {
        if (get_option('_bunnycdn_migrated_wp65')) {
            return;
        }
        if (!get_option('bunnycdn_wizard_finished')) {
            // plugin is not yet setup
            update_option('_bunnycdn_migrated_wp65', true);

            return;
        }
        global $wp_version;
        if (-1 === version_compare($wp_version, '6.5')) {
            return;
        }
        $pullzoneId = $this->cdnConfig->getPullzoneId();
        if (null === $pullzoneId) {
            return;
        }
        try {
            $pullzone = $this->api->getPullzoneById($pullzoneId);
            $extensions = $pullzone->getCorsHeaderExtensions();
            if (!in_array('js', $extensions, true)) {
                $extensions[] = 'js';
                $this->api->updatePullzone($pullzoneId, ['AccessControlOriginHeaderExtensions' => $extensions]);
            }
            update_option('_bunnycdn_migrated_wp65', true);
        } catch (\Exception $e) {
            error_log('bunnycdn: could not upgrade pullzone to support WordPress 6.5: '.$e->getMessage());
        }
    }
}
