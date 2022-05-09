<?php
/**
 * Heroic Knowledge Base Embed Loader
 */

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if (!class_exists('Knowledge_Base_Embed_Page')) {

	class Knowledge_Base_Embed_Page {

		function __construct(){		

			//wp_loaded
			add_action( 'wp_loaded', array( $this, 'wp_loaded' ) );
			//embed page anchor
			//@todo - review is this required/used?
			add_action( 'ht_kb_page_embed_anchor', array( $this, 'embed_page_anchor') );
		}

		function embed_page_anchor(){
			$embed_server_enabled = apply_filters('kb_fe_embed_get_server_enabled', false);

			if( $embed_server_enabled ):
				?>
					<div id="ht-kb-page-embed-anchor"> 
						<?php _e('Heroic Knowledge Base Page Embed Loading', 'ht-knowledge-base'); ?>
					</div>
				<?php
			else :
				?>
					<div id="ht-kb-page-embed-notenabled"> 
						<?php _e('Heroic Knowledge Base Page Embed not enabled on this site, please check settings', 'ht-knowledge-base'); ?>
					</div>
				<?php
			endif;

		}

		function wp_loaded(){

			$kbembed = isset( $_GET['kbembed'] ) ? $_GET['kbembed'] : false;
			//return early exit if not kb embed
			if( empty( $kbembed ) ){
				return;
			}
			switch ($kbembed) {
				case 'content':
					//else load our own template
					$load_file = apply_filters( 'kb_embed_page_template', plugin_dir_path( __FILE__ ) . 'embed-page-template.php' );
					include_once( $load_file );
					exit;
				case 'style':
					//load the style
					$redirect = apply_filters( 'kb_embed_style_redirect', plugins_url( 'snippet-style.css', __FILE__ ) );
					wp_redirect( $redirect );
					exit;
				case 'script':
					//load the script
					$snippet_script_src = (HKB_DEBUG_SCRIPTS) ? 'snippet-script.js' : 'snippet-script.min.js';
					$redirect = apply_filters( 'kb_embed_script_redirect', plugins_url(  $snippet_script_src, __FILE__ ) );
					wp_redirect( $redirect );
					exit;				
				default:
					break;
			}
		}


	}//end class

}

if (class_exists('Knowledge_Base_Embed_Page')) {
	new Knowledge_Base_Embed_Page();
}