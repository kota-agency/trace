<?php
/**
* Analytics
* Self contained analytics core
*/

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

//if you want to disable hkb data capture, remove the next line
define( 'HKB_ANALYTICS_DATA_CAPTURE', true );

define( 'HKB_ANALYTICS_SOURCE_LIVE_SEARCH', 1 );
define( 'HKB_ANALYTICS_SOURCE_FULL_SEARCH', 2 );

if( !class_exists( 'HT_Knowledge_Base_Analytics_Core' ) ){
    class HT_Knowledge_Base_Analytics_Core {

        //constructor
        function __construct() {
            //init the saving fuctionality
            if ( defined( 'HKB_ANALYTICS_DATA_CAPTURE' ) &&  true === HKB_ANALYTICS_DATA_CAPTURE ){
                add_action( 'the_posts', array( $this, 'hkba_save_searches' ), 20 );
                add_action( 'wp_footer', array( $this, 'ht_kb_commit_last_save_search' ), 10 );
            }

            //check hkb database version
            add_action( 'admin_notices', array( $this, 'hkb_analytics_search_atomic_exists_message' ) );            

            //add activation action for table
            add_action( 'ht_kb_activate', array( $this, 'on_activate' ), 10, 1);
            //deactivation hook, currently unused
            //register_deactivation_hook( __FILE__, array( 'HT_Knowledge_Base_Analytics_Core', 'hkba_plugin_deactivation_hook' ) );

            
        }

        /**
        * Captures returned posts
        * @param (Array) $posts Array of post objects
        * @return (Array) Array of post objects, which will be unaltered
        */
        function hkba_save_searches($posts) {
            global $wp_query;

            //break if already performing a save search
            if ( defined( 'DOING_HKBA_SAVE_SEARCH' ) && true === DOING_HKBA_SAVE_SEARCH ) {
                return $posts;
            } else {
                define('DOING_HKBA_SAVE_SEARCH', true);
            }

            //check if the request is a search, and if so then save details.
            //hooked on a filter but does not change the posts

            if( is_search()
                && !is_paged() 
                && !is_admin() 
                && !empty($_GET['ht-kb-search']) )
                {
                    //get search terms
                    //search string is the raw query
                    $search_string = $wp_query->query_vars['s'];
                    //strip slashes
                    $search_string = stripslashes($search_string);

                    //search terms is the words in the query
                    $search_terms = $search_string;
                    $search_terms = preg_replace('/[," ]+/', ' ', $search_terms);
                    $search_terms = trim($search_terms);
                    $hit_count = $wp_query->found_posts;
                    $details = '';

                    //sanitise as necessary
                    $search_string = esc_sql( htmlentities( $search_string ) );
                    $search_terms = esc_sql( htmlentities( $search_terms ) );
                    $details = esc_sql( htmlentities( $details ) );

                    /* live search */
                    if(!empty($_GET['ajax'])){
                        $search_data = (object) array(
                        'search_string' => $search_terms,
                        'hit_count' => $hit_count,
                        'timestamp' => current_time( 'timestamp' ),
                        'details'   => ''
                    );

                        //save search to db
                        $this->ht_kb_save_live_search($search_data);
                    }
            }


           if(  is_search()
                && !empty($_GET['ht-kb-search']) //Knowledge Base search
                && !is_paged() //is not a second page search
                && !is_admin()//is not the dashboard
                && empty($_GET['ajax']) //not live search
                ){
                    //Non-Live search flow
                    //create search data object
                    $search_data = (object) array(
                        'search_string' => $search_terms,
                        'hit_count' => $hit_count,
                        'timestamp' => current_time( 'timestamp' ),
                        'details'   => ''
                    );

                    //save search to db
                    $this->ht_kb_save_search($search_data);
                    return $posts;
            } 

            return $posts;
        }

        /**
        * Saves search data in the database
        * @param (Array) $search_data Search data to be saved
        */
        private function ht_kb_save_search($search_data, $retry=true){
                global $wpdb;

                //user ip 
                $user_ip = hkb_get_user_ip();
                //user_id
                $user_id = hkb_get_current_user_id();

                $save_search_data = apply_filters('ht_kb_record_user_search', true, $user_id, $search_data);

                //return if set to not save search data 
                if(!$save_search_data){
                    return;
                }

                //save search into the db
                //review sql vulns - safe due to the use of esc_sql on search string, terms and details
                $query = "INSERT INTO {$wpdb->prefix}hkb_analytics_search_atomic ( id ,  terms , datetime , hits, user_id, user_ip, source )
                VALUES (NULL, '$search_data->search_string', NOW(), $search_data->hit_count, $user_id, '$user_ip', ". HKB_ANALYTICS_SOURCE_FULL_SEARCH . ")";
                
                $wpdb->hide_errors();
                $run_query = $wpdb->query($query); 
                if(!$run_query && $retry){
                    //add the source column and retry
                    $this->maybe_add_source_column_to_search_atomic_table();
                    $this->ht_kb_save_search($search_data, false);    
                }  
                $wpdb->show_errors(); 
                
        }

        /**
        * Saves live search data in the database
        * @param (Array) $search_data Search data to be saved
        */
        private function ht_kb_save_live_search($search_data){
                global $wpdb;

                //user ip 
                $user_ip = hkb_get_user_ip();
                //user_id
                $user_id = hkb_get_current_user_id();

                //generate user hash
                $user_hash = md5($user_ip . $user_id);

                //get the last 
                $user_last_search_data = get_transient($user_hash.'_kb_last_search');

                $user_last_search_terms = (!empty($user_last_search_data) && is_object($user_last_search_data) && property_exists($user_last_search_data, 'search_string')) ? $user_last_search_data->search_string : false;

                $user_current_search_terms = (!empty($search_data) && is_object($search_data) && property_exists($search_data, 'search_string')) ? $search_data->search_string : false;

                if($user_last_search_terms){

                    if( function_exists('mb_stripos' ) ){
                        //mb_stripos version, prefered for non-latin charset support
                        if(!empty($user_current_search_terms) && mb_stripos($user_current_search_terms, $user_last_search_terms ) !== false ){
                            //expansion of previous search, update kb_last search and exit
                        } else {
                            //new search, commit last search to database and set new kb_last_search
                            $this->ht_kb_commit_last_save_search();
                        }
                    } else {
                        //non-mb_stripos version
                        if(!empty($user_current_search_terms) && stripos($user_current_search_terms, $user_last_search_terms ) !== false ){
                            //expansion of previous search, update kb_last search and exit
                        } else {
                            //new search, commit last search to database and set new kb_last_search
                            $this->ht_kb_commit_last_save_search();
                        }
                    }                    
                } else {
                    //no last search, set new kb_last _search
                }

                $this->set_live_search_transient($user_hash, $search_data);                
        }

        private function set_live_search_transient($user_hash, $search_data){
            $user_current_search_terms = (!empty($search_data) && is_object($search_data) && property_exists($search_data, 'search_string')) ? $search_data->search_string : false;

            if($user_current_search_terms && is_string($user_current_search_terms) && strlen($user_current_search_terms) > apply_filters( 'ht_kb_livesearch_trigger_length', 3 ) ){
                //commit transient
                set_transient($user_hash.'_kb_last_search', $search_data, 5 * MINUTE_IN_SECONDS);
            } else {
                //do nothing
                return;
            }
        }

        //manual and with footer hook?
        function ht_kb_commit_last_save_search($retry=true){
            global $wpdb;

            //user ip 
            $user_ip = hkb_get_user_ip();
            //user_id
            $user_id = hkb_get_current_user_id();

            //generate user hash
            $user_hash = md5($user_ip . $user_id);

            $user_last_search = get_transient($user_hash.'_kb_last_search');

            $save_search_data = apply_filters('ht_kb_record_user_search', true, $user_id, $user_last_search);
            //return if set to not save search data 
            if(!$save_search_data){
                return;
            }

            if($user_last_search){
                //unpack
                $search_data = $user_last_search;

                //save search into the db
                //review sql vulns - safe due to the use of esc_sql on search string, terms and details
                $query = "INSERT INTO {$wpdb->prefix}hkb_analytics_search_atomic ( id ,  terms , datetime , hits, user_id, user_ip, source )
                VALUES (NULL, '$search_data->search_string', NOW(), $search_data->hit_count, $user_id, '$user_ip', ". HKB_ANALYTICS_SOURCE_LIVE_SEARCH . ")";
                
                $wpdb->hide_errors();
                $run_query = $wpdb->query($query); 
                if(!$run_query && $retry){
                    //add the source column and retry
                    $this->maybe_add_source_column_to_search_atomic_table();
                    $this->ht_kb_commit_last_save_search(false);    
                }   
                $wpdb->show_errors();
            }

            //delete the transient
            delete_transient($user_hash.'_kb_last_search');
        }

        /**
        * Create database table
        */
        function hkba_create_table() {
            //add the table into the database
            global $wpdb;
            $table_name = $wpdb->prefix . "hkb_analytics_search_atomic";
            //check database version
            $db_version = get_option('hkb_analytics_search_atomic_db_version');
            if ($wpdb->get_var("SHOW tables LIKE '$table_name'") != $table_name || HT_KB_VERSION_NUMBER != $db_version ) {
                require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
                $create_hkb_analytics_table_sql = "CREATE TABLE {$table_name} (
                                                    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                                                    terms VARCHAR(100) NOT NULL,
                                                    datetime DATETIME NOT NULL,
                                                    hits INT(11) NOT NULL,
                                                    user_id BIGINT(20) unsigned NOT NULL,
                                                    user_ip VARCHAR(15) NOT NULL,
                                                    source INT( 4 ) NULL,
                                                    PRIMARY KEY  (id)
                                                  )
                                                  CHARACTER SET utf8 COLLATE utf8_general_ci;
                                                  ";
                //dbDelta will automagically create any missing fields and update where appropriate
                dbDelta($create_hkb_analytics_table_sql);
                //set database version
                update_option('hkb_analytics_search_atomic_db_version', HT_KB_VERSION_NUMBER);
            }
        }

        /**
        * On activate function
        * @param (Bool) $network_wide True when network activate being performed
        */
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
                    $this->hkba_create_table();
                    $this->maybe_add_source_column_to_search_atomic_table();
                    restore_current_blog();
                }
            } else {
                $this->hkba_create_table();
                $this->maybe_add_source_column_to_search_atomic_table();
            }
        }

        /**
        * Maybe add the source column to hkb_analytics_search_atomic table
        */
        function maybe_add_source_column_to_search_atomic_table(){
            global $wpdb;
            $table_name = $wpdb->prefix . "hkb_analytics_search_atomic";
            if (!$this->does_source_column_exist_in_search_atomic_table()) { 
                $wpdb->query("ALTER TABLE {$table_name} ADD `source` INT( 4 ) NULL DEFAULT '0'"); 
            }
        }

        /**
        * Does the source column exist in hkb_analytics_search_atomic table
        */
        function does_source_column_exist_in_search_atomic_table(){
            global $wpdb;
            $table_name = $wpdb->prefix . "hkb_analytics_search_atomic";
            $init_query = $wpdb->query("SHOW COLUMNS FROM {$table_name} LIKE 'source'");
            if($init_query == 0 || $init_query === false ){
                return false;
            } else {
                return true;
            }
        }

        /**
        * Check the search analytics table exists and is OK
        */
        function hkb_analytics_search_atomic_exists_message(){
            //only proceed if we are admin 
            if(!is_admin() || !current_user_can('activate_plugins')){
                return;
            }

            if( !$this->does_source_column_exist_in_search_atomic_table() ){
                $message = __('The search analytics table does not exist or is missing columns, please deactivate and re-activate the knowledge base plugin', 'ht-knowledge-base');
                echo '<div class="error"><p>' . $message . '</p></div>';
            }
        }

        static function hkba_plugin_deactivation_hook() {
            //do nothing, this is handled by our own ht_kb_activate hook
        }

    }
}

//run the module
if( class_exists( 'HT_Knowledge_Base_Analytics_Core' ) ){
    new HT_Knowledge_Base_Analytics_Core();
}