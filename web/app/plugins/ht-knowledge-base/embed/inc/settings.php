<?php
/**
 * Heroic Knowledge Base Settings
 */

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if (!class_exists('Knowledge_Base_Embed_Settings')) {

	class Knowledge_Base_Embed_Settings {

		function __construct(){			
			//settings action (OK?)
			add_action( 'add_ht_kb_settings_page_additional_settings', array( $this, 'ht_kb_fe_embed_settings_section_fields' ) );

			add_action( 'ht_kb_settings_display_additional_sections', array( $this, 'ht_kb_fe_embed_settings_section_display' ) ); 

			//add embed tab
			add_action( 'ht_kb_settings_display_tabs', array( $this, 'ht_kb_embed_settings_tab' ), 5 );

			//add filter
			add_filter( 'ht_knowledge_base_settings_validate', array( $this, 'ht_kb_fe_embed_settings_validate'), 10, 2);

			//settings filters
			add_filter( 'kb_fe_embed_get_server_enabled', array( $this, 'kb_fe_embed_get_server_enabled'), 10, 1);
			add_filter( 'kb_fe_embed_get_embed_enabled', array( $this, 'kb_fe_embed_get_embed_enabled'), 10, 1);
			add_filter( 'kb_fe_embed_get_target_url', array( $this, 'kb_fe_embed_get_target_url'), 10, 1);
			add_filter( 'kb_fe_embed_get_embed_color', array( $this, 'kb_fe_embed_get_embed_color'), 10, 1);

			//add_action( 'admin_notices', array( $this, 'test' ) );

			//load settings page js 

			//load settings page css


		}

		function ht_kb_embed_settings_tab(){
			if(apply_filters('hkb_add_embed_settings_section', true)): ?>
				<a href="#embed-section" id="embed-section-link" data-section="embed"><?php _e('Embed', 'ht-knowledge-base'); ?></a>
			<?php endif;
		}

		function ht_kb_fe_embed_settings_section_display(){
			if( apply_filters('hkb_add_embed_settings_section', true) ): ?>
				<?php
					//enqueue style - can be removed once amalgameted with hkb-style-admin
					wp_enqueue_style('wp-color-picker');
					wp_enqueue_style( 'hkb-style-embed-settings', plugins_url( 'dist/ht-kb-fe-embed-settings-page.css', HT_KB_FE_EMBED_MAIN_FILE ), array(), HT_KB_VERSION_NUMBER );  
					$hkb_embed_settings_page_js_src = (HKB_DEBUG_SCRIPTS) ? 'src/ht-kb-fe-embed-settings-page.js' : 'dist/ht-kb-fe-embed-settings-page.min.js';
					//$hkb_embed_settings_page_js_src =  'src/ht-kb-fe-embed-settings-page.js' ;
					
					wp_enqueue_script( 'ht-kb-embed-settings-page', plugins_url( $hkb_embed_settings_page_js_src, HT_KB_FE_EMBED_MAIN_FILE ), array( 'jquery', 'wp-color-picker' ), HT_KB_VERSION_NUMBER, true ); 


					wp_localize_script( 'ht-kb-embed-settings-page', 
                                		'embedSettings', 
                                		array( 
                                			'siteURL' => get_site_url('/')
                                  		) 
                            ); 
				?>   
				<div id="embed-section" class="hkb-settings-section" style="display:none;">
					<?php do_settings_sections('ht_kb_settings_embed_section'); ?>   
				</div>
			<?php endif; 
		}  

		function ht_kb_fe_embed_settings_validate($output, $input){
			global $ht_knowledge_base_settings;

			//kb-fe-embed-server
			$value =  isset($input['kb-fe-embed-server']) ? 1 : 0;
			$output['kb-fe-embed-server'] = $value;

			//kb-fe-embed-enable
			$value =  isset($input['kb-fe-embed-enable']) ? 1 : 0;
			$output['kb-fe-embed-enable'] = $value;            

			//kb-fe-embed-target-url
			if( isset($input['kb-fe-embed-target-url']) ) {
				$output['kb-fe-embed-target-url'] = esc_attr($input['kb-fe-embed-target-url']);
			} else {
				$output['kb-fe-embed-target-url'] = '';
			}

			//customize-color
			$value =  isset($input['kb-fe-embed-customize-color']) && !empty($input['kb-fe-embed-customize-color']) ? esc_attr($input['kb-fe-embed-customize-color']) : '#2d9bf3';
			$output['kb-fe-embed-customize-color'] = $value;

			return $output;
		}

		function ht_kb_fe_embed_settings_section_fields(){
			add_settings_section('ht_knowledge_base_embed_settings', __('Embed Options', 'ht-knowledge-base'), array($this, 'ht_kb_settings_embed_section_description'), 'ht_kb_settings_embed_section'); 

			//add_settings_field('kb-fe-embed-title-dummy', __('Heroic Knowledge Base Embed', 'ht-knowledge-base'), array($this, 'integrations_kb_fe_embed_section_title_dummy_option_render'), 'ht_kb_settings_embed_section', 'ht_knowledge_base_embed_settings');

			add_settings_field('kb-fe-embed-server', __('Enable Embed', 'ht-knowledge-base'), array($this, 'integrations_kb_fe_embed_server_option_render'), 'ht_kb_settings_embed_section', 'ht_knowledge_base_embed_settings');

			add_settings_field('kb-fe-embed-enable', __('Show Embed on this Site', 'ht-knowledge-base'), array($this, 'integrations_kb_fe_embed_enable_option_render'), 'ht_kb_settings_embed_section', 'ht_knowledge_base_embed_settings');

			//add_settings_field('kb-fe-embed-target-url', __('Target Knowledge Base URL', 'ht-knowledge-base'), array($this, 'integrations_kb_fe_embed_target_url_option_render'), 'ht_kb_settings_embed_section', 'ht_knowledge_base_embed_settings');

			add_settings_field('kb-fe-embed-snippet-generate-dummy', __('Add Embed to External Site', 'ht-knowledge-base'), array($this, 'integrations_kb_fe_embed_snippet_generate_dummy_option_render'), 'ht_kb_settings_embed_section', 'ht_knowledge_base_embed_settings');

			add_settings_field('kb-fe-embed-customize-dummy', __('Customize Embed', 'ht-knowledge-base'), array($this, 'integrations_kb_fe_embed_section_customize_dummy_option_render'), 'ht_kb_settings_embed_section', 'ht_knowledge_base_embed_settings');

			add_settings_field('kb-fe-embed-customize-color', __('Main Color', 'ht-knowledge-base'), array($this, 'integrations_kb_fe_embed_section_customize_color_option_render'), 'ht_kb_settings_embed_section', 'ht_knowledge_base_embed_settings');

		}

		function ht_kb_settings_embed_section_description(){
			?>
				<div class="hkb-settings-embed-section-start"></div>
			<?php
		}

		function integrations_kb_fe_embed_section_title_dummy_option_render(){
			?>
				<div><?php _e('Embed Knowledge Base on this or another site', 'ht-knowledge-base'); ?></div>              
			<?php            
		}

		/**
		* Enable embed server
		*/
		function integrations_kb_fe_embed_server_option_render(){
			global $ht_knowledge_base_settings;
			if(!apply_filters('hkb_integrations_kb_fe_embed_server_option_render', true)){
				return;
			} ?>
			<?php $current_value = isset($ht_knowledge_base_settings['kb-fe-embed-server']) ? $ht_knowledge_base_settings['kb-fe-embed-server'] : 0; ?>
			<input type="checkbox" class="ht-knowledge-base-settings-kb-fe-embed-server__input" name="ht_knowledge_base_settings[kb-fe-embed-server]" value="1" <?php checked($current_value, 1); ?> ></input>
			<span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Enable the knowledge base embed functionality', 'ht-knowledge-base'); ?></span>
			<br/>                        
			<?php            
		}


		/**
		* Enable frontend embed
		*/
		function integrations_kb_fe_embed_enable_option_render(){
			global $ht_knowledge_base_settings;
			if(!apply_filters('hkb_integrations_kb_fe_embed_enable_option_render', true)){
				return;
			} ?>
			<?php $current_value = isset($ht_knowledge_base_settings['kb-fe-embed-enable']) ? $ht_knowledge_base_settings['kb-fe-embed-enable'] : 0; ?>
			<input type="checkbox" class="ht-knowledge-base-settings-kb-fe-embed-enable__input" name="ht_knowledge_base_settings[kb-fe-embed-enable]" value="1" <?php checked($current_value, 1); ?> ></input>
			<span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Show embed on this site', 'ht-knowledge-base') ?></span>
			<br/>                        
			<?php            
		}

		/**
		* Embed target url
		*/
		function integrations_kb_fe_embed_target_url_option_render(){
			global $ht_knowledge_base_settings;

			$integration_kb_fe_embed_target_url = $this->get_integration_kb_fe_embed_target_url();
			if(!apply_filters('integrations_kb_fe_embed_target_url_option_render', true)){
				return;
			} if(apply_filters('temp_kb_fe_embed_target_url_option_disable', true)){
				echo get_site_url();
				return;  
			}

			 ?>
				<input type="text" class="ht-knowledge-base-settings-kb-fe-embed-target-url__input" name="ht_knowledge_base_settings[kb-fe-embed-target-url]" value="<?php echo esc_attr($integration_kb_fe_embed_target_url); ?>" style="width: 585px;" data-control="ht-kb-embed-target-url" placeholder="https://example.com/"></input>
				<span class="hkb_setting_desc"><?php _e('Enter the web address of the target knowledge base server (that site must have the embed server enabled)', 'ht-knowledge-base'); ?></span>   
				<span class="hkb_setting_desc"><?php _e('Leave this setting blank to use current site', 'ht-knowledge-base'); ?></span>               
			<?php            
		}

		/** 
		* Generate Snippet
		*/
		function integrations_kb_fe_embed_snippet_generate_dummy_option_render(){
				$ht_kb_fe_snippet_include = apply_filters( 'ht_kb_fe_snippet_include', 'ht-kb-fe-snippet.php' );
			?>
				<a class="button button-primary" id="hkb-setting-show-embed-snippet"><?php _e('Show Snippet', 'ht-knowledge-base'); ?></a>	
				<div id="hkb-setting-embed-snippet-section" class="hkb-setting-embed-snippet-section"> 
					<span class="hkb-setting-embed-snippet-info"><?php _e('Copy and paste this into the footer (eg footer.php) of the site where you wish to embed the knowledge base', 'ht-knowledge-base'); ?></span>
					<div contenteditable="true" id="hkb-setting-embed-snippet" class="hkb-setting-embed-snippet"> 		             
						<?php 
								ob_start();
								include_once($ht_kb_fe_snippet_include);
								$output = ob_get_clean();
								echo nl2br(htmlspecialchars( $output ));  

						?>
					</div>
				</div>
			<?php
		}

		function integrations_kb_fe_embed_section_customize_dummy_option_render(){
			?>
				<div></div>              
			<?php            
		}

		/**
		* Embed color
		*/
		function integrations_kb_fe_embed_section_customize_color_option_render(){
			global $ht_knowledge_base_settings;

			$integration_kb_fe_embed_color = $this->get_integration_kb_fe_embed_color();
			if(!apply_filters('integrations_kb_fe_embed_color_option_render', true)){
				return;
			} 

			 ?>
				<input type="text" class="ht-knowledge-base-settings-kb-fe-embed-customize-color__input" name="ht_knowledge_base_settings[kb-fe-embed-customize-color]" value="<?php echo esc_attr($integration_kb_fe_embed_color); ?>" data-control="ht-kb-embed-customize-color" placeholder=""></input>
				<span class="hkb_setting_desc"><?php _e('Color of embed', 'ht-knowledge-base'); ?></span>             
			<?php            
		}


		/**
		* Get the server url settings
		*/
		function get_integration_kb_fe_embed_target_url(){
			global $ht_knowledge_base_settings;
			$integration_kb_fe_embed_target_url = isset($ht_knowledge_base_settings['kb-fe-embed-target-url']) ? $ht_knowledge_base_settings['kb-fe-embed-target-url'] : '';

			return $integration_kb_fe_embed_target_url;
		}

		/**
		* Get the color
		*/
		function get_integration_kb_fe_embed_color(){
			global $ht_knowledge_base_settings;
			$integration_kb_fe_embed_color = isset($ht_knowledge_base_settings['kb-fe-embed-customize-color']) ? $ht_knowledge_base_settings['kb-fe-embed-customize-color'] : '#2d9bf3';

			return $integration_kb_fe_embed_color;
		}

		function kb_fe_embed_get_server_enabled( $bool ){
			global $ht_knowledge_base_settings;
			$kb_fe_embed_get_server_enabled = isset($ht_knowledge_base_settings['kb-fe-embed-server']) ? $ht_knowledge_base_settings['kb-fe-embed-server'] : 0;

			return (bool) $kb_fe_embed_get_server_enabled;
		}

		function kb_fe_embed_get_embed_enabled( $bool ){
			global $ht_knowledge_base_settings;
			$kb_fe_embed_get_embed_enabled = isset($ht_knowledge_base_settings['kb-fe-embed-enable']) ? $ht_knowledge_base_settings['kb-fe-embed-enable'] : 0;

			return (bool) $kb_fe_embed_get_embed_enabled;
		}

		function kb_fe_embed_get_target_url( $url ){
			$base_url = $this->get_integration_kb_fe_embed_target_url();
			//if the setting is empty, use current site
			if(empty($base_url)){
				$base_url = get_site_url();
			}
			$affix = '/?kbembed=content';
			return $base_url . $affix;
		}

		function kb_fe_embed_get_embed_color( $color ){
			$embed_color = $this->get_integration_kb_fe_embed_color();
			return $embed_color;
		}


	}//end class

}

if (class_exists('Knowledge_Base_Embed_Settings')) {
	new Knowledge_Base_Embed_Settings();
}