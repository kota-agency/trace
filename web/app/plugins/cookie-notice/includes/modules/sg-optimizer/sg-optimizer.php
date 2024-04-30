<?php
// exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Cookie Notice Modules SiteGround Optimizer class.
 *
 * Compatibility since: 5.5.0
 *
 * @class Cookie_Notice_Modules_SGOptimizer
 */
class Cookie_Notice_Modules_SGOptimizer {

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		add_filter( 'sgo_javascript_combine_excluded_external_paths', [ $this, 'exclude_script' ] );
		add_filter( 'sgo_javascript_combine_excluded_inline_content', [ $this, 'exclude_code' ] );
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

new Cookie_Notice_Modules_SGOptimizer();