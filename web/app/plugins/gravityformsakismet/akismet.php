<?php
/*
Plugin Name: Gravity Forms Akismet Add-On
Plugin URI: https://gravityforms.com
Description: Enhance Gravity Forms with advanced spam protection, powered by Akismet.
Version: 1.0
Author: Gravity Forms
Author URI: https://gravityforms.com
License: GPL-3.0+
Text Domain: gravityformsakismet
Domain Path: /languages

------------------------------------------------------------------------
Copyright 2020 Rocketgenius Inc.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see http://www.gnu.org/licenses.

*/

defined( 'ABSPATH' ) || die();

// Defines the current version of the Gravity Forms Akismet Add-On.
define( 'GF_AKISMET_VERSION', '1.0' );

// Defines the minimum version of Gravity Forms required to run Gravity Forms Akismet Add-On.
define( 'GF_AKISMET_MIN_GF_VERSION', '2.4.18.5' );

// After Gravity Forms is loaded, load the Add-On.
add_action( 'gform_loaded', array( 'GF_Akismet_Bootstrap', 'load_addon' ), 5 );

use Gravity_Forms\Gravity_Forms_Akismet\GF_Akismet;

/**
 * Loads the Gravity Forms Akismet Add-On.
 *
 * Includes the main class and registers it with GFAddOn.
 *
 * @since 1.0
 */
class GF_Akismet_Bootstrap {

	/**
	 * Loads the required files.
	 *
	 * @since  1.0
	 */
	public static function load_addon() {

		// Requires the class file.
		require_once plugin_dir_path( __FILE__ ) . '/class-gf-akismet.php';

		// Registers the class name with GFAddOn.
		GFAddOn::register( 'Gravity_Forms\Gravity_Forms_Akismet\GF_Akismet' );
	}

}

/**
 * Returns an instance of the GF_Akismet class.
 *
 * @since  1.0
 *
 * @return GF_Akismet|bool An instance of the GF_Akismet class
 */
function gf_akismet() {
	return class_exists( 'Gravity_Forms\Gravity_Forms_Akismet\GF_Akismet' ) ? GF_Akismet::get_instance() : false;
}
