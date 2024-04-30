<?php
// exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Cookie Notice Modules WP Fastest Cache class.
 *
 * Compatibility since: 1.0.0
 *
 * @class Cookie_Notice_Modules_WPFastestCache
 */
class Cookie_Notice_Modules_WPFastestCache {

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'check_wpfc' ], 11 );
	}

	/**
	 * Add compatibility to WP Fastest Cache plugin.
	 *
	 * @return void
	 */
	public function check_wpfc() {
		// is caching enabled?
		if ( isset( $GLOBALS['wp_fastest_cache_options']->wpFastestCacheStatus ) ) {
			// update 2.4.9+
			if ( version_compare( Cookie_Notice()->db_version, '2.4.9', '<=' ) )
				$this->delete_cache();

			// delete cache files after updating settings or status
			add_action( 'cn_configuration_updated', [ $this, 'delete_cache' ] );
		}
	}

	/**
	 * Delete all cache files.
	 *
	 * @return void
	 */
	public function delete_cache() {
		if ( isset( $GLOBALS['wp_fastest_cache'] ) && method_exists( $GLOBALS['wp_fastest_cache'], 'deleteCache' ) )
			$GLOBALS['wp_fastest_cache']->deleteCache( false );
	}
}

new Cookie_Notice_Modules_WPFastestCache();