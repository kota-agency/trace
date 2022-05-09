<?php
/**
* WPML Compat functions
*/

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


if (!class_exists('HT_Knowledge_Base_WPML_Compat')) {

	class HT_Knowledge_Base_WPML_Compat {

		//Constructor
		function __construct() {

			//translate archive page id using wpml_object_id filter
			add_filter( 'ht_kb_get_kb_archive_page_id', array ($this, 'wpml_translate_ht_kb_get_kb_archive_page_id' ), 10, 2 );

		}

		function wpml_translate_ht_kb_get_kb_archive_page_id( $hkb_archive_page_id, $kb_key = false ){
			//apply wpml translations as required
			return apply_filters( 'wpml_object_id', $hkb_archive_page_id, 'page' );
		}

	} //end class
}//end class exists

//run the module
if(class_exists('HT_Knowledge_Base_WPML_Compat')){
	new HT_Knowledge_Base_WPML_Compat();
}