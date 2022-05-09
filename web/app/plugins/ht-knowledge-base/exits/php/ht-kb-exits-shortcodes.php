<?php
/**
* Exits module 
* Shortcodes functionality
*
*/

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists('HT_KB_Exits_Shortcodes') ){

	//define exits shortcode
	if(!defined('HKB_EXITS_SHORTCODE')){
		define('HKB_EXITS_SHORTCODE', 'hkb_exit');
	}

	class HT_KB_Exits_Shortcodes {	

		//constructor
		function __construct(){
			
			add_shortcode( HKB_EXITS_SHORTCODE , array( $this, 'ht_kb_exit_shortcode' ) );
		}

		/**
		* Call with [hkb_exit 		title="Not the solution you were looking for?" 
		*							text="Click the link below to submit a support ticket"
		*							btn="Submit Ticket"
		*							url="http://www.example.com/submit-ticket" ] 
		*/
		function ht_kb_exit_shortcode( $atts ){

			$default_url = ht_kb_exit_url_option();
			$new_window = ht_kb_exit_new_window_option() ? 'target="_blank"' : '';

			//extract arttributes
			extract(shortcode_atts(array(  
	                'title' => __('Not the solution you were looking for?', 'ht-knowledge-base'),
					'text' => __('Click the link below to submit a support ticket', 'ht-knowledge-base'),
					'btn' => __('Submit Ticket', 'ht-knowledge-base'),
					'url' => $default_url
	            ), $atts, HKB_EXITS_SHORTCODE));

			//check url not empty
			$url = empty($url) ? $default_url : $url;

			//filter hkb_exits_nofollow_tag
			$hkb_exits_nofollow_tag = apply_filters('hkb_exits_nofollow_tag', 'rel="nofollow"');
			
			$exit_shortcode = '<h3 class="hkb-exit-shortcode-title">' . $title . '</h3>';
			$exit_shortcode .= '<div class="hkb-exit-shortcode-text">' . $text . '</div>';
			$exit_shortcode .= '<a class="hkb-exit-shortcode-button button" href="' . apply_filters(HKB_EXITS_URL_FILTER_TAG, $url, 'shortcode') . '" ' . $new_window . ' ' . $hkb_exits_nofollow_tag . '>' . $btn . '</a>';

			return $exit_shortcode;

		}
	
	} //end class
} //end class exists

if(class_exists('HT_KB_Exits_Shortcodes')){
	global $ht_kb_exits_shortcodes_init;

	$ht_kb_exits_shortcodes_init = new HT_KB_Exits_Shortcodes();

}