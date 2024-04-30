<?php
// exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Cookie Notice Modules WP Rocket Optimizer class.
 *
 * Compatibility since: 3.8.0
 *
 * @class Cookie_Notice_Modules_WPRocket
 */
class Cookie_Notice_Modules_WPRocket {

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		add_filter( 'rocket_exclude_defer_js', [ $this, 'exclude_script' ] );
		add_filter( 'rocket_defer_inline_exclusions ', [ $this, 'exclude_code' ] );
	}

	/**
	 * Exclude JavaScript file.
	 *
	 * @param array $excludes
	 * @return array
	 */
	function exclude_script( $excludes ) {
		// add widget url
		$excludes[] = basename( Cookie_Notice()->get_url( 'widget' ) );

		return $excludes;
	}

	/**
	 * Exclude JavaScript inline code.
	 *
	 * @param array $excludes
	 * @return array
	 */
	function exclude_code( $excludes ) {
		// add widget inline code
		$excludes[] = 'huOptions';

		return $excludes;
	}
}

new Cookie_Notice_Modules_WPRocket();