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

/*
Plugin Name: bunny.net
Plugin URI: https://bunny.net/
Description: Speed up your website with bunny.net Content Delivery Network. This plugin allows you to easily enable Bunny CDN on your WordPress website and enjoy greatly improved loading times around the world.
Version: 2.2.6
Requires at least: 6.0
Tested up to: 6.5
Requires PHP: 7.4
Author: bunny.net
Author URI: https://bunny.net/
License: GPLv3
Text Domain: bunnycdn
*/

const BUNNYCDN_WP_VERSION = '2.2.6';

function bunnycdn_activate_plugin(): void
{
    if (!get_option('bunnycdn_wizard_finished')) {
        require_once __DIR__.'/vendor/autoload.php';
        bunnycdn_container()->newMigrateFromV1()->perform();
    }
}

function bunnycdn_uninstall_plugin(): void
{
    require_once __DIR__.'/vendor/autoload.php';
    \Bunny\Wordpress\Config\Reset::all();
}

register_activation_hook(__FILE__, 'bunnycdn_activate_plugin');
register_uninstall_hook(__FILE__, 'bunnycdn_uninstall_plugin');

add_action('upgrader_process_complete', function (\WP_Upgrader $upgrader, array $hook_extra) {
    if (!isset($hook_extra['type']) || 'plugin' !== $hook_extra['type']) {
        return;
    }

    // cleanup pre-v2.0.3 user info
    if ('agency' === get_option('bunnycdn_wizard_mode') && false !== get_option('bunnycdn_api_user')) {
        delete_option('bunnycdn_api_user');
    }
}, 10, 2);

add_action('init', function () {
    require_once __DIR__.'/vendor/autoload.php';

    \Bunny\Wordpress\Offloader::register();

    if (is_admin()) {
        require_once __DIR__.'/admin.php';
    } else {
        require_once __DIR__.'/frontend.php';
    }
});

add_action('rest_api_init', function () {
    $container = bunnycdn_container();

    $controller = new \Bunny\Wordpress\REST\Controller(
        $container->getAttachmentCounter(),
        $container->newAttachmentMover(),
        $container->getOffloaderConfig(),
    );

    register_rest_route('bunnycdn/v2', '/offloader/sync', [
        'methods' => \WP_REST_Server::CREATABLE,
        'callback' => [$controller, 'sync'],
        'show_in_index' => false,
        'permission_callback' => '__return_true',
    ]);
});

function bunnycdn_container(): \Bunny\Wordpress\Container
{
    static $container;

    if (null !== $container) {
        return $container;
    }

    $container = new \Bunny\Wordpress\Container();

    return $container;
}

function bunnycdn_http_proxy(): ?string
{
    static $proxy = '';

    if ('' !== $proxy) {
        return $proxy;
    }

    $wpProxy = new \WP_HTTP_Proxy();

    if (!$wpProxy->is_enabled()) {
        $proxy = null;

        return $proxy;
    }

    if ($wpProxy->use_authentication()) {
        $proxy = sprintf('http://%s@%s:%d', $wpProxy->authentication(), $wpProxy->host(), $wpProxy->port());
    } else {
        $proxy = sprintf('http://%s:%d', $wpProxy->host(), $wpProxy->port());
    }

    return $proxy;
}
