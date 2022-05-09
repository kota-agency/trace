<?php
/**
* Sidebar / Widget Functionality
* @since 3.0.0
*/

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'HT_Knowledge_Base_Sidebars' ) ){
	class HT_Knowledge_Base_Sidebars {

		//constructor
		function __construct() {
			//register sidebars
			add_action( 'widgets_init', array( $this, 'ht_kb_register_sidebars' ) );
		}

		function ht_kb_register_sidebars(){

			//home sidebar
			$ht_kb_sidebar_home = array(
				'name'          => __( 'Knowledge Base Sidebar (Home)', 'ht-knowledge-base' ),
				'id'            => 'ht-kb-sidebar-archive',
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			);
			$ht_kb_sidebar_home = apply_filters( 'ht_kb_sidebar_home', $ht_kb_sidebar_home );
			if( apply_filters( 'ht_kb_home_register_sidebar', true ) ){
				register_sidebar( $ht_kb_sidebar_home );
			}			

			//taxonomy sidebar
			$ht_kb_sidebar_taxonomy = array(
				'name'          => __( 'Knowledge Base Sidebar (Categories and Tags)', 'ht-knowledge-base' ),
				'id'            => 'ht-kb-sidebar-taxonomy',
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			);
			$ht_kb_sidebar_taxonomy = apply_filters( 'ht_kb_sidebar_taxonomy', $ht_kb_sidebar_taxonomy );
			if( apply_filters( 'ht_kb_taxonomy_register_sidebar', true ) ){
				register_sidebar( $ht_kb_sidebar_taxonomy );
			}

			//article sidebar
			$ht_kb_sidebar_article =  array(
				'name'          => __( 'Knowledge Base Sidebar (Article)', 'ht-knowledge-base' ),
				'id'            => 'ht-kb-sidebar-article',
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			);
			$ht_kb_sidebar_article = apply_filters( 'ht_kb_sidebar_article', $ht_kb_sidebar_article );
			if( apply_filters( 'ht_kb_article_register_sidebar', true ) ){
				register_sidebar( $ht_kb_sidebar_article );
			}
		}

	}
}

//run the module
if( class_exists( 'HT_Knowledge_Base_Sidebars' ) ){
	new HT_Knowledge_Base_Sidebars();
}