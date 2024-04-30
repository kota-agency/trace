<?php
// exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Cookie Notice Modules WP-Optimize class.
 *
 * Compatibility since: 3.0.12
 *
 * @class Cookie_Notice_Modules_WPOptimize
 */
class Cookie_Notice_Modules_WPOptimize {

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		// has to be executed on plugins_loaded with priority 0
		$this->check_wpo();
	}

	/**
	 * Add compatibility to WP-Optimize plugin.
	 *
	 * @return void
	 */
	public function check_wpo() {
		// get wp-optimize configuration
		if ( class_exists( 'WPO_Cache_Config' ) ) {
			$options = WPO_Cache_Config::instance()->get();

			// is caching enabled?
			if ( ! empty( $options['enable_page_caching'] ) )
				add_filter( 'wpo_purge_cache_hooks', [ $this, 'add_purge_cache' ] );
		}
	}

	/**
	 * Add action when cache is purged.
	 *
	 * @param array $actions
	 * @return array
	 */
	public function add_purge_cache( $actions ) {
		$actions[] = 'cn_configuration_updated';

		return $actions;
	}
}

new Cookie_Notice_Modules_WPOptimize();