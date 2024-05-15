<?php

/**
 * The plugin bootstrap file
 *
 * @link              https://iamankitpanchal.com/
 * @since             1.0.0
 * @package           Hide_Admin_Bar_Based_On_User_Roles
 *
 *
 * @wordpress-plugin
 * Plugin Name:       Hide Admin Bar Based on User Roles
 * Plugin URI:        https://wordpress.org/plugins/hide-admin-bar-based-on-user-roles/
 * Description:       This plugin is very useful to hide admin bar based on selected user roles and user capabilities.
 * Version:           3.8.0
 * Author:            Ankit Panchal
 * Author URI:        https://iamankitpanchal.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       hide-admin-bar-based-on-user-roles
 * Domain Path:       /languages
 */

/*
Hide Admin Bar Based on User Roles is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
Hide Admin Bar Based on User Roles is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with Hide Admin Bar Based on User Roles. If not, see {URI to Plugin License}.
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.7.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'HIDE_ADMIN_BAR_BASED_ON_USER_ROLES', '3.8.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-hide-admin-bar-based-on-user-roles-activator.php
 */
function hab_activate_hide_admin_bar_based_on_user_roles() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-hide-admin-bar-based-on-user-roles-activator.php';
	hab_Hide_Admin_Bar_Based_On_User_Roles_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-hide-admin-bar-based-on-user-roles-deactivator.php
 */
function hab_deactivate_hide_admin_bar_based_on_user_roles() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-hide-admin-bar-based-on-user-roles-deactivator.php';
	hab_Hide_Admin_Bar_Based_On_User_Roles_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'hab_activate_hide_admin_bar_based_on_user_roles' );
register_deactivation_hook( __FILE__, 'hab_deactivate_hide_admin_bar_based_on_user_roles' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-hide-admin-bar-based-on-user-roles.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.7.0
 */
function hab_run_hide_admin_bar_based_on_user_roles() {

	$plugin = new hab_Hide_Admin_Bar_Based_On_User_Roles();
	$plugin->run();

}
hab_run_hide_admin_bar_based_on_user_roles();




/*
*
* ONE CLICK INSTALLER SCRIPT FOR ULTIMAKIT FOR WP
*/
// Enqueue JavaScript
function habbour_enqueue_scripts() {
    wp_enqueue_script('habbour-ajax-script', plugin_dir_url(__FILE__) . 'admin/js/habbour-ajax.js', array('jquery'), null, true);
    wp_localize_script('habbour-ajax-script', 'habbourAjax', array('ajaxurl' => admin_url('admin-ajax.php'), 'habbour_nonce' => wp_create_nonce('habbour_nonce')));
}
add_action('admin_enqueue_scripts', 'habbour_enqueue_scripts');

// AJAX action for authenticated users
add_action('wp_ajax_habbour_action', 'habbour_handle_ajax');
add_action('wp_ajax_habbour_hide_notice', 'habbour_handle_ajax_options');

function habbour_handle_ajax_options(){
    if (!wp_verify_nonce($_REQUEST['nonce'], 'habbour_nonce')) {
        wp_send_json_error('Nonce not verified');
    }

    update_option('habbour_hide_notice','yes');

    wp_send_json_success(['success' => true, 'message' => 'The notice has been successfully hidden.']);
}

function habbour_handle_ajax() {
    // Check for the nonce, if necessary (for security)
    if (!wp_verify_nonce($_REQUEST['nonce'], 'habbour_nonce')) {
        wp_send_json_error('Nonce not verified');
    }

    require_once(ABSPATH . 'wp-admin/includes/plugin-install.php');
	require_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');
	require_once(ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php');
	require_once(ABSPATH . 'wp-admin/includes/plugin.php');

	$plugin_slug = 'ultimakit-for-wp';

    // Plugin installation
    $api = plugins_api('plugin_information', ['slug' => $plugin_slug, 'fields' => ['sections' => false]]);
    if (is_wp_error($api)) {
        echo json_encode(['success' => false, 'message' => 'Plugin information retrieval failed']);
        exit;
    }

    $upgrader = new Plugin_Upgrader();
    $result = $upgrader->install($api->download_link);

    // Activate Plugin
    $activate_result = activate_plugin($upgrader->plugin_info());
    if (is_wp_error($activate_result)) {
        wp_send_json_error(['success' => false, 'message' => 'Installation failed']);
    }

    update_option('habbour_hide_notice','yes');
    wp_send_json_success(['success' => true, 'message' => 'Plugin installed successfully']);
}

function habbour_check_if_plugin_exists($plugin_path) {
    include_once(ABSPATH . 'wp-admin/includes/plugin.php'); // Required for plugin API functions

    // Check if the plugin file is valid
    $check = validate_plugin($plugin_path);
    if (is_wp_error($check)) {
        return false; // Plugin not found or invalid
    } else {
        return true; // Plugin exists
    }
}

function habbour_custom_notice_for_other_plugins() {
	if (!current_user_can('install_plugins')) {
		return;
	}

	$plugin_path_free = 'ultimakit-for-wp/wp-ultimakit.php';
	$plugin_path_pro = 'ultimakit-for-wp-pro/wp-ultimakit-pro.php';
	if (habbour_check_if_plugin_exists($plugin_path_free) || habbour_check_if_plugin_exists($plugin_path_pro) ) {
		return;
	}
	if ('yes' !== get_option('habbour_hide_notice', 'no')) {
    ?>
    <style>
    	.habbour_ad_container {
		    display: flex;
		    flex-wrap: wrap;
		    width: 100%;
		    background: #6610f200;
		    align-items: center;
		}

		.habbour_ad_column {
		    padding: 10px; /* Padding for spacing, adjust as needed */
		    box-sizing: border-box; /* Include padding and border in the width */
		}

		.habbour_ad_column-30 {
		    flex: 0 0 15%; /* Flex-grow, flex-shrink, flex-basis */
		}

		.habbour_ad_column-30 h3{
			margin: 10px;
		}

		.habbour_ad_column-70 {
		    flex: 0 0 85%; /* Flex-grow, flex-shrink, flex-basis */
		}

		.habbour_ad_notice, div.updated{
			border-left-color: #6610F2;
		}

		.habbour_ad_column p{
			margin-bottom: 15px;
			max-width: 700px;
			width: 100%;
			font-size: 14px;
		}

		.habbour_ad_column h1 {
			padding-top: 0px;
		}
		.habbour_ad_column h1 span{
			font-size: 26px;
			color: #6610F2;
		}	
		.habbour_ad_column a{
			color: #6610F2;
			margin-right: 10px;
			font-size: 14px;
			text-decoration: none;
		}

		.habbour_install{
			padding: 7px;
		    border: 1px solid #6610F2;
		    border-radius: 5px;
		}
		.habbour_install:hover{
			background-color: #6610F2;
			color: #ffffff;
		}

		.habbour-loader {
		    border: 16px solid #f3f3f3; /* Light grey */
		    border-top: 16px solid #6610F2; /* Blue */
		    border-radius: 50%;
		    width: 120px;
		    height: 120px;
		    animation: spin 2s linear infinite;
		    position: absolute;
		    right: 25px;
		    top: 10px;
		    display: none;
		}

		@keyframes spin {
		    0% { transform: rotate(0deg); }
		    100% { transform: rotate(360deg); }
		}

		/* Media Query for mobile devices */
		@media (max-width: 768px) {
		    .habbour_ad_column-30, .habbour_ad_column-70 {
		        flex: 0 0 100%; /* Make both columns full width on mobile */
		    }
		    .habbour_ad_column a{
		    	float: left;
		    	margin: 10px;
		    }
		}
    </style>
    <div class="habbour_ad_notice notice notice-success is-dismissible">
    	<div class="habbour-loader" id="habbour-loader"></div>
    	<div class="habbour_ad_container">
		    <div class="habbour_ad_column habbour_ad_column-30">
		       <img src="<?php echo plugin_dir_url(__FILE__);?>/admin/images/wp-ultimakit-logo.webp" width="150px">
		    </div>
		    <div class="habbour_ad_column habbour_ad_column-70">
		        <!-- Content for the second column -->
		        <h1><strong>Tired of <span>25+</span> Plugins? Simplify with UltimaKit For WP</strong></h1>
		        <p><strong>The Essential WordPress Toolkit.</strong> Enhance your site with powerful features like SVG upload, user export, Gutenberg customization, admin bar control, pixel tag management, and more!</p>
		        <a href="#" class="habbour_install">Install in One Click</a>
		        <a href="javascript:void(0);" class="habbour_hide_notice" >Never Show This Again</a>
		        <a href="https://wpultimakit.com" target="_blank">Learn More About UltimaKit</a>
		    </div>
		</div>
    </div>
    <?php
	}
}
add_action( 'admin_notices', 'habbour_custom_notice_for_other_plugins' );