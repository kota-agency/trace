<?php
/**
*	Plugin Name: Heroic Knowledge Base
*	Plugin URI:  https://herothemes.com
*	Description: Knowledge Base plugin for WordPress 
*	Author: HeroThemes
*	Version: 3.3.0
*   Build: 3073
*   Build Date: 2021-11-25 1:22:15PM
*	Author URI: https://herothemes.com/
*	Text Domain: ht-knowledge-base
*	Domain Path: /languages
*/


//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'HT_Knowledge_Base' ) ){

	//controls the global testing/debug mode
	if(!defined('HKB_TESTING_MODE')){
		define('HKB_TESTING_MODE', false);
	}

	//enable to use unminfied scripts
	if(!defined('HKB_DEBUG_SCRIPTS')){
		define('HKB_DEBUG_SCRIPTS', false);
	}

	if(!defined('HT_KB_VERSION_NUMBER')){
		define('HT_KB_VERSION_NUMBER', '3.3.0');
	}

	if ( ! defined( 'HT_KB_BUILD_NUMBER' ) ) {
		define( 'HT_KB_BUILD_NUMBER', 3073 );
	}

	if ( ! defined( 'HT_KB_DISTRIBUTION' ) ) {
		//standalone or packaged
		define( 'HT_KB_DISTRIBUTION', 'packaged' );
	}

	if(!defined('HT_USEFULNESS_KEY')){
		define('HT_USEFULNESS_KEY', '_ht_kb_usefulness');
	}

	//knowledge base cpt slug
	if(!defined('KB_CPT_SLUG')){
		define('KB_CPT_SLUG', 'knowledge-base');
	}

	//knowledge base category slug
	if(!defined('KB_CAT_SLUG')){
		define('KB_CAT_SLUG', 'article-categories');
	}

	//knowlege base tag slug
	if(!defined('KB_TAG_SLUG')){
		define('KB_TAG_SLUG', 'article-tags');
	}

	//define this as the main plugin file - required for updater and elsewhere
	if(!defined('HT_KB_MAIN_PLUGIN_FILE')){
		define('HT_KB_MAIN_PLUGIN_FILE', __FILE__);
	}

	//documentation/support url
	if(!defined('HT_KB_SUPPORT_URL')){
		define('HT_KB_SUPPORT_URL', 'https://herothemes.com/hkbdocs/');
	}

	//HT Account URL
	if(!defined('HT_STORE_ACCOUNT_URL')){
		define('HT_STORE_ACCOUNT_URL', 'https://herothemes.com/your-account/');
	}

	//HT HKB Integration Guide URL
	if(!defined('HT_HKB_INTEGRATION_GUIDE_URL')){
		define('HT_HKB_INTEGRATION_GUIDE_URL', 'https://herothemes.com/support/knowledge-base/integrating-heroic-knowledge-base-with-your-theme/');
	}

	//HT Search in WordPress
	if(!defined('HT_SEARCH_IN_WORDPRESS_GUIDE_URL')){
		define('HT_SEARCH_IN_WORDPRESS_GUIDE_URL', 'https://herothemes.com/support/knowledge-base/search-in-wordpress/');
	}

	class HT_Knowledge_Base {

		private $temp_query;
		public $is_single, $is_singular_ht_kb, $is_ht_kb_category_tax, $is_ht_kb_tag_tax, $is_ht_kb_archive, $is_ht_kb_search, $ht_kb_is_ht_kb_front_page, $nothing_found, $taxonomy, $term, $theme_template_in_use, $custom_content_compat, $orig_post, $hkb_category, $hkb_master_tax_terms, $hkb_current_term_id, $hkb_current_article_id, $ht_calling_depth, $theme_compat_css;


		//Constructor
		function __construct(){

			//uncomment or use filter in theme functions to enable debug mode
			//add_filter('hkb_debug_mode', '__return_true');
			
			//register the ht_kb custom post type
			add_action( 'init', array( $this,  'register_ht_knowledge_base_cpt' ) );
			//register the ht_kb_category taxonomy
			add_action( 'init', array( $this,  'register_ht_knowledge_base_category_taxonomy' ) );
			//register the ht_kb_tag taxonomy
			add_action( 'init', array( $this,  'register_ht_knowledge_base_tag_taxonomy' ) );
			//maybe flush rewrite rules, lower priority
			add_action( 'init', array( $this,  'ht_knowledge_base_maybe_flush_rewrite' ), 30 );
			//load plugin textdomain
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
			//check hkb database version
			add_action( 'admin_notices', array( $this, 'database_version_messages' ) );
			//display notice if ht voting not installed
			add_action( 'admin_notices', array( $this,  'ht_kb_voting_warning' ), 10 );
			//display notice if analytics module is separate plugin
			add_action( 'admin_notices', array( $this,  'ht_kb_analytics_plugin_warning' ), 10 );
			//display notice if permalinks settings incorrect (no longer required)
			//add_action( 'admin_notices', array( $this,  'ht_kb_permalinks_warning' ), 10 );
			//display notice if site urls settings incorrect
			add_action( 'admin_notices', array( $this,  'ht_kb_site_urls_warning' ), 10 );
			//display notice packaged plugin not used with theme
			add_action( 'admin_notices', array( $this,  'ht_kb_packaged_no_theme_managed_updates' ), 10 );
			//voting warning
			add_action( 'admin_init', array( $this,  'ht_kb_voting_warning_ignore' ), 10 );
			//voting old version check
			add_action( 'admin_init', array( $this,  'ht_kb_voting_old_version_check' ), 10 );
			//database old version check
			add_action( 'admin_init', array( $this,  'ht_kb_database_old_version_check' ), 10 );
			//new plugin activation check
			add_action( 'admin_init', array( $this,  'ht_kb_activation_check' ), 10 );
			//display notice for no category
			add_action( 'admin_notices', array( $this,  'ht_kb_no_category_warning' ), 20 );
			//display notice for no articles
			add_action( 'admin_notices', array( $this,  'ht_kb_no_articles_warning' ), 20 );
			//display notice for no articles
			add_action( 'admin_notices', array( $this,  'ht_kb_no_default_homepage' ), 20 );
			//display notice if debug options on
			add_action( 'admin_notices', array( $this, 'ht_kb_debug_options_enabled' ), 20 );
			//save post
			add_action( 'save_post_ht_kb', array( $this, 'save_ht_kb_article'), 10, 3 );
			//enqueue scripts and styles
			add_action( 'wp_enqueue_scripts', array( $this, 'ht_kb_enqueue_styles' ), 999 );
			//add activation action for upgrades
			add_action( 'ht_kb_activate', array( $this, 'ht_kb_plugin_activation_upgrade_actions' ), 10, 1 );
			//custom admin menu order
			//@deprecated - now uses order on add_submenu_page
			//add_filter( 'custom_menu_order' , '__return_true');
			//add_filter( 'menu_order', array( $this, 'ht_kb_custom_admin_menu_order' ) );	

			
			//filter the templates - note this will be overriden if theme uses single-ht_kb or archive-ht_kb
			//@removed since 3.0
			//add_filter( 'template_include', array( $this, 'ht_knowledge_base_custom_template' ) );
			

			//filter for body classes on ht_kb
			add_filter( 'body_class', array( $this, 'ht_knowledge_base_custom_body_classes' ) );
			
			//filter for the title ht_kb (inside the loop)
			//add_filter( 'the_title', array( $this, 'ht_knowledge_base_custom_title' ), 10, 2 );

			//filter content for ht_kb
			//@removed since 3.0
			//add_filter( 'the_content', array( $this, 'ht_knowledge_base_custom_content' ) );

			//compat for themes such as hello elementor
			//@removed since 3.0
			//add_filter( 'the_excerpt', array( $this, 'ht_knowledge_base_custom_content_excerpt' ) );

			//search filter
			add_filter( 'pre_get_posts', array( $this, 'ht_kb_pre_get_posts_filter' ), 10 );

			//knowledge base preview
			add_filter( 'pre_get_posts', array( $this, 'ht_kb_modify_kb_preview_pre_get_posts' ), 20 );

			//sort order
			add_filter( 'pre_get_posts', array( $this, 'ht_kb_modify_sort_order_pre_get_posts' ), 50 );

			//number of posts in taxonomy
			add_filter( 'pre_get_posts', array( $this, 'ht_kb_posts_per_taxonomy' ), 50 );

			//remove dummy post from page editor list
			//@removed since 3.0
			//add_filter( 'pre_get_posts', array( $this, 'ht_kb_remove_kb_dummy_post_from_edit_screen' ), 10 );

			//comments open filter
			add_filter( 'comments_open', array( $this, 'ht_kb_comments_open' ), 10, 2 );

			//comments template filter
			add_filter( 'comments_template', array( $this, 'ht_kb_comments_template' ), 10 );

			//add to menu items
			//@deprecated since 3.0 - now use archive page
			//add_action( 'admin_head-nav-menus.php', array( $this, 'ht_knowledge_base_menu_metabox' ) );
			//add_filter( 'wp_get_nav_menu_items', array( $this,'ht_knowledge_base_archive_menu_filter'), 10, 3 );

			//add filter for ht_kb archive title
			//@deprecated in 3.1.1
			//add_filter( 'wp_title', array( $this, 'ht_kb_wp_title_filter' ), 10, 3 );
			//add filter for ht_kb archive title (2016+)
			//@deprecated in 3.1.1
			//add_filter( 'pre_get_document_title', array( $this, 'ht_kb_wp_title_filter' ), 10, 3 );
			//deprecated since 3.0
			//add filter for ht_kb archive title (WordPress SEO shiv), no longer needed due to ht_kb_wp_seo_fix
			//add_filter( 'wpseo_title', array( $this, 'ht_kb_wp_title_filter' ), 10, 3 );
			//wpseo_opengraph_title, no longer needed due to ht_kb_wp_seo_fix
			//add_filter( 'wpseo_opengraph_title', array( $this, 'ht_kb_wp_title_filter' ), 10, 1 );
			//custom title filter
			//@deprecated in 3.1.1
			//add_filter( 'ht_kb_wp_title', array( $this, 'ht_kb_wp_title_dummy' ), 10, 2 );

			//wpseo fix
			//deprecated since 3.0
			//add_action( 'wpseo_head', array( $this, 'ht_kb_wp_seo_fix' ), 10 );			

			//filter for plugin action links		
			add_filter( 'plugin_action_links', array( $this, 'ht_kb_plugin_row_action_links' ), 10, 2 );
			//filter for plugin meta links		
			add_filter( 'plugin_row_meta', array( $this, 'ht_kb_plugin_row_meta_links' ), 10, 2 );

			//custom front page
			//@removed since 3.0
			//add_action( 'pre_get_posts', array( $this, 'ht_knowledge_base_custom_front_page' ), 10 );

			//set posts views and article helpfulness to 0
			add_action( 'publish_ht_kb', array( $this, 'ht_kb_article_publish' ), 10, 2 );

			//set custom order when object terms (ht_kb_categories) change
			add_action( 'set_object_terms', array( $this, 'ht_kb_set_object_terms' ), 10, 4 );

			//add custom css
			add_action( 'wp_head', array( $this, 'ht_kb_custom_css_head' ), 10, 0 );

			//add generator tag
			add_action( 'wp_head', array( $this, 'ht_kb_generator_head' ), 10, 0 );

			//add custom image size
			add_image_size( 'ht-kb-thumb', 50, 50 );

			//get_pages	filter
			//@deprecated, no longer required
			//add_filter( 'get_pages', array( $this, 'ht_kb_filter_get_pages' ));

			//remove the jetpack open graph functionality as it breaks this plugin
			add_filter( 'jetpack_enable_open_graph', '__return_false' );

			//filter post states
			add_filter( 'display_post_states', array( $this, 'ht_kb_display_post_states' ), 10, 2 );

			//filter post type archive link
			//@deprecated - no archive in 3.0
			//add_filter( 'post_type_archive_link', array( $this, 'ht_kb_post_type_archive_link' ), 10, 2 );

			//Core template filters used by 3.0
			//filter for page template
			add_filter( 'page_template', array( $this, 'ht_kb_page_template' ), 10, 3 );
			//filter for taxonomy template
			add_filter( 'taxonomy_template', array( $this, 'ht_kb_taxonomy_template' ), 10, 3 );
			//filter for single template
			add_filter( 'single_template', array( $this, 'ht_kb_single_template' ), 10, 3 );
			//search results template
			add_filter( 'search_template', array( $this, 'ht_kb_search_template' ), 10, 3 );

			//categories widget
			include_once('widgets/widget-kb-categories.php');
			//articles widget
			include_once('widgets/widget-kb-articles.php');
			//authors widget
			include_once('widgets/widget-kb-authors.php');
			//search widget
			include_once('widgets/widget-kb-search.php');
			//toc widget
			include_once('widgets/widget-kb-toc.php');
			//helper functions
			include_once('php/ht-knowledge-base-article-helpers.php');
			include_once('php/ht-knowledge-base-settings-helpers.php');
			include_once('php/ht-knowledge-base-template-helpers.php');
			//meta-boxes
			include_once('php/ht-knowledge-base-meta-boxes.php');
			//category ordering
			include_once('php/ht-knowledge-base-category-ordering.php');
			//article ordering
			include_once('php/ht-knowledge-base-article-ordering.php');
			//category meta
			include_once('php/ht-knowledge-base-category-meta.php');
			//live search
			include_once('php/ht-knowledge-base-live-search.php');
			//welcome page
			include_once('php/ht-knowledge-base-welcome.php');
			//sample installer
			include_once('php/ht-knowledge-base-sample-installer.php');
			//updater
			include_once('php/ht-knowledge-base-updater.php');

			//options new
			require_once('php/ht-knowledge-base-settings.php');
			require_once('php/ht-knowledge-base-settings-upgrade.php');
			//edit columns
			require_once('php/ht-knowledge-base-edit-columns.php');
			//ht-voting
			include_once('voting/ht-voting.php');
			//ht-analytics-core
			include_once('php/ht-knowledge-base-analytics-core.php');
			//ht-data-tools
			include_once('php/ht-knowledge-base-data-tools.php');
			//view count
			include_once('php/ht-knowledge-base-view-count.php');
			//utility functions
			include_once('php/ht-knowledge-base-utility-functions.php');
			//exits module
			include_once('exits/ht-kb-exits.php');
			//analytics module
			include_once('analytics/ht-knowledge-base-analytics.php');
			//restrict access module
			include_once('php/ht-knowledge-base-restrict-access.php');       
			//debug info
			require_once('php/ht-knowledge-base-debug-info.php');
			//site health (debug+)
			require_once('php/ht-knowledge-base-site-health.php');

			//search extensions
			include_once('php/ht-knowledge-base-search-extensions.php');

			//icons
			include_once('php/ht-knowledge-base-icons.php');

			//stubs
			include_once('php/ht-knowledge-base-stubs.php');

			//blocks loader (will be implemented in a later version)
			//include_once('blocks/ht-kb-blocks-loader.php');

			//api
			include_once('php/ht-knowledge-base-api.php');

			//embed
			include_once('embed/ht-kb-fe-embed.php');

			//sidebars
			include_once('php/ht-knowledge-base-sidebars.php');

			//wpml compat
			include_once('php/ht-knowledge-base-wpml-compat.php');

			//welcome setup
			include_once('setup/ht-kb-welcome-setup.php');

			//dummy data modules
			//view count dummy data, for testing functionality
			if( apply_filters( 'hkb_debug_mode', false ) ){
				include_once('php/ht-knowledge-base-views-dummy-data-creator.php');
			}

			//@todo - review effectiveness of search without date functionality
			add_filter('hkb_search_without_date', '__return_false');

			//activation hook
			register_activation_hook( __FILE__, array( 'HT_Knowledge_Base', 'ht_knowlege_base_plugin_activation_hook' ) );

			//deactivation hook
			register_deactivation_hook( __FILE__, array( 'HT_Knowledge_Base', 'ht_knowlege_base_plugin_deactivation_hook' ) );	
		}


		/**
		* Initial activation to add option flush the rewrite rules
		*/
		static function ht_knowlege_base_plugin_activation_hook( $network_wide = null ){
			//flush the rewrite rules
			add_option('ht_kb_flush_rewrite_required', true);

			//perform upgrade actions, moved to a non-static option check
			//new activation method
			add_option( 'ht_kb_activate', true );

			//set network activation status
			add_option( 'ht_kb_network_activate', $network_wide );

			//set installation transient, used for welcome page
			set_transient('_ht_kb_just_installed', true);

			//add term_order to terms table
			HT_Knowledge_Base::knowledgebase_customtaxorder_activate($network_wide);
		}

		/**
		* Initial activation to add option flush the rewrite rules
		*/
		static function ht_knowlege_base_plugin_deactivation_hook(){
			//remove flush the rewrite rules option
			delete_option('ht_kb_flush_rewrite_required');
			//remove activation option
			delete_option('ht_kb_activate');
		}

		/**
		* Register the ht_kb custom post type
		*/
		function register_ht_knowledge_base_cpt(){

			if(apply_filters('ht_kb_disable_ht_kb_cpt', false)){
				return;
			}

			$singular_item = _x('Article', 'Post Type Singular Name', 'ht-knowledge-base');
			$plural_item = _x('Articles', 'Post Type Plural Name', 'ht-knowledge-base');
			$kb_item = __('Heroic KB', 'ht-knowledge-base');
			$rewrite = $this->get_cpt_slug();
			$show_in_rest = apply_filters('ht_kb_show_in_rest', true);
			$rest_base = apply_filters('ht_kb_rest_base', 'ht-kb');
			//@since 3.0 - archive is false 
			$has_archive = apply_filters('ht_kb_cpt_has_archive', false);

			$labels = array(
				'name'	      		 => $plural_item,
				'singular_name'      => $singular_item,
				'add_new'            => __('Add New', 'ht-knowledge-base') . ' ' .  $singular_item,
				'add_new_item'       => __('Add New', 'ht-knowledge-base') . ' ' .  $singular_item,
				'edit_item'          => __('Edit', 'ht-knowledge-base') . ' ' .  $singular_item,
				'new_item'           => __('New', 'ht-knowledge-base') . ' ' .  $singular_item,
				'all_items'          => __('All', 'ht-knowledge-base') . ' ' .  $plural_item,
				'view_item'          => __('View', 'ht-knowledge-base') . ' ' .  $singular_item,
				'search_items'       => __('Search', 'ht-knowledge-base') . ' ' .  $plural_item,
				'not_found'          => sprintf( __( 'No %s found', 'ht-knowledge-base' ), $plural_item ),
				'not_found_in_trash' => sprintf( __( 'No %s found in trash', 'ht-knowledge-base' ), $plural_item ),
				'parent_item_colon'  => '',
				'menu_name'          => $kb_item,
			);

			$args = array(
				'labels'             => $labels,
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'show_in_nav_menus'	 => true,
				'query_var'          => true,
				'rewrite'            => array( 'slug' => $rewrite, 'with_front'	=>	false ),
				'capability_type'    => 'post',
				'has_archive'        => $has_archive,
				'hierarchical'       => false,
				'show_in_rest'       => $show_in_rest,
				'rest_base'          => $rest_base,
				'menu_icon'					 => 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" x="0" y="0" viewBox="0 0 142.42 140.74"><style>.st0{fill:#1d2327};.st1{fill:#fff}</style><path class="st0" d="M116.9 141.73H26.2c-14.09 0-25.51-11.42-25.51-25.51V25.51C.68 11.42 12.11 0 26.2 0h90.71c14.09 0 25.51 11.42 25.51 25.51v90.71c0 14.09-11.43 25.51-25.52 25.51z" fill="#5b6ebd"/><path class="st1" d="M47.91 43.41c-1.18 0-2.14.96-2.14 2.14 0 1.18.96 2.14 2.14 2.14h48.58c1.18 0 2.14-.96 2.14-2.14 0-1.18-.96-2.14-2.14-2.14H47.91z"/><path class="st1" d="M109.02 81.04l-7.63-24.72c-.01-.02-.02-.03-.03-.05a2.1 2.1 0 0 0-.27-.54.265.265 0 0 0-.05-.06c-.11-.15-.24-.28-.39-.39-.03-.02-.05-.04-.07-.06-.16-.11-.33-.2-.52-.27a.564.564 0 0 1-.12-.04c-.19-.06-.4-.1-.61-.1h-51.9c-5.1 0-9.25-4.15-9.25-9.25s4.15-9.25 9.25-9.25h51.9c1.18 0 2.14-.96 2.14-2.14s-.96-2.14-2.14-2.14h-51.9c-7.46 0-13.53 6.07-13.53 13.53v47.75c0 9.05 7.36 16.4 16.4 16.4h44.16c2.02 0 3.66-1.64 3.66-3.66V85.78h7.39c1.16 0 2.26-.56 2.95-1.49.7-.93.91-2.14.56-3.25z"/></svg>'),
				'menu_position'      => null,
				'supports'           => apply_filters( 'ht_kb_cpt_supports', array( 'title', 'editor', 'author', 'comments', 'post-formats', 'custom-fields', 'revisions', 'publicize', 'wpcom-markdown', 'excerpt' ) )
			);

		  register_post_type( 'ht_kb', $args );
		}

		/**
		* Get the slug for the custom post type
		* @return (String) The CPT slug
		*/
		function get_cpt_slug(){
			$default = KB_CPT_SLUG;
			$user_option = hkb_kb_article_slug();
			$slug = ( empty( $user_option ) ) ? $default : $user_option;
			//apply filters
			$slug = apply_filters('ht_kb_cpt_slug', $slug);
			return $slug;
		}

		/**
		* Register ht_kb_category taxonomy
		*/
		function register_ht_knowledge_base_category_taxonomy(){

			if(apply_filters('ht_kb_disable_ht_kb_category', false)){
				return;
			}

			$singular_item = __('Knowledge Base', 'ht-knowledge-base');
			$rewrite = $this->get_cat_slug();
			$show_in_rest = apply_filters('ht_kb_category_show_in_rest', true);
			$rest_base = apply_filters('ht_kb_category_rest_base', 'ht-kb-category');

			$labels = array(
				'name'                       => _x( 'Article Categories', 'Taxonomy General Name', 'ht-knowledge-base' ),
				'singular_name'              => _x( 'Article Category', 'Taxonomy Singular Name', 'ht-knowledge-base' ),
				'menu_name'                  => __( 'Article Categories', 'ht-knowledge-base' ),
				'all_items'                  => __( 'All Article Categories', 'ht-knowledge-base' ),
				'parent_item'                => __( 'Parent Article Category', 'ht-knowledge-base' ),
				'parent_item_colon'          => __( 'Parent Article Category:', 'ht-knowledge-base' ),
				'new_item_name'              => __( 'New Article Category', 'ht-knowledge-base' ),
				'add_new_item'               => __( 'Add New Article Category', 'ht-knowledge-base' ),
				'edit_item'                  => __( 'Edit Article Category', 'ht-knowledge-base' ),
				'update_item'                => __( 'Update Article Category', 'ht-knowledge-base' ),
				'separate_items_with_commas' => __( 'Separate Article Categories with commas', 'ht-knowledge-base' ),
				'search_items'               => __( 'Search Article Categories', 'ht-knowledge-base' ),
				'add_or_remove_items'        => __( 'Add or remove categories', 'ht-knowledge-base' ),
				'choose_from_most_used'      => __( 'Choose from the most used categories', 'ht-knowledge-base' ),
			);
			$args = array(
				'labels'                     => $labels,
				'hierarchical'               => true,
				'rewrite'            		 => array( 'slug' => $rewrite, 'with_front'	=>	false, 'hierarchical' => apply_filters( 'ht_kb_category_rewrite_hierachical', false ) ),
				'public'                     => true,
				'show_ui'                    => true,
				'show_admin_column'          => true,
				'show_in_nav_menus'          => true,
				'show_tagcloud'              => true,
				'show_in_rest'       		 => $show_in_rest,
				'rest_base'          		 => $rest_base,
			);
			register_taxonomy( 'ht_kb_category', 'ht_kb', $args );
		}

		/**
		* Get the slug for the category taxonomy
		* @return (String) The category slug
		*/
		function get_cat_slug(){
			$default = KB_CAT_SLUG;
			$user_option = hkb_kb_category_slug();
			$slug = ( empty( $user_option ) ) ? $default : $user_option;
			//apply filters
			$slug = apply_filters('ht_kb_cat_slug', $slug);
			return $slug;
		}

		/**
		* Register ht_kb_tag taxonomy
		*/
		function register_ht_knowledge_base_tag_taxonomy()  {

			if(apply_filters('ht_kb_disable_ht_kb_tag', false)){
				return;
			}

			$singular_item = __('Knowledge Base Tag', 'ht-knowledge-base');
			$rewrite = $this->get_tag_slug();
			$show_in_rest = apply_filters('ht_kb_tag_show_in_rest', true);
			$rest_base = apply_filters('ht_kb_tag_rest_base', 'ht-kb-tag');

			$labels = array(
				'name'                       => _x( 'Article Tags', 'Taxonomy General Name', 'ht-knowledge-base' ),
				'singular_name'              => _x( 'Article Tag', 'Taxonomy Singular Name', 'ht-knowledge-base' ),
				'menu_name'                  => __( 'Article Tags', 'ht-knowledge-base' ),
				'all_items'                  => __( 'All Tags', 'ht-knowledge-base' ),
				'parent_item'                => __( 'Parent Tag', 'ht-knowledge-base' ),
				'parent_item_colon'          => __( 'Parent Tag:', 'ht-knowledge-base' ),
				'new_item_name'              => __( 'New Tag Name', 'ht-knowledge-base' ),
				'add_new_item'               => __( 'Add New Tag', 'ht-knowledge-base' ),
				'edit_item'                  => __( 'Edit Tag', 'ht-knowledge-base' ),
				'update_item'                => __( 'Update Tag', 'ht-knowledge-base' ),
				'separate_items_with_commas' => __( 'Separate tags with commas', 'ht-knowledge-base' ),
				'search_items'               => __( 'Search tags', 'ht-knowledge-base' ),
				'add_or_remove_items'        => __( 'Add or remove tags', 'ht-knowledge-base' ),
				'choose_from_most_used'      => __( 'Choose from the most used tags', 'ht-knowledge-base' ),
			);

			$rewrite_arr = array(
				'slug'                       => $rewrite,
				'with_front'                 => false,
				'hierarchical'               => false,
			);

			$args = array(
				'labels'                     => $labels,
				'hierarchical'               => false,
				'public'                     => true,
				'show_ui'                    => true,
				'show_admin_column'          => true,
				'show_in_nav_menus'          => true,
				'show_tagcloud'              => true,
				'query_var'                  => 'article_tag',
				'rewrite'                    => $rewrite_arr,
				'show_in_rest'       		 => $show_in_rest,
				'rest_base'          		 => $rest_base,
			);

			register_taxonomy( 'ht_kb_tag', 'ht_kb', $args );
		}

		/**
		* Get the slug for the tag taxonomy
		* @return (String) The tag slug
		*/
		function get_tag_slug(){
			$default = KB_TAG_SLUG;
			$user_option = hkb_kb_tag_slug();
			$slug = ( empty( $user_option ) ) ? $default : $user_option;
			//apply filters
			$slug = apply_filters('ht_kb_tag_slug', $slug);
			return $slug;
		}

		/**
		* Flush rewrite rules if required
		*/
		function ht_knowledge_base_maybe_flush_rewrite(){
			// Check the option we set on activation.
			if (true == get_option('ht_kb_flush_rewrite_required')) {
				flush_rewrite_rules();
				delete_option('ht_kb_flush_rewrite_required');
			}
		}

		/**
		* Load plugin textdomain
		*/
		function load_textdomain(){
			load_plugin_textdomain('ht-knowledge-base', false, basename( dirname( __FILE__ ) ) . '/languages' );
		}


		/**
		* Displays any database version check warnings / errors
		*/
		function database_version_messages(){
			//only proceed if we are admin and can activate plugins
			if(!is_admin() || !current_user_can('activate_plugins')){
				return;
			}

			$database_check = $this->check_database_version();

			if( is_admin() && is_wp_error( $database_check ) ){
				$message = $database_check->get_error_message();
				echo '<div class="error"><p>' . $message . '</p></div>';
			}
		}

		/**
		* Check for correct database version
		*/
		function check_database_version(){
			$ht_knowledge_base_anayltics_db_version_ok = false;
			$db_version = get_option('hkb_analytics_search_atomic_db_version');
			if(FALSE===$db_version){
				//do nothing
			} else {
				$ht_knowledge_base_anayltics_db_version_ok = true;
			}
			if( ! $ht_knowledge_base_anayltics_db_version_ok && ! get_transient('_ht_kb_just_installed') ) {
				return new WP_Error( 'ht-kb-db-upgrade-required', sprintf( __( 'You need upgrade your Knowledge Base database, <a href="%s">Deactivate</a> then re-Activate the Heroic Knowledge Base plugin', 'ht-knowledge-base'), admin_url('plugins.php#heroic-knowledge-base') ) );
			}
			return false;
		}

		/**
		* Set the tax and terms
		*/
		function set_taxonomy_and_terms(){
			global $wp_query;
			$filtered_queries = @$this->filter_tax_queries( @$wp_query->tax_query->queries );
			$tmp_filtered_queries_values = array_values( $filtered_queries );
			$filtered_query = array_shift( $tmp_filtered_queries_values );
			$this->taxonomy = @$filtered_query['taxonomy'];
			$tmp_filtered_query_term_values = array_values( $filtered_query['terms'] );
			$this->term = array_shift( $tmp_filtered_query_term_values );
		}

		/**
		* Filter a tax query to remove all non ht_kb_category items
		*/
		function filter_tax_queries($query){
			return array_filter( $query, function ( $tax ) {
									return ( 'ht_kb_category' == $tax['taxonomy'] || 'ht_kb_tag' == $tax['taxonomy'] ) ? true : false;
								}
						);
		}

		/**
		* Custom content filter
		* @param (Array) $classes The current classes
		* @return (Array) Filtered classes
		*/
		function ht_knowledge_base_custom_body_classes( $classes = array() ) {
			global $post;
			if( isset( $post ) && $post->post_type == 'ht_kb' ){
				$classes[] = 'ht-kb';
			}
			//is_post_type_archive( 'ht_kb' )
			if( ht_kb_get_kb_archive_page_id() == get_queried_object_id() ){
				$classes[] = 'ht-kb';
			}
			return $classes;
		}

		/**
		* Custom content filter placeholder
		* @param (String) $title The current title
		* @param (Int) $id Post ID
		* @return (String) Filtered title
		* @deprecated No longer required for 3.1.1
		*/
		function ht_knowledge_base_custom_title( $title, $id = null ) {
			global $post, $wp_query;
			if( (isset( $post ) && $post->post_type == 'ht_kb' && !is_author()) && !$this->custom_content_compat && !$this->is_ht_kb_search){
				//return knowledge base as the title tag (entry), may also affect other areas the title appears such as widgets and feeds
				//false to return to original title
				if($this->is_ht_kb_archive && 'ht_kb' == $title || ( ht_kb_is_ht_kb_front_page() && $post->ID == $wp_query->queried_object_id && in_the_loop() ) ){
					$title = __('Knowledge Base', 'ht-knowledge-base');
					return apply_filters('ht_knowledge_base_custom_title', $title);
				} else {
					//check title not empty
					if(''==$title){
						$title = __('Untitled Article', 'ht-knowledge-base');
					}
					return apply_filters('ht_knowledge_base_custom_title', $title);
				}				
			}
			return $title;
		}

		/**
		* Custom pre get posts filter for knowledge base search and author archive
		* @param (Object) $query The WordPress query object
		* @return (Object) Filtered WordPress query object
		*/
		function ht_kb_pre_get_posts_filter( $query ) {
			global $wp_query;

			if ( ! isset( $wp_query ) ) {
				return $query;
			}

			//assign is_ht_kb_search
			$this->is_ht_kb_search = ( array_key_exists('ht-kb-search', $_REQUEST) ) ? true : false;

			//live search 
			if ( $this->is_ht_kb_search && !is_preview() && !is_singular() && !is_admin() ) {

				//configurable post types are now set with hkb_search_post_types filter
				$post_types = apply_filters( 'hkb_search_post_types', array('ht_kb') );

				$existing_post_type = (!empty($query) && isset($query->query_vars) && is_array($query->query_vars) && array_key_exists('post_type', $query->query_vars) ) ? $query->query_vars['post_type'] : null; 
				if ( empty( $existing_post_type ) ) {
					//update post_type variable
					$query->set( 'post_type' , $post_types );
					//suppress filters false for wpml compatibility
					//$query->set( 'suppress_filters' , 0 );
				}
			}
			
			//author archive
			if ( $query->is_author && !is_preview() && !is_singular() && !is_admin() ) {
			
				//configurable post types are now set with hkb_author_archive_post_types filter
				//can add more post types here, eg forum topics/replies
				$post_types = apply_filters( 'hkb_author_archive_post_types', array('ht_kb', 'post') );
				
				$existing_post_type = (!empty($query) && isset($query->query_vars) && is_array($query->query_vars) && array_key_exists('post_type', $query->query_vars) ) ? $query->query_vars['post_type'] : null; 
				if ( empty( $existing_post_type ) ) {
					//update post_type variable
					$query->set( 'post_type' , $post_types );
				}

			}

			//search elsewhere on site, not implemented in this version
			/*
			if ($query->is_search && !$this->is_ht_kb_search && hkb_kb_search_sitewide()) {
				$search_post_types = get_query_var( 'post_type', array() );
				$search_post_types[] = 'ht_kb';
				$query->set('post_type', $search_post_types);
			}
			*/

			return $query;
		}

		/**
		* Comments open filter
		* @param (boolean) $open Unfiltered comments open status
		* @return (boolean) Filtered comments open
		*/
		function ht_kb_comments_open( $open, $post_id ) {

			 $post = get_post( $post_id );
			 
			 //check if post type is knowledge base
			 if($post->post_type == 'ht_kb'){ 
				if( hkb_show_comments_display() ){
					return $open;
				} else {
					return false;
				}
			 }

			 return $open;
		}

		/**
		* Comments template filter
		* @param (String) $comment_template The comment template file
		* @return (String) Filtered comment template file
		*/
		function ht_kb_comments_template( $comment_template ) {
			 global $post;
			 if ( !( is_singular() && ( have_comments() || 'open' == $post->comment_status ) ) ) {
				return $comment_template;
			 }
			 //check if post type is knowledge base
			 if($post->post_type == 'ht_kb'){ 
				if( hkb_show_comments_display() ){
					return apply_filters('hk_kb_comments_template', $comment_template);
				} else {
					//don't return the template if closed
					return apply_filters('hk_kb_comments_template', false);
				}
			 }
			 return $comment_template;

		}

		/**
		* Admin warning message if Heroic Voting not installed
		*/
		function ht_kb_voting_warning() {
			global $current_user;
			$user_id = $current_user->ID;
			if( !class_exists('HT_Voting')  &&  current_user_can( 'install_plugins' ) && !get_user_meta($user_id, 'ht_voting_warning_ignore')  && current_theme_supports('ht-kb-theme-voting-suggested')   ):		    
			?>
				<div class="notice notice-info">
					<p><?php  printf(__('The Heroic Voting plugin is required to use voting features | <a href="%1$s">Hide Notice</a>', 'ht-knowledge-base'), '?ht_voting_warning_ignore=1'); ?></p>
				</div>
			<?php
			endif; //end class exists
		}

		/**
		* Admin warning message dismissal
		*/		
		function ht_kb_voting_warning_ignore() {
			global $current_user;
			$user_id = $current_user->ID;
			if ( isset($_GET['ht_voting_warning_ignore']) && '1' == $_GET['ht_voting_warning_ignore'] ) {
				 add_user_meta($user_id, 'ht_voting_warning_ignore', 'true', true);
			}
		}

		/**
		* Admin warning message if Heroic Knowledge Base Analytics Plugin is installed and activated
		*/
		function ht_kb_analytics_plugin_warning() {
			global $current_user;
			$user_id = $current_user->ID;
			if( class_exists('HKB_Analytics')  &&  current_user_can( 'install_plugins' )   ):		    
			?>
				<div class="notice notice-info">
					<p><?php  printf(__('The Heroic Knowledge Base Analytics plugin is active and is no longer required | Please <a href="%1$s">Deactivate the Heroic Analytics Plugin</a>', 'ht-knowledge-base'), admin_url('plugins.php#heroic-knowledge-base-analytics')); ?></p>
				</div>
			<?php
			endif; 
		}

		/**
		* Admin warning message if site urls incorrect
		* Both frontend and backend should be ssl
		*/
		function ht_kb_site_urls_warning() {
			global $current_user;
			$user_id = $current_user->ID;
			$site_url = site_url();
			$home_url = home_url();
			$admin_url = admin_url();

			//site url and admin url need to be the same protocol 
			$match_protocol = substr($home_url, 0, 5) === substr($admin_url, 0, 5);
			if( current_user_can( 'manage_options' )  &&  !$match_protocol  && apply_filters('ht_kb_enable_site_urls_warning_notice', true) ):		    
			?>
				<div class="notice notice-info">
					<p><?php  printf(__('The Heroic Knowledge Base encourages both front end and back end are served over the same protocols - either SSL (https) or plain (http) | Please <a href="%1$s">Update your Site Addresses</a>', 'ht-knowledge-base'), admin_url('options-general.php')); ?></p>
				</div>
			<?php
			endif; 
		}


		/** 
		* Old voting version check and deactivate
		*/
		function ht_kb_voting_old_version_check() {
			//check for file
			$old_voting_file_path = dirname(dirname( __FILE__ )) . '/ht-voting/ht-voting.php';
			if( file_exists( $old_voting_file_path) ){
				add_action('admin_notices', array($this, 'ht_kb_voting_old_version_check_notice'));
			}

			//deactivate if necessary
			if( is_plugin_active( plugin_basename( $old_voting_file_path ) ) ){
				deactivate_plugins( plugin_basename( $old_voting_file_path ) );
			} 
		}

		/**
		* Admin warning message if Heroic Voting not installed
		*/
		function ht_kb_voting_old_version_check_notice() {	    
			?>
				<div class="notice notice-error">
					<p><?php  _e('An old version of Heroic Voting plugin was detected and deactivated, please remove the Heroic Voting plugin and ht-voting folder', 'ht-knowledge-base'); ?></p>
				</div>
			<?php
		}

		/** 
		* Old database version check and deactivate
		*/
		function ht_kb_database_old_version_check() {
			$db_version = get_option('hkb_database_terms_db_version', 0);

			if( version_compare( HT_KB_VERSION_NUMBER, $db_version ) == 1  ){
				add_action('admin_notices', array($this, 'ht_kb_database_old_version_check_notice'));
			}

		}

		/**
		* Admin warning message if Old database version detected not installed
		*/
		function ht_kb_database_old_version_check_notice() {	
			//perform the database update
			HT_Knowledge_Base::knowledgebase_taxmeta_update_database();  
			?>
				<div class="notice notice-info">
					<p><?php  _e('The database needs updating, please refresh this page to complete the database update', 'ht-knowledge-base'); ?></p>
				</div>
			<?php
		}

		/**
		* Activation check, on action as of 2.0.7
		*/
		function ht_kb_activation_check(){
			if ( is_admin() && true == get_option('ht_kb_activate') ) {
				//network_activate check
				$network_activate = get_option('ht_kb_network_activate');
				//perform the actions
				do_action('ht_kb_activate', $network_activate);
				//delete the option
				delete_option('ht_kb_activate');
				//delete network activate option
				delete_option('ht_kb_network_activate');
			}
		}

		/**
		* Add custom css to head
		* TODO: old conditional doesn't work. Reimplement
		*/
		function ht_kb_custom_css_head(){
			echo hkb_get_css_variables();
			//@deprecated - old KB Settings, use Appreance > Customize > Additional CSS instead (or better still, your child theme)
			//echo hkb_get_custom_styles_css();
		}

		/**
		* Add generator to head
		*/
		function ht_kb_generator_head(){
			echo apply_filters('ht_kb_generator_head', '<meta name="generator" content="Heroic Knowledge Base v' . HT_KB_VERSION_NUMBER. '" />' . "\n" );			
		}

		/**
		* Display post states
		* @since 3.0
		*/
		function ht_kb_display_post_states( $post_states, $post ){
			$knowledge_bases = ht_kb_get_registered_knowledge_base_keys();
			foreach ($knowledge_bases as $index => $key) {
				//kb archive page id
				$page_id = ht_kb_get_kb_archive_page_id( $key );
				if( $page_id == $post->ID ){
					$post_states['ht_kb_archive_'.$key] = sprintf(__( '%s Knowledge Base Archive Page', 'ht-knowledge-base' ), ucfirst( $key ) );	
				}
			}
			return apply_filters( 'ht_kb_display_post_states', $post_states, $post );
		}

		/**
		* Filter the page template
		* @since 3.0
		*/
		function ht_kb_page_template( $template, $type, $templates ){
			$knowledge_bases = ht_kb_get_registered_knowledge_base_keys();
			foreach ($knowledge_bases as $index => $key) {
				//kb archive page id
				$hkb_archive_page_id = ht_kb_get_kb_archive_page_id( $key );

				//if a knowledge base archive load the hkb-archive template
				if( $hkb_archive_page_id == get_the_ID() ){
					$template = hkb_locate_template('hkb-archive');	
				}
			}			
			return apply_filters( 'ht_kb_page_template', $template ); 
		}

		/**
		* Filter the taxonomy template
		* @since 3.0
		*/
		function ht_kb_taxonomy_template( $template, $type, $templates ){
			$taxonomy = get_query_var('taxonomy');
			switch ($taxonomy) {
				case 'ht_kb_category':
					$template = hkb_locate_template('hkb-taxonomy-category');	
					break;
				case 'ht_kb_tag':
					$template = hkb_locate_template('hkb-taxonomy-tag');	
					break;					
				default:
					//do nothing
					break;
			}			
			return apply_filters( 'ht_kb_taxonomy_template', $template ); 
		}

		/**
		* Filter the single template
		* @since 3.0
		*/
		function ht_kb_single_template( $template, $type, $templates ){
			$post_type = get_query_var('post_type');
			switch ($post_type) {
				case 'ht_kb':
					$template = hkb_locate_template('hkb-single');	
					break;			
				default:
					//do nothing
					break;
			}			
			return apply_filters( 'ht_kb_single_template', $template ); 
		}

		/**
		* Filter the search template
		* @since 3.0
		*/
		function ht_kb_search_template( $template, $type, $templates ){
			//detect ht-kb-search
			//@todo - this detection needs moving to a query_var with query_vars filter + get_query_var
			if( $this->is_ht_kb_search ){
				$template = hkb_locate_template('hkb-search');	
			}					
					
			return apply_filters( 'ht_kb_search_template', $template ); 
		}

		/**
		* Filter to remove breadcrumbs for all registered hooks
		*/
		function ht_remove_breadcrumbs( $status ){
			return false;
		}

		/**
		* Remove edit option from the admin bar
		* @param (Object) $wp_admin_bar Unfiltered admin bar
		*/
		function ht_kb_remove_edit_option_from_admin_bar( $wp_admin_bar ){
				$wp_admin_bar->remove_node('edit');			
		}

		/**
		* Add edit option to the admin bar if taxonmy term
		* @param (Object) $wp_admin_bar Unfiltered admin bar
		*/
		function ht_kb_add_edit_option_to_admin_bar( $wp_admin_bar ){
			//check tax and term set
			if( isset($this->taxonomy) && isset($this->term) ){
				$taxonomy = get_taxonomy($this->taxonomy);
				$term = get_term_by('slug', $this->term, $this->taxonomy);
				if( isset($taxonomy) && isset($term) ){
					//build the edit term url
					$edit_term_url = 'edit-tags.php?' . 'action=edit&taxonomy=' . $this->taxonomy . '&tag_ID=' . $term->term_id . '&post_type=ht_kb';
					$args = array(
									'id'    => 'edit_ht_kb_term',
									'title' => $taxonomy->labels->edit_item,
									'href'  => admin_url( $edit_term_url ),
									'meta'  => array( 'class' => 'edit-term-item' )
						);
					$wp_admin_bar->add_node( $args );
				}
			}				
		}

		/**
		* Modify <title> for ht_kb archive page
		* Note for users of SEO plugins, hook to this function, response will be the just the main title
		* eg. Article Name, Category Name or Knowledge Base for the archive
		* @param (String) $title Unfiltered page title
		* @return (String) Filtered page title
		* @deprecated - no longer required for 3.1.1+
		*/
		function ht_kb_wp_title_filter( $title, $sep=' ', $seplocation=null ) {
			global $post, $wp_query;

			//define pad string
			$pad_str = apply_filters('ht_kb_wp_title_filter_pad_str', ' ');

			if(!is_archive() || !is_object($post) ){
				//don't do anything if not an archive or post not object
			} elseif($this->theme_template_in_use){
				//don't do anything if theme template in use
			} else {
				//build titles				
				$main_title = __('Knowledge Base', 'ht-knowledge-base');
				$site_name = get_bloginfo('name');		
				$title_front = '';
				$page_type = '';		

				if(isset($wp_query->query_vars['ht_kb']) && !empty($wp_query->query_vars['ht_kb'])){
					$page_type = 'ht_kb_single';
					//article - post name
					//@todo - identify more efficient method for getting post id here
					$slug = $wp_query->query_vars['ht_kb'];
					$page = get_page_by_path( $slug, 'OBJECT', 'ht_kb' );
					if(isset($page) && is_a($page, 'WP_Post') ){
						//get the article title
						$title_front = get_the_title($page->ID);
					} else {
						//default
						$title_front = __('Article', 'ht-knowledge-base');
					}
				} elseif(isset($this->term) && isset($this->taxonomy)){
					//ht_kb_category or ht_kb_tag
					$page_type = $this->taxonomy;
					//set tax state on wp_query (wp-seo fix)
					$wp_query->is_tax = true;
					$term = get_term_by( 'slug', $this->term, $this->taxonomy );
					if($term){
						$title_front = $term->name;
					} else {
						$title_front = $main_title; 
					}
				} elseif(isset($wp_query->query_vars['post_type']) && !empty($wp_query->query_vars['post_type']) && ('ht_kb' == $wp_query->query_vars['post_type'])){
					$page_type = 'ht_kb_archive';
					//archive
					$title_front = $main_title;
				} elseif( $this->is_ht_kb_search ){
					$page_type = 'ht_kb_search';
					//search
					$title_front = sprintf( __( 'Search Results for %s', 'ht-knowledge-base' ), get_search_query() );
				} else {
					//not something we are interested in 
				}				

				//pad separator if required
				if(function_exists('ctype_space')){
					$sep = (ctype_space($sep)) ? $sep : $pad_str . $sep . $pad_str;
				} else {
					$sep = $pad_str . $sep; 
				}	

				//apply suffix
				$title_suffix = '';
				$title_suffix = apply_filters( 'ht_kb_wp_title_suffix', $title_suffix, 'ht_kb', $page_type );		

				//build new title
				$filtered_title = $title_front . $sep . $pad_str . $title_suffix;				

				//filter types for post types, other posts types can go here
				$types = array(
					array( 
						'post_type' => 'ht_kb', 
						'title' => apply_filters( 'ht_kb_wp_title', $filtered_title, 'ht_kb', $page_type )
					)
				);

				$post_type = $post->post_type;

				//iterate over types to filter the the title
				foreach ( $types as $key => $value) {
					if ( in_array($post_type, $types[$key])) {
						$title = $types[$key]['title'];
						break;
					}
				}

				//does this have any effect?
				//$title = apply_filters('wpseo_title', $title);
				/*
				if(class_exists('WPSEO_Frontend')){
					$wpseo_frontend = WPSEO_Frontend::get_instance();
					$title = $wpseo_frontend->title($title, $sep, $seplocation);
				}
				*/

				//apply master filters
				$title = apply_filters( 'ht_kb_wp_title_tag_filter', $title, $sep, $seplocation, $post_type, $page_type );
				
			}

			//return the title
			return $title;
		}


		/**
		* Dummy function for ht_kb_wp_title filter, utilize this function in a theme's functions.php
		* This way you can add additional suffixes to the title
		* @param (String) $title The unfiltered title
		* @param (String) $post_type The archive post type
		* @param (String) $page_type The page type to operate on
		* @return (String) Filtered title
		*/
		function ht_kb_wp_title_dummy($title='', $post_type='ht_kb', $page_type='ht_kb_category'){
			return $title;
		}


		/**
		* Custom pre get posts filter for knowledge base article previews
		* Fixes a bug where WordPress will query post_type=post instead of post_type=ht_kb on Article preview
		* @param (Object) $query The WordPress query object
		* @return (Object) Filtered WordPress query object
		*/
		function ht_kb_modify_kb_preview_pre_get_posts( $query ){
			if( $query->is_main_query() && '' !== get_query_var('ht_kb') ){
				$query->set( 'post_type' , 'ht_kb' );
			}

			return $query;					
		}

		/**
		* Custom pre get posts filter for knowledge base article order
		* @param (Object) $query The WordPress query object
		* @return (Object) Filtered WordPress query object
		*/
		function ht_kb_modify_sort_order_pre_get_posts( $query ){
			global $wp_query, $ht_kb_display_archive, $ht_kb_display_uncategorized_articles;

			if ( ! isset( $wp_query ) ) {
				return $query;
			}

			//exit if feed
			if( is_feed() ){
				return $query; 
			}

			//exit if options not set

			//get the user set sort by and sort order
			$user_sort_by = hkb_archive_sortby();
			$user_sort_order = hkb_archive_sortorder();

			//exit if post type archive and custom sort by, this sorting will be handled by the hkb_get_archive_articles function
			//also ensure we're also not in the main query
			if( 'custom'==$user_sort_by && !$query->is_main_query() && ( is_front_page() || is_post_type_archive( 'ht_kb' ) || is_tax() ) ){
				return $query; 
			}

			if(!is_preview() && !is_singular() && !is_admin() && 
				( 	$ht_kb_display_archive==true ||
					$ht_kb_display_uncategorized_articles==true ||
					( $query->is_main_query() && is_post_type_archive( 'ht_kb' ) ) || 
					( $query->is_main_query() && is_tax('ht_kb_category') ) || 
					( $query->is_main_query() && is_tax('ht_kb_tag') ) )
				){

					$sort_meta_key = '';

					$valid_sort_orders = array('date', 'title', 'comment_count', 'rand', 'modified', 'popular', 'helpful', 'custom');
					if ( in_array($user_sort_by, $valid_sort_orders) ) {
					  $sort_by = $user_sort_by;
					  $sort_order = ($user_sort_order=='asc') ? 'ASC' : 'DESC';
					} else {
					  // by default, display latest first
					  $sort_by = 'date';
					  $sort_order = 'DESC';
					}

					if($user_sort_by=='popular'){
					  $sort_by = 'meta_value_num';
					  $sort_meta_key = HT_KB_POST_VIEW_COUNT_KEY;
					}

					if($user_sort_by=='helpful'){
					  $sort_by = 'meta_value_num';
					  $sort_meta_key = HT_USEFULNESS_KEY;
					} 

					if($user_sort_by=='custom' && ( is_tax('ht_kb_category') ) ){
						$sort_by = 'meta_value_num date';
						$term = get_queried_object();
						if(property_exists($term, 'term_id')){
							$sort_meta_key = '_ht_article_order_'.$term->term_id;
						}
						
					}        

				   
				  //set query 
				   $query->set( 'orderby' ,  $sort_by );
				   $query->set( 'order' ,  $sort_order );
				   $query->set( 'meta_key' ,  $sort_meta_key );

				   return $query;
			  }   

			 return $query;   	
		}

		/**
		* Custom pre get posts filter for knowledge base taxonomy to set posts_per_page
		* @param (Object) $query The WordPress query object
		* @return (Object) Filtered WordPress query object
		*/
		function ht_kb_posts_per_taxonomy( $query ){
			global $wp_query;

			if ( ! isset( $wp_query ) ) {
				return $query;
			}
			
			if(!is_preview() && !is_singular() && !is_admin() && 
				( 	( $query->is_main_query() && is_tax('ht_kb_category') ) || 
					( $query->is_main_query() && is_tax('ht_kb_tag') ) )
				){	
				   
					//remove child articles
					$tax_obj = $query->get_queried_object();
					$tax_query = array(
									'taxonomy' => $tax_obj->taxonomy,
									'field' => 'slug',
									'terms' => $tax_obj->slug,
									'include_children' => FALSE
							);
					$query->tax_query->queries[] = $tax_query;
					$query->query_vars['tax_query'] = $query->tax_query->queries;

					//get the user set sort by and sort order
					$user_number_posts = hkb_category_articles();

					//set query 
					$query->set( 'posts_per_page' ,  $user_number_posts );
					return $query;	  		      
			   }    

			return $query;  	
		}


		/**
		* Post published action hook
		* @param (String) $id The post id
		* @param (Object) $post The WordPress post object
		*/
		function ht_kb_article_publish( $id, $post ){
			//set the initial meta
			$this->ht_kb_set_initial_meta( $post->ID );
			//set the inital order
			$this->ht_kb_set_initial_custom_order_meta( $post->ID );
		}

		/**
		* Save knowledge base article hook
		*/
		function save_ht_kb_article($post_id, $post, $update) {

			//if this is just a auto draft, go no further
			if($post->post_status=='auto-draft'){
				return;
			}

			// If this is a revision, get real post ID
			if ( $parent_id = wp_is_post_revision( $post_id ) ) {
				$post_id = $parent_id;
			}				

			//get category terms
			$ht_kb_categories = wp_get_post_terms( $post_id, 'ht_kb_category' );

			if(is_a($ht_kb_categories, 'WP_Error') || empty($ht_kb_categories) ){
				//no category
				set_transient( '_'.$post_id.'_no_categories', true, 5*60 ); 
			} else {
				//categories set
				delete_transient( '_'.$post_id.'_no_categories' );
			}
		}

		/**
		* Enqueue knowledge base styles
		*/
		function ht_kb_enqueue_styles(){
			//check the we're not in theme mode
			$theme_mode = locate_template('hkb-templates', false, false);
			if($theme_mode){
				//don't load hkb-styles
			} else {
				//load hkb-styles
				//wp_enqueue_style( 'font-awesome', plugins_url( 'css/font-awesome.min.css', __FILE__ ) );
				//by default styles will only load on ht-kb archive, tax
				$load_styles = false;

				//single, category or tag or post type archive, 
				$load_styles = 	//appears to not work  - use get_post_type instead
								//is_single( 'ht_kb' ) || 
								'ht_kb' == get_post_type() ||
								is_tax( 'ht_kb_category' ) || 
								is_tax( 'ht_kb_tag' ) ||
								ht_kb_is_ht_kb_search() ||
								//appears to not work - use get_archive page id instead
								//is_post_type_archive( 'ht_kb' )
								ht_kb_get_kb_archive_page_id() == get_queried_object_id()
								;

				//pass through filter
				$load_styles = apply_filters( 'ht_kb_enqueue_styles_load_styles', $load_styles );						

				//conditionally load styles 
				if( $load_styles ){

					wp_enqueue_style( 'hkb-style', plugins_url( 'css/hkb-style.css', __FILE__ ), array(), HT_KB_VERSION_NUMBER );

					$this->hkb_load_theme_compat_css_files();

				}

				
			}
		}

		/**
		* Load the css theme shivs 
		*/
		function hkb_load_theme_compat_css_files(){
			//get the (parent) theme slug
			$theme_slug = $this->hkb_get_root_theme_slug();
			$theme_compat_css_file = $this->hkb_locate_theme_compat_css_file($theme_slug);
			if(!empty($theme_compat_css_file)){
				wp_enqueue_style( 'hkb-compat-styles', $theme_compat_css_file, array(), HT_KB_VERSION_NUMBER );
			}			
		}

		/**
		* Get the (parent) theme slug
		* @return (String) the theme slug, if a child theme, will return parent theme slug 
		*/
		function hkb_get_root_theme_slug(){
			//get current theme
			$current_theme = wp_get_theme();
			$theme_name = $current_theme->get( 'Name' );
			//check if current theme has parent theme
			$parent_theme_directory = $current_theme->get( 'Template' );
			if(!empty($parent_theme_directory)){
				$parent_theme_object = wp_get_theme($parent_theme_directory);
				$theme_name = $parent_theme_object->get( 'Name' );
			}
			return sanitize_title( $theme_name );
		}

		/**
		 * Locate css file
		 * Filterable - use hkb_locate_theme_compat_css_file
		 * @param (String) $slug Slug
		 * @param (String) $name The filename
		 * @return (String) Located css file
		 */
		function hkb_locate_theme_compat_css_file( $slug, $name = null ) {
			$theme_compat_css_file = '';
			$theme_compat_css_url = '';
			//build filename
			$file_name = ($name) ? $slug . '-' . $name . '.css' : $slug . '.css';  
			//look in css/compat directory
			$theme_compat_css_file = plugin_dir_path( __FILE__ ) . 'css/compat/' . $file_name;
			
			
			//check css file exits
			if( file_exists($theme_compat_css_file) && !empty( $theme_compat_css_file ) ){
				$this->theme_compat_css = true;
				//translate to url
				$theme_compat_css_url = plugins_url( 'css/compat/' . $file_name, __FILE__ );
			} elseif( $name ){
				//recursive call to load base (without $name)
				$theme_compat_css_file = $this->hkb_locate_theme_compat_css_file( $slug );
			}

			return apply_filters('hkb_locate_theme_compat_css_file', $theme_compat_css_url);
		}

		/**
		* Admin warning if no categories set
		*/
		function ht_kb_no_category_warning() {	 
			global $post;   

			//check a post is set
			if( !is_a( $post, 'WP_Post' ) ){
				return;
			}

			//check we are on the post 
			$screen = get_current_screen();
			if( !$screen || 'post'!=$screen->base ){
				return;
			}

			//get the transient to check whether no categories set during save
			$transient = get_transient( '_'.$post->ID.'_no_categories' ); 

			if( $transient ){
				delete_transient( '_'.$post->ID.'_no_categories' ); 
				?>
					<div class="notice notice-warning is-dismissible">
						<p><?php _e('This article has no knowledge base categories set, it will appear as uncategorized', 'ht-knowledge-base'); ?></p>
					</div>
				<?php
			} //end if transient
		}

		/**
		* Admin warning if no articles
		*/
		function ht_kb_no_articles_warning() {
			//check we are on the post 
			$screen = get_current_screen();
			//only display the warning from the edit kb list or dashboard home
			if( !$screen || ( 'edit-ht_kb'!=$screen->id && 'dashboard'!=$screen->id ) ){
				return;
			}

			//only display if no articles
			$articles = get_posts('post_type=ht_kb&posts_per_page=10');
			$article_count = count($articles);
			if($article_count>0){
				return;
			}

			?>
				<div class="notice notice-warning">
					<p><?php printf( 
								__('It looks like you have no Knowledge Base articles, you can <a href="%s">add new article now</a>, or install the demo content from the <a href="%s">welcome screen</a>.', 'ht-knowledge-base'),
								admin_url('post-new.php?post_type=ht_kb'),
								admin_url('index.php?page=ht-kb-welcome')
							);
					 ?></p>
				</div>
			<?php
		}

		/**
		* Admin warning if debug options are on
		*/
		function ht_kb_debug_options_enabled() {
			if(!current_user_can('manage_options')){
				return;
			}

			if( defined('HKB_DEBUG_SCRIPTS') && HKB_DEBUG_SCRIPTS ){
				?>
					<div class="notice notice-warning">
						<p><?php _e('HKB_DEBUG_SCRIPTS is enabled, please turn off for production environments', 'ht-knowledge-base'); ?></p>
					</div>
				<?php
			}

			if( defined('HKB_TESTING_MODE') && HKB_TESTING_MODE ){
				?>
					<div class="notice notice-warning">
						<p><?php _e('HKB_TESTING_MODE is enabled, please turn off for production environments', 'ht-knowledge-base'); ?></p>
					</div>
				<?php
			}

		}

		/**
		* Admin warning if no default homepage
		*/
		function ht_kb_no_default_homepage() {

			$default_kb_homepage = ht_kb_get_kb_archive_page_id( 'default' );
			if( is_int( $default_kb_homepage ) && $default_kb_homepage > 0  ){
				return;
			}

			?>
				<div class="notice notice-warning">
					<p><?php printf( 
								__('It looks like you have no default KB Home page set, the knowledge base may not work correctly. You can <a href="%s">set this now</a>, or install the demo content from the <a href="%s">welcome screen</a>.', 'ht-knowledge-base'),
								admin_url('edit.php?post_type=ht_kb&page=ht_knowledge_base_settings_page#archive-section'),
								admin_url('index.php?page=ht-kb-welcome')
							);
					 ?></p>
				</div>
			<?php
		}

		/** 
		* Admin warning if packaged plugin and not theme managed updates
		*/
		function ht_kb_packaged_no_theme_managed_updates(){
			if( 	defined('HT_KB_DISTRIBUTION') && 
					'packaged' == HT_KB_DISTRIBUTION && 					 
					!current_theme_supports('ht-kb-theme-managed-updates') &&
					apply_filters( 'ht_kb_warn_on_non_packaged_usage', true )
			  ){
				?>
					<div class="notice notice-error">
						<p><?php printf( __('Heroic Knowledge Base plugin needs to be used with the theme it is packaged with, please <a href="%s">activate a valid HeroThemes theme</a>', 'ht-knowledge-base'), admin_url('themes.php') ); ?></p>
					</div>
				<?php
			}
		}


		/**
		 * Plugin row action links
		 * @param (Array) $input Already defined action links
		 * @param (String) $file Plugin file path and name being processed
		 * @return (Array) The filtered input
		 */
		function ht_kb_plugin_row_action_links( $input, $file ) {
			if ( plugin_basename(__FILE__) != $file ){
				return $input;
			}

			$links = array(
				'<a href="' . admin_url( 'edit.php?post_type=ht_kb&page=ht_knowledge_base_settings_page' ) . '">' . esc_html__( 'Settings', 'ht-knowledge-base' ) . '</a>',
			);

			$output = array_merge( $input, $links );

			return $output;
		}

		/**
		 * Plugin row meta links
		 * @param (Array) $input Already defined meta links
		 * @param (String) $file plugin file path and name being processed
		 * @return (Array) The filtered input
		 */
		function ht_kb_plugin_row_meta_links( $input, $file ) {
			if ( plugin_basename(__FILE__) != $file ){
				return $input;
			}

			$links = array();

			if( current_theme_supports('ht-kb-theme-managed-updates') &&
				apply_filters( 'ht_kb_remove_plugin_meta_links_on_theme_managed_updates', true )
		  	){
			  	//do nothing
			} else {
				//add getting started link
				$links[] = '<a href="' . admin_url( apply_filters('ht_kb_getting_started_page_link', 'index.php?page=ht-kb-welcome' ) ) . '">' . esc_html__( 'Getting Started', 'ht-knowledge-base' ) . '</a>';	
			}			

			//add documentation link
			$links[] = '<a href="' . HT_KB_SUPPORT_URL . '" target="_blank">' . esc_html__( 'Support and Documentation', 'ht-knowledge-base' ) . '</a>';

			$output = array_merge( $input, $links );

			return $output;
		}

		/**
		 * Custom get_template_part function that locates and loads a template
		 * @param (String) $slug Slug
		 * @param (String) $name The filename
		 */
		function hkb_get_template_part( $slug, $name = null ) {
			do_action( 'before_hkb_get_template_part_' . $slug );
			$template = hkb_locate_template($slug, $name);
			load_template($template, false);
			do_action( 'after_hkb_get_template_part_' . $slug );
		}

		/**
		 * Locate template
		 * Filterable - use hkb_locate_template
		 * @param (String) $slug Slug
		 * @param (String) $name The filename
		 * @return (String) Located template
		 */
		function hkb_locate_template( $slug, $name = null ) {

			$template = '';
			//build filename
			$file_name = ( ($name) && !empty($name) ) ? $slug . '-' . $name . '.php' : $slug . '.php';  
			//check the theme does not override template
			$theme_template = locate_template( 'hkb-templates/' . $file_name ) ;
			$plugin_template = plugin_dir_path( __FILE__ ) . 'hkb-templates/' . $file_name;
			//load the template
			if( file_exists($theme_template) && !empty( $theme_template) ){
				$this->theme_template_in_use = true;
				//load the theme template first
				$template = $theme_template;
			} elseif( file_exists( $plugin_template ) && !empty( $plugin_template ) ){
				//load the plugin template second
				$template = $plugin_template;
			} elseif( $name ){
				//recursive call to load base (without $name)
				$template = hkb_locate_template( $slug );
			}

			//add hkb_template_used to wp_footer, since 3.0
			add_action( 'wp_footer', array( $this, 'hkb_template_wp_footer_action') );

			return apply_filters( 'hkb_locate_template', $template );
		}

		/**
		* Footer action when a hkb template is used 
		* Used for viewcounts
		* @since 3.0
		*/
		function hkb_template_wp_footer_action( ){
			//call a recallable action
			do_action( 'hkb_template_wp_footer_action' );
		}

		/**
		* Get the term, if one isn't passed get it from the query
		* @param (Object) $term The default term (not required)
		* @return (Object) The term
		*/
		function hkb_get_term($term=null){
			$term = isset($term) ? $term : get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) ); 
			if(is_int($term)){
				//if integer get the term object
				$term = get_term($term);
			}
			return $term;
		}

		/**
		* Get the term id for a given category
		* @param (Object) $category The category to get the term id for (not required)
		* @return (Int) The term id
		*/
		function hkb_get_term_id($category=null){
			$term_id = 0;
			$term = hkb_get_term( $category );
			if( $term ){
				//term id
				$term_id = $term->term_id;
			}
			return $term_id;
		}

		/**
		* Get subcategories
		* @return (array) An array of categories
		*/
		function hkb_get_subcategories($parent_id=null){
			$parent_id = empty($parent_id) ? hkb_get_current_term_id() : $parent_id; 
			$hkb_master_tax_terms = hkb_get_master_tax_terms();

			$sub_categories = wp_list_filter( $hkb_master_tax_terms, array( 'parent'=>$parent_id ) );
			//apply hkb_get_subcategories filter
			$sub_categories = apply_filters( 'hkb_get_subcategories', (Array)$sub_categories, $parent_id );
			return $sub_categories;
		}

		/**
		* Get post ancestors - used in breadcrumbs etc
		* Note posts can have multiple and divergent ancestory
		* @return (Array) Post ancestors
		*/
		function ht_kb_get_ancestors() {
			global $post, $cat, $hkb_current_article_id;

			$ancestors = array(); 

			//path number
			$i = 0;
			$ancestors[$i] = array();


			if ( !ht_kb_is_ht_kb_front_page() ) {
	
				$taxonomy = get_query_var( 'taxonomy' );
				$term_string = get_query_var( 'term' );
				$visited = array();
							
				if (!empty($taxonomy) && !empty($term_string)) {

					//category terms bread crumb

					//add homepage path
					$ancestors[$i] =  $this->ht_kb_get_homepage_ancestors();

					$term = get_term_by( 'slug', $term_string, $taxonomy );
					
					if($term==false)
						return;

					if ($term->parent != 0) { 
						//has parents
						$parents =  $this->ht_get_custom_category_ancestors($term->term_id, 'ht_kb_category', true,null, false, $visited, $ancestors, $i );
						//itself
						//$ancestors[$i][] = array('label'=>$term->name, 'link' => esc_attr(get_term_link($term, 'ht_kb_category')), 'title'=>sprintf( __( "View all posts in %s" ), $term->name), 'type'=>'kb_current_page');	
					
					} else {
						//path 
						//add knowledge base to path
						$ancestors[$i][] = array(	'label'=>apply_filters( 'hkb_breadcrumbs_kb_tax_label', $term->name ), 
													'link' => apply_filters( 'hkb_breadcrumbs_kb_tax_link', esc_attr(get_term_link($term, 'ht_kb_category')) ), 
													'title'=>sprintf( apply_filters( 'hkb_breadcrumbs_kb_tax_title', __( 'View all posts in %s', 'ht-knowledge-base' ) ), $term->name), 
													'type'=>'kb_tax'
												);	
						$visited[] = $term->term_id;
					}
					

				} elseif ( !is_single() && 'ht_kb' == get_post_type() ) {
					//add homepage path
					$ancestors[$i] = $this->ht_kb_get_homepage_ancestors();

					//search?
					if(ht_kb_is_ht_kb_search()){

						$ancestors[$i][] = array(	'label'=>sprintf( apply_filters( 'hkb_breadcrumbs_kb_search_label', __('Search Results for %s', 'ht-knowledge-base') ), 										get_search_query() ), 
													'link' =>apply_filters( 'hkb_breadcrumbs_kb_search_url', '' ), 
													'title'=>sprintf( apply_filters( 'hkb_breadcrumbs_kb_search_title', __('Search Results for %s', 'ht-knowledge-base') ), 			get_search_query() ), 
													'type'=>'kb_search'
												);
					}


				} elseif ( is_single() && 'ht_kb' == get_post_type() ) {
					//Single  kb post


					$hkb_current_article_id = empty($hkb_current_article_id) ? get_the_ID() : $hkb_current_article_id;
					$terms = wp_get_post_terms( $hkb_current_article_id , 'ht_kb_category');

					if( !empty($terms) ){
						foreach($terms as $term) {

							//add homepage path
							$ancestors[$i] = $this->ht_kb_get_homepage_ancestors();

							if ($term->parent != 0) { 
								$parents =  $this->ht_get_custom_category_ancestors($term->term_id, 'ht_kb_category', true,null, false, $visited, $ancestors, $i );
								//itself
								//$ancestors[$i][] = array('label'=>$term->name, 'link' => esc_attr(get_term_link($term, 'ht_kb_category')), 'title'=>sprintf( __( 'Viewing a post in %s' ), $term->name), 'type'=>'kb_current_page');	
					
							} else {
								//add knowledge base to path
								$ancestors[$i][] = array(	'label'=>apply_filters( 'hkb_breadcrumbs_kb_tax_label', $term->name ), 
															'link' => apply_filters( 'hkb_breadcrumbs_kb_tax_link', esc_attr(get_term_link($term, 'ht_kb_category')) ), 
															'title'=>sprintf( apply_filters( 'hkb_breadcrumbs_kb_tax_title', __( 'View all posts in %s', 'ht-knowledge-base' ) ), $term->name), 
															'type'=>'kb_tax'
														);	
								$visited[] = $term->term_id;
							}
							//itself
							$ancestors[$i][] = array(	'label'=>apply_filters( 'hkb_breadcrumbs_kb_current_page_label', get_the_title() ), 
														'link' =>apply_filters( 'hkb_breadcrumbs_kb_current_page_link', get_permalink() ), 
														'title'=>apply_filters( 'hkb_breadcrumbs_kb_current_page_title', get_the_title() ), 
														'type'=>'kb_current_page'
													);	
					
							//increment the counter
							$i++;

						} // End foreach
					} else {
						//add homepage path
						$ancestors[$i] = $this->ht_kb_get_homepage_ancestors();

						//uncategorized article
						$ancestors[$i][] = array(	'label'=>apply_filters( 'hkb_breadcrumbs_kb_current_page_label', get_the_title() ), 
													'link' =>apply_filters( 'hkb_breadcrumbs_kb_current_page_link', get_permalink() ), 
													'title'=>apply_filters( 'hkb_breadcrumbs_kb_current_page_title', get_the_title() ), 
													'type'=>'kb_current_page'
												);	
						$i++;
					}		
					
				} elseif ( is_page() && 'page' == get_post_type() ) {

					//Single page or non ht_kb search results
					$hkb_current_article_id = empty($hkb_current_article_id) ? get_the_ID() : $hkb_current_article_id;

					//add homepage path
					$ancestors[$i] = $this->ht_kb_get_homepage_ancestors();

					//maybe page ancestors?
					$page_ancestors = get_post_ancestors( $post );
					//reverse the ancestors array
					$page_ancestors = array_reverse( $page_ancestors );
					if( !empty( $page_ancestors ) ){
						foreach( $page_ancestors as $page_ancestor ) {
							$page = get_post( $page_ancestor );
							//add page ancestors
							$ancestors[$i][] = array(	'label'=>apply_filters( 'hkb_breadcrumbs_page_label', get_the_title( $page ) ), 
														'link' => apply_filters( 'hkb_breadcrumbs_page_link', get_page_link( $page ) ), 
														'title'=>sprintf( apply_filters( 'hkb_breadcrumbs_page_title', __( 'Back to  %s', 'ht-knowledge-base' ) ), get_the_title( $page )), 
														'type'=>'page'
														);
						}
					}

					//current page
					$ancestors[$i][] = array(	'label'=>apply_filters( 'hkb_breadcrumbs_kb_current_page_label', get_the_title() ), 
												'link' =>apply_filters( 'hkb_breadcrumbs_kb_current_page_link', get_permalink() ), 
												'title'=>apply_filters( 'hkb_breadcrumbs_kb_current_page_title', get_the_title() ), 
												'type'=>'kb_current_page'
											);	
					
					//pages don't have categories or multiple assignments so this has no effect
					$i++;	
					
				} else {
						//Is a search (without kb results)
						if( ht_kb_is_ht_kb_search() ){
							//add homepage path
							$ancestors[$i] = $this->ht_kb_get_homepage_ancestors();

							$ancestors[$i][] = array(	'label'=>sprintf( apply_filters( 'hkb_breadcrumbs_non_kb_search_label', __('Search Results for %s', 'ht-knowledge-base') ), 									get_search_query() ), 
														'link' =>apply_filters( 'hkb_breadcrumbs_non_kb_search_url', '' ), 
														'title'=>sprintf( apply_filters( 'hkb_breadcrumbs_non_kb_search_title', __('Search Results for %s', 'ht-knowledge-base') ), get_search_query() ), 
														'type'=>'non_kb_search'
													);
						} else {
							//Display the post title.
							$ancestors[$i][] = array(	'label'=>apply_filters( 'hkb_breadcrumbs_kb_current_page_label', get_the_title() ), 
													'link' =>apply_filters( 'hkb_breadcrumbs_kb_current_page_link', get_permalink() ), 
													'title'=>apply_filters( 'hkb_breadcrumbs_kb_current_page_title', get_the_title() ), 
													'type'=>'kb_current_page'
												);
						}
						
							
						$i++;
				}
						
				
			} //is_front_page

			return apply_filters('ht_kb_get_ancestors', $ancestors);
		} //end function

		function ht_kb_get_homepage_ancestors(){
			$homepage_ancestors = array();

			//add home link to path
			$homepage_ancestors[] = array(	'label'=>apply_filters( 'hkb_breadcrumbs_blog_home_label', __('Home', 'ht-knowledge-base') ), 
										'link' => apply_filters( 'hkb_breadcrumbs_blog_home_url',  home_url() ), 
										'title'=>apply_filters( 'hkb_breadcrumbs_blog_home_title', __('Home', 'ht-knowledge-base') ), 
										'type'=>'blog_home'
									);

			//kb archive set as home test
			if(!hkb_is_kb_set_as_front()){
				//add knowledge base to path
				//currently only works for default knowledge base key
				$homepage_ancestors[] = array(	'label'=>apply_filters( 'hkb_breadcrumbs_kb_home_label', __('Knowledge Base', 'ht-knowledge-base') ), 
											'link' =>apply_filters( 'hkb_breadcrumbs_kb_home_url', get_permalink( ht_kb_get_kb_archive_page_id( 'default' ) ) ), 
											'title'=>apply_filters( 'hkb_breadcrumbs_kb_home_title', __('Knowledge Base', 'ht-knowledge-base') ), 
											'type'=>'kb_home'
										);					
			}

			return $homepage_ancestors;
		}

		/**
		* Custom ancestory walker
		* @param (Int) $id Term id
		* @param (Object) $taxonomy Taxonomy object
		* @param (Bool) $link Whether to link the ancestor item
		* @param (String) $seperator @deprecated 
		* @param (Bool) $nicename Use slug or name (default false - name)
		* @param (Object) $visited The visted terms
		* @param (Object) $ancestors Ancestors list
		* @param (Int) $i Counter
		* @return (Array) Post ancestors
		*/
		function ht_get_custom_category_ancestors( $id, $taxonomy = false, $link = false, $separator = '/', $nicename = false, $visited = array(), &$ancestors = array(), $i=0 ) {

			if(!($taxonomy && is_taxonomy_hierarchical( $taxonomy )))
				return;

			$chain = array();
			// $parent = get_category( $id );
			$parent = get_term( $id, $taxonomy);

			if ( is_wp_error( $parent ) ){
				$ancestors[$i][] = array('label'=>$parent->name, 'link' => esc_attr(get_term_link($parent, 'ht_kb_category')), 'title'=>sprintf( __( "View all posts in %s", 'ht-knowledge-base' ), $parent->name), 'type'=>'kb_current_page');	
				return;
			}
				

			if ( $nicename ){
				$name = $parent->slug;
			} else {
				$name = $parent->name;
			}

				
			//reset visited if null
			if(empty($visited)){
				$visited = array();
			}				

			if ( $parent->parent && 
				( $parent->parent != $parent->term_id ) && 
				(!in_array( $parent->parent, $visited ) ) ) {
					$visited[] = $parent->parent;
					 $this->ht_get_custom_category_ancestors( $parent->parent, $taxonomy, $link, $separator, $nicename, $visited, $ancestors, $i );
					 //return;
			}

			if ( $link ) {
				$ancestors[$i][] = array('label'=>$name, 'link' => esc_url( get_term_link( (int) $parent->term_id, $taxonomy ) ), 'title'=>sprintf( __( "View all posts in %s", 'ht-knowledge-base' ), $name), 'type'=>'kb_tax');			
			} else {
				$ancestors[$i][] = array('label'=>get_the_title(), 'link' => get_permalink(), 'title'=>get_the_title(), 'type'=>'kb_current_page');	
			}

			return;
		}

		/**
		* Get top level archive taxonomy terms for the Knowledge Base archive
		* @param (Int) $columns Number of columns
		* @param (Int) $sub_cat_depth Depth of subcategories to display
		* @param (Bool) $display_sub_cat_count Display a count in subcategories
		* @param (Bool) $display_sub_cat_articles Display/list subcategory articles
		* @param (String) $sort_by How to sort the archive
		* @param (String) $sort_order Sort order
		* @param (Bool) $hide_empty_kb_categories Whether to hide empty categories
		* @param (Int) $i Counter
		* @return (Array) Category terms
		*/
		function hkb_get_master_tax_terms($columns=2, $sub_cat_depth=2, $display_sub_cat_count=true, $display_sub_cat_articles=true, $sort_by='date', $sort_order='asc', $hide_empty_kb_categories=false){
			global $ht_kb_display_archive, $hkb_master_tax_terms;

			//the following is commented out for optimization
			//$columns = hkb_archive_columns();
			//$sub_cat_display = hkb_archive_display_subcategories();
			//$display_sub_cat_count = hkb_archive_display_subcategory_count();
			//$display_sub_cat_articles = hkb_archive_display_subcategory_articles();

			//this variable has been moved to hkb_filter_terms_by_depth
			//$sub_cat_depth = hkb_archive_subcategory_depth();
			$hide_empty_kb_categories = hkb_archive_hide_empty_categories();


			//list terms in a given taxonomy
			$args = array(
				'orderby' 		=> 'meta_value_num slug',
				'meta_query' => array(
										'relation' => 'OR', //default AND
										array(
											'key' => 'term_order',
											'compare' => 'NOT EXISTS'
										),
										array(
											'key' => 'term_order',
											'value' => '',
											'compare' => '!='
										)
								),
				//ensure order is asc for custom order
				'order' 		=> 'asc',
				'depth'         =>  0,
				'child_of'      => 	0,
				'hide_empty'    =>  $hide_empty_kb_categories,
				'pad_counts'   	=>	true,
			);

			$legacy_args = array(
					'taxonomy' => 'ht_kb_category',
					'hide_empty' => false,
					//cache use OK
					//'update_term_meta_cache' => false,
			);  
			//@since 3.0 updated get_terms call 
			$hkb_master_tax_terms = get_terms( array_merge( $legacy_args, $args ) );

			
			//limit depth if archive display or front page
			if( $this->is_ht_kb_archive || ht_kb_is_ht_kb_front_page() ){
				$hkb_master_tax_terms = $this->hkb_filter_terms_by_depth($hkb_master_tax_terms, $sub_cat_depth);
			}

			$hkb_master_tax_terms = apply_filters( 'hkb_master_tax_terms', $hkb_master_tax_terms );

			return $hkb_master_tax_terms;
		}

		/**
		* Get top level archive taxonomy terms for the Knowledge Base archive
		* @param (Array) $term_list The unfiltered term list
		* @param (Int) $max_depth The max depth of terms
		* @return (Array) Filtered terms
		*/
		function hkb_filter_terms_by_depth($term_list, $max_depth=100){
			$filtered_terms = array();

			$max_depth = hkb_archive_subcategory_depth();

			foreach ($term_list as $key => $term) {
				$term_depth = $this->hkb_get_category_depth($term->term_id);
				if($term_depth<=$max_depth){
					//add to filtered terms if less than max depth
					$filtered_terms[] = $term;
				}
			}

			return $filtered_terms;
		}

		/**
		* Get category depth
		* @param (Int) $term_id The term id of the category
		* @return (Int) Term depth
		*/
		function hkb_get_category_depth($term_id){
			//get ancestors
			$ancestors = get_ancestors($term_id, 'ht_kb_category');
			//calculate term_depth
			$term_depth = count($ancestors);
			return (int) $term_depth;
		}

		/**
		* Get top level archive taxonomy terms for the Knowledge Base archive
		* @param (Int) $columns Number of columns
		* @param (Int) $sub_cat_depth Depth of subcategories to display
		* @param (Bool) $display_sub_cat_count Display a count in subcategories
		* @param (Bool) $display_sub_cat_articles Display/list subcategory articles
		* @param (String) $sort_by How to sort the archive
		* @param (String) $sort_order Sort order
		* @param (Bool) $hide_empty_kb_categories Whether to hide empty categories
		* @param (Int) $i Counter
		* @return (Array) Category terms
		*/
		function hkb_get_archive_tax_terms($columns=2, $sub_cat_depth=2, $display_sub_cat_count=true, $display_sub_cat_articles=true, $sort_by='date', $sort_order='asc', $hide_empty_kb_categories=false){
			global $ht_kb_display_archive, $hkb_master_tax_terms;
			
			$hkb_master_tax_terms = hkb_get_master_tax_terms($columns, $sub_cat_depth, $display_sub_cat_count, $display_sub_cat_articles, $sort_by, $sort_order, $hide_empty_kb_categories);
			
			$tax_terms = wp_list_filter($hkb_master_tax_terms,array('parent'=>0));

			$ht_kb_display_archive = false;

			return $tax_terms;
		}

		/**
		* Get articles for a category
		* @param (Object) $category The category
		* @param (String) @deprecated $sort_by How to sort the archive - handled by hkb_archive_sortby()
		* @param (String) @deprecated $sort_order Sort order - handled by hkb_archive_sortorder()
		* @param (String) $location
		* @return (Array) Posts array
		*/
		function hkb_get_archive_articles($category, $sort_by='date', $sort_order='asc', $location=''){
			global $ht_kb_display_archive, $wp_query;

			$numberposts = 0;
			switch ($location) {
				case 'article_ordering':
					$numberposts = -1;
					break;
				
				default:
					$numberposts = hkb_home_articles();
					break;
			}			

			if( 'kb_home'==$location && 0 == $numberposts ){
				//return blank array
				return array();
			}

			$ht_kb_display_archive = true;

			$sort_by = hkb_archive_sortby();
			$sort_order = hkb_archive_sortorder();
			
			//get posts per category
			$args = array( 
				'post_type'  => 'ht_kb',
				'posts_per_page' => $numberposts,
				'order' => $sort_order,
				'orderby' => $sort_by,
				'suppress_filters' => 1,
				'tax_query' => array(
					array(
						'taxonomy' => 'ht_kb_category',
						'field' => 'term_id',
						'include_children' => false,
						'terms' => $category->term_id
					)
				)				
			);		

			//if sort by is custom order //?
			if( 'custom' == $sort_by ){
				$args['orderby'] = 'meta_value_num date';
				$args['meta_key'] = '_ht_article_order_'.$category->term_id;
				//ensure order is asc for custom order
				$args['order'] = 'asc';
			}
			
			//temporarily store the wp_query
			$temp_query = clone $wp_query;
			//reset the query
			wp_reset_query();
			//create new query object
			$ht_kb_category_articles_query = new WP_Query();
			//populate category posts
			$cat_posts =  $ht_kb_category_articles_query->query($args);
			//restore the wp_query object
			$wp_query = $temp_query;

			//end displaying archive
			$ht_kb_display_archive = false;

			return $cat_posts;
		}

		/**
		* Get articles without a category
		* @return (Array) Posts array
		*/
		function hkb_get_uncategorized_articles(){
			global $ht_kb_display_uncategorized_articles;

			//hide uncategorized articles - return empty array
			if(hkb_archive_hide_uncategorized_articles()){
				return array();
			}

			//now getting uncategorized posts
			$ht_kb_display_uncategorized_articles = true;
			
			//set max number of articles to fetch
			$numberposts = 100;
			//$numberposts = (array_key_exists('tax-cat-article-number', $ht_knowledge_base_options)) ? $ht_knowledge_base_options['tax-cat-article-number'] : 10;

			//get the master tax terms
			$args = array(
				'orderby'       =>  'term_order',
				'depth'         =>  0,
				'child_of'      => 	0,
				'hide_empty'    =>  0,
				'pad_counts'   	=>	true
			); 
			$legacy_args = array(
					'taxonomy' => 'ht_kb_category',
					'hide_empty' => false,
					//cache use OK
					//'update_term_meta_cache' => false,
			);  
			//@since 3.0 updated get_terms call 
			$hkb_master_tax_terms = get_terms( array_merge( $legacy_args, $args ) );

			//get the top level terms, now unused
			$top_level_tax_terms = wp_list_filter($hkb_master_tax_terms,array('parent'=>0));
			$tax_terms_ids = array();
			if( !empty ($hkb_master_tax_terms ) && !is_a( $hkb_master_tax_terms, 'WP_Error' ) && is_array( $hkb_master_tax_terms ) ){
				foreach ( (array)$hkb_master_tax_terms as $key => $term ) {
					array_push($tax_terms_ids, $term->term_id);
				}
			}
			$args = array( 
					'numberposts' => $numberposts, 
					'post_type'  => 'ht_kb',
					'orderby' => 'date',
					'suppress_filters' => false,
					'tax_query' => array(
						array(
							'taxonomy' => 'ht_kb_category',
							'field' => 'term_id',
							'include_children' => false,
							'terms' => $tax_terms_ids,
							'operator'  => 'NOT IN'
						)
					)
				);

			$uncategorized_posts = get_posts( $args );  

			$ht_kb_display_uncategorized_articles = false;

			return $uncategorized_posts;
		}

		/**
		* Returns true if there a no public posts
		* @todo - needs testing in context
		* @return (Bool) No public posts
		*/
		function hkb_no_public_posts(){
			global $wp_query;
			if ($wp_query->post_count > 0)
				return false;
			else
				return true;
		}

		/**
		 * Print the post excerpt
		 */
		function hkb_the_excerpt($s=''){
			add_filter( 'excerpt_more', array( $this, 'hkb_custom_excerpt_more_string'), 20 );
			add_filter( 'excerpt_length', array( $this, 'hkb_custom_excerpt_length'), 999 );
			echo hkb_get_the_excerpt($s) ;
		}

		/**
		 * Custom get_the_excerpt function
		 * @param (String) $s The search string
		 * @return (String) The modified excerpt
		 */
		function hkb_get_the_excerpt($s='') {

			$post = get_post();
			if ( empty( $post ) ) {
				return '';
			}

			if ( post_password_required() ) {
				return __( 'There is no excerpt because this is a protected post.', 'ht-knowledge-base' );
			}

			if(!empty($s) && hkb_highlight_search_term_in_excerpt()){
				$content = $post->post_content;

				//if a search string is supplied the content will be returned padded with the search string wrapped in a highlight tag

				/**
					Invidunt euripidis definiebas qui an, harum nonumy mea et. Eu mei vocent disputando, ad pro graeci convenire. Vim id falli malorum. Sea cu quod errem commodo, perfecto elaboraret omittantur sea te, graeci vocibus appellantur eos te.

				**/

				//replace newlines with space
				//$content = trim(preg_replace('/\s+/', ' ', $content));
				//strip html
				//$content = wp_strip_all_tags( $content, true );
				//is this required?
				//$content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');


				//custom content replacement 
				//@link https://stackoverflow.com/a/53962057/2985710				
				$content = preg_replace([
					'/<(?:br|p)[^>]*>/i', //replace br p with ' '
					'/<[^>]*>/',  //replace any tag with ''
					'/\s+/u', //remove run on space - replace using the unicode flag
					'/^\s+|\s+$/u' //trim - replace using the unicode flag
				],[
					' ', '', ' ', ''
				], $content);


				//get the position of the 
				$pos = stripos($content, $s);

				//if $s not contained in content, return the unaltered excerpt
				if(false === $pos){
					$content = apply_filters( 'get_the_excerpt', $post->post_excerpt, $post );
					return $content;
				}

				//perform replacement
				$replace = sprintf( '%s%s%s', 
									apply_filters('hkb_get_the_excerpt_highlight_tag_open', '<span class="highlight">'),
									$s,
									apply_filters('hkb_get_the_excerpt_highlight_tag_close', '</span>')
								);

				$replaced_content = str_ireplace($s, $replace, $content);

				//WordPress default excerpt length is 55 words (note this is overridden by hkb_custom_excerpt_length)
				$excerpt_length_words = apply_filters('excerpt_length', 55);
				//crudely calculate character length, assuming average word length of 5 characters
				$excerpt_length = $excerpt_length_words * apply_filters('hkb_get_the_excerpt_word_length_guess', 5);

				//the end pos of the found word
				$end_pos = $pos + strlen($s);

				$excerpt_start = $end_pos - round($excerpt_length/2);

				$additional_offset = apply_filters('hkb_additional_excerpt_offset', 31);

				//31 offset for new content
				$content_excerpt = substr($replaced_content, $excerpt_start, $excerpt_length + $additional_offset );

				//prefix
				if($excerpt_start>1){
					$content_excerpt = apply_filters('hkb_custom_excerpt_more_string', '...') . $content_excerpt;	
				}

				//suffix
				if($end_pos<strlen($content)){
					$content_excerpt =  $content_excerpt . apply_filters('hkb_custom_excerpt_more_string', '...');	
				}				

				$content = apply_filters( 'get_the_excerpt', $content_excerpt, $post );
				return apply_filters( 'hkb_the_excerpt', $content );

				//compat with wp search?
			}

			

			$content = apply_filters( 'get_the_excerpt', $post->post_excerpt, $post );
			return $content;
		}

		/**
		 * Remove any read more links for search
		 */
		function hkb_custom_excerpt_more_string($more) {
			return apply_filters( 'hkb_custom_excerpt_more_string' , __('...', 'ht-knowledge-base') );
		}

		/**
		 * Custom excerpt length
		 */
		function hkb_custom_excerpt_length() {
			//default excerpt length is 20 words
			return apply_filters( 'hkb_custom_excerpt_length', 20 );
		}
		
		/**
		* Get tag taxonomies
		* @param (String) $search A search string to match
		* @return (Array) An array of matching tags
		*/
		function get_ht_kb_tags($search=''){

			$args = array(
				//@since 3.0 updated call parameter
				'taxonomy' 		=> 'ht_kb_tag',
				'orderby'       => 'name', 
				'order'         => 'ASC',
				'hide_empty'    => true, 
				'exclude'       => array(), 
				'exclude_tree'  => array(), 
				'include'       => array(),
				'number'        => '', 
				'fields'        => 'all', 
				'slug'          => '', 
				'parent'         => '',
				'hierarchical'  => true, 
				'child_of'      => 0, 
				'get'           => '', 
				'name__like'    => $search,
				'pad_counts'    => false, 
				'offset'        => '', 
				'search'        => '', 
				//use cache ok
				'cache_domain'  => 'core'
			);

			$tags = get_terms( $args );

			return $tags;
		}//end function

		/**
		* Get category taxonomies
		* @param (String) $search A search string to match (unused in this version)
		* @param (String) $orderby How to order results (currently not working/implemented)
		* @return (Array) An array of matching categories
		*/
		function get_ht_kb_categories($search='', $orderby=''){

			//note orderby does not appear to be working, possible WordPress issue
			$orderby = (empty($orderby)) ? 'term_id' : $orderby;

			$args = array(
				//@since 3.0 updated call parameter
				'taxonomy' 		=> 'ht_kb_category',
				'orderby'       => $orderby, 
				'order'         => 'ASC',
				'hide_empty'    => false, 
				'exclude'       => array(), 
				'exclude_tree'  => array(), 
				'include'       => array(),
				'number'        => '', 
				'fields'        => 'all', 
				'slug'          => '', 
				'parent'         => '',
				'hierarchical'  => true, 
				'child_of'      => 0, 
				'get'           => '', 
				'name__like'    => $search,
				'pad_counts'    => false, 
				'offset'        => '', 
				'search'        => '', 
				//use cache ok
				'cache_domain'  => 'core'
			);

			$categories = get_terms( $args );

			return $categories;
		}//end function

		/**
		* Set the current term id
		* @param (Int) Current term id
		*/
		function hkb_set_current_term_id($id=null){
			global $hkb_current_term_id;
			$id = empty($id) ? hkb_get_term_id() : $id;
			$hkb_current_term_id = $id;
		}

		/**
		* Get current term id
		* @return (Int) term id
		*/
		function hkb_get_current_term_id(){
			global $hkb_current_term_id;
			$hkb_current_term_id = empty($hkb_current_term_id) ? hkb_get_term_id() : $hkb_current_term_id;
			return $hkb_current_term_id;
		}

		/**
		* Get the attachments
		* @return (Object) The file list
		*/
		function hkb_get_attachments($post_id=null){
			global $post;
			$post_id = empty($post_id) ? $post->ID : $post_id;
			return get_post_meta($post_id, '_ht_knowledge_base_file_advanced', true);
		}

		/**
		* Get the attachments open in new window option
		* @return (bool) Whether to open post attachments in new window
		*/
		function hkb_get_attachments_new_window($post_id=null){
			global $post;
			$post_id = empty($post_id) ? $post->ID : $post_id;
			return (bool) get_post_meta($post_id, '_ht_knowledge_base_file_new_window', true);
		}

		/**
		* Has attachments
		* @return (Object) Whether post has article attachments
		*/
		function hkb_has_attachments($post_id=null){
			global $post;
			$post_id = empty($post_id) ? $post->ID : $post_id;
			$attachments = hkb_get_attachments($post_id);
			$has_attachments = empty($attachments) ? false : true;
			return $has_attachments;
		}

	   /**
		* Has attachments
		* @return (Int) Count attachments
		*/
		function hkb_count_attachments($post_id=null){
			global $post;
			$post_id = empty($post_id) ? $post->ID : $post_id;
			$attachments = hkb_get_attachments($post_id);
			$has_attachments = empty($attachments) ? 0 : count($attachments);
			return $has_attachments;
		}

		/**
		* Whether the kb archive is set as front, this differs from ht_kb_is_ht_kb_front_page, 
		* as is does not check whether the front page is also the current page
		* @return (Bool) Knowledge Base archive set as front page
		*/
		function hkb_is_kb_set_as_front(){
			$set_as_front = false;

			//if not a page- false
			if( 'page' != get_option( 'show_on_front' ) ){
				return $set_as_front;
			}

			//get page on front option
			$page_on_front = get_option('page_on_front');

			//loop the knowledge bases a check if one of these is front 
			$knowledge_bases = ht_kb_get_registered_knowledge_base_keys();
			foreach ($knowledge_bases as $index => $key) {
				//kb archive page id
				$page_id = ht_kb_get_kb_archive_page_id( $key );
				if( $page_id == $page_on_front ){
					$set_as_front = true;
				}
			
			}
			
			/* @todo - check WPML behaviour */
			/*
			if(defined('ICL_LANGUAGE_CODE')){
				//translate the dummy page id if necessary
				$dummy_page_id = icl_object_id($dummy_page_id, 'page', false, ICL_LANGUAGE_CODE);
			}
			*/
			
			return $set_as_front;
		}

		/**
		* Get the custom article order for a given post_id and category term_id
		* @param (Int) $post_id The article post id
		* @param (Int) $term_id The term id for the ht_kb_category
		* @return (Int) The custom article/post order if set
		*/
		function hkb_get_custom_article_order($post_id, $term_id){
			return get_post_meta($post_id, '_ht_article_order_'.$term_id, true);
		}

		/**
		* Set the custom article order for a given post_id and category term_id
		* @param (Int) $post_id The article post id
		* @param (Int) $term_id The term id for the ht_kb_category
		* @param (Int) $order The new 
		* @return (Int) New meta ID
		*/
		function hkb_set_custom_article_order($post_id, $term_id, $order){
			return update_post_meta($post_id,   '_ht_article_order_'.$term_id, $order);
		}
		

		/** UPGRADE **/

		/**
		* Set initial post view count and helpfulness as 0
		* @param (String) $id The post id
		*/
		function ht_kb_set_initial_meta( $id ){
			//set post view count to 0 if none
			$post_view_count =  get_post_meta( $id, HT_KB_POST_VIEW_COUNT_KEY, true );
			if($post_view_count == ''){
				//set view count to 0
				update_post_meta($id, HT_KB_POST_VIEW_COUNT_KEY, 0);
			}
			//set post helpfulness meta to 0 if none
			$helpfulness =  get_post_meta( $id, HT_USEFULNESS_KEY, true );
			if($helpfulness == ''){
				//set helpfulness to 0
				update_post_meta($id, HT_USEFULNESS_KEY, 0);
			}
		}

		function ht_kb_plugin_activation_upgrade_actions(){
			//upgrade - set initial meta if required

			//early exit if ht_kb_plugin_activation_upgrade_actions is set to false
			if( !apply_filters( 'ht_kb_plugin_activation_upgrade_actions', true ) ){
				return;
			}

			//it might be possible to remove this functionality in the next release, assuming article meta has been upgraded
			$this->ht_kb_upgrade_articles_meta();

			//backup settings
			$this->ht_kb_copy_kb_settings_on_upgrade();
		}

		function ht_kb_upgrade_articles_meta(){
			//get all ht_kb articles
			$args = array(
					  'post_type' => 'ht_kb',
					  'posts_per_page' => -1,
					 );
			$ht_kb_posts = get_posts( $args );

			//loop and upgrade
			foreach ( $ht_kb_posts as $post ) {
				//upgrade if required
			   $this->ht_kb_set_initial_meta( $post->ID );
			   $this->ht_kb_upgrade_article_meta_fields( $post->ID );
			}
		}

		/**
		 * Upgrade the meta key values.
		 */
		public function ht_kb_upgrade_article_meta_fields($post_id){
			//keys to be upgraded
			$this->ht_kb_upgrade_meta_field($post_id, 'file_advanced');
			$this->ht_kb_upgrade_meta_field($post_id, 'voting_checkbox');
			$this->ht_kb_upgrade_meta_field($post_id, 'voting_reset');
			$this->ht_kb_upgrade_meta_field($post_id, 'voting_reset_confirm');
			$this->ht_kb_upgrade_view_count_meta($post_id);
			$this->ht_kb_upgrade_custom_order_meta($post_id);
		}

		/**
		 * Upgrade a post meta field.
		 * @param (Int) $post_id The id of the post to upgrade
		 * @param (String) $name The name of the meta field to upgrade
		 */
		function ht_kb_upgrade_meta_field($post_id, $name){
			$old_prefix = 'ht_knowledge_base_';
			$new_prefix = '_ht_knowledge_base_';

			//get the old value
			$old_value = get_post_meta($post_id, $old_prefix . $name, true);
			if(!empty($old_value)){
				//get the new value
				$new_value = get_post_meta($post_id, $new_prefix . $name, true);
				if(empty($new_value)){
					//sync the new value to the old value
					update_post_meta($post_id, $new_prefix . $name, $old_value);
				}
				
			}
			//delete old meta key
			delete_post_meta($post_id, $old_prefix . $name);
		}

		/**
		 * Upgrade a view count meta field
		 * @param (Int) $post_id The id of the post to upgrade
		 */
		function ht_kb_upgrade_view_count_meta($post_id){
			$old_key = 'ht_kb_post_views_count';
			$new_key = HT_KB_POST_VIEW_COUNT_KEY;

			//get the old value
			$old_value = get_post_meta($post_id, $old_key, true);
			if(!empty($old_value)){
				//get the new value
				$new_value = get_post_meta($post_id, $new_key, true);
				//upgrade regardless of whether the new value is empty
				if(true){
					//sync the new value to the old value
					update_post_meta($post_id, $new_key, $old_value);
				}
				
			}
			//delete old meta key
			delete_post_meta($post_id, $old_key);
		}

		/**
		 * Upgrade a view count meta field
		 * @param (Int) $post_id The id of the post to upgrade
		 */
		function ht_kb_upgrade_custom_order_meta($post_id){
			$this->ht_kb_set_initial_custom_order_meta($post_id);
		}

		/**
		* Copy the knowledge base settings on new version activation
		*/
		function ht_kb_copy_kb_settings_on_upgrade(){
			$kb_settings = get_option( 'ht_knowledge_base_settings' );
			//note we are using add_option to ensure this is not updated and only run once per version upgrade
			add_option( 'ht_knowledge_base_settings_bak_' . HT_KB_VERSION_NUMBER, $kb_settings  );
		}


		/**
		 * Set object terms filter
		 * @param (Int) $object_id
		 * @param (Array) $category_term
		 * @param (Array) $tt_ids
		 * @param (Int) $taxonomy
		 */
		function ht_kb_set_object_terms($object_id, $category_terms, $tt_ids, $taxonomy){

			//check 
			if(empty($taxonomy) || 'ht_kb_category' != $taxonomy){
				return;
			}

			//for each of the new terms set the article order
			foreach ($category_terms as $key => $category_term) {
				//get the term if it's not a proper term object, seems wordpress can either pass this as an array of
				//strings(slug) or integers(term_id), depending on context
				if(!isset($category_term->taxonomy)){ 
					if(is_int($category_term)){
						$category_term = get_term_by('id', $category_term, $taxonomy);
					} else {
						$category_term = get_term_by('slug', $category_term, $taxonomy);
					}					
				}

				//check term taxonomy is ht_kb_category
				if($category_term && isset($category_term->taxonomy) && 'ht_kb_category' === $category_term->taxonomy){
					$category_term_id = $category_term->term_id;
					$current_custom_order = hkb_get_custom_article_order($object_id, $category_term_id);
					//upgrade if empty, set custom order initially to post_id
					if(empty($current_custom_order)){
						hkb_set_custom_article_order($object_id, $category_term_id, $object_id);
					}
				}						
			}
		}


		/**
		* Set initial custom order meta
		* @param (Int) $post_id The id of the post to set meta
		*/
		function ht_kb_set_initial_custom_order_meta( $post_id ){
			//get ht_kb_category terms
			$category_terms = wp_get_post_terms( $post_id, 'ht_kb_category' );
			//loop terms and ensure order is set
			foreach ($category_terms as $key => $category_term) {
				$category_term_id = $category_term->term_id;
				$current_custom_order = hkb_get_custom_article_order($post_id, $category_term_id);
				//upgrade if empty, set custom order initially to post_id
				if(empty($current_custom_order)){
					hkb_set_custom_article_order($post_id, $category_term_id, $post_id);
				}		
			}
		}


		/**
		* Divi compat 
		* dummy function as this function is no longer used by Heroic Knowledge Base
		* should be fixed in Divi and will be removed for future releases of this plugin
		*/
		function ht_kb_theme_compat_reset_post( $args = array() ) {
			return;
		}


		/** STATIC FUNCTIONS **/

		/**
		* Loop through each blog and add custom tables where necessary
		* fixes network activate on multisite issues
		*/
		static function knowledgebase_customtaxorder_activate( $network_wide = null ) {
			global $wpdb;

			if ( is_multisite() && $network_wide ) {
				//store current blog
				$current_blog = $wpdb->blogid;

				//loop all blogs in the network and create table
				$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					HT_Knowledge_Base::knowledgebase_taxmeta_update_database();
					restore_current_blog();
				}
			} else {
				HT_Knowledge_Base::knowledgebase_taxmeta_update_database();
			}
		}

		/**
		* Upgrade term_order and tax meta previously stored in options to use term_meta API instead of custom database column
		*/
		static function knowledgebase_taxmeta_update_database(){
			global $wpdb;
			//check if term order in terms column
			$check_query = $wpdb->query("SHOW COLUMNS FROM $wpdb->terms LIKE 'term_order'");
			if ($check_query == 0) {
				//terms table does not contain term order, early exit
				update_option('hkb_database_terms_db_version', HT_KB_VERSION_NUMBER);	
				return;
			}

			//if it is, loop through all categories, get the term order and assign it to the term with the term_meta API
			$args = array(
				'hide_empty'    =>  0
			);

			$legacy_args = array(
					'taxonomy' => 'ht_kb_category',
					'hide_empty' => false,
					//cache use OK
					//'update_term_meta_cache' => false,
			);  
			//@since 3.0 updated get_terms call 
			//get all the terms for the ht_kb_category taxonomy
			$taxonomy_terms = get_terms( array_merge( $legacy_args, $args ) );

			foreach ( $taxonomy_terms as $key => $term ) {
				//returns array of WP_Term objects
				$order = property_exists($term, 'term_order') ? $term->term_order : '0';
				//update the term order meta
				update_term_meta($term->term_id, 'term_order', $order);

				//get the old meta from the site option and populate
				$t_id = $term->term_id;
				// retrieve the existing value(s) for this meta field. This returns an array
				$term_meta = get_option( "taxonomy_$t_id" );
				//meta_image
				$meta_image = ( isset ( $term_meta['meta_image'] ) ) ?  $term_meta['meta_image']: null ;
				if( isset( $meta_image ) ){
					update_term_meta($term->term_id, 'meta_image', $meta_image );
				}
				//meta_svg
				$meta_svg = ( isset ( $term_meta['meta_svg'] ) ) ?  $term_meta['meta_svg']: null ;
				if( isset( $meta_svg ) ){
					update_term_meta($term->term_id, 'meta_svg', $meta_svg );
				}
				//meta_svg_color
				$meta_svg_color = ( isset ( $term_meta['meta_svg_color'] ) ) ?  $term_meta['meta_svg_color']: null ;
				if( isset( $meta_svg_color ) ){
					update_term_meta($term->term_id, 'meta_svg_color', $meta_svg_color );
				}
				//custom_link
				$custom_link = ( isset ( $term_meta['custom_link'] ) ) ?  $term_meta['custom_link']: null ;
				if( isset( $custom_link ) ){
					update_term_meta($term->term_id, 'custom_link', $custom_link );
				}
				//meta_color
				$meta_color = ( isset ( $term_meta['meta_color'] ) ) ?  $term_meta['meta_color']: null ;
				if( isset( $meta_color ) ){
					update_term_meta($term->term_id, 'meta_color', $meta_color );
				}
				//restrict_access
				$restrict_access = ( isset ( $term_meta['restrict_access'] ) ) ?  $term_meta['restrict_access']: null ;
				if( isset( $restrict_access ) ){
					update_term_meta($term->term_id, 'restrict_access', $restrict_access );
				}
			}

			//finally, set site option for database version
			update_option('hkb_database_terms_db_version', HT_KB_VERSION_NUMBER);
		}

		/**
		* Remove columns from the WP database terms table that were added during installation
		* @todo Implement this function (note this should be on plugin UNINSTALL, not deactivation)
		*/
		static function knowledgebase_customtaxorder_uninstall() {
			global $wpdb;
			$init_query = $wpdb->query("SHOW COLUMNS FROM $wpdb->terms LIKE 'term_order'");
			if ($init_query != 0) {	
				$wpdb->query("ALTER TABLE $wpdb->terms DROP COLUMN `term_order`"); 
			}
		}



	} //end class HT_Knowledge_Base
}//end class exists test


//run the plugin
if( class_exists( 'HT_Knowledge_Base' ) ){
	global $ht_knowledge_base_init;
	$ht_knowledge_base_init = new HT_Knowledge_Base();

	function ht_kb_get_taxonomy(){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->taxonomy;
	}

	function ht_kb_get_term(){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->term;
	}

	function ht_kb_is_ht_kb_search(){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->is_ht_kb_search;
	}

	function ht_kb_is_ht_kb_front_page(){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->ht_kb_is_ht_kb_front_page;
	}

	function ht_kb_get_cpt_slug(){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->get_cpt_slug();
	}

	function ht_kb_get_cat_slug(){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->get_cat_slug();
	}

	function ht_kb_get_tag_slug(){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->get_tag_slug();
	}

	function ht_kb_view_count($post_id=null){
		global $post;
		//set the post id
		$post_id = ( empty( $post_id ) ) ? $post->ID : $post_id;
		//get the post usefulness meta
		$post_view_count = get_post_meta( $post_id, HT_KB_POST_VIEW_COUNT_KEY, true );
		//convert to integer
		$post_view_count_int = empty($post_view_count) ? 0 : intval($post_view_count);
		//apply filters
		$post_view_count_int = apply_filters( 'ht_kb_view_count', $post_view_count_int, $post_id );
		//return as integer
		return $post_view_count_int;
	}

	//@deprecated since 3.0
	function get_ht_kb_dummy_page_id(){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->get_ht_kb_dummy_page_id();
	}

	function hkb_get_template_part($slug, $name = null){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_get_template_part($slug, $name);
	}

	function hkb_locate_template($slug, $name = null){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_locate_template($slug, $name);
	}

	function hkb_get_term($term=null){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_get_term($term);
	}

	function hkb_get_term_id($category=null){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_get_term_id($category);
	}

	function hkb_get_subcategories($category=null){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_get_subcategories($category);
	}

	function ht_kb_get_ancestors(){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->ht_kb_get_ancestors();
	}

	function hkb_get_master_tax_terms($columns=2, $sub_cat_depth=2, $display_sub_cat_count=true, $display_sub_cat_articles=true, $sort_by='date', $sort_order='asc', $hide_empty_kb_categories=false){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_get_master_tax_terms($columns, $sub_cat_depth, $display_sub_cat_count, $display_sub_cat_articles, $sort_by, $sort_order, $hide_empty_kb_categories);		
	}

	function hkb_get_archive_tax_terms($columns=2, $sub_cat_depth=2, $display_sub_cat_count=true, $display_sub_cat_articles=true, $sort_by='date', $sort_order='asc', $hide_empty_kb_categories=false){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_get_archive_tax_terms($columns, $sub_cat_depth, $display_sub_cat_count, $display_sub_cat_articles, $sort_by, $sort_order, $hide_empty_kb_categories);		
	}

	function hkb_get_archive_articles($category, $sort_by='date', $sort_order='asc', $location=''){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_get_archive_articles($category, $sort_by, $sort_order, $location);
	}

	function hkb_get_uncategorized_articles(){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_get_uncategorized_articles();
	}

	function hkb_no_public_posts(){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_no_public_posts();
	}

	function hkb_the_excerpt($s=''){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_the_excerpt($s);
	}

	function hkb_get_the_excerpt($s=''){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_get_the_excerpt($s);
	}

	function get_ht_kb_tags($s=''){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->get_ht_kb_tags($s);
	}

	function get_ht_kb_categories($s='', $orderby=''){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->get_ht_kb_categories($s, $orderby);
	}

	function hkb_set_current_term_id($id=null){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_set_current_term_id($id);	
	}

	function hkb_get_current_term_id(){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_get_current_term_id();	
	}

	function hkb_get_attachments($post_id=null){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_get_attachments($post_id);	
	}

	function hkb_get_attachments_new_window($post_id=null){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_get_attachments_new_window($post_id);	
	}

	function hkb_has_attachments($post_id=null){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_has_attachments($post_id);	
	}

	function hkb_count_attachments($post_id=null){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_count_attachments($post_id);	
	}

	function hkb_is_kb_set_as_front(){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_is_kb_set_as_front();
	}

	function hkb_get_custom_article_order($post_id, $term_id){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_get_custom_article_order($post_id, $term_id);	
	}

	function hkb_set_custom_article_order($post_id, $term_id, $order){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_set_custom_article_order($post_id, $term_id, $order);	
	}

	function ht_kb_upgrade_article_meta_fields($post_id){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->ht_kb_upgrade_article_meta_fields($post_id);
	}

}