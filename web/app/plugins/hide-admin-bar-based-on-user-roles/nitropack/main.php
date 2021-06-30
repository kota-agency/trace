<?php
/*
Plugin Name:  NitroPack
Plugin URI:   https://nitropack.io/platform/wordpress
Description:  Everything you need for a fast website. Simple set up, easy to use, awesome support. Caching, Lazy Loading, Minification, Defer CSS/JS, CDN and more!
Version:      1.5.4
Author:       NitroPack LLC
Author URI:   https://nitropack.io/
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
$np_basePath = dirname(__FILE__) . '/';
require_once $np_basePath . 'functions.php';
require_once $np_basePath . 'diagnostics.php';

if (nitropack_is_wp_cli()) {
    require_once $np_basePath . 'wp-cli.php';
}

if ( nitropack_is_ezoic_active() ) {
    if (!nitropack_is_optimizer_request()) {
        // We need to serve the cached content after Ezoic's output buffering has started at plugins_loaded,0
        add_action( 'plugins_loaded', function() {
            add_filter( 'home_url', 'nitropack_ezoic_home_url' );
            nitropack_handle_request("plugin-ezoic");
            remove_filter( 'home_url', 'nitropack_ezoic_home_url' );
        }, 1 );
    } else {
        add_action( 'plugins_loaded', 'nitropack_disable_ezoic', 1);
    }
} else {
    nitropack_handle_request("plugin");
}

add_action( 'pre_post_update', 'nitropack_log_post_pre_update', 10, 3);
add_action( 'transition_post_status', 'nitropack_handle_post_transition', 10, 3);
add_action( 'transition_comment_status', 'nitropack_handle_comment_transition', 10, 3);
add_action( 'comment_post', 'nitropack_handle_comment_post', 10, 2);
add_action( 'switch_theme', 'nitropack_switch_theme' );
add_action( 'shutdown', 'nitropack_execute_purges', -1000 );
add_action( 'shutdown', 'nitropack_execute_invalidations', -1000 );
add_action( 'shutdown', 'nitropack_execute_warmups', -1000 );

add_action( 'woocommerce_updated_product_stock', 'nitropack_handle_product_stock_updates', 0, 1);
add_action( 'woocommerce_updated_product_price', 'nitropack_handle_product_price_updates', 0, 1);
add_action( 'woocommerce_rest_insert_product', function($post, $request, $creating) {
    if (!$creating) {
        nitropack_clean_post_cache($post);
    }
}, 10, 3);
add_action( 'woocommerce_rest_insert_product_object', function($product, $request, $creating) {
    if (!$creating) {
        $post = get_post($product->id);
        nitropack_clean_post_cache($post);
    }
}, 10, 3);

add_action('wcml_set_client_currency', function($currency) {
    setcookie('np_wc_currency', $currency, time() + (86400 * 7), "/");
});

if (nitropack_has_advanced_cache()) {
    // Handle automated updates
    if (!defined("NITROPACK_ADVANCED_CACHE_VERSION") || NITROPACK_VERSION != NITROPACK_ADVANCED_CACHE_VERSION) {
        add_action( 'plugins_loaded', 'nitropack_install_advanced_cache' );
    }
}

add_action('wp_footer', 'nitropack_print_heartbeat_script');
add_action('admin_footer', 'nitropack_print_heartbeat_script');
add_action('get_footer', 'nitropack_print_heartbeat_script');

if ( is_admin() ) {
    add_action( 'admin_menu', 'nitropack_menu' );
    add_action( 'admin_init', 'register_nitropack_settings' );
    add_action( 'admin_notices', 'nitropack_admin_notices' );
    add_action( 'network_admin_notices', 'nitropack_admin_notices' );
    add_action( 'wp_ajax_nitropack_purge_cache', 'nitropack_purge_cache' );
    add_action( 'wp_ajax_nitropack_invalidate_cache', 'nitropack_invalidate_cache' );
    add_action( 'wp_ajax_nitropack_verify_connect', 'nitropack_verify_connect_ajax' );
    add_action( 'wp_ajax_nitropack_disconnect', 'nitropack_disconnect' );
    add_action( 'wp_ajax_nitropack_test_compression_ajax', 'nitropack_test_compression_ajax' );
    add_action( 'wp_ajax_nitropack_set_compression_ajax', 'nitropack_set_compression_ajax' );
    add_action( 'wp_ajax_nitropack_set_auto_cache_purge_ajax', 'nitropack_set_auto_cache_purge_ajax' );
    add_action( 'wp_ajax_nitropack_set_cacheable_post_types', 'nitropack_set_cacheable_post_types' );
    add_action( 'wp_ajax_nitropack_enable_warmup', 'nitropack_enable_warmup' );
    add_action( 'wp_ajax_nitropack_disable_warmup', 'nitropack_disable_warmup' );
    add_action( 'wp_ajax_nitropack_warmup_stats', 'nitropack_warmup_stats' );
    add_action( 'wp_ajax_nitropack_estimate_warmup', 'nitropack_estimate_warmup' );
    add_action( 'wp_ajax_nitropack_run_warmup', 'nitropack_run_warmup' );
    add_action( 'wp_ajax_nitropack_purge_single_cache', 'nitropack_purge_single_cache' );
    add_action( 'wp_ajax_nitropack_invalidate_single_cache', 'nitropack_invalidate_single_cache' );
    add_action( 'wp_ajax_nitropack_dismiss_hosting_notice', 'nitropack_dismiss_hosting_notice' );
    add_action( 'wp_ajax_nitropack_reconfigure_webhooks', 'nitropack_reconfigure_webhooks' );
    add_action( 'wp_ajax_nitropack_generate_report', 'nitropack_generate_report' );//diag_ajax_hook
    add_action( 'wp_ajax_nitropack_enable_safemode', 'nitropack_enable_safemode' );
    add_action( 'wp_ajax_nitropack_disable_safemode', 'nitropack_disable_safemode' );
    add_action( 'wp_ajax_nitropack_safemode_status', 'nitropack_safemode_status' );
    add_action( 'update_option_nitropack-enableCompression', 'nitropack_handle_compression_toggle', 10, 2 );
    add_action( 'add_meta_boxes', 'nitropack_add_meta_box' );

    register_activation_hook( __FILE__, 'nitropack_activate' );
    register_deactivation_hook( __FILE__, 'nitropack_deactivate' );
} else {
    if (null !== $nitro = get_nitropack_sdk()) {
        $GLOBALS["NitroPack.instance"] = $nitro;
        if (get_option('nitropack-enableCompression') == 1) {
            $nitro->enableCompression();
        }
        add_action( 'wp', 'nitropack_init' );
    }
}

function nitropack_menu() {
    add_options_page( 'NitroPack Options', 'NitroPack', 'manage_options', 'nitropack', 'nitropack_options' );
    add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'nitropack_action_links' );
}

function nitropack_action_links ( $links ) {
    $nitroLinks = array(
        '<a href="' . admin_url( 'options-general.php?page=nitropack' ) . '">Settings</a>',
    );
    return array_merge( $nitroLinks, $links );
}

add_action( 'init', function() {
    if (current_user_can( 'manage_options' )) {
        // Enqueue font awesome
        add_action( 'wp_enqueue_scripts', 'nitropack_enqueue_load_fa');
        add_action( 'admin_enqueue_scripts', 'nitropack_enqueue_load_fa');

        // Enqueue admin bar menu custom stylesheet
        add_action( 'wp_enqueue_scripts', 'enqueue_nitropack_admin_bar_menu_stylesheet');
        add_action( 'admin_enqueue_scripts', 'enqueue_nitropack_admin_bar_menu_stylesheet');

        // Enqueue admin menu custom javascript
        add_action( 'wp_enqueue_scripts', 'nitropack_admin_bar_script' );

        // Add our admin menu bar entry
        add_action('admin_bar_menu', 'nitropack_admin_bar_menu', 50);
        add_action('plugins_loaded', 'nitropack_plugin_notices'); // Run the checks early, because we need to set some headers. The results from the checks will be cached, so future calls will work as expected.
    }
});
