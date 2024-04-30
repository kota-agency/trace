<?php
// exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Check if cookies are accepted.
 *
 * @return bool Whether cookies are accepted
 */
if ( ! function_exists( 'cn_cookies_accepted' ) ) {
	function cn_cookies_accepted() {
		return (bool) Cookie_Notice::cookies_accepted();
	}
}

/**
 * Check if cookies are set.
 *
 * @return bool Whether cookies are set
 */
if ( ! function_exists( 'cn_cookies_set' ) ) {
	function cn_cookies_set() {
		return (bool) Cookie_Notice::cookies_set();
	}
}

/**
 * Get active caching plugins.
 *
 * @param array $args
 * @return array
 */
function cn_get_active_caching_plugins( $args = [] ) {
	if ( isset( $args['versions'] ) && $args['versions'] === true )
		$version = true;
	else
		$version = false;

	$active_plugins = [];

	// autoptimize 2.4.0+
	if ( cn_is_plugin_active( 'autoptimize' ) ) {
		if ( $version )
			$active_plugins['Autoptimize'] = '2.4.0';
		else
			$active_plugins[] = 'Autoptimize';
	}

	// wp-optimize 3.0.12+
	if ( cn_is_plugin_active( 'wpoptimize' ) ) {
		if ( $version )
			$active_plugins['WP-Optimize'] = '3.0.12';
		else
			$active_plugins[] = 'WP-Optimize';
	}

	// litespeed 3.0.0+
	if ( cn_is_plugin_active( 'litespeed' ) ) {
		if ( $version )
			$active_plugins['LiteSpeed Cache'] = '3.0.0';
		else
			$active_plugins[] = 'LiteSpeed Cache';
	}

	// siteground optimizer 5.5.0+
	if ( cn_is_plugin_active( 'sgoptimizer' ) ) {
		if ( $version )
			$active_plugins['SiteGround Optimizer'] = '5.5.0';
		else
			$active_plugins[] = 'SiteGround Optimizer';
	}

	// wp fastest cache 1.0.0+
	if ( cn_is_plugin_active( 'wpfastestcache' ) ) {
		if ( $version )
			$active_plugins['WP Fastest Cache'] = '1.0.0';
		else
			$active_plugins[] = 'WP Fastest Cache';
	}

	// wp rocket 3.8.0+
	if ( cn_is_plugin_active( 'wprocket' ) ) {
		if ( $version )
			$active_plugins['WP Rocket'] = '3.8.0';
		else
			$active_plugins[] = 'WP Rocket';
	}

	// hummingbird 2.1.0+
	if ( cn_is_plugin_active( 'hummingbird' ) ) {
		if ( $version )
			$active_plugins['Hummingbird'] = '2.1.0';
		else
			$active_plugins[] = 'Hummingbird';
	}

	// wp super cache 1.6.9+
	if ( cn_is_plugin_active( 'wpsupercache' ) ) {
		if ( $version )
			$active_plugins['WP Super Cache'] = '1.6.9';
		else
			$active_plugins[] = 'WP Super Cache';
	}

	return $active_plugins;
}

/**
 * Check whether specified plugin is active.
 *
 * @global object $siteground_optimizer_loader
 * @global int $wpsc_version
 *
 * @return bool
 */
function cn_is_plugin_active( $plugin = '' ) {
	// no valid plugin?
	if ( ! in_array( $plugin, [ 'autoptimize', 'wpoptimize', 'litespeed', 'sgoptimizer', 'wpfastestcache', 'wprocket', 'wpsupercache', 'contactform7', 'elementor', 'amp', 'hummingbird' ], true ) )
		return false;

	// set default flag
	$is_plugin_active = false;

	switch ( $plugin ) {
		// autoptimize 2.4.0+
		case 'autoptimize':
			if ( function_exists( 'autoptimize' ) && defined( 'AUTOPTIMIZE_PLUGIN_VERSION' ) && version_compare( AUTOPTIMIZE_PLUGIN_VERSION, '2.4', '>=' ) )
				$is_plugin_active = true;
			break;

		// wp-optimize 3.0.12+
		case 'wpoptimize':
			if ( function_exists( 'WP_Optimize' ) && defined( 'WPO_VERSION' ) && version_compare( WPO_VERSION, '3.0.12', '>=' ) )
				$is_plugin_active = true;
			break;

		// litespeed 3.0.0+
		case 'litespeed':
			if ( class_exists( 'LiteSpeed\Core' ) && defined( 'LSCWP_CUR_V' ) && version_compare( LSCWP_CUR_V, '3.0', '>=' ) )
				$is_plugin_active = true;
			break;

		// siteground optimizer 5.5.0+
		case 'sgoptimizer':
			global $siteground_optimizer_loader;

			if ( ! empty( $siteground_optimizer_loader ) && is_object( $siteground_optimizer_loader ) && is_a( $siteground_optimizer_loader, 'SiteGround_Optimizer\Loader\Loader' ) && defined( '\SiteGround_Optimizer\VERSION' ) && version_compare( \SiteGround_Optimizer\VERSION, '5.5', '>=' ) )
				$is_plugin_active = true;
			break;

		// wp fastest cache 1.0.0+
		case 'wpfastestcache':
			if ( function_exists( 'wpfc_clear_all_cache' ) )
				$is_plugin_active = true;
			break;

		// wp rocket 3.8.0+
		case 'wprocket':
			if ( function_exists( 'rocket_init' ) && defined( 'WP_ROCKET_VERSION' ) && version_compare( WP_ROCKET_VERSION, '3.8', '>=' ) )
				$is_plugin_active = true;
			break;

		// wp super cache 1.6.9+
		case 'wpsupercache':
			global $wpsc_version;

			if ( ( ! empty( $wpsc_version ) && $wpsc_version >= 169 ) || ( defined( 'WPSC_VERSION' ) && version_compare( WPSC_VERSION, '1.6.9', '>=' ) ) )
				$is_plugin_active = true;
			break;

		// contact form 7 5.1.0+
		case 'contactform7':
			if ( class_exists( 'WPCF7' ) && class_exists( 'WPCF7_RECAPTCHA' ) && defined( 'WPCF7_VERSION' ) && version_compare( WPCF7_VERSION, '5.1', '>=' ) )
				$is_plugin_active = true;
			break;

		// elementor 1.3.0+
		case 'elementor':
			if ( did_action( 'elementor/loaded' ) && defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '1.3', '>=' ) )
				$is_plugin_active = true;
			break;

		// amp 2.0.0+
		case 'amp':
			if ( function_exists( 'amp_is_enabled' ) && defined( 'AMP__VERSION' ) && version_compare( AMP__VERSION, '2.0', '>=' ) )
				$is_plugin_active = true;
			break;

		// hummingbird 2.1.0+
		case 'hummingbird':
			if ( class_exists( 'Hummingbird\\WP_Hummingbird' ) && defined( 'WPHB_VERSION' ) && version_compare( WPHB_VERSION, '2.1.0', '>=' ) )
				$is_plugin_active = true;
			break;
	}

	return $is_plugin_active;
}