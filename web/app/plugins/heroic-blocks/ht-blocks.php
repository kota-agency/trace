<?php
/*
*	Plugin Name: Heroic Blocks
*	Plugin URI:  https://herothemes.com/heroic-blocks
*	Description: Blocks for HeroThemes Products
*	Author: HeroThemes
*	Version: 1.2.2
*	Author URI: http://www.herothemes.com/
*	Text Domain: ht-blocks
*/


if( !class_exists( 'HT_Blocks' ) ){

	//load block modules
	include_once('ht-blocks-modules.php');

	class HT_Blocks {
		//Constructor
		function __construct(){
			global $wp_version;
			//load the text domain
			load_plugin_textdomain( 'ht-blocks', false, basename( dirname( __FILE__ ) ) . '/languages' );
			//add block category
			if ( version_compare( $wp_version, '5.8', '<' ) ) {
				add_filter( 'block_categories', array( $this, 'ht_blocks_add_block_category' ), 10, 2);
			} else {
				add_filter( 'block_categories_all', array( $this, 'ht_blocks_add_block_category' ), 10, 2);	
			}	
			
		}

		/**
		* Add custom block category
		* @param $categories
		* @param $post
		* @return $categories
		*/
		function ht_blocks_add_block_category( $categories, $post ){
			$categories[] = array( 'slug' => 'heroic-blocks', 'title' => __( 'Heroic Blocks', 'ht-blocks' ) );
			return $categories;
		}

	} //end class HT_Blocks

}//end class exists test

//run the plugin
if( class_exists( 'HT_Blocks' ) ){
	$ht_blocks_init = new HT_Blocks();
}