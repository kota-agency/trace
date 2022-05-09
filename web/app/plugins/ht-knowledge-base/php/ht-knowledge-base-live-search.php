<?php
/**
* Live search extension
*/

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'HT_Knowledge_Base_Live_Search' ) ){
	class HT_Knowledge_Base_Live_Search {

		public $add_script;

		//Constructor
		function __construct(){

			//3.3.0, activation action
			add_action( 'ht_knowledge_base_activate_live_search', array( $this, 'ht_knowledge_base_activate_live_search' ) );
			//search template
			add_filter( 'search_template', array( $this, 'ht_knowledge_base_live_search_template' ) );
			//register scripts
			add_action( 'wp_enqueue_scripts', array( $this, 'ht_knowledge_base_live_search_register_scripts' ) );	
			//add filter to print editor styles and scripts
			add_action( 'wp_footer', array( $this, 'ht_knowledge_base_live_search_print_scripts' ) );
			//add filter for hkb_search_url (used to display search url)
			add_filter( 'hkb_search_url', array( $this, 'ht_knowledge_base_search_url_filter' ), 10, 2 );
		}

		/**
		* Search results url filter
		*/
		function ht_knowledge_base_search_url_filter( $s=false, $ajax = false ){
			$search_affix = '?';
			if($ajax){
				$search_affix .= 'ajax=1&';
			} else {
				//no action required
			}
			$search_affix .= 'ht-kb-search=1&';
			
			//if wpml is installed append language code if not in default language
			global $sitepress;
			if(defined('ICL_LANGUAGE_CODE') && isset($sitepress)){
				$default_lang = $sitepress->get_default_language();
				$affix_format = $sitepress->get_setting( 'language_negotiation_type');

				switch ($affix_format) {
					case 1:
						//directory, eg example.com/en/?s=test
						if($default_lang != ICL_LANGUAGE_CODE ){
							//2.23 update - new WPML switch
							$search_affix = '/' . ICL_LANGUAGE_CODE . '/' . $search_affix;
						}
						break;
					case 2:
						//subdomain, eg en.example.com/?s=test
						//no modification required?
						break;						
					case 3:
						//parameter, eg example.com/?s=test&lang=en
						if($default_lang != ICL_LANGUAGE_CODE ){
							$search_affix .= 'lang=' . ICL_LANGUAGE_CODE . '&';
						}
						break;					
					default:
						break;
				}
								
			}

			//polylang beta support (note polylang not yet fully supported)
			elseif(defined('ICL_LANGUAGE_CODE') && function_exists('pll_current_language')){
				$language = pll_current_language();
				$search_affix .= 'lang=' . $language . '&';								
			}
			
			$search_affix .= 's=';
			if($s){
				//ht_kb_search_sanitize_text allows you to use your own santize function
				$search_affix .= apply_filters( 'ht_kb_search_sanitize_text', sanitize_text_field($s), $s );
			}

			//apply filters over affix
			$search_affix = apply_filters( 'ht_kb_search_affix', $search_affix );

			//get home url, 2.23 - replace home_url with get_site_url
			$search_home = apply_filters( 'ht_kb_search_home_url', get_site_url() );

			//designed for applying aribtary paths such as knowledge-base/ etc
			$search_base = apply_filters( 'ht_kb_search_base', '' );

			//build search url
			$search_url = $search_home . $search_base . $search_affix;
			$search_url = apply_filters( 'hkb_search_url_result', $search_url );

			//deprecated behavior of ht_kb_search_home_url
			//return apply_filters( 'ht_kb_search_home_url', home_url( $search_url ) );

			return $search_url;
		}

		/**
		* Live search results functionality
		*/
		function ht_knowledge_base_live_search_template( $template ){
			//ensure this is a live search
			$ht_kb_search = ( array_key_exists('ht-kb-search', $_REQUEST) ) ? true : false;
			if( $ht_kb_search == false )
				return $template;

			if(!empty($_GET['ajax']) ? $_GET['ajax'] : null) { // Is Live Search 
				//check custom search

				//search string
				global $s;
				// Get FAQ cpt
				$ht_kb_cpt = 'ht_kb';

				if( is_string($s) && strlen($s) > apply_filters( 'ht_kb_livesearch_trigger_length', 3 ) ){
					hkb_get_template_part('hkb-search-ajax');
					wp_reset_query();

				}

				//required to stop 
				die();
			} else {
				//non ajax search
				return $template;
			}
		}

		/**
		* Enqueue the javascript for live search
		*/
		function ht_knowledge_base_live_search_register_scripts(){
			if(SCRIPT_DEBUG){
				//register live search script
				wp_register_script('ht-kb-live-search-plugin', plugins_url( 'js/jquery.livesearch.js', dirname( __FILE__ ) ), array( 'jquery' ), HT_KB_VERSION_NUMBER, true);
				$hkb_livesearch_js_src = 'js/hkb-livesearch-js.js';
				wp_register_script('ht-kb-live-search', plugins_url( $hkb_livesearch_js_src, dirname( __FILE__ ) ), array( 'jquery', 'ht-kb-live-search-plugin' ), HT_KB_VERSION_NUMBER, true);
				$this->ht_knowledge_base_localize_live_search_scripts('ht-kb-live-search');
			} else {
				wp_register_script('ht-kb-frontend-scripts', plugins_url( 'dist/ht-kb-frontend.min.js' , HT_KB_MAIN_PLUGIN_FILE ), array( 'jquery' ), HT_KB_VERSION_NUMBER, true);
				$this->ht_knowledge_base_localize_live_search_scripts('ht-kb-frontend-scripts');
			}
			
		}

		function ht_knowledge_base_localize_live_search_scripts($script_handle){
			global $wp_customize;
			//don't focus search if in WP Customizer
			if ( !isset( $wp_customize ) ) {
				$focus_searchbox = !ht_kb_is_ht_kb_search() && hkb_focus_on_search_box();
			} else {
				$focus_searchbox = false;
			}
			$search_url = apply_filters('hkb_search_url', false, true);

			// 3.3.0 - added sprintf functionality and make translatable with helper function
			$keep_typing_text = apply_filters( 'hkb_search_keep_typing_text', __('Keep typing for live search results', 'ht-knowledge-base') );
			$keep_typing_prompt = sprintf( '<ul id="hkb" class="hkb-searchresults" role="listbox"><li class="hkb-searchresults__noresults" role="option"><span>%s</span> </li></ul>', $keep_typing_text );

			wp_localize_script( $script_handle, 'hkbJSSettings', array( 
				'liveSearchUrl' => $search_url, 
				'focusSearchBox' => $focus_searchbox,
				'keepTypingPrompt' => $keep_typing_prompt,
				'triggerLength' => apply_filters( 'ht_kb_livesearch_trigger_length', 3 ),
				)
			);
		}

		/**
		* Print the javascript for live search
		*/
		function ht_knowledge_base_live_search_print_scripts() {

			global $ht_kb_frontend_scripts_loaded;
			if ( ! $this->add_script )
				return;

			if(SCRIPT_DEBUG){
				wp_print_scripts('ht-kb-live-search-plugin');
				wp_print_scripts('ht-kb-live-search');	
			} else {
				if(!$ht_kb_frontend_scripts_loaded){
					wp_print_scripts('ht-kb-frontend-scripts');
					$ht_kb_frontend_scripts_loaded = true;
				}
			}
			
		}


		/**
		* Activate live search
		*/
		function ht_knowledge_base_activate_live_search(){
			if( !apply_filters('ht_kb_disable_live_search', false ) ){
				$this->add_script = true;
			}				
		}



    }//end class
}//end class test

//run the module
if(class_exists('HT_Knowledge_Base_Live_Search')){
	global $ht_knowledge_base_live_search_init;	
	$ht_knowledge_base_live_search_init = new HT_Knowledge_Base_Live_Search();
	
	/**
	* @deprecated - use do_action('ht_knowledge_base_activate_live_search') instead
	* This function currently works, but will be removed in an upcoming release
	*/
	function ht_knowledge_base_activate_live_search(){
		global $ht_knowledge_base_live_search_init;	
		_doing_it_wrong( 'ht_knowledge_base_activate_live_search()', __('Do not use ht_knowledge_base_activate_live_search() directly, use do_action(\'ht_knowledge_base_activate_live_search\') instead', 'ht-knowledge-base'), '3.3.0' );	
		$ht_knowledge_base_live_search_init->ht_knowledge_base_activate_live_search();
	}
}