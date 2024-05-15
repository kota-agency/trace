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

// Don't load directly.
if (!defined('ABSPATH')) {
    exit('-1');
}

// auto-upgrade from V1
if (!get_option('bunnycdn_wizard_finished')) {
    bunnycdn_container()->newMigrateFromV1()->perform();
}

// reconfigure CORS for WP 6.5+
if (get_option('bunnycdn_wizard_finished') && !get_option('_bunnycdn_migrated_wp65')) {
    try {
        bunnycdn_container()->newMigrateToWP65()->perform();
    } catch (\Exception $e) {
        error_log('bunnycdn: could not upgrade pullzone to support WordPress 6.5: '.$e->getMessage());
    }
}

add_action('admin_menu', function () {
    add_menu_page(
        'bunny.net',
        'bunny.net',
        'manage_options',
        'bunnycdn',
        function () {
            (new \Bunny\Wordpress\Admin\Router(bunnycdn_admin_container()))->route();
        },
        'dashicons-carrot'
    );

    if ('1' !== get_option('bunnycdn_wizard_finished')) {
        return;
    }

    $isAgencyMode = 'agency' === get_option('bunnycdn_wizard_mode', 'standalone');
    add_submenu_page('bunnycdn', 'bunny.net', $isAgencyMode ? 'CDN' : 'Overview', 'manage_options', 'bunnycdn');

    $submenus = [
        'cdn' => 'CDN',
        'offloader' => 'Offloader',
        'optimizer' => 'Optimizer',
        'fonts' => 'Fonts',
    ];

    if ($isAgencyMode) {
        unset($submenus['cdn']);
        unset($submenus['offloader']);
        unset($submenus['optimizer']);
    }

    foreach ($submenus as $slug => $text) {
        add_submenu_page(
            'bunnycdn',
            $text,
            $text,
            'manage_options',
            'admin.php?page=bunnycdn&section='.$slug,
        );
    }
});

add_filter('submenu_file', function ($submenu_file, $parent_file) {
    if ('bunnycdn' === $parent_file) {
        $section = $_GET['section'] ?? null;

        if (is_string($section)) {
            return 'admin.php?page=bunnycdn&section='.$section;
        }
    }

    return $submenu_file;
}, 10, 2);

add_action('wp_ajax_bunnycdn', function () {
    (new \Bunny\Wordpress\Admin\Router(bunnycdn_admin_container()))->route(true);
});

add_action('load-toplevel_page_bunnycdn', function () {
    $container = bunnycdn_admin_container();

    if (isset($_GET['section']) && 'attachment' === $_GET['section']) {
        $container->newAttachmentController()->run();

        wp_die();
        exit;
    }

    wp_enqueue_script('bunnycdn-admin', $container->assetUrl('admin.js'), [], BUNNYCDN_WP_VERSION);
    wp_enqueue_style('bunnycdn-admin', $container->assetUrl('admin.css'), [], BUNNYCDN_WP_VERSION);
});

add_action('admin_notices', function () {
    $migrationWarning = get_option('_bunnycdn_migration_warning');
    if (!empty($migrationWarning)) {
        $url = add_query_arg([
            'page' => 'bunnycdn',
        ], admin_url('admin.php'));

        wp_admin_notice(
            str_replace('%url%', $url, $migrationWarning),
            ['type' => 'error', 'dismissible' => true],
        );
    }

    if (bunnycdn_container()->getOffloaderUtils()->shouldShowSyncDelayedMessage()) {
        wp_admin_notice(
            'bunny.net: There was an issue while offloading your files to the Edge Storage. To get help, please <a href="https://dash.bunny.net/support/tickets" target="_blank">reach out to our Super Bunnies</a>.',
            ['type' => 'error', 'dismissible' => true],
        );
    }
});

function bunnycdn_admin_container(): \Bunny\Wordpress\Admin\Container
{
    static $container;

    if (null !== $container) {
        return $container;
    }

    $container = new \Bunny\Wordpress\Admin\Container(
        bunnycdn_container(),
        plugin_dir_url(__FILE__),
        dirname(__FILE__).'/templates/admin',
    );

    return $container;
}
