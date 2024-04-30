<?php
// exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

use Hummingbird\Core\Utils;

/**
 * Cookie Notice Modules Hummingbird class.
 *
 * Compatibility since: 2.1.0
 *
 * @class Cookie_Notice_Modules_Hummingbird
 */
class Cookie_Notice_Modules_Hummingbird {

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'check_hb' ], 11 );
	}

	/**
	 * Add compatibility to Hummingbird plugin.
	 *
	 * @return void
	 */
	public function check_hb() {
		if ( class_exists( 'Hummingbird\Core\Utils' ) ) {
			// get caching module
			$mod = Utils::get_module( 'page_cache' );

			// is caching enabled?
			if ( $mod->is_active() ) {
				// delete cache files after updating settings or status
				add_action( 'cn_configuration_updated', [ $this, 'delete_cache' ] );
			}
		}
	}

	/**
	 * Delete all cache files.
	 *
	 * @return void
	 */
	public function delete_cache() {
		do_action( 'wphb_clear_page_cache' );
	}
}

new Cookie_Notice_Modules_Hummingbird();