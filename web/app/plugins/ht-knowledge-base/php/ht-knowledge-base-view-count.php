<?php
/**
* Self contained article view count functionality
*/

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

//if you want to disable hkb view count functionality remove the next line
define( 'HKB_VIEW_COUNT', true );

if( !class_exists( 'HT_Knowledge_Base_View_Count' ) ){

	if(!defined('HT_KB_POST_VIEW_COUNT_KEY')){
		define('HT_KB_POST_VIEW_COUNT_KEY', '_ht_kb_post_views_count');
	}

	if(!defined('HT_VISITS_TABLE')){
		define('HT_VISITS_TABLE', 'hkb_visits');
	}

	class HT_Knowledge_Base_View_Count {

		//constructor
		function __construct() {

			//init the saving fuctionality
			if ( defined( 'HKB_VIEW_COUNT' ) &&  true === HKB_VIEW_COUNT ){                     

				//increase postcounts (deprecated in 3.0)
				//add_action( 'ht_knowledge_base_custom_template', array( $this, 'ht_kb_increase_article_viewcount' ), 10, 0 );
				//use hkb_locate_template wp_footer hook to increase viewcount
				add_action( 'hkb_template_wp_footer_action', array( $this, 'ht_kb_increase_article_viewcount' ), 10 );
				//remove actions
				//to keep the count accurate, remove prefetching
				//@todo - there is probably a better way to do this, eg with a hook, entire function may no longer be required
				//remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);  

			} 

			//get post views filter, allows for an offset to be added
			add_filter( 'ht_kb_get_post_views', array($this, 'ht_kb_get_post_views'), 10, 2 );

			//set post views filter, allows for an offset to be added
			add_filter( 'ht_kb_set_post_views', array($this, 'ht_kb_set_post_views'), 10, 2 );

			//add activation action for table
			add_action( 'ht_kb_activate', array( $this, 'on_activate' ), 10, 1 );
			//deactivation hook, currently unused
			//register_deactivation_hook( __FILE__, array( 'HT_Knowledge_Base_View_Count', 'hkb_visits_plugin_deactivation_hook' ) );
		}

		/**
		* Increment article view count on wp_footer (footer since 3.0)
		*/
		function ht_kb_increase_article_viewcount(){;
			$queried_object_data = hkb_get_queried_object_data();

			if( empty( $queried_object_data ) ){
				return;
			}

			$this->ht_kb_add_post_view_data_to_table($queried_object_data);

			//update counts if article
			if('ht_kb_article'==$queried_object_data['object_type']){
				$this->ht_kb_set_post_views(0, $queried_object_data['object_id']);
			}           

		}

		/**
		* Add post view data to database table
		* @param (Array) $data The data to add
		*/
		function ht_kb_add_post_view_data_to_table($data){
			global $wpdb;
			//new/insert
			$visit_id =  (isset($data['visit_id'])) ? $data['visit_id'] : 'NULL';
			$object_type = (isset($data['object_type'])) ? $data['object_type'] : 'undefined';
			$object_id = (isset($data['object_id'])) ? $data['object_id'] : 0;
			$datetime = (isset($data['datetime'])) ? $data['datetime'] : time();

			//only let defined object types into the db
			if('undefined'==$object_type)
				return;

			//user ip 
			$user_ip = hkb_get_user_ip();

			//user_id
			$user_id = hkb_get_current_user_id();

			$save_visit = apply_filters('ht_kb_record_user_visit', true,  $user_id, $object_id);

			//return if set to not save visit
			if(!$save_visit){
				return;
			}            

			//convert for sql
			$mysqltime = date ("Y-m-d H:i:s", $datetime);
			//duration
			//currently set to 0 for all users, will be implemented in the future
			$duration = (isset($data['duration'])) ? $data['duration'] : 0;

			//review sql vulns - safe due to use of wpdb::prepare
			$wpdb->query( $wpdb->prepare(
								"INSERT INTO {$wpdb->prefix}" . HT_VISITS_TABLE .   " ( visit_id ,  object_type, object_id, user_id, user_ip, datetime, duration)
			VALUES ('%d', '%s', '%d', '%s', '%s', '%s', '%s')",
								$visit_id,
								$object_type,
								$object_id,
								$user_id,
								$user_ip,
								$mysqltime,
								$duration
							) );
		}

		/**
		* Get the post views from the database
		* @param (Int) $offset How much to offset the view count by
		* @param (Int) $post_id ID of article to fetch count
		* @return (Int) The total number of visits
		*/
		function ht_kb_get_post_views( $offset=0, $post_id=0 ){
			global $wpdb;
			$get_visit_query = "SELECT 
										COUNT(*) AS sum
										FROM {$wpdb->prefix}" . HT_VISITS_TABLE  .
									" WHERE object_id = '{$post_id}'
								";
			$visits_total =  $wpdb->get_var( $get_visit_query, 0 );

			$combined_views = (int)$visits_total + (int)$offset;

			return $combined_views;
		}

		/**
		* Article post views
		* @param (Int) $post_id The ID of the post to increment the view count
		*/
		function ht_kb_set_post_views( $offset=0, $post_id=0 ) {
			$visits_total = apply_filters('ht_kb_get_post_views', $offset, $post_id);
			update_post_meta($post_id, HT_KB_POST_VIEW_COUNT_KEY, $visits_total);
		}

		/* CREATION / ACTIVATION FUNCTIONS */

		function hkb_visits_create_table() {
			//add the table into the database
			global $wpdb;
			$table_name = $wpdb->prefix . HT_VISITS_TABLE;
			if ($wpdb->get_var("show tables like '$table_name'") != $table_name) {
			  require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
			  $create_hkb_visits_table_sql = "CREATE TABLE {$table_name} (
													visit_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
													object_type VARCHAR(50) NOT NULL,
													object_id bigint(20) unsigned NOT NULL,
													user_id BIGINT(20) unsigned NOT NULL,
													user_ip VARCHAR(15) NOT NULL,
													datetime DATETIME NOT NULL,
													duration INT(11) NOT NULL,
													PRIMARY KEY (visit_id)
												  )
												  CHARACTER SET utf8 COLLATE utf8_general_ci;
												  ";
			  dbDelta($create_hkb_visits_table_sql);
			}
		}

		function on_activate( $network_wide = null ) {
			global $wpdb;
			//@todo - query multisite compatibility
			if ( is_multisite() && $network_wide ) {
				//store the current blog id
				$current_blog = $wpdb->blogid;
				//get all blogs in the network and activate plugin on each one
				$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					$this->hkb_visits_create_table();
					$this->upgrade_views();
					restore_current_blog();
				}
			} else {
				$this->hkb_visits_create_table();
				$this->upgrade_views();
			}
		}

		static function hkb_visits_plugin_deactivation_hook() {
			//do nothing
		}


		/**
		* Upgrade article
		* @param (Int) $post_id Article ID
		*/
		function upgrade_article($post_id){
			//upgrade required
			if($this->ht_kb_get_post_views(0, $post_id)!=0){
				//do nothing
			} else {
				//convert
				$this->convert_article_views($post_id);

				//delete old postmeta
				delete_post_meta( $post_id, HT_KB_POST_VIEW_COUNT_KEY );

				//set the new post views
				$this->ht_kb_set_post_views(0, $post_id);
			}            
		}

		/**
		* Convert post meta to table entities
		* @param (Int) $post_id Article ID
		*/
		function convert_article_views($post_id){
			//get existing views
			$view_count = (int) get_post_meta( $post_id, HT_KB_POST_VIEW_COUNT_KEY, true );

			//add a dummy view for each current view
			for ($i=0; $i < $view_count; $i++) {
				$this->add_dummy_view_to_article( $post_id );
			}
		}

		/**
		* Add one dummy view to an article
		* @param (Int) $post_id Article ID
		*/
		function add_dummy_view_to_article($post_id){
			$data = array( 'object_type' => 'ht_kb_article', 'object_id' => $post_id, 'duration' => -1 );
			$this->ht_kb_add_post_view_data_to_table($data);
		}

		/**
		* Upgrade Knowledge Base install - hook to ht_kb_activate
		*/
		function upgrade_views(){
			$kb_articles_args = array( 'post_type' => 'ht_kb', 'posts_per_page' => -1, 'post_status' => 'any' );
			$kb_articles = get_posts($kb_articles_args);

			foreach ($kb_articles as $key => $article) {
			   $this->upgrade_article($article->ID);
			}
		}

	}
}

//run the module
if( class_exists( 'HT_Knowledge_Base_View_Count' ) ){
	new HT_Knowledge_Base_View_Count();
}