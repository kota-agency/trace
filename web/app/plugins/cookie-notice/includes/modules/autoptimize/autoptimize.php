<?php
// exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Cookie Notice Modules Autoptimize class.
 *
 * Compatibility since: 2.4.0
 *
 * @class Cookie_Notice_Modules_Autoptimize
 */
class Cookie_Notice_Modules_Autoptimize {

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		add_filter( 'autoptimize_filter_js_exclude', [ $this, 'exclude' ] );
	}

	/**
	 * Exclude JavaScript files or inline code.
	 *
	 * @param string $excludes
	 * @return string
	 */
	function exclude( $excludes ) {
		if ( empty( $excludes ) )
			$new_excludes = [];
		else {
			$new_excludes = explode( ',', $excludes );
			$new_excludes = array_filter( $new_excludes );
			$new_excludes = array_map( 'trim', $new_excludes );
		}

		// not found huOptions?
		if ( strpos( $excludes, 'huOptions' ) === false )
			$new_excludes[] = 'huOptions';

		// get widget url
		$widget_url = basename( Cookie_Notice()->get_url( 'widget' ) );

		// not found widget url?
		if ( strpos( $excludes, $widget_url ) === false )
			$new_excludes[] = $widget_url;

		return implode( ', ', $new_excludes );
	}
}

new Cookie_Notice_Modules_Autoptimize();