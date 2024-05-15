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

namespace Bunny\Wordpress\Config;

class Reset
{
    public static function all(): void
    {
        delete_option('_bunnycdn_migrated_wp65');
        delete_option('_bunnycdn_migration_warning');
        delete_option('_bunnycdn_offloader_last_password_check');
        delete_option('_bunnycdn_offloader_last_sync');
        delete_option('bunnycdn');
        delete_option('bunnycdn_api_key');
        delete_option('bunnycdn_api_user');
        delete_option('bunnycdn_cdn_disable_admin');
        delete_option('bunnycdn_cdn_enabled');
        delete_option('bunnycdn_cdn_excluded');
        delete_option('bunnycdn_cdn_hostname');
        delete_option('bunnycdn_cdn_included');
        delete_option('bunnycdn_cdn_pullzone');
        delete_option('bunnycdn_cdn_status');
        delete_option('bunnycdn_cdn_url');
        delete_option('bunnycdn_fonts_enabled');
        delete_option('bunnycdn_offloader_enabled');
        delete_option('bunnycdn_offloader_storage_password');
        delete_option('bunnycdn_offloader_storage_zone');
        delete_option('bunnycdn_offloader_storage_zoneid');
        delete_option('bunnycdn_offloader_sync_existing');
        delete_option('bunnycdn_offloader_sync_path_prefix');
        delete_option('bunnycdn_offloader_sync_token_hash');
        delete_option('bunnycdn_wizard_finished');
        delete_option('bunnycdn_wizard_mode');
    }

    public static function convertToAgencyMode(): void
    {
        update_option('bunnycdn_wizard_mode', 'agency');
        delete_option('bunnycdn_api_key');
        delete_option('bunnycdn_api_user');
    }
}
