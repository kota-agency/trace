<?php
/**
 * Heroic Knowledge Base Embed Loader
 */

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if (!class_exists('Knowledge_Base_Embed_Frontend_Loader')) {

	class Knowledge_Base_Embed_Frontend_Loader {

		function __construct(){			
			//frontend hook
			add_action( 'wp_footer', array( $this, 'footer_loader' ) );
		}

		function footer_loader(){
			//return early exit if already a kbembed page
			if(get_query_var('kbembed')){
				return;
			}

			//return if embed not enabled
			$embed_enabled = apply_filters('kb_fe_embed_get_embed_enabled', false);
			if( !$embed_enabled ){
				return;
			}

			//dont use for in the legacy widget preview
			if ( !empty( $_GET['legacy-widget-preview'] ) ) {
				return;
			}

			$ht_kb_fe_snippet_include = apply_filters( 'ht_kb_fe_snippet_include', 'ht-kb-fe-snippet.php' );
			include_once($ht_kb_fe_snippet_include);
		}

	}//end class

}

if (class_exists('Knowledge_Base_Embed_Frontend_Loader')) {
	new Knowledge_Base_Embed_Frontend_Loader();
}