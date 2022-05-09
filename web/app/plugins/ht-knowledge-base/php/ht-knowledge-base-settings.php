<?php
/**
 * 2.6.5 New Settings Page
 */

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if(!defined('HT_KB_SETTINGS_PAGE_MENU_POSITION')){
	define('HT_KB_SETTINGS_PAGE_MENU_POSITION', 90);
}

if (!class_exists('HT_Knowledge_Base_Settings')) {

	class HT_Knowledge_Base_Settings {

		private $ht_kb_settings;
		private $reserved_terms;
		private $existing_post_names;

		//Constructor
		function __construct(){  
			global $ht_knowledge_base_settings;

			//get option
			$this->ht_kb_settings = get_option( 'ht_knowledge_base_settings' );

			$ht_knowledge_base_settings = $this->ht_kb_settings;			

			//register settings
			add_action('admin_init', array($this, 'register_settings') );

			//add settings page
			add_action('admin_menu', array($this, 'add_ht_kb_settings_page'), HT_KB_SETTINGS_PAGE_MENU_POSITION );         

			//admin notices
			add_action('admin_notices', array($this, 'check_ht_kb_settings'), 10 );         

		}

		function register_settings(){
			register_setting( 'ht_knowledge_base_settings', 'ht_knowledge_base_settings', array($this,'ht_knowledge_base_settings_validate') );
		} 


		function add_ht_kb_settings_page(){

			//add the submenu page
			add_submenu_page(
					'edit.php?post_type=ht_kb',
					__('Heroic Knowledge Base Settings', 'ht-knowledge-base'), 
					__('Settings', 'ht-knowledge-base'), 
					apply_filters('ht_kb_global_settings_page_capability', 'manage_options'), 
					'ht_knowledge_base_settings_page', 
				   array($this, 'ht_kb_settings_display')
				);

			//
			// General section
			//
			if( apply_filters( 'ht_kb_settings_general_section_add', true ) ){
				add_settings_section('ht_knowledge_base_general_settings', __('Categories & Tags', 'ht-knowledge-base'), array($this, 'ht_kb_settings_general_section_description'), 'ht_kb_settings_general_section');  	
			}			        

			// > Option: sort by
			add_settings_field('sort-by', __('Sort By', 'ht-knowledge-base'), array($this, 'general_sort_by_option_render'), 'ht_kb_settings_general_section', 'ht_knowledge_base_general_settings');
			$this->set_default('sort-by', 'date');

			// > Option: sort order
			add_settings_field('sort-order', __('Sort Order', 'ht-knowledge-base'), array($this, 'general_sort_order_option_render'), 'ht_kb_settings_general_section', 'ht_knowledge_base_general_settings');
			$this->set_default('sort-order', 'asc');

			// > Option: number of articles
			add_settings_field('num-articles', __('Number of Articles', 'ht-knowledge-base'), array($this, 'general_num_articles_option_render'), 'ht_kb_settings_general_section', 'ht_knowledge_base_general_settings');
			$this->set_default('num-articles', 5);

			// > Option: article excerpt in taxonomy
			add_settings_field('display-taxonomy-article-excerpt', __('Article Excerpts', 'ht-knowledge-base'), array($this, 'general_display_taxonomy_article_excerpt_option_render'), 'ht_kb_settings_general_section', 'ht_knowledge_base_general_settings');
			$this->set_default('display-taxonomy-article-excerpt', true);

			//
			// Archive section
			//
			if( apply_filters( 'ht_kb_settings_archive_section_add', true ) ){
				add_settings_section('ht_knowledge_base_archive_settings', __('Home & General', 'ht-knowledge-base'), array($this, 'ht_kb_settings_archive_section_description'), 'ht_kb_settings_archive_section'); 
			}
			
			// > Option: archive page id
			add_settings_field('kb-archive-page-id', __('KB Home Page', 'ht-knowledge-base'), array($this, 'archive_display_kb_archive_page_option_render'), 'ht_kb_settings_archive_section', 'ht_knowledge_base_archive_settings');
			$this->set_default('kb-archive-page-id', array() );
			
			// > Option: header text
			add_settings_field('kb-archive-page-header-text', __('Header Text', 'ht-knowledge-base'), array($this, 'archive_display_kb_archive_page_header_text_option_render'), 'ht_kb_settings_archive_section', 'ht_knowledge_base_archive_settings');
			$this->set_default('kb-archive-page-header-text', __('How can we help?', 'ht-knowledge-base') );

			// > Option: breadcrumbs
			add_settings_field('display-breadcrumbs', __('Breadcrumbs', 'ht-knowledge-base'), array($this, 'general_display_breadcrumbs_option_render'), 'ht_kb_settings_archive_section', 'ht_knowledge_base_archive_settings');
			$this->set_default('display-breadcrumbs', true);  
			
			// > Option: kb columns
			add_settings_field('archive-columns', __('KB Category Columns', 'ht-knowledge-base'), array($this, 'archive_archive_columns_option_render'), 'ht_kb_settings_archive_section', 'ht_knowledge_base_archive_settings');
			$this->set_default('archive-columns', 2);
			
			// > Option: article count
			add_settings_field('display-article-count', __('Display KB Category Counts', 'ht-knowledge-base'), array($this, 'archive_display_article_count_option_render'), 'ht_kb_settings_archive_section', 'ht_knowledge_base_archive_settings');
			$this->set_default('display-article-count', true);
			
			// > Option: articles in home
			add_settings_field('num-articles-home', __('KB Category Articles', 'ht-knowledge-base'), array($this, 'archive_num_articles_home_option_render'), 'ht_kb_settings_archive_section', 'ht_knowledge_base_archive_settings');
			$this->set_default('num-articles-home', 5);
			
			// > Option: display sub categories
			//@deprecated by 3.0.0
			//add_settings_field('display-sub-cats', __('Display Sub Categories', 'ht-knowledge-base'), array($this, 'archive_display_sub_cats_option_render'), 'ht_kb_settings_archive_section', 'ht_knowledge_base_archive_settings');
			$this->set_default('display-sub-cats', false);
			
			// > Option: sub category depth
			//@deprecated by 3.0.0
			//add_settings_field('sub-cat-depth', __('Sub Category Depth', 'ht-knowledge-base'), array($this, 'archive_sub_cat_depth_option_render'), 'ht_kb_settings_archive_section', 'ht_knowledge_base_archive_settings');
			$this->set_default('sub-cat-depth', 0);
			
			// > Option: display subcategory articles
			//@deprecated by 3.0.0
			//add_settings_field('display-sub-cat-articles', __('Display Sub Category Articles', 'ht-knowledge-base'), array($this, 'archive_display_sub_cat_articles_option_render'), 'ht_kb_settings_archive_section', 'ht_knowledge_base_archive_settings');
			$this->set_default('display-sub-cat-articles', false);
			
			// > Option: hide empty categories
			add_settings_field('hide-empty-cats', __('Hide Empty Categories', 'ht-knowledge-base'), array($this, 'archive_hide_empty_cats_option_render'), 'ht_kb_settings_archive_section', 'ht_knowledge_base_archive_settings');
			$this->set_default('hide-empty-cats', false);
			
			// > Option: hide uncategorized articles
			add_settings_field('hide-uncat-articles', __('Hide Uncategorized Articles', 'ht-knowledge-base'), array($this, 'archive_hide_uncat_articles_option_render'), 'ht_kb_settings_archive_section', 'ht_knowledge_base_archive_settings');
			$this->set_default('hide-uncat-articles', false);

						// > Option: restrict access
			add_settings_field('restrict-access', __('Restrict Access', 'ht-knowledge-base'), array($this, 'general_restrict_access_option_render'), 'ht_kb_settings_archive_section', 'ht_knowledge_base_archive_settings');
			$this->set_default('restrict-access', 'public');

			//
			// Article section    
			//
			if( apply_filters( 'ht_kb_settings_article_section_add', true ) ){         
				add_settings_section('ht_knowledge_base_article_settings', __('Article Options', 'ht-knowledge-base'), array($this, 'ht_kb_settings_article_section_description'), 'ht_kb_settings_article_section'); 
			}

			// > Option: enable comments
			add_settings_field('enable-article-comments', __('Enable Comments', 'ht-knowledge-base'), array($this, 'article_enable_article_comments_option_render'), 'ht_kb_settings_article_section', 'ht_knowledge_base_article_settings');
			$this->set_default('enable-article-comments', false);
			
			// > Option: display usefulness
			//@deprecated by 3.0.0
			//add_settings_field('display-article-usefulness', __('Display Usefulness', 'ht-knowledge-base'), array($this, 'article_display_article_usefulness_option_render'), 'ht_kb_settings_article_section', 'ht_knowledge_base_article_settings');
			$this->set_default('display-article-usefulness', false);
			
			// > Option: display views
			//@deprecated by 3.0.0
			//add_settings_field('display-article-views-count', __('Display Views', 'ht-knowledge-base'), array($this, 'article_display_article_views_count_option_render'), 'ht_kb_settings_article_section', 'ht_knowledge_base_article_settings');
			$this->set_default('display-article-views-count', false);
			
			// > Option: comment count
			//@deprecated by 3.0.0
			//add_settings_field('display-article-comment-count', __('Display Comment Count', 'ht-knowledge-base'), array($this, 'article_display_article_comment_count_option_render'), 'ht_kb_settings_article_section', 'ht_knowledge_base_article_settings');
			$this->set_default('display-article-comment-count', false);
			
			// > Option: comment count
			add_settings_field('display-article-author', __('Display Author Bio', 'ht-knowledge-base'), array($this, 'article_display_article_author_option_render'), 'ht_kb_settings_article_section', 'ht_knowledge_base_article_settings');
			$this->set_default('display-article-author', false);
			
			// > Option: related articles
			add_settings_field('display-related-articles', __('Display Related Articles', 'ht-knowledge-base'), array($this, 'article_display_related_articles_option_render'), 'ht_kb_settings_article_section', 'ht_knowledge_base_article_settings');
			$this->set_default('display-related-articles', true);

			//
			// Search section
			// 
			if( apply_filters( 'ht_kb_settings_search_section_add', true ) ){ 
				add_settings_section('ht_knowledge_base_search_settings', __('Search Options', 'ht-knowledge-base'), array($this, 'ht_kb_settings_search_section_description'), 'ht_kb_settings_search_section');
			}
			
			// > Option: live search
			//@deprecated in 3.0
			//add_settings_field('display-live-search', __('Live Search', 'ht-knowledge-base'), array($this, 'search_display_live_search_option_render'), 'ht_kb_settings_search_section', 'ht_knowledge_base_search_settings');
			$this->set_default('display-live-search', true);
			
			// > Option: search focus
			add_settings_field('focus-live-search', __('Search Focus', 'ht-knowledge-base'), array($this, 'search_focus_live_search_option_render'), 'ht_kb_settings_search_section', 'ht_knowledge_base_search_settings');
			$this->set_default('focus-live-search', true);
			
			//search placeholder      
			add_settings_field('search-placeholder-text', __('Search Placeholder', 'ht-knowledge-base'), array($this, 'search_search_placeholder_text_option_render'), 'ht_kb_settings_search_section', 'ht_knowledge_base_search_settings');
			$this->set_default('search-placeholder-text', __('Search the Knowledge Base...', 'ht-knowledge-base'));
			
			// > Option: search result excerpt
			add_settings_field('display-search-result-excerpt', __('Search Result Excerpt', 'ht-knowledge-base'), array($this, 'search_display_search_result_excerpt_option_render'), 'ht_kb_settings_search_section', 'ht_knowledge_base_search_settings');
			$this->set_default('display-search-result-excerpt', true);


			add_settings_field('highlight-search-result-excerpt', __('Highlight Search Query in Excerpt', 'ht-knowledge-base'), array($this, 'highlight_search_result_excerpt_option_render'), 'ht_kb_settings_search_section', 'ht_knowledge_base_search_settings');
			$this->set_default('highlight-search-result-excerpt', false);

			//
			// Slugs section - note the defaults are no longer translated
			//
			if( apply_filters( 'ht_kb_settings_slugs_section_add', true ) ){
				add_settings_section('ht_knowledge_base_slugs_settings', __('Slugs Options', 'ht-knowledge-base'), array($this, 'ht_kb_settings_slugs_section_description'), 'ht_kb_settings_slugs_section'); 
			}
			
			// > Option: article slug
			add_settings_field('kb-article-slug', __('KB Article Slug', 'ht-knowledge-base'), array($this, 'slugs_kb_article_slug_option_render'), 'ht_kb_settings_slugs_section', 'ht_knowledge_base_slugs_settings');
			$this->set_default('kb-article-slug', 'knowledge-base');
			
			// > Option: category slug
			add_settings_field('kb-category-slug', __('KB Category Slug', 'ht-knowledge-base'), array($this, 'slugs_kb_category_slug_option_render'), 'ht_kb_settings_slugs_section', 'ht_knowledge_base_slugs_settings');
			$this->set_default('kb-category-slug', 'article-categories');
			
			// > Option: tag slug
			add_settings_field('kb-tag-slug', __('KB Tag Slug', 'ht-knowledge-base'), array($this, 'slugs_kb_tag_slug_option_render'), 'ht_kb_settings_slugs_section', 'ht_knowledge_base_slugs_settings');
			$this->set_default('kb-tag-slug', 'article-tags');


			//
			// Sidebars section
			//
			if( apply_filters( 'ht_kb_settings_sidebars_section_add', true ) ){
				add_settings_section('ht_knowledge_base_sidebars_settings', __('Sidebars', 'ht-knowledge-base'), array($this, 'ht_kb_settings_sidebars_section_description'), 'ht_kb_settings_sidebars_section');
			}

			// > Option: Archive sidebar position
			add_settings_field('archive-sidebar-position', __('KB Home Sidebar Position', 'ht-knowledge-base'), array($this, 'archive_sidebar_position_option_render'), 'ht_kb_settings_sidebars_section', 'ht_knowledge_base_sidebars_settings');
			$this->set_default('archive-sidebar-position', 'right');

			// > Option: category sidebar position
			add_settings_field('category-sidebar-position', __('Category Sidebar Position', 'ht-knowledge-base'), array($this, 'category_sidebar_position_option_render'), 'ht_kb_settings_sidebars_section', 'ht_knowledge_base_sidebars_settings');
			$this->set_default('category-sidebar-position', 'right');

			// > Option: article sidebar position
			add_settings_field('article-sidebar-position', __('Article Sidebar Position', 'ht-knowledge-base'), array($this, 'article_sidebar_position_option_render'), 'ht_kb_settings_sidebars_section', 'ht_knowledge_base_sidebars_settings');
			$this->set_default('article-sidebar-position', 'right');

			// > Option: article sidebar sticky
			add_settings_field('article-sticky-sidebar', __('Sticky Article Sidebar?', 'ht-knowledge-base'), array($this, 'article_sticky_sidebar_option_render'), 'ht_kb_settings_sidebars_section', 'ht_knowledge_base_sidebars_settings');
			$this->set_default('article-sticky-sidebar', true);


			//
			// Styling Section
			//
			if( apply_filters( 'ht_kb_settings_customstyles_section_add', true ) ){
				add_settings_section('ht_knowledge_base_customstyles_settings', __('Styling Options', 'ht-knowledge-base'), array($this, 'ht_kb_settings_customstyles_section_description'), 'ht_kb_settings_customstyles_section'); 
			}

			// > Option: Custom CSS
			// @deprecated - use theme customizer custom css or child theme instead
			//add_settings_field('custom-kb-styling-content', __('Styling', 'ht-knowledge-base'), array($this, 'customstyles_custom_kb_styline_content_option_render'), 'ht_kb_settings_customstyles_section', 'ht_knowledge_base_customstyles_settings');
			$this->set_default('custom-kb-styling-content', '');

			// > Option: Custom CSS sitewide
			// @deprecated - use theme customizer custom css or child theme instead
			//add_settings_field('enable-kb-styling-sitewide', __('Site-Wide Custom Styles', 'ht-knowledge-base'), array($this, 'customstyles_enable_kb_styling_sitewide_option_render'), 'ht_kb_settings_customstyles_section', 'ht_knowledge_base_customstyles_settings');
			$this->set_default('enable-kb-styling-sitewide', true);

			// > Option: KB Width
			add_settings_field('kb-width', __('KB Width', 'ht-knowledge-base'), array($this, 'customstyles_kb_width_option_render'), 'ht_kb_settings_customstyles_section', 'ht_knowledge_base_customstyles_settings');
			$this->set_default('kb-width', 1200);

			// > Option: KB Link Color
			add_settings_field('kb-linkcolor', __('Link Color', 'ht-knowledge-base'), array($this, 'customstyles_kb_linkcolor_option_render'), 'ht_kb_settings_customstyles_section', 'ht_knowledge_base_customstyles_settings');
			$this->set_default('kb-linkcolor', '#2358a1'); 

			// > Option: KB Link Color (Hover)
			add_settings_field('kb-linkcolorhover', __('Link Color (Hover)', 'ht-knowledge-base'), array($this, 'customstyles_kb_linkcolorhover_option_render'), 'ht_kb_settings_customstyles_section', 'ht_knowledge_base_customstyles_settings');
			$this->set_default('kb-linkcolorhover', '#2861b0');

			// > Option: KB Header Style
			add_settings_field('kb-headerstyle', __('KB Header Styling', 'ht-knowledge-base'), array($this, 'customstyles_kb_headerstyle_option_render'), 'ht_kb_settings_customstyles_section', 'ht_knowledge_base_customstyles_settings');
			$this->set_default('kb-headerstyle', 'solid');	//solid, gradient, image
			$this->set_default('kb-headerstyle-bg', '#2358a1'); 
			$this->set_default('kb-headerstyle-graddir', 90); 
			$this->set_default('kb-headerstyle-grad1', '#2358a1'); 
			$this->set_default('kb-headerstyle-grad2', '#2358a1'); 
			$this->set_default('kb-headerstyle-img-attachment-id', 0);
					
			//solid? //color?

			// > Option: KB Header Color (text)
			add_settings_field('kb-headercolor', __('KB Header Text Color', 'ht-knowledge-base'), array($this, 'customstyles_kb_headercolor_option_render'), 'ht_kb_settings_customstyles_section', 'ht_knowledge_base_customstyles_settings');
			$this->set_default('kb-headercolor', '#ffffff'); 			

			//
			// Article feedback section
			//   
       		if( apply_filters( 'ht_kb_settings_articlefeedback_section_add', true ) ){
				add_settings_section('ht_knowledge_base_articlefeedback_settings', __('Article Feedback Options', 'ht-knowledge-base'), array($this, 'ht_kb_settings_articlefeedback_section_description'), 'ht_kb_settings_articlefeedback_section'); 
			}
			
			// > Option: 
			add_settings_field('enable-article-feedback', __('Enable Feedback', 'ht-knowledge-base'), array($this, 'articlefeedback_enable_article_feedback_option_render'), 'ht_kb_settings_articlefeedback_section', 'ht_knowledge_base_articlefeedback_settings');
			$this->set_default('enable-article-feedback', true);
			
			// > Option: 
			add_settings_field('enable-anon-article-feedback', __('Enable Anonymous', 'ht-knowledge-base'), array($this, 'articlefeedback_enable_anon_article_feedback_option_render'), 'ht_kb_settings_articlefeedback_section', 'ht_knowledge_base_articlefeedback_settings');
			$this->set_default('enable-anon-article-feedback', true);
			
			// > Option: 
			add_settings_field('enable-upvote-article-feedback', __('Upvote Feedback', 'ht-knowledge-base'), array($this, 'articlefeedback_enable_upvote_article_feedback_option_render'), 'ht_kb_settings_articlefeedback_section', 'ht_knowledge_base_articlefeedback_settings');
			$this->set_default('enable-upvote-article-feedback', true);
			
			// > Option
			add_settings_field('enable-downvote-article-feedback', __('Downvote Feedback', 'ht-knowledge-base'), array($this, 'articlefeedback_enable_downvote_article_feedback_option_render'), 'ht_kb_settings_articlefeedback_section', 'ht_knowledge_base_articlefeedback_settings');
			$this->set_default('enable-downvote-article-feedback', true);

			
			//
			// Transfer options section
			//
			if( apply_filters( 'ht_kb_settings_transfers_section_add', true ) ){
				add_settings_section('ht_knowledge_base_transfers_settings', __('Transfer Options', 'ht-knowledge-base'), array($this, 'ht_kb_settings_transfers_section_description'), 'ht_kb_settings_transfers_section'); 
			}
			
			// > Option: 
			add_settings_field('kb-transfer-url', __('Default Transfer URL', 'ht-knowledge-base'), array($this, 'transfers_kb_transfer_url_option_render'), 'ht_kb_settings_transfers_section', 'ht_knowledge_base_transfers_settings');
			$this->set_default('kb-transfer-url', 'https://www.example.com/support-desk');
			
			// > Option: 
			add_settings_field('kb-transfer-new-window', __('Load in new window', 'ht-knowledge-base'), array($this, 'transfers_kb_transfer_new_window_option_render'), 'ht_kb_settings_transfers_section', 'ht_knowledge_base_transfers_settings');
			$this->set_default('kb-transfer-new-window', true);

			//
			// License and updates section
			//
			if( apply_filters( 'ht_kb_settings_license_section_add', true ) ){
				add_settings_section('ht_knowledge_base_license_settings', __('License and Update Options', 'ht-knowledge-base'), array($this, 'ht_kb_settings_license_section_description'), 'ht_kb_settings_license_section'); 
			}


			add_settings_field('kb-license-key', __('License Key', 'ht-knowledge-base'), array($this, 'license_kb_license_key_option_render'), 'ht_kb_settings_license_section', 'ht_knowledge_base_license_settings');
			$this->set_default('kb-license-key', '');

			do_action( 'add_ht_kb_settings_page_additional_settings' );
			
			$this->set_default( 'activetab', apply_filters( 'ht_kb_settings_page_activetab', 'archive-section' ) );

			do_action( 'maybe_upgrade_ht_kb_settings_fields' );
		}

		/**
		* Check initial settings are valid and notify any issues
		* @since 3.0
		*/
		function check_ht_kb_settings(){

			$screen = get_current_screen();

			//only display on options page
			if(is_admin() && is_object($screen) && ('ht_kb_page_ht_knowledge_base_settings_page' == $screen->base) ){  

				$knowledge_bases = ht_kb_get_registered_knowledge_base_keys();
				//loop registered knowledge base keys

				foreach ($knowledge_bases as $index => $key):
					//get some knowledge base property
					$page_id = ht_kb_get_kb_archive_page_id($key); 
					$page = get_post( $page_id );

					if( !is_a($page, 'WP_Post') ):
						//not a valid page
						?>
						<div class="error">
							<p><?php printf(__( 'The %s knowledge base archive page has not been set, please set a valid page from the <b>General</b> section.', 'ht-knowledge-base' ), $key ); ?></p>
						</div>
					<?php 
					endif;
				endforeach;
			}

		}

		function ht_kb_settings_display(){
			global $ht_knowledge_base_settings;
			//enqueue media
			wp_enqueue_media();
			//enqueue style - can be removed once amalgameted with hkb-style-admin
			wp_enqueue_style( 'hkb-style-settings', plugins_url( 'css/hkb-style-settings-page.css', dirname(__FILE__) ), array(), HT_KB_VERSION_NUMBER );  
			$hkb_admin_settings_page_js_src = (HKB_DEBUG_SCRIPTS) ? 'js/hkb-admin-settings-page-js.js' : 'js/hkb-admin-settings-page-js.min.js';
			wp_enqueue_script( 'ht-kb-settings-page', plugins_url( $hkb_admin_settings_page_js_src, dirname(__FILE__) ), array( 'jquery', 'jquery-ui-core',  'jquery-ui-tabs', 'media-upload' ), HT_KB_VERSION_NUMBER, true );              
			
			$this->populate_reserved_terms();

			//populate existing post names
			$this->existing_post_names = array();
			//will get first 1000 posts (better than -1?)
			//apply ht_kb_slugs_check_posts_per_page filters 
			$ht_kb_slugs_check_posts_per_page = apply_filters( 'ht_kb_slugs_check_posts_per_page', 1000);
			$all_posts = get_posts( array( 'post_type' => array('post', 'page'), 'posts_per_page' => $ht_kb_slugs_check_posts_per_page ) );
			foreach ($all_posts as $key => $post) {
				$this->existing_post_names[] = $post->post_name;
			}

			$conflicted_slug_error = __('Slug cannot be the same as another Heroic Knowledge Base slug', 'ht-knowledge-base');
			$heirachical_slug_error = __('Slugs cannot be included in another Heroic Knowledge Base slug', 'ht-knowledge-base');
			$reserved_term_error = __('Slug cannot be a reserved WordPress term', 'ht-knowledge-base');
			$slug_in_use_error = __('Slug cannot be the same as an existing post or page, please remove post with this slug first', 'ht-knowledge-base');

			wp_localize_script( 'ht-kb-settings-page', 
								'settingsPageObjects', 
								array( 'reservedTerms' => $this->reserved_terms, 
									  'existingPostNames' => $this->existing_post_names,
									  'conflictedSlugError' => $conflicted_slug_error,
									  'heirachicalSlugError' => $heirachical_slug_error,
									  'reservedTermError' => $reserved_term_error,
									  'slugInUseError' => $slug_in_use_error 
								  ) 
							);
			$active_tab = $this->ht_kb_settings['activetab'];
			?>
				<div class="hkba-settingpage">
					<div class="hkba-settingpage__header">

						<div class="hkba-container">
							<div class="hkba-settingpage__logo">
							<svg id="icon-hkb" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 142.42 140.74" height="64" width="64">
								<path d="M116.9 141.73H26.2c-14.09 0-25.51-11.42-25.51-25.51V25.51C.68 11.42 12.11 0 26.2 0h90.71c14.09 0 25.51 11.42 25.51 25.51v90.71c0 14.09-11.43 25.51-25.52 25.51z" fill="#5b6ebd"/><path fill="#fff" d="M47.91 43.41c-1.18 0-2.14.96-2.14 2.14 0 1.18.96 2.14 2.14 2.14h48.58c1.18 0 2.14-.96 2.14-2.14 0-1.18-.96-2.14-2.14-2.14H47.91z"/><path fill="#fff" d="M109.02 81.04l-7.63-24.72c-.01-.02-.02-.03-.03-.05a2.1 2.1 0 0 0-.27-.54.265.265 0 0 0-.05-.06c-.11-.15-.24-.28-.39-.39-.03-.02-.05-.04-.07-.06-.16-.11-.33-.2-.52-.27a.564.564 0 0 1-.12-.04c-.19-.06-.4-.1-.61-.1h-51.9c-5.1 0-9.25-4.15-9.25-9.25s4.15-9.25 9.25-9.25h51.9c1.18 0 2.14-.96 2.14-2.14s-.96-2.14-2.14-2.14h-51.9c-7.46 0-13.53 6.07-13.53 13.53v47.75c0 9.05 7.36 16.4 16.4 16.4h44.16c2.02 0 3.66-1.64 3.66-3.66V85.78h7.39c1.16 0 2.26-.56 2.95-1.49.7-.93.91-2.14.56-3.25z"/>
							</svg>
								<h1><?php _e('Heroic KB', 'ht-knowledge-base'); ?></h1>
							</div>
						</div>

					</div>

					<?php settings_errors(); ?>

					<div class="hkba-settingpage__content">
						<div class="hkba-container">

							<div class="hkba-settingsmain">
						<div class="hkba-settingsmain__nav" id="hkb-settings-tabs">
							
								<?php if(apply_filters('hkb_add_archive_settings_section', true)): ?>
									<a href="#archive-section" id="archive-section-link" data-section="archive"  class="active"><?php _e('Home & General', 'ht-knowledge-base'); ?></a>
								<?php endif; ?>
								<?php if(apply_filters('hkb_add_general_settings_section', true)): ?>
									<a href="#general-section" id="general-section-link" data-section="general"><?php _e('Categories & Tags', 'ht-knowledge-base'); ?></a>
								<?php endif; ?>                            
								<?php if(apply_filters('hkb_add_article_settings_section', true)): ?>
									<a href="#article-section" id="article-section-link" data-section="article"><?php _e('Article', 'ht-knowledge-base'); ?></a>
								<?php endif; ?>
								<?php if(apply_filters('hkb_add_search_settings_section', true)): ?>
									<a href="#search-section" id="search-section-link" data-section="search"><?php _e('Search', 'ht-knowledge-base'); ?></a>
								<?php endif; ?>
								<?php if(apply_filters('hkb_add_slugs_settings_section', true)): ?>
									<a href="#slugs-section" id="slugs-section-link" data-section="slugs"><?php _e('Slugs', 'ht-knowledge-base'); ?></a>
								<?php endif; ?>
								<?php if(apply_filters('hkb_add_sidebars_settings_section', true)): ?>
									<a href="#sidebars-section" id="sidebars-section-link" data-section="sidebars"><?php _e('Sidebars', 'ht-knowledge-base'); ?></a>
								<?php endif; ?>
								<?php if(apply_filters('hkb_add_customstyles_settings_section', true)): ?>
									<a href="#customstyles-section" id="customstyles-section-link" data-section="customstyles"><?php _e('Styles', 'ht-knowledge-base'); ?></a>
								<?php endif; ?>
								<?php if(apply_filters('hkb_add_articlefeedback_settings_section', true)): ?>
									<a href="#articlefeedback-section" id="articlefeedback-section-link" data-section="articlefeedback"><?php _e('Article Feedback', 'ht-knowledge-base'); ?></a>
								<?php endif; ?>
								<?php if(apply_filters('hkb_add_transfers_settings_section', true)): ?>
									<a href="#transfers-section" id="transfers-section-link" data-section="tranfers"><?php _e('Transfers', 'ht-knowledge-base'); ?></a>
								<?php endif; ?>
								<?php if( apply_filters('hkb_add_license_settings_section', true) && !$this->theme_managed_updates() ) : ?>
									<a href="#license-section" id="license-section-link" data-section="license"><?php _e('License and Updates', 'ht-knowledge-base'); ?></a>
								<?php endif; ?>
								<?php do_action('ht_kb_settings_display_tabs'); ?>
							
							</div><!-- /tabs -->

							<div class="hkba-settingsmain__content">

							<form action="options.php" method="post">
								<?php settings_fields('ht_knowledge_base_settings'); ?>

								<input type="hidden" id="activetab" name="ht_knowledge_base_settings[activetab]" value="<?php echo $active_tab; ?>" />
								<?php if(apply_filters('hkb_add_general_settings_section', true)): ?>
									<div id="general-section" class="hkb-settings-section active">
										<?php do_settings_sections('ht_kb_settings_general_section'); ?> 
									</div>
								<?php endif; ?>
								<?php if(apply_filters('hkb_add_archive_settings_section', true)): ?>
									<div id="archive-section" class="hkb-settings-section" style="display:none;">
										<?php do_settings_sections('ht_kb_settings_archive_section'); ?> 
									</div>
								<?php endif; ?>
								<?php if(apply_filters('hkb_add_article_settings_section', true)): ?>
									<div id="article-section" class="hkb-settings-section" style="display:none;">
										<?php do_settings_sections('ht_kb_settings_article_section'); ?>
									</div>
								<?php endif; ?>
								<?php if(apply_filters('hkb_add_search_settings_section', true)): ?>
									<div id="search-section" class="hkb-settings-section" style="display:none;"> 
										<?php do_settings_sections('ht_kb_settings_search_section'); ?> 
									</div>
								<?php endif; ?>
								<?php if(apply_filters('hkb_add_slugs_settings_section', true)): ?>
									<div id="slugs-section" class="hkb-settings-section" style="display:none;">
										<?php do_settings_sections('ht_kb_settings_slugs_section'); ?>
									</div>
								<?php endif; ?>
								<?php if(apply_filters('hkb_add_sidebars_settings_section', true)): ?>
									<div id="sidebars-section" class="hkb-settings-section" style="display:none;">
										<?php do_settings_sections('ht_kb_settings_sidebars_section'); ?>
									</div>
								<?php endif; ?>
								<?php if(apply_filters('hkb_add_customstyles_settings_section', true)): ?>
									<div id="customstyles-section" class="hkb-settings-section" style="display:none;">
										<?php do_settings_sections('ht_kb_settings_customstyles_section'); ?>
									</div>
								<?php endif; ?>
								<?php if(apply_filters('hkb_add_articlefeedback_settings_section', true)): ?>
									<div id="articlefeedback-section" class="hkb-settings-section" style="display:none;">  
										<?php do_settings_sections('ht_kb_settings_articlefeedback_section'); ?> 
									</div>
								<?php endif; ?>
								<?php if(apply_filters('hkb_add_transfers_settings_section', true)): ?>
									<div id="transfers-section" class="hkb-settings-section" style="display:none;"> 
										<?php do_settings_sections('ht_kb_settings_transfers_section'); ?>
									</div>
								<?php endif; ?>
								<?php if(apply_filters('hkb_add_license_settings_section', true)): ?>
									<div id="license-section" class="hkb-settings-section" style="display:none;">
										<?php do_settings_sections('ht_kb_settings_license_section'); ?>   
									</div>
								<?php endif; ?>
								<?php do_action('ht_kb_settings_display_additional_sections'); ?>
								<div class="hkb-settings-infoarea"></div>
								<input name="Submit" type="submit" class="button button-primary" value="<?php esc_attr_e(__('Save Changes', 'ht-knowledge-base')); ?>" />
							</form>
							</div>
						
					</div>

						</div>
					</div>
				 <?php if(apply_filters('hkb_show_settings_page_footer', true)): ?>
						<div class="hkba-settingpage__footer">
							<div class="hkba-container">
								<ul>
									<?php if(apply_filters('hkb_show_settings_page_version_number', true)): ?>
										<li><?php printf( __('Version %s', 'ht-knowledge-base'), HT_KB_VERSION_NUMBER ); ?></li>
									<?php endif; ?>
									<?php if(apply_filters('hkb_show_settings_page_debug_link', true)): ?>
										<li><a href="<?php echo admin_url('edit.php?post_type=ht_kb&page=ht_knowledge_base_debug_info'); ?>"><?php _e('Debug', 'ht-knowledge-base'); ?></a></li>
									<?php endif; ?>
								</ul>
							</div>
						</div>
					<?php endif; ?>
				   
				</div>
				</div><!-- /hkb-admin-settings-page -->
			<?php
		}

		function set_default($id, $default=false){
			if(isset($this->ht_kb_settings[$id])){
				//do nothing
			} else {
				//set
				$this->ht_kb_settings[$id] = $default;
				//save
				update_option( 'ht_knowledge_base_settings', $this->ht_kb_settings );
			}
		}

		/* GENERAL SECTION */

		//section header 
		function ht_kb_settings_general_section_description(){
			?>
				<div class="hkb-settings-general-section-start"></div>
			<?php
		}

		//breadcrumbs
		function general_display_breadcrumbs_option_render(){
			if(!apply_filters('hkb_general_display_breadcrumbs_option_render', true)){
				echo apply_filters(
					'hkb_general_display_breadcrumbs_option_render_false',
					esc_attr__( '-', 'ht-knowledge-base' )
				);
				return;
			} ?>
				<input type="checkbox" class="ht-knowledge-base-settings-display-breadcrumbs__input"  name="ht_knowledge_base_settings[display-breadcrumbs]" value="1" <?php checked( $this->ht_kb_settings['display-breadcrumbs'], 1 ); ?> />
				<span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Display breadcrumbs in knowledge base', 'ht-knowledge-base'); ?></span>                
			<?php
		}        


		//sort by
		function general_sort_by_option_render(){
			if(!apply_filters('hkb_general_sort_by_option_render', true)){
				echo apply_filters(
					'hkb_general_sort_by_option_render_false',
					esc_attr__( '-', 'ht-knowledge-base' )
				);
				return;
			} ?>
				<select class="ht-knowledge-base-settings-sortby__input" name="ht_knowledge_base_settings[sort-by]">
					<option value="date" <?php selected( $this->ht_kb_settings['sort-by'], 'date', true); ?> ><?php _e('Date', 'ht-knowledge-base') ?></option>
					<option value="title" <?php selected( $this->ht_kb_settings['sort-by'], 'title', true); ?> ><?php _e('Title', 'ht-knowledge-base') ?></option>
					<option value="commment-count" <?php selected( $this->ht_kb_settings['sort-by'], 'commment-count', true); ?> ><?php _e('Comment Count', 'ht-knowledge-base') ?></option>
					<option value="random" <?php selected( $this->ht_kb_settings['sort-by'], 'random', true); ?> ><?php _e('Random', 'ht-knowledge-base') ?></option>
					<option value="modified" <?php selected( $this->ht_kb_settings['sort-by'], 'modified', true); ?> ><?php _e('Modified', 'ht-knowledge-base') ?></option>
					<option value="popular" <?php selected( $this->ht_kb_settings['sort-by'], 'popular', true); ?> ><?php _e('Popular', 'ht-knowledge-base') ?></option>
					<option value="helpful" <?php selected( $this->ht_kb_settings['sort-by'], 'helpful', true); ?> ><?php _e('Helpful', 'ht-knowledge-base') ?></option>
					<option value="custom" <?php selected( $this->ht_kb_settings['sort-by'], 'custom', true); ?> ><?php _e('Custom', 'ht-knowledge-base') ?></option>
				</select>   
				<span class="hkb_setting_desc"><?php _e('Sort order for display of knowledge base articles', 'ht-knowledge-base'); ?></span>      
			<?php
		}

		//sort order
		function general_sort_order_option_render(){
			if(!apply_filters('hkb_general_sort_order_option_render', true)){
				echo apply_filters(
					'hkb_general_sort_order_option_render_false',
					esc_attr__( '-', 'ht-knowledge-base' )
				);
				return;
			} ?>
				<select class="ht-knowledge-base-settings-sort-order__input" name="ht_knowledge_base_settings[sort-order]">
					<option value="asc" <?php selected( $this->ht_kb_settings['sort-order'], 'asc', true); ?> ><?php _e('Ascending', 'ht-knowledge-base') ?></option>
					<option value="desc" <?php selected( $this->ht_kb_settings['sort-order'], 'desc', true); ?> ><?php _e('Decending', 'ht-knowledge-base') ?></option>
				</select>   
				<span class="hkb_setting_desc"><?php _e('Sort direction for display of knowledge base articles', 'ht-knowledge-base'); ?></span>      
			<?php
		}

		//number of articles
		function general_num_articles_option_render(){
			$num_articles = isset($this->ht_kb_settings['num-articles']) ? $this->ht_kb_settings['num-articles'] : ''; 
			if(!apply_filters('hkb_general_num_articles_option_render', true)){
				echo apply_filters(
					'hkb_general_num_articles_option_render_false',
					esc_attr__( '-', 'ht-knowledge-base' )
				);
				return;
			} ?>
				<input type="text" class="ht-knowledge-base-settings-num-articles__input ht-validate-number-input" name="ht_knowledge_base_settings[num-articles]" value="<?php esc_attr_e($num_articles, 'ht-knowledge-base'); ?>" data-lower-limit="0" data-upper-limit="100" data-validation-requirements="<?php _e('Must be a valid number between 0 and 100', 'ht-knowledge-base');  ?>"></input>
				<span class="hkb_setting_desc"><?php _e('Number of articles to display for each category or tag from taxonomy archive', 'ht-knowledge-base'); ?></span>      
			<?php
		}

		//taxonomy article excerpt
		function general_display_taxonomy_article_excerpt_option_render(){
			if(!apply_filters('hkb_general_display_taxonomy_article_excerpt_option_render', true)){
				echo apply_filters(
					'hkb_general_display_taxonomy_article_excerpt_option_render_false',
					esc_attr__( '-', 'ht-knowledge-base' )
				);
				return;
			} ?>
				<input type="checkbox" class="ht-knowledge-base-settings-display-taxonomy-article-excerpt__input" name="ht_knowledge_base_settings[display-taxonomy-article-excerpt]" value="1" <?php checked( $this->ht_kb_settings['display-taxonomy-article-excerpt'], 1 ); ?> />
				<span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Display an excerpt of knowledge base articles in taxonomy archive', 'ht-knowledge-base'); ?></span>                
			<?php
		}

		//restrict access
		function general_restrict_access_option_render(){
			if(!apply_filters('hkb_general_sort_by_option_render', true)){
				echo apply_filters(
					'hkb_general_sort_by_option_render_false',
					esc_attr__( '-', 'ht-knowledge-base' )
				);
				return;
			} ?>
				<select class="ht-knowledge-base-settings-restrict-access__input" name="ht_knowledge_base_settings[restrict-access]">
					<?php $valid_restrict_access_levels = apply_filters('hkb_restrict_access_levels', array()); ?>
					<?php foreach ($valid_restrict_access_levels as $level_key => $level_label): ?>
						<option value="<?php echo $level_key; ?>" <?php selected( $this->ht_kb_settings['restrict-access'], $level_key, true); ?> >
							<?php echo $level_label; ?>
						</option>
					<?php endforeach; ?>                   
				</select>   
				<span class="hkb_setting_desc"><?php _e('Visibility of Knowledge Base', 'ht-knowledge-base'); ?></span>      
			<?php
		}

		/* ARCHIVE SECTION */

		//section header 
		function ht_kb_settings_archive_section_description(){
			?>
				<div class="hkb-settings-archive-section-start"></div>
			<?php
		}        

		//kb archive page id @since 3.0
		function archive_display_kb_archive_page_option_render(){
			if(!apply_filters('archive_display_kb_archive_page_option_render', true)){
				echo apply_filters(
					'archive_display_kb_archive_page_option_render_false',
					esc_attr__( '-', 'ht-knowledge-base' )
				);
				return;
			}
			$knowledge_bases = ht_kb_get_registered_knowledge_base_keys();
			//loop registered knowledge base keys

			foreach ($knowledge_bases as $index => $key):
				//get some knowledge base property
				$page_id = ht_kb_get_kb_archive_page_id($key); 

				wp_dropdown_pages(
					array(
						'name'              => 'ht_knowledge_base_settings[kb-archive-page-id]['.$key.']',
						'echo'              => true,
						'show_option_none'  => __( '&mdash; Select &mdash;', 'ht-knowledge-base' ),
						'option_none_value' => '0',
						'selected'          => $page_id,
					)
				);
				?>
				<span class="hkb_setting_desc"><?php printf( __('The page to use as the %s knowledge base home page', 'ht-knowledge-base'), $key ); ?></span>
				<?php if( $page_id && $page_id > 0 ):  ?>
						<span class="hkb_setting_info"><?php printf( '<a href="%s" target="_blank">' . __('Edit this page', 'ht-knowledge-base') . '</a>', get_edit_post_link( $page_id ) ); ?></span>
						&nbsp;
						 <span class="hkb_setting_info"><?php printf( '<a href="%s" target="_blank">' . __('View this page', 'ht-knowledge-base') . '</a>', get_post_permalink( $page_id ) ); ?></span>
				<?php endif; ?>   
			   
			<?php
			endforeach;
		}

		//page header text @since 3.0
		function archive_display_kb_archive_page_header_text_option_render(){
			$header_text = isset($this->ht_kb_settings['kb-archive-page-header-text']) ? $this->ht_kb_settings['kb-archive-page-header-text'] : ''; 
			if(!apply_filters('hkb_archive_display_kb_archive_page_header_text_option_render', true)){
				echo apply_filters(
					'hkb_archive_display_kb_archive_page_header_text_option_render_false',
					esc_attr__( '-', 'ht-knowledge-base' )
				);
				return;
			} ?>
				<input type="text" class="ht-knowledge-base-settings-kb-archive-page-header-text__input" name="ht_knowledge_base_settings[kb-archive-page-header-text]" value="<?php esc_attr_e($header_text, 'ht-knowledge-base'); ?>"></input>
				<span class="hkb_setting_desc"><?php _e('Header text on the knowledge base home page', 'ht-knowledge-base'); ?></span>      
			<?php
		}

		//number of columns
		function archive_archive_columns_option_render(){
			$kbcolumns = isset($this->ht_kb_settings['archive-columns']) ? $this->ht_kb_settings['archive-columns'] : ''; 
			if(!apply_filters('hkb_archive_archive_columns_option_render', true)){
				echo apply_filters(
					'hkb_archive_archive_columns_option_render_false',
					esc_attr__( '-', 'ht-knowledge-base' )
				);
				return;
			} ?>
				<select class="ht-knowledge-base-settings-archive-columns__input" name="ht_knowledge_base_settings[archive-columns]">
					<option value="1" <?php selected( $this->ht_kb_settings['archive-columns'], '1', true); ?> ><?php _e('One', 'ht-knowledge-base') ?></option>
					<option value="2" <?php selected( $this->ht_kb_settings['archive-columns'], '2', true); ?> ><?php _e('Two', 'ht-knowledge-base') ?></option>
					<option value="3" <?php selected( $this->ht_kb_settings['archive-columns'], '3', true); ?> ><?php _e('Three', 'ht-knowledge-base') ?></option>
					<option value="4" <?php selected( $this->ht_kb_settings['archive-columns'], '4', true); ?> ><?php _e('Four', 'ht-knowledge-base') ?></option>
				</select>   

				<span class="hkb_setting_desc"><?php _e('Number of columns for the knowledge base home', 'ht-knowledge-base'); ?></span>
			<?php
		}

		//display article count
		function archive_display_article_count_option_render(){
			if(!apply_filters('hkb_archive_display_article_count_option_render', true)){
				echo apply_filters(
					'hkb_archive_display_article_count_option_render_false',
					esc_attr__( '-', 'ht-knowledge-base' )
				);
				return;
			} ?>
				<input type="checkbox" class="ht-knowledge-base-settings-display-article-count__input" name="ht_knowledge_base_settings[display-article-count]" value="1" <?php checked( $this->ht_kb_settings['display-article-count'], 1 ); ?> />
				<span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Display the count of articles in categories', 'ht-knowledge-base'); ?></span>                
			<?php
		}

		//articles in home
		function archive_num_articles_home_option_render(){
			//$num_articles_home = isset($this->ht_kb_settings['num-articles-home']) ? $this->ht_kb_settings['num-articles-home'] : ''; 
			if(!apply_filters('hkb_archive_num_articles_home_option_render', true)){
				echo apply_filters(
					'hkb_archive_num_articles_home_option_render_false',
					esc_attr__( '-', 'ht-knowledge-base' )
				);
				return;
			} ?>
				<select class="ht-knowledge-base-settings-num-articles-home__input" name="ht_knowledge_base_settings[num-articles-home]">
					<option value="0" <?php selected( $this->ht_kb_settings['num-articles-home'], '0', true); ?> ><?php _e('0', 'ht-knowledge-base') ?></option>
					<option value="1" <?php selected( $this->ht_kb_settings['num-articles-home'], '1', true); ?> ><?php _e('1', 'ht-knowledge-base') ?></option>
					<option value="2" <?php selected( $this->ht_kb_settings['num-articles-home'], '2', true); ?> ><?php _e('2', 'ht-knowledge-base') ?></option>
					<option value="3" <?php selected( $this->ht_kb_settings['num-articles-home'], '3', true); ?> ><?php _e('3', 'ht-knowledge-base') ?></option>
					<option value="4" <?php selected( $this->ht_kb_settings['num-articles-home'], '4', true); ?> ><?php _e('4', 'ht-knowledge-base') ?></option>
					<option value="5" <?php selected( $this->ht_kb_settings['num-articles-home'], '5', true); ?> ><?php _e('5', 'ht-knowledge-base') ?></option>
					<option value="6" <?php selected( $this->ht_kb_settings['num-articles-home'], '6', true); ?> ><?php _e('6', 'ht-knowledge-base') ?></option>
					<option value="7" <?php selected( $this->ht_kb_settings['num-articles-home'], '7', true); ?> ><?php _e('7', 'ht-knowledge-base') ?></option>
					<option value="8" <?php selected( $this->ht_kb_settings['num-articles-home'], '8', true); ?> ><?php _e('8', 'ht-knowledge-base') ?></option>
					<option value="9" <?php selected( $this->ht_kb_settings['num-articles-home'], '9', true); ?> ><?php _e('9', 'ht-knowledge-base') ?></option>
					<option value="10" <?php selected( $this->ht_kb_settings['num-articles-home'], '10', true); ?> ><?php _e('10', 'ht-knowledge-base') ?></option>
				</select> 

				<span class="hkb_setting_desc"><?php _e('Number of articles to display for each category in knowledge base home', 'ht-knowledge-base'); ?></span>      
			<?php
		}

		//display subcats
		//@deprecated
		function archive_display_sub_cats_option_render(){
			if(!apply_filters('hkb_archive_display_sub_cats_option_render', true)){
				echo apply_filters(
					'hkb_archive_display_sub_cats_option_render_false',
					esc_attr__( '-', 'ht-knowledge-base' )
				);
				return;
			} ?>
				<input type="checkbox" class="ht-knowledge-base-settings-display-sub-cats__input" name="ht_knowledge_base_settings[display-sub-cats]" value="1" <?php checked( $this->ht_kb_settings['display-sub-cats'], 1 ); ?> />
				<span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Display the sub-categories from knowledge base home', 'ht-knowledge-base'); ?></span>                
			<?php
		}

		//subcat depth
		function archive_sub_cat_depth_option_render(){
			$subcatdepth = isset($this->ht_kb_settings['sub-cat-depth']) ? $this->ht_kb_settings['sub-cat-depth'] : ''; 
			if(!apply_filters('hkb_archive_sub_cat_depth_option_render', true)){
				echo apply_filters(
					'hkb_archive_sub_cat_depth_option_render_false',
					esc_attr__( '-', 'ht-knowledge-base' )
				);
				return;
			} ?>
				<select class="ht-knowledge-base-settings-sub-cat-depth__input" name="ht_knowledge_base_settings[sub-cat-depth]">
					<option value="0" <?php selected( $this->ht_kb_settings['sub-cat-depth'], '0', true); ?> ><?php _e('None', 'ht-knowledge-base') ?></option>
					<option value="1" <?php selected( $this->ht_kb_settings['sub-cat-depth'], '1', true); ?> ><?php _e('One', 'ht-knowledge-base') ?></option>
					<option value="2" <?php selected( $this->ht_kb_settings['sub-cat-depth'], '2', true); ?> ><?php _e('Two', 'ht-knowledge-base') ?></option>
				</select>  
				<span class="hkb_setting_desc"><?php _e('Number of sub-categories to display in knowledge base home', 'ht-knowledge-base'); ?></span>      
			<?php
		}

		//display subcats articles
		function archive_display_sub_cat_articles_option_render(){
			if(!apply_filters('hkb_archive_display_sub_cat_articles_option_render', true)){
				echo apply_filters(
					'hkb_archive_display_sub_cat_articles_option_render_false',
					esc_attr__( '-', 'ht-knowledge-base' )
				);
				return;
			} ?>
				<input type="checkbox" class="ht-knowledge-base-settings-display-sub-cat-articles__input" name="ht_knowledge_base_settings[display-sub-cat-articles]" value="1" <?php checked( $this->ht_kb_settings['display-sub-cat-articles'], 1 ); ?> />
				<span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Display the articles in sub-categories from knowledge base home', 'ht-knowledge-base'); ?></span>                
			<?php
		}

		//hide empty categories
		function archive_hide_empty_cats_option_render(){
			if(!apply_filters('hkb_archive_hide_empty_cats_option_render', true)){
				echo apply_filters(
					'hkb_archive_hide_empty_cats_option_render_false',
					esc_attr__( '-', 'ht-knowledge-base' )
				);
				return;
			} ?>
				<input type="checkbox" class="ht-knowledge-base-settings-hide-empty-cats__input" name="ht_knowledge_base_settings[hide-empty-cats]" value="1" <?php checked( $this->ht_kb_settings['hide-empty-cats'], 1 ); ?> />
				<span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Hide empty categories in the knowledge base home', 'ht-knowledge-base'); ?></span>                
			<?php
		}

		//hide uncategorized articles
		function archive_hide_uncat_articles_option_render(){
			if(!apply_filters('hkb_archive_hide_uncat_articles_option_render', true)){
				echo apply_filters(
					'hkb_archive_hide_uncat_articles_option_render_false',
					esc_attr__( '-', 'ht-knowledge-base' )
				);
				return;
			} ?>
				<input type="checkbox" class="ht-knowledge-base-settings-hide-uncat-articles__input" name="ht_knowledge_base_settings[hide-uncat-articles]" value="1" <?php checked( $this->ht_kb_settings['hide-uncat-articles'], 1 ); ?> />
				<span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Hide uncategorized articles in the knowledge base home', 'ht-knowledge-base'); ?></span>                
			<?php
		}

		/* ARTICLE SECTION */

		//section header 
		function ht_kb_settings_article_section_description(){
			?>
				<div class="hkb-settings-article-section-start"></div>
			<?php
		}        

		//article comments
		function article_enable_article_comments_option_render(){
			if(!apply_filters('hkb_article_enable_article_comments_option_render', true)){
				echo apply_filters(
					'hkb_article_enable_article_comments_option_render_false',
					esc_attr__( '-', 'ht-knowledge-base' )
				);
				return;
			} ?>
				<input type="checkbox" class="ht-knowledge-base-settings-enable-article-comments__input" name="ht_knowledge_base_settings[enable-article-comments]" value="1" <?php checked( $this->ht_kb_settings['enable-article-comments'], 1 ); ?> />
				<span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Allow readers to comment on article', 'ht-knowledge-base'); ?></span>                
			<?php
		}

		//article usefulness display
		function article_display_article_usefulness_option_render(){
			if(!apply_filters('hkb_article_display_article_usefulness_option_render', true)){
				echo apply_filters(
					'hkb_article_display_article_usefulness_option_render_false',
					esc_attr__( '-', 'ht-knowledge-base' )
				);
				return;
			} ?>
				<input type="checkbox" class="ht-knowledge-base-settings-display-article-usefulness__input" name="ht_knowledge_base_settings[display-article-usefulness]" value="1" <?php checked( $this->ht_kb_settings['display-article-usefulness'], 1 ); ?> />
				<span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Display the usefulness of article', 'ht-knowledge-base'); ?></span>                
			<?php
		}

		//article viewcount display
		function article_display_article_views_count_option_render(){
			if(!apply_filters('hkb_article_display_article_views_count_option_render', true)){
				echo apply_filters(
					'hkb_article_display_article_views_count_option_render_false',
					esc_attr__( '-', 'ht-knowledge-base' )
				);
				return;
			} ?>
				<input type="checkbox" class="ht-knowledge-base-settings-display-article-views-count__input" name="ht_knowledge_base_settings[display-article-views-count]" value="1" <?php checked( $this->ht_kb_settings['display-article-views-count'], 1 ); ?> />
				<span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Display the view count of article', 'ht-knowledge-base'); ?></span>                
			<?php
		}

		//comment count display
		function article_display_article_comment_count_option_render(){
			if(!apply_filters('hkb_article_display_article_comment_count_option_render', true)){
				echo apply_filters(
					'hkb_article_display_article_comment_count_option_render_false',
					esc_attr__( '-', 'ht-knowledge-base' )
				);
				return;
			} ?>
				<input type="checkbox" class="ht-knowledge-base-settings-display-article-comment-count__input" name="ht_knowledge_base_settings[display-article-comment-count]" value="1" <?php checked( $this->ht_kb_settings['display-article-comment-count'], 1 ); ?> />
				<span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Display the comments count of article', 'ht-knowledge-base'); ?></span>                
			<?php
		}

		//author display
		function article_display_article_author_option_render(){
			if(!apply_filters('hkb_article_display_article_author_option_render', true)){
				echo apply_filters(
					'hkb_article_display_article_author_option_render_false',
					esc_attr__( '-', 'ht-knowledge-base' )
				);
				return;
			} ?>
				<input type="checkbox" class="ht-knowledge-base-settings-display-article-author__input" name="ht_knowledge_base_settings[display-article-author]" value="1" <?php checked( $this->ht_kb_settings['display-article-author'], 1 ); ?> />
				<span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Display the author bio at the end of the article', 'ht-knowledge-base'); ?></span>                
			<?php
		}

		//related articles display
		function article_display_related_articles_option_render(){
			if(!apply_filters('hkb_article_display_related_articles_option_render', true)){
				echo apply_filters(
					'hkb_article_display_related_articles_option_render_false',
					esc_attr__( '-', 'ht-knowledge-base' )
				);
				return;
			} ?>
				<input type="checkbox" class="ht-knowledge-base-settings-display-related-articles__input" name="ht_knowledge_base_settings[display-related-articles]" value="1" <?php checked( $this->ht_kb_settings['display-related-articles'], 1 ); ?> />
				<span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Display related (articles that appear in the same category)', 'ht-knowledge-base'); ?></span>                
			<?php
		}

		/* SEARCH SECTION */

		//section header 
		function ht_kb_settings_search_section_description(){
			?>
				<div class="hkb-settings-search-section-start"></div>
			<?php
		}

		//live search
		function search_display_live_search_option_render(){
			if(!apply_filters('hkb_search_display_live_search_option_render', true)){
				echo apply_filters(
					'hkb_search_display_live_search_option_render_false',
					esc_attr__( '-', 'ht-knowledge-base' )
				);
				return;
			} ?>
				<input type="checkbox" class="ht-knowledge-base-settings-display-live-search__input" name="ht_knowledge_base_settings[display-live-search]" value="1" <?php checked( $this->ht_kb_settings['display-live-search'], 1 ); ?> />
				<span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Display knowledge base search box on knowledge base pages', 'ht-knowledge-base'); ?></span>                
			<?php
		}

		//focus search
		function search_focus_live_search_option_render(){
			if(!apply_filters('hkb_search_focus_live_search_option_render', true)){
				echo apply_filters(
					'hkb_search_focus_live_search_option_render_false',
					esc_attr__( '-', 'ht-knowledge-base' )
				);
				return;
			} ?>
				<input type="checkbox" class="ht-knowledge-base-settings-focus-live-search__input" name="ht_knowledge_base_settings[focus-live-search]" value="1" <?php checked( $this->ht_kb_settings['focus-live-search'], 1 ); ?> />
				<span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Set the mouse focus on the knowledge base search box when page loads', 'ht-knowledge-base'); ?></span>                
			<?php
		}

		//search placeholder
		function search_search_placeholder_text_option_render(){
			$search_placeholder_text = isset($this->ht_kb_settings['search-placeholder-text']) ? $this->ht_kb_settings['search-placeholder-text'] : ''; 
			if(!apply_filters('hkb_search_search_placeholder_text_option_render', true)){
				echo apply_filters(
					'hkb_search_search_placeholder_text_option_render_false',
					esc_attr__( '-', 'ht-knowledge-base' )
				);
				return;
			} ?>
				<input type="text" class="ht-knowledge-base-settings-search-placeholder-text__input" name="ht_knowledge_base_settings[search-placeholder-text]" value="<?php esc_attr_e($search_placeholder_text, 'ht-knowledge-base'); ?>"></input>
				<span class="hkb_setting_desc"><?php _e('Placeholder text for the knowledge base search box', 'ht-knowledge-base'); ?></span>      
			<?php
		}

		//search results excerpt
		function search_display_search_result_excerpt_option_render(){
			if(!apply_filters('hkb_search_display_search_result_excerpt_option_render', true)){
				echo apply_filters(
					'hkb_search_display_search_result_excerpt_option_render_false',
					esc_attr__( '-', 'ht-knowledge-base' )
				);
				return;
			} ?>
				<input type="checkbox" class="ht-knowledge-base-settings-display-search-result-excerpt__input" name="ht_knowledge_base_settings[display-search-result-excerpt]" value="1" <?php checked( $this->ht_kb_settings['display-search-result-excerpt'], 1 ); ?> />
				<span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Display an excerpt of knowledge base articles in search results', 'ht-knowledge-base'); ?></span>                
			<?php
		}

		//highlight search term in excerpt
		function highlight_search_result_excerpt_option_render(){
			if(!apply_filters('hkb_highlight_search_result_excerpt_option_render', true)){
				echo apply_filters(
					'hkb_highlight_search_result_excerpt_option_render_false',
					esc_attr__( '-', 'ht-knowledge-base' )
				);
				return;
			} ?>
				<input type="checkbox" class="ht-knowledge-base-settings-highlight-search-result-excerpt__input" name="ht_knowledge_base_settings[highlight-search-result-excerpt]" value="1" <?php checked( $this->ht_kb_settings['highlight-search-result-excerpt'], 1 ); ?> />
				<span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Highlight search term in search results excerpt', 'ht-knowledge-base'); ?></span>                
			<?php
		}

		/* SLUGS SECTION */

		//section header 
		function ht_kb_settings_slugs_section_description(){
			?>
				<div class="hkb-settings-slugs-section-start"></div>
			<?php
		}

		//article slug
		function slugs_kb_article_slug_option_render(){
			$kb_article_slug = isset($this->ht_kb_settings['kb-article-slug']) ? $this->ht_kb_settings['kb-article-slug'] : ''; 
			if(!apply_filters('hkb_slugs_kb_article_slug_option_render', true)){
				echo apply_filters(
					'hkb_slugs_kb_article_slug_option_render_false',
					esc_attr__( '-', 'ht-knowledge-base' )
				);
				return;
			} ?>
				<input type="text" class="ht-knowledge-base-settings-kb-article-slug__input" name="ht_knowledge_base_settings[kb-article-slug]" value="<?php esc_attr_e($kb_article_slug, 'ht-knowledge-base'); ?>" data-error="<?php _e('This slug is invalid or the same as another slug', 'ht-knowledge-base'); ?>"></input>
				<span class="hkb_setting_desc"><?php _e('Defines the text slug for articles', 'ht-knowledge-base'); ?></span>  
				<span class="hkb_setting_info"><?php _e('The URL for the home page can be set by editing the permalink URL Slug for the assigned page', 'ht-knowledge-base'); ?></span> 
			<?php
		}


		//category slug
		function slugs_kb_category_slug_option_render(){
			$kb_category_slug = isset($this->ht_kb_settings['kb-category-slug']) ? $this->ht_kb_settings['kb-category-slug'] : ''; 
			if(!apply_filters('hkb_slugs_kb_category_slug_option_render', true)){
				echo apply_filters(
					'hkb_slugs_kb_category_slug_option_render_false',
					esc_attr__( '-', 'ht-knowledge-base' )
				);
				return;
			} ?>
				<input type="text" class="ht-knowledge-base-settings-kb-category-slug__input" name="ht_knowledge_base_settings[kb-category-slug]" value="<?php esc_attr_e($kb_category_slug, 'ht-knowledge-base'); ?>" data-error="<?php _e('This slug is invalid or the same as another slug', 'ht-knowledge-base'); ?>"></input>
				<span class="hkb_setting_desc"><?php _e('Defines the text slug for knowledge base categories', 'ht-knowledge-base'); ?></span>      
			<?php
		}

		//tag slug
		function slugs_kb_tag_slug_option_render(){
			$kb_tag_slug = isset($this->ht_kb_settings['kb-tag-slug']) ? $this->ht_kb_settings['kb-tag-slug'] : ''; 
			if(!apply_filters('hkb_slugs_kb_tag_slug_option_render', true)){
				echo apply_filters(
					'hkb_slugs_kb_tag_slug_option_render_false',
					esc_attr__( '-', 'ht-knowledge-base' )
				);
				return;
			} ?>
				<input type="text" class="ht-knowledge-base-settings-kb-tag-slug__input" name="ht_knowledge_base_settings[kb-tag-slug]" value="<?php esc_attr_e($kb_tag_slug, 'ht-knowledge-base'); ?>" data-error="<?php _e('This slug is invalid or the same as another slug', 'ht-knowledge-base'); ?>"></input>
				<span class="hkb_setting_desc"><?php _e('Defines the text slug for knowledge base tags', 'ht-knowledge-base'); ?></span>      
			<?php
		}

		/* ARTICLE SECTION */

		//section header 
		function ht_kb_settings_sidebars_section_description(){
			?>
				<div class="hkb-settings-sidebars-section-start"></div>
			<?php
		}

		// kb archive sidebar postion
		function archive_sidebar_position_option_render(){ 
				$archive_sidebar_position = isset($this->ht_kb_settings['archive-sidebar-position']) ? $this->ht_kb_settings['archive-sidebar-position'] : 'right'; 
				if(!apply_filters('hkb_archive_sidebar_position_option_render', true)){
					echo apply_filters(
						'hkb_archive_sidebar_position_option_render_false',
						esc_attr__( '-', 'ht-knowledge-base' )
					);
					return;
				} ?>
				<div class="hkb-setting-radioimage">
				  <label>
					<input type="radio" name="ht_knowledge_base_settings[archive-sidebar-position]" value="left" <?php checked( 'left', $archive_sidebar_position ); ?> />
					<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" stroke-width="2"><g fill="none" stroke="#444" stroke-linecap="square" stroke-miterlimit="10"><path d="M2 6h44v36H2z"/><path d="M8 12h8v24H8z"/></g></svg>
				  </label>
				  
				  <label>
					<input type="radio" name="ht_knowledge_base_settings[archive-sidebar-position]" value="right" <?php checked( 'right', $archive_sidebar_position ); ?> />
					<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" stroke-width="2"><g fill="none" stroke="#444" stroke-linecap="square" stroke-miterlimit="10"><path d="M2 6h44v36H2z"/><path d="M32 12h8v24h-8z"/></g></svg>
				  </label>
				  
				  <label>
					<input type="radio" name="ht_knowledge_base_settings[archive-sidebar-position]" value="off" <?php checked( 'off', $archive_sidebar_position ); ?> />
					<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" stroke-width="2"><g fill="none" stroke="#444" stroke-linecap="square" stroke-miterlimit="10"><path d="M2 6h44v36H2z"/></g></svg>
				  </label>  
				  
				</div>
				<span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Select the sidebar position on your kb home page.', 'ht-knowledge-base'); ?></span>                
			<?php
		}

		// category sidebar postion
		function category_sidebar_position_option_render(){ 
				$category_sidebar_position = isset($this->ht_kb_settings['category-sidebar-position']) ? $this->ht_kb_settings['category-sidebar-position'] : 'right'; 
				if(!apply_filters('hkb_category_sidebar_position_option_render', true)){
					echo apply_filters(
						'hkb_category_sidebar_position_option_render_false',
						esc_attr__( '-', 'ht-knowledge-base' )
					);
					return;
				} ?>
				<div class="hkb-setting-radioimage">
				  <label>
					<input type="radio" name="ht_knowledge_base_settings[category-sidebar-position]" value="left" <?php checked( 'left', $category_sidebar_position ); ?> />
					<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" stroke-width="2"><g fill="none" stroke="#444" stroke-linecap="square" stroke-miterlimit="10"><path d="M2 6h44v36H2z"/><path d="M8 12h8v24H8z"/></g></svg>
				  </label>
				  
				  <label>
					<input type="radio" name="ht_knowledge_base_settings[category-sidebar-position]" value="right" <?php checked( 'right', $category_sidebar_position ); ?> />
					<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" stroke-width="2"><g fill="none" stroke="#444" stroke-linecap="square" stroke-miterlimit="10"><path d="M2 6h44v36H2z"/><path d="M32 12h8v24h-8z"/></g></svg>
				  </label>
				  
				  <label>
					<input type="radio" name="ht_knowledge_base_settings[category-sidebar-position]" value="off" <?php checked( 'off', $category_sidebar_position ); ?> />
					<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" stroke-width="2"><g fill="none" stroke="#444" stroke-linecap="square" stroke-miterlimit="10"><path d="M2 6h44v36H2z"/></g></svg>
				  </label>  
				  
				</div>
				<span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Select the sidebar position on your category pages.', 'ht-knowledge-base'); ?></span>                
			<?php
		}

		// article sidebar postion
		function article_sidebar_position_option_render(){ 
				$article_sidebar_position = isset($this->ht_kb_settings['article-sidebar-position']) ? $this->ht_kb_settings['article-sidebar-position'] : 'right';
				if(!apply_filters('hkb_article_sidebar_position_option_render', true)){
					echo apply_filters(
						'hkb_article_sidebar_position_option_render_false',
						esc_attr__( '-', 'ht-knowledge-base' )
					);
					return;
				} ?>
				<div class="hkb-setting-radioimage">
				  <label>
					<input type="radio" name="ht_knowledge_base_settings[article-sidebar-position]" value="left" <?php checked( 'left', $article_sidebar_position ); ?> />
					<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" stroke-width="2"><g fill="none" stroke="#444" stroke-linecap="square" stroke-miterlimit="10"><path d="M2 6h44v36H2z"/><path d="M8 12h8v24H8z"/></g></svg>
				  </label>
				  
				  <label>
					<input type="radio" name="ht_knowledge_base_settings[article-sidebar-position]" value="right" <?php checked( 'right', $article_sidebar_position ); ?> />
					<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" stroke-width="2"><g fill="none" stroke="#444" stroke-linecap="square" stroke-miterlimit="10"><path d="M2 6h44v36H2z"/><path d="M32 12h8v24h-8z"/></g></svg>
				  </label>
				  
				  <label>
					<input type="radio" name="ht_knowledge_base_settings[article-sidebar-position]" value="off" <?php checked( 'off', $article_sidebar_position ); ?> />
					<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" stroke-width="2"><g fill="none" stroke="#444" stroke-linecap="square" stroke-miterlimit="10"><path d="M2 6h44v36H2z"/></g></svg>
				  </label>  
				  
				</div>
				<span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Select the sidebar position on your article pages.', 'ht-knowledge-base'); ?></span>                
			<?php
		}

		// article sticky sidebar
		function article_sticky_sidebar_option_render(){ 
				if(!apply_filters('hkb_article_sticky_sidebar_option_render', true)){
					echo apply_filters(
						'hkb_article_sticky_sidebar_option_render_false',
						esc_attr__( '-', 'ht-knowledge-base' )
					);
					return;
				} ?>
				<input type="checkbox" class="" name="ht_knowledge_base_settings[article-sticky-sidebar]" value="1" <?php checked( $this->ht_kb_settings['article-sticky-sidebar'], 1 ); ?> />
				<span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Enable sticky sidebar on the article pages.', 'ht-knowledge-base'); ?></span>                
			<?php
		}


		/* CUSTOM STYLES SECTION */

		// section header 
		function ht_kb_settings_customstyles_section_description(){
			?>
				<div class="hkb-settings-customstyles-section-start"></div>
			<?php
		}

		// custom styles textarea
		function customstyles_custom_kb_styline_content_option_render(){
			$custom_kb_styling_content = isset($this->ht_kb_settings['custom-kb-styling-content']) ? $this->ht_kb_settings['custom-kb-styling-content'] : ''; 
			if(!apply_filters('hkb_customstyles_custom_kb_styline_content_option_render', true)){
				echo apply_filters(
						'hkb_customstyles_custom_kb_styline_content_option_render_false',
						esc_attr__( '-', 'ht-knowledge-base' )
					);
				return;
			} ?>
				<textarea type="text" class="ht-knowledge-base-settings-custom-kb-styling-content__input" name="ht_knowledge_base_settings[custom-kb-styling-content]"><?php esc_attr_e($custom_kb_styling_content, 'ht-knowledge-base'); ?></textarea>
				<span class="hkb_setting_desc"><?php printf(__('Important - use valid CSS only, you may want to <a href="%s" target="_blank">validate your styles</a> before saving', 'ht-knowledge-base'), 'https://jigsaw.w3.org/css-validator/#validate_by_input'); ?></span>      
			<?php
		}

		// custom styles sitewide
		function customstyles_enable_kb_styling_sitewide_option_render(){
			if(!apply_filters('hkb_customstyles_enable_kb_styling_sitewide_option_render', true)){
				echo apply_filters(
						'hkb_customstyles_enable_kb_styling_sitewide_option_render_false',
						esc_attr__( '-', 'ht-knowledge-base' )
					);
				return;
			} ?>
				<input type="checkbox" class="ht-knowledge-base-settings-enable-kb-styling-sitewide__input" name="ht_knowledge_base_settings[enable-kb-styling-sitewide]" value="1" <?php checked( $this->ht_kb_settings['enable-kb-styling-sitewide'], 1 ); ?> />
				<span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Use these custom styles across entire site', 'ht-knowledge-base'); ?></span>                
			<?php
		}

		// KB width
		function customstyles_kb_width_option_render(){ 
			$kb_width = isset($this->ht_kb_settings['kb-width']) ? (int) $this->ht_kb_settings['kb-width'] : 1200; 
			if(!apply_filters('hkb_customstyles_kb_width_option_render', true)){
				echo apply_filters(
						'hkb_customstyles_kb_width_option_render_false',
						esc_attr__( '-', 'ht-knowledge-base' )
					);
				return;
			} ?>
				<div class="hkb-setting-range">
				  <input id="kbWidthPicker" type="range" name="ht_knowledge_base_settings[kb-width]" min="700" max="1800" value="<?php esc_attr_e($kb_width, 'ht-knowledge-base'); ?>" step="10" onchange="kbWidthValue.value=value" oninput="kbWidthValue.value=value">
				  <input id="kbWidthValue" type="number" min="700" max="1800" value="<?php esc_attr_e($kb_width, 'ht-knowledge-base'); ?>" step="10" onchange="kbWidthPicker.value=value" />
				  <span>px</span>
				</div>

				<span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Set the width of the knowledge base.', 'ht-knowledge-base'); ?></span>                
			<?php
		}

		// Link Color
		function customstyles_kb_linkcolor_option_render(){ 
			$kb_linkcolor = isset($this->ht_kb_settings['kb-linkcolor']) ? esc_attr($this->ht_kb_settings['kb-linkcolor']) : '#1e73be'; 
			if(!apply_filters('hkb_customstyles_kb_linkcolor_option_render', true)){
				echo apply_filters(
						'hkb_customstyles_kb_linkcolor_option_render_false',
						esc_attr__( '-', 'ht-knowledge-base' )
					);
				return;
			} ?>
				<div class="hkb-setting">
				  <input type="text" class="hkb-colorpicker" name="ht_knowledge_base_settings[kb-linkcolor]" value="<?php echo $kb_linkcolor; ?>" />
				</div>

				<span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Set the link color in the KB.', 'ht-knowledge-base'); ?></span>                
			<?php
		}

		// Link Color (Hover)
		function customstyles_kb_linkcolorhover_option_render(){
			$kb_linkcolorhover = isset($this->ht_kb_settings['kb-linkcolorhover']) ? esc_attr($this->ht_kb_settings['kb-linkcolorhover']) : '#1e73be';
			if(!apply_filters('hkb_customstyles_kb_linkcolorhover_option_render', true)){
				echo apply_filters(
						'hkb_customstyles_kb_linkcolorhover_option_render_false',
						esc_attr__( '-', 'ht-knowledge-base' )
					);
				return;
			} ?>
				<div class="hkb-setting">
				  <input type="text" class="hkb-colorpicker" name="ht_knowledge_base_settings[kb-linkcolorhover]" value="<?php echo $kb_linkcolorhover; ?>" />
				</div>

				<span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Set the link hover color in the KB.', 'ht-knowledge-base'); ?></span>
			<?php
		}

		// KB Header Styling
		function customstyles_kb_headerstyle_option_render(){ 

			$header_img_placeholder = plugins_url( 'img/no-image.png', HT_KB_MAIN_PLUGIN_FILE ); 

			$kb_headerstyle = isset($this->ht_kb_settings['kb-headerstyle']) ? $this->ht_kb_settings['kb-headerstyle'] : 'solid';  
			$kb_headerstyle_bg = isset($this->ht_kb_settings['kb-headerstyle-bg']) ? esc_attr($this->ht_kb_settings['kb-headerstyle-bg']) : '#7a7a7a'; 
			$kb_headerstyle_graddir = isset($this->ht_kb_settings['kb-headerstyle-graddir']) ? (int) esc_attr($this->ht_kb_settings['kb-headerstyle-graddir']) : 90; 
			$kb_headerstyle_grad1 = isset($this->ht_kb_settings['kb-headerstyle-grad1']) ? esc_attr($this->ht_kb_settings['kb-headerstyle-grad1']) : '#7a7a7a'; 
			$kb_headerstyle_grad2 = isset($this->ht_kb_settings['kb-headerstyle-grad2']) ? esc_attr($this->ht_kb_settings['kb-headerstyle-grad2']) : '#7a7a7a'; 
			$kb_headerstyle_img_attachment_id = isset($this->ht_kb_settings['kb-headerstyle-img-attachment-id']) ? (int) $this->ht_kb_settings['kb-headerstyle-img-attachment-id'] : 0;

			$kb_headerstyle_img_preview = $kb_headerstyle_img_attachment_id ? wp_get_attachment_thumb_url($kb_headerstyle_img_attachment_id) : $header_img_placeholder; 

			if(!apply_filters('hkb_customstyles_kb_headerstyle_option_render', true)){
				echo apply_filters(
						'hkb_customstyles_kb_headerstyle_option_render_false',
						esc_attr__( '-', 'ht-knowledge-base' )
					);
				return;
			} ?>
			
			<div class="hkb-setting-bg">
				<div class="hkb-btngroup">
					<label>
						<input type="radio" class="hkb-btngroup__radio" name="ht_knowledge_base_settings[kb-headerstyle]" value="solid" <?php checked( 'solid', $kb_headerstyle ); ?> />
				  		<span type="button" class="hkb-btngroup__btn" data-tab="solid"><?php _e('Solid Color', 'ht-knowledge-base'); ?></span>
				  	</label>
				  	<label>
				  		<input type="radio" class="hkb-btngroup__radio" name="ht_knowledge_base_settings[kb-headerstyle]" value="gradient" <?php checked( 'gradient', $kb_headerstyle ); ?> />  
				  		<span type="button" class="hkb-btngroup__btn" data-tab="gradient"><?php _e('Gradient', 'ht-knowledge-base'); ?></span>
					</label>
					<label>
						<input type="radio" class="hkb-btngroup__radio" name="ht_knowledge_base_settings[kb-headerstyle]" value="image" <?php checked( 'image', $kb_headerstyle ); ?> />
						<span type="button" class="hkb-btngroup__btn" data-tab="image"><?php _e('Image', 'ht-knowledge-base'); ?></span>
					</label>
					
				</div>

				<div class="hkb-settings-minitab">
					<?php // Solid Color ?>
					<div id="solid" class="hkb-setting-bg__solid">
						<input type="text" class="hkb-colorpicker" name="ht_knowledge_base_settings[kb-headerstyle-bg]" value="<?php echo $kb_headerstyle_bg; ?>"/>
					</div>
					
					<?php // Grad ?>
					<div id="gradient" class="hkb-setting-bg__grad">
						<div class="hkb-setting-range">
							<label for="kbGradPicker"><?php _e('Direction', 'ht-knowledge-base'); ?></label>
							<input id="kbGradPicker" type="range" name="ht_knowledge_base_settings[kb-headerstyle-graddir]" min="0" max="360" step="1" value="<?php esc_attr_e($kb_headerstyle_graddir, 'ht-knowledge-base'); ?>" onchange="kbGradValue.value=value" oninput="kbGradValue.value=value">
							<input id="kbGradValue" type="number" min="0" max="360" value="<?php esc_attr_e($kb_headerstyle_graddir, 'ht-knowledge-base'); ?>" step="1" value="<?php echo $kb_headerstyle_graddir; ?>" onchange="kbGradPicker.value=value" />
						</div>
						<div class="hkb-setting-bg__gradcolors">
						<div>
							<label for="headerstyle-grad1"><?php _e('Color 1', 'ht-knowledge-base'); ?></label>
							<input type="text" class="hkb-colorpicker" name="ht_knowledge_base_settings[kb-headerstyle-grad1]" value="<?php echo $kb_headerstyle_grad1; ?>" />
						</div>
						<div>
							<label for="headerstyle-grad1"><?php _e('Color 2', 'ht-knowledge-base'); ?></label>
							<input type="text" class="hkb-colorpicker" name="ht_knowledge_base_settings[kb-headerstyle-grad2]" value="<?php echo $kb_headerstyle_grad2; ?>" />
						</div>
						</div>
					</div>

					<?php // Image ?>
					<div id="image" class="hkb-setting-bg__image">
						<img class="hkb-bgimageselect__preview" src="<?php echo $kb_headerstyle_img_preview; ?>" data-fallback="<?php echo $header_img_placeholder; ?>" height="150px" width="150px" />
						<?php //_e('Image Upload & Position', 'ht-knowledge-base'); ?>
						<button class="hkb-bgimageselect__btn"><?php _e('Select or Upload Image', 'ht-knowledge-base'); ?></button>
						<?php if( $kb_headerstyle_img_attachment_id > 0 ): ?>
							<button class="hkb-bgimageselect__remove"><?php _e('Remove', 'ht-knowledge-base'); ?></button>
						<?php endif; ?>
						<input type="hidden" class="hkb-bgimageselect__id" name="ht_knowledge_base_settings[kb-headerstyle-img-attachment-id]" value="<?php echo (int) $kb_headerstyle_img_attachment_id; ?>" />
						


					</div>
				</div>

				<span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Set the background of the knowledge base header', 'ht-knowledge-base'); ?></span>


			</div>




			<?php
		}

		// KB Header Color
		function customstyles_kb_headercolor_option_render(){ 
			$kb_headercolor = isset($this->ht_kb_settings['kb-headercolor']) ? esc_attr($this->ht_kb_settings['kb-headercolor']) : '#fff'; 

			if(!apply_filters('hkb_customstyles_kb_headercolor_option_render', true)){
				echo apply_filters(
						'hkb_customstyles_kb_headercolor_option_render_false',
						esc_attr__( '-', 'ht-knowledge-base' )
					);
				return;
			} ?>
				<div class="hkb-setting">
				  <input type="text" class="hkb-colorpicker" name="ht_knowledge_base_settings[kb-headercolor]" value="<?php echo $kb_headercolor; ?>" />
				</div>

				<span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Set the text color in the KB header.', 'ht-knowledge-base'); ?></span>                
			<?php
		}






		/* ARTICLE FEEDBACK SECTION */

		//section header 
		function ht_kb_settings_articlefeedback_section_description(){
			?>
				<div class="hkb-settings-articlefeedback-section-start"></div>
			<?php
		}

		function articlefeedback_enable_article_feedback_option_render(){
			if(!apply_filters('hkb_articlefeedback_enable_article_feedback_option_render', true)){
				echo apply_filters(
						'hkb_articlefeedback_enable_article_feedback_option_render_false',
						esc_attr__( '-', 'ht-knowledge-base' )
					);
				return;
			} ?>
				<input type="checkbox" class="ht-knowledge-base-settings-enable-article-feedback__input" name="ht_knowledge_base_settings[enable-article-feedback]" value="1" <?php checked( $this->ht_kb_settings['enable-article-feedback'], 1 ); ?> />
				<span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Allow visitors to rate articles', 'ht-knowledge-base'); ?></span>                
			<?php
		}

		function articlefeedback_enable_anon_article_feedback_option_render(){
			if(!apply_filters('hkb_articlefeedback_enable_anon_article_feedback_option_render', true)){
				echo apply_filters(
						'hkb_articlefeedback_enable_anon_article_feedback_option_render_false',
						esc_attr__( '-', 'ht-knowledge-base' )
					);
				return;
			} ?>
				<input type="checkbox" class="ht-knowledge-base-settings-enable-anon-article-feedback__input" name="ht_knowledge_base_settings[enable-anon-article-feedback]" value="1" <?php checked( $this->ht_kb_settings['enable-anon-article-feedback'], 1 ); ?> />
				<span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Allow visitors to rate articles when not logged in', 'ht-knowledge-base'); ?></span>
			<?php
		}

		function articlefeedback_enable_upvote_article_feedback_option_render(){
			if(!apply_filters('hkb_articlefeedback_enable_upvote_article_feedback_option_render', true)){
				echo apply_filters(
						'hkb_articlefeedback_enable_upvote_article_feedback_option_render_false',
						esc_attr__( '-', 'ht-knowledge-base' )
					);
				return;
			} ?>
				<input type="checkbox" class="ht-knowledge-base-settings-enable-upvote-article-feedback__input" name="ht_knowledge_base_settings[enable-upvote-article-feedback]" value="1" <?php checked( $this->ht_kb_settings['enable-upvote-article-feedback'], 1 ); ?> />
				<span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Collect feedback for up ratings', 'ht-knowledge-base'); ?></span>                
			<?php
		}

		function articlefeedback_enable_downvote_article_feedback_option_render(){
			if(!apply_filters('hkb_articlefeedback_enable_downvote_article_feedback_option_render', true)){
				echo apply_filters(
						'hkb_articlefeedback_enable_downvote_article_feedback_option_render_false',
						esc_attr__( '-', 'ht-knowledge-base' )
					);
				return;
			} ?>
				<input type="checkbox" class="ht-knowledge-base-settings-enable-downvote-article-feedback__input" name="ht_knowledge_base_settings[enable-downvote-article-feedback]" value="1" <?php checked( $this->ht_kb_settings['enable-downvote-article-feedback'], 1 ); ?> />
				<span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Collect feedback for down ratings', 'ht-knowledge-base'); ?></span>                
			<?php
		}

		/* TRANSFERS SECTION */

		//section header 
		function ht_kb_settings_transfers_section_description(){
			?>
				<div class="hkb-settings-transfers-section-start"></div>
			<?php
		}

		function transfers_kb_transfer_url_option_render(){
			$kb_transfer_url = isset($this->ht_kb_settings['kb-transfer-url']) ? $this->ht_kb_settings['kb-transfer-url'] : ''; 
			if(!apply_filters('hkb_transfers_kb_transfer_url_option_render', true)){
				echo apply_filters(
						'hkb_transfers_kb_transfer_url_option_render_false',
						esc_attr__( '-', 'ht-knowledge-base' )
					);
				return;
			} ?>
				<input type="text" class="ht-knowledge-base-settings-kb-transfer-url__input" name="ht_knowledge_base_settings[kb-transfer-url]" value="<?php esc_attr_e($kb_transfer_url, 'ht-knowledge-base'); ?>"></input>
				<span class="hkb_setting_desc"><?php _e('Default location that users will be transferred to if no URL specified', 'ht-knowledge-base'); ?></span>      
			<?php
		}

		function transfers_kb_transfer_new_window_option_render(){
			if(!apply_filters('hkb_transfers_kb_transfer_new_window_option_render', true)){
				echo apply_filters(
						'hkb_transfers_kb_transfer_new_window_option_render_false',
						esc_attr__( '-', 'ht-knowledge-base' )
					);
				return;
			} ?>
				<input type="checkbox" class="ht-knowledge-base-settings-kb-transfer-new-window__input" name="ht_knowledge_base_settings[kb-transfer-new-window]" value="1" <?php checked( $this->ht_kb_settings['kb-transfer-new-window'], 1 ); ?> />
				<span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Load transfer in a new window', 'ht-knowledge-base'); ?></span>                
			<?php
		}

		/* LICENSE AND UPDATES SECTION */

		//section header 
		function ht_kb_settings_license_section_description(){
			$ht_kb_license_key = get_option( 'ht_kb_license_key' );

			if(!apply_filters('hkb_settings_license_section_description', true)){
				echo apply_filters(
						'hkb_settings_license_section_description_false',
						esc_attr__( '-', 'ht-knowledge-base' )
					);
				return;
			} 

			if($this->theme_managed_updates()){
				_e('Updates of this plugin are controlled by the theme, there is nothing required in this section', 'ht-knowledge-base');
				return;
			}
			
			$default_license_status_text = '';
			$license_key_status_class = '';
			if(empty($ht_kb_license_key)){
				$default_license_status_text = __('Please enter your license key below to enable support and updates, this is contained in your download email', 'ht-knowledge-base');
				$license_key_status_class = 'hkb-license-status--invalid';
			} else {
				$default_license_status_text = $ht_kb_license_key;
				$default_license_status_text = sprintf(__('Unverified, Inactive or Expired - check you license status on your account at <a href="%s" target="_blank">HeroThemes</a>' , 'ht-knowledge-base'), 'https://herothemes.com');
				$license_key_status_class = 'hkb-license-status--invalid';
			}
			//supporting theme 
			$ht_kb_license_status = get_option('ht_kb_license_status');
			$ht_kb_license_status_form = empty($ht_kb_license_status) ? '' : '(' . $ht_kb_license_status . ') ';

			$ht_kb_license_status_text = ('valid'==$ht_kb_license_status) ? __('Valid and Active', 'ht-knowledge-base') : $ht_kb_license_status_form . $default_license_status_text;

			//set the status class
			if('valid'==$ht_kb_license_status){
				$license_key_status_class = 'hkb-license-status--valid';
			}

			//counter transients
			$ht_kb_license_function = get_transient( '_ht_kb_license_function' );
			if(isset($ht_kb_license_function)){
				//$ht_kb_license_status_text = __('Updating - Refresh page to see license status', 'ht-knowledge-base');
			}
			?>

				<div class="hkb-license-status <?php echo $license_key_status_class; ?>">
					<?php echo $ht_kb_license_status_text; ?>
				</div>
			<?php
		}

		function license_kb_license_key_option_render(){
			$kb_license_key = get_option( 'ht_kb_license_key', '' );  //isset($this->ht_kb_settings['kb-license-key']) ? $this->ht_kb_settings['kb-license-key'] : '';


			if(!apply_filters('hkb_license_kb_license_key_option_render', true)){
				echo apply_filters(
						'hkb_license_kb_license_key_option_render_false',
						esc_attr__( '-', 'ht-knowledge-base' )
					);
				return;
			} 

			if($this->theme_managed_updates()){
				_e('Managed by theme (Not required)', 'ht-knowledge-base');
				return;
			}
			?>
				<input type="text" class="ht-knowledge-base-settings-kb-license-key__input" name="ht_knowledge_base_settings[kb-license-key]" value="<?php esc_attr_e($kb_license_key, 'ht-knowledge-base'); ?>"></input>
				<span class="hkb_setting_desc"><?php _e('Enter your Heroic Knowledge Base license key', 'ht-knowledge-base'); ?></span>      
			<?php
		}

		/* VALIDATION OF SETTINGS */

		function ht_knowledge_base_settings_validate($input){

			$output = array();


			//archive page id
			$knowledge_bases = ht_kb_get_registered_knowledge_base_keys();
			foreach ($knowledge_bases as $index => $key) {             
				if( isset( $input['kb-archive-page-id'][$key] ) ) {
					//check $page_id  is one of the accepted values
					$page_id = $input['kb-archive-page-id'][$key];

					if(is_numeric($page_id) ){
						$page_id = intval($page_id);
						$page = get_post($page_id);
						if ( 0 == $page_id || is_a( $page, 'WP_Post') ){
							$output['kb-archive-page-id'][$key] = $page_id;
						} else {
							add_settings_error(
								'kb-archive-page-id',
								'archive_page_id_error',
								__('Home page ID is not a page', 'ht-knowledge-base'),
								'error'                        // type of message
							);
						}
					}     
				}
			}

			//archive page header text
			if( isset($input['kb-archive-page-header-text']) ) {
				$output['kb-archive-page-header-text'] = esc_attr($input['kb-archive-page-header-text']);
			} else {
				$output['kb-archive-page-header-text'] = '';
			}



			//boolean validate breadcrumbs
			if( isset($input['display-breadcrumbs']) ) {
				$output['display-breadcrumbs'] = true;
			} else {
				$output['display-breadcrumbs'] = false;
			}

			//sort by
			if( isset($input['sort-by']) ) {
				//check sort by is one of the accepted values
				$sort_by = $input['sort-by'];
				if('date'==$sort_by||'title'==$sort_by||'commment-count'==$sort_by||'random'==$sort_by||'modified'==$sort_by||'popular'==$sort_by||'helpful'==$sort_by||'custom'==$sort_by){
					//valid sort_by
					$output['sort-by'] = $sort_by;
					//ensure sort order is always asc if custom sort order
					if('custom'==$sort_by){
						$input['sort-order'] = 'asc';
					}
				}
			}

			//sort order
			if( isset($input['sort-order']) ) {
				//check sort order is one of the accepted values
				$sort_order = $input['sort-order'];
				if('asc'==$sort_order||'desc'==$sort_order){
					//valid sort_order
					$output['sort-order'] = $sort_order;
				}
			}

			//number of articles
			if( isset($input['num-articles']) ) {
				//check num-articles is one of the accepted values
				$num_articles = $input['num-articles'];

				if(is_numeric($num_articles) ){
					$num_articles = intval($num_articles);
					if ($num_articles >= 1 && $num_articles <= 100 ){
						$output['num-articles'] = $num_articles;
					} else {
						add_settings_error(
							'num-articles',
							'num_articles_error',
							__('Number of articles must be between 1-100', 'ht-knowledge-base'),
							'error'                        // type of message
						);
					}
				}     
			}

			//boolean validate display-taxonomy-article-excerpt
			if( isset($input['display-taxonomy-article-excerpt']) ) {
				$output['display-taxonomy-article-excerpt'] = true;
			} else {
				$output['display-taxonomy-article-excerpt'] = false;
			}

			//restrict access
			if( isset($input['restrict-access']) ) {
				//check sort by is one of the accepted values
				$restrict_access = $input['restrict-access'];
				//get the valid restriction levels
				$valid_restrict_access_levels = apply_filters('hkb_restrict_access_levels', array());
				//check newly assigned level is valid
				if(array_key_exists($restrict_access, $valid_restrict_access_levels)){
					//valid restrict_access
					$output['restrict-access'] = $restrict_access;
				}
			} 

			//number of columns
			if( isset($input['archive-columns']) ) {
				//check archive-columns is one of the accepted values
				$archive_columns = $input['archive-columns'];

				if(is_numeric($archive_columns) ){
					$archive_columns = intval($archive_columns);
					if ($archive_columns >= 1 && $archive_columns <= 4 ){
						$output['archive-columns'] = $archive_columns;
					}
				}     
			} 

			//boolean validate displayarticlecount
			if( isset($input['display-article-count']) ) {
				$output['display-article-count'] = true;
			} else {
				$output['display-article-count'] = false;
			}

			//number of articles in home
			if( isset($input['num-articles-home']) ) {
				//check num-articles-home is one of the accepted values
				$num_articles_home = $input['num-articles-home'];

				if(is_numeric($num_articles_home) ){
					$num_articles_home = intval($num_articles_home);
					if ($num_articles_home >= 0 && $num_articles_home <= 10 ){
						$output['num-articles-home'] = $num_articles_home;
					}
				}     
			}

			//boolean validate displaysubcats
			if( isset($input['display-sub-cats']) ) {
				$output['display-sub-cats'] = true;
			} else {
				$output['display-sub-cats'] = false;
			}


			//subcat depth
			if( isset($input['sub-cat-depth']) ) {
				//check sub-cat-depth is one of the accepted values
				$sub_cat_depth = $input['sub-cat-depth'];

				if(is_numeric($sub_cat_depth) ){
					$sub_cat_depth = intval($sub_cat_depth);
					if ($sub_cat_depth >= 0 && $sub_cat_depth <= 2 ){
						$output['sub-cat-depth'] = $sub_cat_depth;
					}
				}     
			} 

			//boolean validate display-sub-cat-articles
			if( isset($input['display-sub-cat-articles']) ) {
				$output['display-sub-cat-articles'] = true;
			} else {
				$output['display-sub-cat-articles'] = false;
			}

			//boolean validate hide-empty-cats
			if( isset($input['hide-empty-cats']) ) {
				$output['hide-empty-cats'] = true;
			} else {
				$output['hide-empty-cats'] = false;
			}

			//boolean validate hide-uncat-articles
			if( isset($input['hide-uncat-articles']) ) {
				$output['hide-uncat-articles'] = true;
			} else {
				$output['hide-uncat-articles'] = false;
			}

			//boolean validate enable-article-comments
			if( isset($input['enable-article-comments']) ) {
				$output['enable-article-comments'] = true;
			} else {
				$output['enable-article-comments'] = false;
			}

			//boolean validate display-article-usefulness
			if( isset($input['display-article-usefulness']) ) {
				$output['display-article-usefulness'] = true;
			} else {
				$output['display-article-usefulness'] = false;
			}

			//boolean validate display-article-views-count
			if( isset($input['display-article-views-count']) ) {
				$output['display-article-views-count'] = true;
			} else {
				$output['display-article-views-count'] = false;
			}

			//boolean validate display-article-comment-count
			if( isset($input['display-article-comment-count']) ) {
				$output['display-article-comment-count'] = true;
			} else {
				$output['display-article-comment-count'] = false;
			}

			//boolean validate display-article-author
			if( isset($input['display-article-author']) ) {
				$output['display-article-author'] = true;
			} else {
				$output['display-article-author'] = false;
			}

			//boolean validate display-related-articles
			if( isset($input['display-related-articles']) ) {
				$output['display-related-articles'] = true;
			} else {
				$output['display-related-articles'] = false;
			}

			//boolean validate display-live-search
			if( isset($input['display-live-search']) ) {
				$output['display-live-search'] = true;
			} else {
				$output['display-live-search'] = false;
			}

			//boolean validate focus-live-search
			if( isset($input['focus-live-search']) ) {
				$output['focus-live-search'] = true;
			} else {
				$output['focus-live-search'] = false;
			}

			//search placeholder
			if( isset($input['search-placeholder-text']) ) {
				$output['search-placeholder-text'] = esc_attr($input['search-placeholder-text']);
			} else {
				$output['search-placeholder-text'] = '';
			}

			//boolean validate display-search-result-excerpt
			if( isset($input['display-search-result-excerpt']) ) {
				$output['display-search-result-excerpt'] = true;
			} else {
				$output['display-search-result-excerpt'] = false;
			}

			//boolean validate highlight-search-result-excerpt
			if( isset($input['highlight-search-result-excerpt']) ) {
				$output['highlight-search-result-excerpt'] = true;
			} else {
				$output['highlight-search-result-excerpt'] = false;
			}

			//kb-article-slug
			if( isset($input['kb-article-slug']) ) {
				$slug = $this->validate_slug($input['kb-article-slug'], $this->ht_kb_settings['kb-article-slug'], 'kb-article-slug');
				$output['kb-article-slug'] =  $slug;
			}

			//kb-category-slug
			if( isset($input['kb-category-slug']) ) {
				$slug = $this->validate_slug($input['kb-category-slug'], $this->ht_kb_settings['kb-category-slug'], 'kb-category-slug');
				$output['kb-category-slug'] =  $slug;
			}

			//kb-tag-slug
			if( isset($input['kb-tag-slug']) ) {
				$slug = $this->validate_slug($input['kb-tag-slug'], $this->ht_kb_settings['kb-tag-slug'], 'kb-tag-slug');
				$output['kb-tag-slug'] =  $slug;
			}

			if( isset($input['archive-sidebar-position']) ){
				$archive_sidebar_position = $input['archive-sidebar-position'];
				if( in_array( $archive_sidebar_position, ['left', 'right', 'off'] ) ){
					$output['archive-sidebar-position'] =  $archive_sidebar_position;
				}
			}

			if( isset($input['category-sidebar-position']) ){
				$category_sidebar_position = $input['category-sidebar-position'];
				if( in_array( $category_sidebar_position, ['left', 'right', 'off'] ) ){
					$output['category-sidebar-position'] =  $category_sidebar_position;
				}
			}

			if( isset($input['article-sidebar-position']) ){
				$article_sidebar_position = $input['article-sidebar-position'];
				if( in_array( $article_sidebar_position, ['left', 'right', 'off'] ) ){
					$output['article-sidebar-position'] =  $article_sidebar_position;
				}
			}

			//boolean validate article-sticky-sidebar
			if( isset($input['article-sticky-sidebar']) ) {
				$output['article-sticky-sidebar'] = true;
			} else {
				$output['article-sticky-sidebar'] = false;
			}

			//no validation required on custom-kb-styling-content?
			if( isset($input['custom-kb-styling-content']) ) {
				$output['custom-kb-styling-content'] =  $input['custom-kb-styling-content'];
			}


			//boolean validate enable-kb-styling-sitewide
			if( isset($input['enable-kb-styling-sitewide']) ) {
				$output['enable-kb-styling-sitewide'] = true;
			} else {
				$output['enable-kb-styling-sitewide'] = false;
			}

			//validate kb-width
			if( isset($input['kb-width']) ) {
				$kb_width = (int) $input['kb-width'];
				if ( $kb_width >= 700 && $kb_width <= 1800 ){
					$output['kb-width'] = $kb_width;	
				}
			}

			//validate kb-linkcolor
			if( isset($input['kb-linkcolor']) ) {
				$kb_linkcolor = sanitize_hex_color($input['kb-linkcolor']);
				if ( !empty( $kb_linkcolor ) ){
					$output['kb-linkcolor'] = $kb_linkcolor;	
				}
			}

			//validate kb-linkcolorhover
			if( isset($input['kb-linkcolorhover']) ) {
				$kb_linkcolorhover = sanitize_hex_color($input['kb-linkcolorhover']);
				if ( !empty( $kb_linkcolorhover )){
					$output['kb-linkcolorhover'] = $kb_linkcolorhover;	
				}
			}

			if( isset($input['kb-headerstyle']) ){
				$kb_headerstyle = $input['kb-headerstyle'];
				if( in_array( $kb_headerstyle, ['solid', 'gradient', 'image'] ) ){
					$output['kb-headerstyle'] =  $kb_headerstyle;
				}
			}

			if( isset($input['kb-headerstyle-bg']) ) {
				$kb_headerstyle_bg = sanitize_hex_color($input['kb-headerstyle-bg']);
				if ( !empty( $kb_headerstyle_bg )){
					$output['kb-headerstyle-bg'] = $kb_headerstyle_bg;	
				}
			}

			if( isset($input['kb-headerstyle-graddir']) ) {
				$kb_headerstyle_graddir = (int) $input['kb-headerstyle-graddir'];
				if ( $kb_headerstyle_graddir >= 0 && $kb_headerstyle_graddir <= 360 ){
					$output['kb-headerstyle-graddir'] = $kb_headerstyle_graddir;	
				}
			}

			if( isset($input['kb-headerstyle-grad1']) ) {
				$kb_headerstyle_grad1 = sanitize_hex_color($input['kb-headerstyle-grad1']);
				if ( !empty( $kb_headerstyle_grad1 )){
					$output['kb-headerstyle-grad1'] = $kb_headerstyle_grad1;	
				}
			}

			if( isset($input['kb-headerstyle-grad2']) ) {
				$kb_headerstyle_grad2 = sanitize_hex_color($input['kb-headerstyle-grad2']);
				if ( !empty( $kb_headerstyle_grad2 )){
					$output['kb-headerstyle-grad2'] = $kb_headerstyle_grad2;	
				}
			}

			if( isset($input['kb-headerstyle-img-attachment-id']) ) {
				$kb_headerstyle_img_attachment_id = intval($input['kb-headerstyle-img-attachment-id']);
				if ( is_int( $kb_headerstyle_img_attachment_id ) ){
					$output['kb-headerstyle-img-attachment-id'] = $kb_headerstyle_img_attachment_id;	
				}
			}

			//validate kb-headercolor
			if( isset($input['kb-headercolor']) ) {
				$kb_headercolor = sanitize_hex_color($input['kb-headercolor']);
				if ( !empty( $kb_headercolor )){
					$output['kb-headercolor'] = $kb_headercolor;	
				}
			}

			//boolean validate enable-article-feedback
			if( isset($input['enable-article-feedback']) ) {
				$output['enable-article-feedback'] = true;
			} else {
				$output['enable-article-feedback'] = false;
			}

			//boolean validate enable-anon-article-feedback
			if( isset($input['enable-anon-article-feedback']) ) {
				$output['enable-anon-article-feedback'] = true;
			} else {
				$output['enable-anon-article-feedback'] = false;
			}

			//boolean validate enable-upvote-article-feedback
			if( isset($input['enable-upvote-article-feedback']) ) {
				$output['enable-upvote-article-feedback'] = true;
			} else {
				$output['enable-upvote-article-feedback'] = false;
			}

			//boolean validate enable-downvote-article-feedback
			if( isset($input['enable-downvote-article-feedback']) ) {
				$output['enable-downvote-article-feedback'] = true;
			} else {
				$output['enable-downvote-article-feedback'] = false;
			}

			//kb-transfer-url
			if( isset($input['kb-transfer-url']) ) {
				$output['kb-transfer-url'] = esc_attr($input['kb-transfer-url']);
			} else {
				$output['kb-transfer-url'] = '';
			}

			//boolean validate kb-transfer-new-window
			if( isset($input['kb-transfer-new-window']) ) {
				$output['kb-transfer-new-window'] = true;
			} else {
				$output['kb-transfer-new-window'] = false;
			}

			//kb-license-key
			if( isset($input['kb-license-key']) ) {
				$output['kb-license-key'] = esc_attr($input['kb-license-key']);
				$this->validate_license(esc_attr($input['kb-license-key']), $this->ht_kb_settings['kb-license-key']);
			} else {
				$output['kb-license-key'] = '';
				$this->validate_license('', $this->ht_kb_settings['kb-license-key']);
			}

			//activetab
			if( isset($input['activetab']) ) {
				$output['activetab'] = esc_attr($input['activetab']);
			} else {
				$output['activetab'] = '';
			}

			return apply_filters( 'ht_knowledge_base_settings_validate', $output, $input );
		}


		function validate_slug($slug, $existing_value, $field) {

			$error = false;

			//replace spaces in slugs
			$slug = preg_replace('/\s+/', '', $slug);

			//slug cant be less than 2 characters
			if( strlen($slug) < 2 ){
				$error = true;
			}

			//slug should not be reserved term
			if($this->is_reserved_term($slug)){
				$error = true;
			}

			//slug can't end with a /
			if ( strlen($slug) > 1 && substr($slug, -1) == '/' ){
				$slug = substr($slug, 0, -1);
				//recursive call
				return $this->validate_slug($slug, $existing_value, $field);
			}

			//slug can't start with a /
			if ( strlen($slug) > 1 && substr($slug, 0, 1) == '/' ){
				$slug = substr($slug, 1);
				//recursive call
				return $this->validate_slug($slug, $existing_value, $field);
			}

			if ( $error ) {
				$slug = $existing_value;
			} else {
				//if no error and value has changed, flag to flush rewrite rules
				if( $slug!=$existing_value )
					update_option('ht_kb_flush_rewrite_required', true);
			}

			return $slug;
		}

		function is_reserved_term($slug){
			$this->populate_reserved_terms();
			if ( in_array( $slug, $this->reserved_terms )){
				return true;
			} else {
				return false;
			}
		}

		function validate_license($new_license_key, $current_license_key) {
			if ($current_license_key != $new_license_key ){
				if( isset($current_license_key) && $current_license_key!='' ){
					//deactivate old license
					HT_Knowledge_Base_Updater::deactivate_license($current_license_key);
				}
				if( isset($new_license_key) && $new_license_key!='' ){
					//activate new license
					HT_Knowledge_Base_Updater::activate_license($new_license_key);
				}
			} else {
				//else just check license
				HT_Knowledge_Base_Updater::check_license($new_license_key);
			}

			if ( ($current_license_key != $new_license_key ) && ( ! isset( $new_license_key ) || empty( $new_license_key ) ) ) {
				//empty license can remove key if deactivated and empty
				HT_Knowledge_Base_Updater::deactivate_license($current_license_key);
				delete_option( 'ht_kb_license_key' );
				delete_option( 'ht_kb_license_status' );
			} else {
				//other checks?
			}
			//always returns true
			return true;
		} 

		function theme_managed_updates(){
			if( ( current_theme_supports('ht_kb_theme_managed_updates') || current_theme_supports('ht-kb-theme-managed-updates') ) ){
				return true;
			} else {
				return false;
			}
		}

		function populate_reserved_terms(){
			//populate reserved terms
			$this->reserved_terms = array(   'attachment',
											 'attachment_id',
											 'author',
											 'author_name',
											 'calendar',
											 'cat',
											 'category',
											 'category__and',
											 'category__in',
											 'category__not_in',
											 'category_name',
											 'comments_per_page',
											 'comments_popup',
											 'cpage',
											 'day',
											 'debug',
											 'error',
											 'exact',
											 'feed',
											 'hour',
											 'link_category',
											 'm',
											 'minute',
											 'monthnum',
											 'more',
											 'name',
											 'nav_menu',
											 'nopaging',
											 'offset',
											 'order',
											 'orderby',
											 'p',
											 'page',
											 'page_id',
											 'paged',
											 'pagename',
											 'pb',
											 'perm',
											 'post',
											 'post__in',
											 'post__not_in',
											 'post_format',
											 'post_mime_type',
											 'post_status',
											 'post_tag',
											 'post_type',
											 'posts',
											 'posts_per_archive_page',
											 'posts_per_page',
											 'preview',
											 'robots',
											 's',
											 'search',
											 'second',
											 'sentence',
											 'showposts',
											 'static',
											 'subpost',
											 'subpost_id',
											 'tag',
											 'tag__and',
											 'tag__in',
											 'tag__not_in',
											 'tag_id',
											 'tag_slug__and',
											 'tag_slug__in',
											 'taxonomy',
											 'tb',
											 'term',
											 'type',
											 'w',
											 'withcomments',
											 'withoutcomments',
											 'year',
										  );

			//apply ht_kb_slugs_check_reserved_terms filters
			$this->reserved_terms = apply_filters( 'ht_kb_slugs_check_reserved_terms', $this->reserved_terms );
		}        

	}//end class

}

if (class_exists('HT_Knowledge_Base_Settings')) {
	new HT_Knowledge_Base_Settings();
}