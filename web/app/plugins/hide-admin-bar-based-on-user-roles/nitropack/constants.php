<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function nitropack_trailingslashit($string) {
    return rtrim( $string, '/\\' ) . '/';
}

define( 'NITROPACK_VERSION', '1.5.4' );
define( 'NITROPACK_OPTION_GROUP', 'nitropack' );
define( 'NITROPACK_DATA_DIR', nitropack_trailingslashit(WP_CONTENT_DIR) . 'nitropack' );
define( 'NITROPACK_CONFIG_FILE', nitropack_trailingslashit(NITROPACK_DATA_DIR) . 'config.json' );
define( 'NITROPACK_PLUGIN_DIR', nitropack_trailingslashit(dirname(__FILE__)));
define( 'NITROPACK_INTEGRATIONS_ACTION', "nitropack_integrations_ready");
define( 'NITROPACK_HEARTBEAT_INTERVAL', 60*5); // 5min

if (!defined("NITROPACK_USE_REDIS")) define("NITROPACK_USE_REDIS", false); // Set this to true to enable storing cache in Redis
if (!defined("NITROPACK_REDIS_HOST")) define("NITROPACK_REDIS_HOST", "127.0.0.1"); // Set this to the IP of your Redis server
if (!defined("NITROPACK_REDIS_PORT")) define("NITROPACK_REDIS_PORT", 6379); // Set this to the port of your Redis server
if (!defined("NITROPACK_REDIS_PASS")) define("NITROPACK_REDIS_PASS", NULL); // Set this to the password of your redis server if authentication is needed
if (!defined("NITROPACK_REDIS_DB")) define("NITROPACK_REDIS_DB", NULL); // Set this to the number of the Redis DB if you'd like to not use the default one

if (!defined("NITROPACK_SUPPORT_BUBBLE_VISIBLE")) define("NITROPACK_SUPPORT_BUBBLE_VISIBLE", true);
if (!defined("NITROPACK_SUPPORT_BUBBLE_URL")) define("NITROPACK_SUPPORT_BUBBLE_URL", "https://support.nitropack.io/");
