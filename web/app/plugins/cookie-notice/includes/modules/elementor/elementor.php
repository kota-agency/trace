<?php
// exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Cookie Notice Modules Elementor class.
 *
 * Compatibility since: 1.3.0
 *
 * @class Cookie_Notice_Modules_Elementor
 */
class Cookie_Notice_Modules_Elementor {

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		add_filter( 'cn_is_preview_mode', [ $this, 'is_preview_mode' ] );
	}

	/**
	 * Whether elementor editor is active.
	 *
	 * @return bool
	 */
	function is_preview_mode() {
		return \Elementor\Plugin::$instance->preview->is_preview_mode();
	}
}

new Cookie_Notice_Modules_Elementor();