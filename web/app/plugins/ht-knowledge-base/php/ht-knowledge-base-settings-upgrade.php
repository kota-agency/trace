<?php
/**
 * v < 2.6.5 Upgrade Routines for New Settings Page
 */

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if (!class_exists('HT_Knowledge_Base_Settings_Upgrade')) {

	if(!defined('HT_KB_OLD_SETTINGS_KEY')){
		define('HT_KB_OLD_SETTINGS_KEY', 'ht_knowledge_base_options');
	}

	if(!defined('HT_KB_NEW_SETTINGS_KEY')){
		define('HT_KB_NEW_SETTINGS_KEY', 'ht_knowledge_base_settings');
	}

	if(!defined('HT_KB_TWO_SEVEN_UPGRADE_KEY')){
		define('HT_KB_TWO_SEVEN_UPGRADE_KEY', 'ht_knowledge_base_2_7_upgrade_complete');
	}

	if(!defined('HT_KB_TWO_SEVEN_BACKUP_SETTINGS_KEY')){
		define('HT_KB_TWO_SEVEN_BACKUP_SETTINGS_KEY', 'ht_knowledge_base_2_7_settings_backup');
	}

	if(!defined('HT_KB_THREE_ZERO_UPGRADE_KEY')){
		define('HT_KB_THREE_ZERO_UPGRADE_KEY', 'ht_knowledge_base_3_0_upgrade_complete');
	}

	class HT_Knowledge_Base_Settings_Upgrade {

		private $old_settings_array;
		private $new_settings_array;

		//constructor
		function __construct(){  
			//upgrade functionality hooked to ht_kb_activate and maybe_upgrade_ht_kb_settings_fields
			add_action( 'maybe_upgrade_ht_kb_settings_fields', array($this, 'ht_kb_settings_upgrade'), 10 );
			add_action( 'ht_kb_activate', array( $this, 'ht_kb_settings_upgrade' ), 10 ); 
			add_action( 'maybe_upgrade_ht_kb_settings_fields', array($this, 'set_kb_archive_pages'), 10 );
			add_action( 'ht_kb_activate', array( $this, 'set_kb_archive_pages' ), 10, 1);
		}

		/**
		* Settings upgrade routine
		*/
		function ht_kb_settings_upgrade( $network_wide = null ){

			//populate new settings array
			$this->new_settings_array = get_option(HT_KB_NEW_SETTINGS_KEY);
			if(false==$this->new_settings_array){
				//initialize array
				$this->new_settings_array = array();
			}

			//test upgrade already done, exit as no further action required
			if(get_option(HT_KB_TWO_SEVEN_UPGRADE_KEY)){
				return;    
			} else {
				//else initialize new settings array
				$this->new_settings_array = array();
			}
				   

			//populate old settings array
			$this->old_settings_array = get_option(HT_KB_OLD_SETTINGS_KEY);


			//MAPPINGS

			//breadcrumbs-display => display-breadcrumbs
			$this->upgrade_setting('breadcrumbs-display', 'display-breadcrumbs');

			//sort-by => sort-by
			$this->upgrade_setting('sort-by', 'sort-by');

			//sort-order => sort-order
			$this->upgrade_setting('sort-order', 'sort-order');

			//tax-cat-article-number  => num-articles
			$this->upgrade_setting('tax-cat-article-number', 'num-articles');

			//kb-home => N/A         
			//N/A

			//archive-columns => archive-columns
			$this->upgrade_setting('archive-columns', 'archive-columns');

			//sub-cat-article-count   => display-article-count
			$this->upgrade_setting('sub-cat-article-count', 'display-article-count');

			//sub-cat-article-number  => num-articles-home
			$this->upgrade_setting('sub-cat-article-number', 'num-articles-home');

			//sub-cat-display => display-sub-cats
			$this->upgrade_setting('sub-cat-display', 'display-sub-cats');

			//sub-cat-depth  => sub-cat-depth
			$this->upgrade_setting('sub-cat-depth', 'sub-cat-depth');

			//sub-cat-article-display => display-sub-cat-articles
			$this->upgrade_setting('sub-cat-article-display', 'display-sub-cat-articles');

			//hide-empty-kb-categories    => hide-empty-cats
			$this->upgrade_setting('hide-empty-kb-categories', 'hide-empty-cats');

			//article-comments  => enable-article-comments
			$this->upgrade_setting('article-comments', 'enable-article-comments');

			//usefulness-display  => display-article-usefulness
			$this->upgrade_setting('usefulness-display', 'display-article-usefulness');

			//viewcount-display   =>display-article-views-count
			$this->upgrade_setting('viewcount-display', 'display-article-views-count');

			//comments-display    =>display-article-comment-count
			$this->upgrade_setting('comments-display', 'display-article-comment-count');

			//related-display =>display-related-articles
			$this->upgrade_setting('related-display', 'display-related-articles');

			//search-display  =>display-live-search
			$this->upgrade_setting('search-display', 'display-live-search');

			//search-focus-box    =>focus-live-search
			$this->upgrade_setting('search-focus-box', 'focus-live-search');

			//search-placeholder-text =>search-placeholder-text
			$this->upgrade_setting('search-placeholder-text', 'search-placeholder-text');

			//search-types    array   {'ht-kb'}    
			//NA           

			//search-excerpt  => display-search-result-excerpt
			$this->upgrade_setting('search-excerpt', 'display-search-result-excerpt');

			//ht-kb-slug  => kb-article-slug
			$this->upgrade_setting('ht-kb-slug', 'kb-article-slug');

			//ht-kb-cat-slug  =>  kb-category-slug
			$this->upgrade_setting('ht-kb-cat-slug', 'kb-category-slug');

			//ht-kb-tag-slug  =>  kb-tag-slug
			$this->upgrade_setting('ht-kb-tag-slug', 'kb-tag-slug');

			//ht-kb-custom-styles =>  custom-kb-styling-content
			$this->upgrade_setting('ht-kb-custom-styles', 'custom-kb-styling-content');

			//ht-kb-custom-styles-sitewide    => enable-kb-styling-sitewide
			$this->upgrade_setting('ht-kb-custom-styles-sitewide', 'enable-kb-styling-sitewide');

			//voting-display  => enable-article-feedback
			$this->upgrade_setting('voting-display', 'enable-article-feedback');

			//anon-voting =>enable-anon-article-feedback
			$this->upgrade_setting('anon-voting', 'enable-anon-article-feedback');

			//upvote-feedback => enable-upvote-article-feedback
			$this->upgrade_setting('upvote-feedback', 'enable-upvote-article-feedback');

			//downvote-feedback   => enable-downvote-article-feedback
			$this->upgrade_setting('downvote-feedback', 'enable-downvote-article-feedback');

			//exit-default-url    =>  kb-transfer-url
			$this->upgrade_setting('exit-default-url',  'kb-transfer-url');

			//exit-new-window => kb-transfer-new-window
			$this->upgrade_setting('exit-new-window', 'kb-transfer-new-window');

			//ht-kb-license   =>  kb-license-key
			$this->upgrade_setting('ht-kb-license',  'kb-license-key');

			//save new settings array back to the db
			update_option(HT_KB_NEW_SETTINGS_KEY, $this->new_settings_array);


			//flag upgrade complete
			update_option(HT_KB_TWO_SEVEN_UPGRADE_KEY, true);


			//backup old settings
			update_option(HT_KB_TWO_SEVEN_BACKUP_SETTINGS_KEY, $this->old_settings_array);

			//delete the old settings
			delete_option(HT_KB_OLD_SETTINGS_KEY);

			//finally, we need to flush the rewrite rules to ensure CPT works again
			update_option('ht_kb_flush_rewrite_required', true);

		}

		/**
		* Upgrade an individual option and populate the new settings array
		* @param (string) $old_name The old key name for the option
		* @param (string) $new_name The new key name for the option
		*/
		function upgrade_setting($old_name, $new_name){
			//get old value
			$old_value = isset($this->old_settings_array[$old_name]) ? $this->old_settings_array[$old_name] : null;

		   
			//if no old value, no further action required - will be set by default
			if(null===$old_value){
				return;
			} else {
				//set new value
				$this->new_settings_array[$new_name] = $old_value;
			}

			//$this->new_settings_array[$new_name] = $old_value;

		} 

		/**
		* Upgrade routines for 3.0
		* @since 3.0
		*/
		function set_kb_archive_pages( $network_wide = null ){
			//loop kbs
			global $ht_knowledge_base_settings;     

			//test upgrade already done, exit as no further action required
			if(get_option(HT_KB_THREE_ZERO_UPGRADE_KEY)){
				return;    
			}
			
			//create a dummy support page that directs the user how to install a contact page
			$this->create_dummy_contact_support_page();

			//populate default widget areas
			$this->populate_default_widgets();     



			//existing page ids
			$kb_archive_page_id = isset($ht_knowledge_base_settings['kb-archive-page-id']) ? $ht_knowledge_base_settings['kb-archive-page-id'] : array();

			$knowledge_bases = ht_kb_get_registered_knowledge_base_keys();
			foreach ($knowledge_bases as $index => $key) {
				//kb archive page id
				$page_id = ht_kb_get_kb_archive_page_id( $key );
				if( $page_id > 0 ){
					//no action required 
				} else {
					$ht_kb_default_kb_archive_page_title_raw = ucwords ( sprintf( __('%s knowledge base archive', 'ht-knowledge-base'), $key ) );
					$slug = sanitize_title($key) . '-' . 'knowledge-base';
					//for the first index (default), remove the key and archive (most likely use case)
					if( 0 == $index ){
						$ht_kb_default_kb_archive_page_title_raw = ucwords ( __( 'knowledge base archive', 'ht-knowledge-base' )  );
						//get article slug if previously set (defaults to knowledge-base)
						$slug = hkb_kb_article_slug();
					}
					$ht_kb_default_kb_archive_page_title = apply_filters( 'ht_kb_default_kb_archive_page_title', $ht_kb_default_kb_archive_page_title_raw, $key );
					
					//wp insert post args
					$archive_page = array(
					  'post_content'   => sprintf( __('This is the archive page for the %s knowledge base', 'ht-knowledge-base'), $key ),
					  'post_title'     => $ht_kb_default_kb_archive_page_title,
					  'post_name'      => $slug,
					  'post_status'    => 'publish',
					  'post_type'      => 'page',                      
					);

					$new_archive_page_id = wp_insert_post( $archive_page );

					$kb_archive_page_id[$key] = $new_archive_page_id;

					$ht_knowledge_base_settings['kb-archive-page-id'] = $kb_archive_page_id;
				}
			} 

			//finally, we need to flush the rewrite rules to ensure slugs are updated
			update_option('ht_kb_flush_rewrite_required', true);

			//flag upgrade complete
			update_option(HT_KB_THREE_ZERO_UPGRADE_KEY, true);

			//commit settings
			update_option( 'ht_knowledge_base_settings', $ht_knowledge_base_settings );
		}  

		function create_dummy_contact_support_page(){
			//wp insert post args
			$dummy_contact_support_page = array(
			  'post_content'   => __('This is an example page where your customers could reach out to support.', 'ht-knowledge-base'),
			  'post_title'     => 'Contact Support',
			  'post_name'      => 'Contact Support',
			  'post_status'    => 'publish',
			  'post_type'      => 'page',                      
			);

			$new_dummy_contact_support_page_id = wp_insert_post( $dummy_contact_support_page );
		}

		function populate_default_widgets(){
			//home
				//info/text
				//popular articles
				//need support
			//cateogry/tag
				//info/text
				//kb categories
				//need support
			//article
				//contents
				//need support

			$sidebar_widgets = get_option('sidebars_widgets');

			//init if required
			$sidebar_widgets = ( empty( $sidebar_widgets ) ) ? array() : $sidebar_widgets;
			

			//A. HOME (Archive Sidebar)  (id: ht-kb-sidebar-archive)

			if( !array_key_exists( 'ht-kb-sidebar-archive', $sidebar_widgets ) ){

				//A1. Knowledge Base Articles:Popular Articles

				//widget_ht-kb-articles-widget
				$articles_widgets = get_option('widget_ht-kb-articles-widget', array());

				//compute an index
				//dev note - this isn't perfect as WP does not seem to index sequentially, it may override an old widget
				//but remains workable to overcome other issues with auto adding widgets
				$articles_widgets_index = count($articles_widgets) + 1;

				$new_articles_widget = array (
					'title' => __('Popular Articles', 'ht-knowledge-base'),
					'num' => 5,
					'sort_by' => 'popular',
					'category' => 'all',
					'asc_sort_order' => 0,
					'comment_num' => 0,
					'rating' => 0
				);
				//add to array
				$articles_widgets[$articles_widgets_index] = $new_articles_widget;
				update_option( 'widget_ht-kb-articles-widget', $articles_widgets );

				$sidebar_widgets['ht-kb-sidebar-archive'][] = 'ht-kb-articles-widget-' . $articles_widgets_index;
				

				//A2. Knowledge Base Exit:Need Support

				//submit ticket page url
				$submit_ticket_page_url = '#';
				$submit_ticket_page = get_page_by_title( esc_html__('Contact Support', 'ht-knowledge-base') );
				if( ! empty( $submit_ticket_page ) ){
					$submit_ticket_page_permalink = get_permalink($submit_ticket_page);
					$submit_ticket_page_url = $submit_ticket_page_permalink;
				}

				//widget_ht-kb-exit-widget
				$exit_widgets = get_option('widget_ht-kb-exit-widget', array());
				//compute an index
				$exit_widgets_index = count($exit_widgets) + 1;

				$new_exit_widget = array (
					'title' => __('Need Support?', 'ht-knowledge-base'),
					'text' => __("Can't find the answer you're looking for?", 'ht-knowledge-base'),
					'btn' => __('Contact Support', 'ht-knowledge-base'),
					'url' => $submit_ticket_page_url 
				);
				//add to array
				$exit_widgets[$exit_widgets_index] = $new_exit_widget;
				update_option( 'widget_ht-kb-exit-widget', $exit_widgets );

				$sidebar_widgets['ht-kb-sidebar-archive'][] = 'ht-kb-exit-widget-' . $exit_widgets_index;
			
			}

			//B. TAXONOMY (Category/Tag) (id: ht-kb-sidebar-taxonomy)

			if( !array_key_exists( 'ht-kb-sidebar-taxonomy', $sidebar_widgets ) ){

				//B1. Article Categories

				//widget_ht-kb-exit-widget
				$article_categories_widgets = get_option('widget_ht-kb-categories-widget', array());
				//compute an index
				$article_categories_widgets_index = count($article_categories_widgets) + 1;

				$new_article_categories_widget = array (
					'title' => __('Knowledge Base Categories', 'ht-knowledge-base'),
					'depth' => '1',
					'hierarchical' => 0,
					'sort_by' => 'name',
					'asc_sort_order' => '', 
					'hide_empty' => '', 
					'disp_article_count' => '',
					'exclude_tax_ids' => '',
					'contextual' => 0,
				);

				//add to array
				$article_categories_widgets[$article_categories_widgets_index] = $new_article_categories_widget;
				update_option( 'widget_ht-kb-categories-widget', $article_categories_widgets );

				$sidebar_widgets['ht-kb-sidebar-taxonomy'][] = 'ht-kb-categories-widget-' . $article_categories_widgets_index;

				//B2. Knowledge Base Exit:Need Support

				//widget_ht-kb-exit-widget
				$exit_widgets = get_option('widget_ht-kb-exit-widget', array());
				//compute an index
				$exit_widgets_index = count($exit_widgets) + 1;

				$new_exit_widget = array (
					'title' => __('Need Support?', 'ht-knowledge-base'),
					'text' => __("Can't find the answer you're looking for?", 'ht-knowledge-base'),
					'btn' => __('Contact Support', 'ht-knowledge-base'),
					'url' => $submit_ticket_page_url
				);

				//add to array
				$exit_widgets[$exit_widgets_index] = $new_exit_widget;
				update_option( 'widget_ht-kb-exit-widget', $exit_widgets );

				$sidebar_widgets['ht-kb-sidebar-taxonomy'][] = 'ht-kb-exit-widget-' . $exit_widgets_index;

			}
				
			//C. ARTICLE (id: ht-kb-sidebar-article)

			if( !array_key_exists( 'ht-kb-sidebar-article', $sidebar_widgets ) ){

				//C1. Knowledge Base Table of Contents:Contents

				//widget_ht-kb-toc-widget
				$toc_widgets = get_option('widget_ht-kb-toc-widget', array());
				//compute an index
				$toc_widgets_index = count($toc_widgets) + 1;

				$new_toc_widget = array (
					'title' => __('Contents', 'ht-knowledge-base'),
				);
				//add_to_array
				$toc_widgets[$toc_widgets_index] = $new_toc_widget;
				update_option( 'widget_ht-kb-toc-widget', $toc_widgets );

				//get and assign the key
				end($toc_widgets);
				$toc_widget_index = key($toc_widgets);
				$sidebar_widgets['ht-kb-sidebar-article'][] = 'ht-kb-toc-widget-' . $toc_widgets_index;

				//C2. Knowledge Base Exit:Need Support

				//widget_ht-kb-exit-widget
				$exit_widgets = get_option('widget_ht-kb-exit-widget', array());
				//compute an index
				$exit_widgets_index = count($exit_widgets) + 1;

				$new_exit_widget = array (
					'title' => __('Need Support?', 'ht-knowledge-base'),
					'text' => __("Can't find the answer you're looking for?", 'ht-knowledge-base'),
					'btn' => __('Contact Support', 'ht-knowledge-base'),
					'url' => $submit_ticket_page_url
				);

				//add to array
				$exit_widgets[$exit_widgets_index] = $new_exit_widget;
				update_option( 'widget_ht-kb-exit-widget', $exit_widgets );

				$sidebar_widgets['ht-kb-sidebar-article'][] = 'ht-kb-exit-widget-' . $exit_widgets_index;


			}

			//UPDATE - update active widgets
			update_option( 'sidebars_widgets', $sidebar_widgets );
		}     

	}//end class

}

if (class_exists('HT_Knowledge_Base_Settings_Upgrade')) {
	new HT_Knowledge_Base_Settings_Upgrade();
}