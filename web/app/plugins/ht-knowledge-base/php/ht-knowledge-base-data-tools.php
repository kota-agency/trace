<?php
/**
* Data Tools
* Tools for modifying the HKB data
*/

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;



if( !class_exists( 'HT_Knowledge_Base_Data_Tools' ) ){
    class HT_Knowledge_Base_Data_Tools {

        private $admin_notices;

        //constructor
        function __construct() {
            add_action('ht_kb_debug_info_display', array( $this, 'hkb_data_tools_action_buttons' ), 10 );
            add_action('admin_init', array( $this, 'hkb_data_tools_action_handler' ), 10 );
            add_action('admin_head', array( $this, 'hkb_data_tools_admin_head' ), 10 );
            add_action('admin_enqueue_scripts', array( $this, 'hkb_data_tools_admin_enqueue_scripts' ), 10 );

            //cron jobs
            register_activation_hook( HT_KB_MAIN_PLUGIN_FILE, array( $this, 'ht_kb_activation_hook' ) );
            //cron action hooks
            add_action( 'hkb_data_cleaner', array( $this, 'hkb_data_cleaner' ) );

            $this->admin_notices = array();
        }

        /**
        * Available action buttons
        */
        function hkb_data_tools_action_buttons(){
            if(current_user_can( apply_filters('ht_kb_data_tools_capability', 'manage_options') ) ){
                $purge_all_analytics_data_url = admin_url('edit.php?post_type=ht_kb&page=ht_knowledge_base_debug_info' . '&purge_hkb_data=all');
                $purge_all_analytics_data_sec_url = wp_nonce_url( $purge_all_analytics_data_url, 'hkb_purge_data_action_' . 'all', 'security_token' );

                $purge_hkb_search_data_url = admin_url('edit.php?post_type=ht_kb&page=ht_knowledge_base_debug_info' . '&purge_hkb_data=search');
                $purge_hkb_search_data_sec_url = wp_nonce_url( $purge_hkb_search_data_url, 'hkb_purge_data_action_' . 'search', 'security_token' );

                $purge_hkb_voting_data_url = admin_url('edit.php?post_type=ht_kb&page=ht_knowledge_base_debug_info' . '&purge_hkb_data=voting');
                $purge_hkb_voting_data_sec_url = wp_nonce_url( $purge_hkb_voting_data_url, 'hkb_purge_data_action_' . 'voting', 'security_token' );

                $purge_hkb_exits_data_url = admin_url('edit.php?post_type=ht_kb&page=ht_knowledge_base_debug_info' . '&purge_hkb_data=exits');
                $purge_hkb_exits_data_sec_url = wp_nonce_url( $purge_hkb_exits_data_url, 'hkb_purge_data_action_' . 'exits', 'security_token' );

                $purge_hkb_visits_data_url = admin_url('edit.php?post_type=ht_kb&page=ht_knowledge_base_debug_info' . '&purge_hkb_data=visits');
                $purge_hkb_visits_data_sec_url = wp_nonce_url( $purge_hkb_visits_data_url, 'hkb_purge_data_action_' . 'visits', 'security_token' );

                $purge_hkb_recalc_usefulness_data_url = admin_url('edit.php?post_type=ht_kb&page=ht_knowledge_base_debug_info' . '&purge_hkb_data=recalc_usefulness');
                $purge_hkb_recalc_usefulness_data_sec_url = wp_nonce_url( $purge_hkb_recalc_usefulness_data_url, 'hkb_purge_data_action_' . 'recalc_usefulness', 'security_token' );

                $purge_hkb_recalc_visits_data_url = admin_url('edit.php?post_type=ht_kb&page=ht_knowledge_base_debug_info' . '&purge_hkb_data=recalc_visits');
                $purge_hkb_recalc_visits_data_sec_url = wp_nonce_url( $purge_hkb_recalc_visits_data_url, 'hkb_purge_data_action_' . 'recalc_visits', 'security_token' );

                $purge_hkb_delete_votes_for_deleted_articles_data_url = admin_url('edit.php?post_type=ht_kb&page=ht_knowledge_base_debug_info' . '&purge_hkb_data=delete_votes_for_deleted_articles');
                $purge_hkb_delete_votes_for_deleted_articles_data_sec_url = wp_nonce_url( $purge_hkb_delete_votes_for_deleted_articles_data_url, 'hkb_purge_data_action_' . 'delete_votes_for_deleted_articles', 'security_token' );

                ?>
                    <h2><?php _e('Data Tools', 'ht-knowledge-base'); ?></h2>
                    <div class="hkb-data-tool-actions">
                        <a class="button hkb-tools_button" href="<?php echo $purge_all_analytics_data_sec_url; ?>" data-confirm-message="<?php esc_attr_e('Are you sure you want to clear all knowledge base analytics data now?', 'ht-knowledge-base'); ?>"><?php _e('Purge All Analytics Data', 'ht-knowledge-base'); ?></a>
                        <br/><br/>
                        <a class="button hkb-tools_button" href="<?php echo $purge_hkb_search_data_sec_url; ?>" data-confirm-message="<?php esc_attr_e('Are you sure you want to clear all knowledge base search history data now?', 'ht-knowledge-base'); ?>"><?php _e('Purge Search Data', 'ht-knowledge-base'); ?></a>
                        <br/><br/>
                        <a class="button hkb-tools_button" href="<?php echo $purge_hkb_voting_data_sec_url; ?>" data-confirm-message="<?php esc_attr_e('Are you sure you want to clear all knowledge base voting data now?', 'ht-knowledge-base'); ?>"><?php _e('Purge Voting Data', 'ht-knowledge-base'); ?></a>
                        <br/><br/>
                        <a class="button hkb-tools_button" href="<?php echo $purge_hkb_exits_data_sec_url; ?>" data-confirm-message="<?php esc_attr_e('Are you sure you want to clear all knowledge base transfers data now?', 'ht-knowledge-base'); ?>"><?php _e('Purge Transfers Data', 'ht-knowledge-base'); ?></a>
                        <br/><br/>
                        <a class="button hkb-tools_button" href="<?php echo $purge_hkb_visits_data_sec_url; ?>" data-confirm-message="<?php esc_attr_e('Are you sure you want to clear all knowledge base view data now?', 'ht-knowledge-base'); ?>"><?php _e('Purge Views Data', 'ht-knowledge-base'); ?></a>
                        <br/><br/>
                        <a class="button hkb-tools_button" href="<?php echo $purge_hkb_recalc_usefulness_data_sec_url; ?>" data-confirm-message="<?php esc_attr_e('Are you sure you want to recalculate usefulness for all articles now?', 'ht-knowledge-base'); ?>"><?php _e('Recalculate Article Usefulness', 'ht-knowledge-base'); ?></a>
                        <br/><br/>
                        <a class="button hkb-tools_button" href="<?php echo $purge_hkb_recalc_visits_data_sec_url; ?>" data-confirm-message="<?php esc_attr_e('Are you sure you want to recalculate views for all articles now?', 'ht-knowledge-base'); ?>"><?php _e('Recalculate Article Views', 'ht-knowledge-base'); ?></a>
                        <br/><br/>
                        <a class="button hkb-tools_button" href="<?php echo $purge_hkb_delete_votes_for_deleted_articles_data_sec_url; ?>" data-confirm-message="<?php esc_attr_e('Are you sure you want to delete votes for deleted articles now?', 'ht-knowledge-base'); ?>"><?php _e('Delete Votes for Deleted Articles', 'ht-knowledge-base'); ?></a>
                    </div>

                <?php
            } else {
                ?>
                <h2><?php _e('Data Tools', 'ht-knowledge-base'); ?></h2>
                <?php _e( 'Missing required permissions for Heroic Knowledge Base data tools management', 'ht-knowledge-base' ); ?>
                <?php
            }
        }

        /*
        * Display the admin notices
        */
        function hkb_data_tools_display_admin_notices(){
            foreach ($this->admin_notices as $key => $notice):
                $state = $notice['state'];
                $text = $notice['text'];
            ?>
                <div class="notice notice-<?php echo $state; ?> is-dismissible">
                    <p><?php echo esc_attr($text); ?></p>
                </div>
            <?php
            endforeach;
        }


        function hkb_data_tools_action_handler(){

            //set allowable actions
            $allowable_actions = array('all','search','voting','exits','visits','recalc_usefulness','recalc_visits', 'delete_votes_for_deleted_articles');

            //set action from passed get request
            $action = array_key_exists('purge_hkb_data', $_GET) ? $_GET['purge_hkb_data'] : false;

            $action = in_array($action, $allowable_actions) ? $action : false;

            //early exit
            if( empty($action) ){
                return;
            }

            if( current_user_can( apply_filters('ht_kb_data_tools_capability', 'manage_options') ) ){

                //check security
                if (!isset($_GET['security_token']) || !wp_verify_nonce($_GET['security_token'], 'hkb_purge_data_action_' . $action)) {
                    die('Security Check');
                }
                switch ($action) {
                    case 'all':
                        $this->hkb_data_tools_clear_all_search_data();
                        $this->hkb_data_tools_clear_all_voting_data();
                        $this->hkb_data_tools_clear_all_exits_data();
                        $this->hkb_data_tools_clear_all_visits_data();
                        $this->hkb_data_tools_recalc_usefulness();
                        $this->hkb_data_tools_recalc_visits();
                        break;
                    case 'search':
                        $this->hkb_data_tools_clear_all_search_data();
                        break;
                    case 'voting':
                        $this->hkb_data_tools_clear_all_voting_data();
                        break;
                    case 'exits':
                        $this->hkb_data_tools_clear_all_exits_data();
                        break;
                    case 'visits':
                        $this->hkb_data_tools_clear_all_visits_data();
                        break;
                    case 'recalc_usefulness':
                        $this->hkb_data_tools_recalc_usefulness();
                        break;
                    case 'recalc_visits':
                        $this->hkb_data_tools_recalc_visits();
                        break;
                    case 'delete_votes_for_deleted_articles':
                        $this->hkb_data_tools_delete_votes_for_deleted_articles();
                        break;
                    default:
                        break;
                }
            } else {
                $this->admin_notices[] = array('state'=>'error', 'text'=>__('User missing required permissions for knowledge base data operations', 'ht-knowledge-base'));
            }

            //display admin messages
            add_action('admin_notices', array( $this, 'hkb_data_tools_display_admin_notices' ) );
        }

        function hkb_data_tools_admin_head(){
            $screen = get_current_screen();

            if(!is_a($screen, 'WP_Screen') || 'ht_kb_page_ht_knowledge_base_debug_info' != $screen->base){
                return;
            }

        }

        function hkb_data_tools_admin_enqueue_scripts($page_slug){
            if('ht_kb_page_ht_knowledge_base_debug_info' != $page_slug){
                return;
            }

            $hkb_data_tools_js_src = (HKB_DEBUG_SCRIPTS) ? 'js/hkb-admin-data-tools-js.js' : 'js/hkb-admin-data-tools-js.min.js';
            wp_enqueue_script( 'ht-analytics', plugins_url( $hkb_data_tools_js_src, dirname( __FILE__ ) ), array('jquery'), HT_KB_VERSION_NUMBER, true );
        }


        function hkb_data_tools_clear_all_search_data(){
            global $wpdb;

            //hkb_analytics_search
            $table_name = $wpdb->prefix . 'hkb_analytics_search';
            if ($wpdb->get_var("SHOW tables LIKE '$table_name'") == $table_name ) {
                $result = $wpdb->query( "TRUNCATE TABLE {$table_name}" );            
                if($result){
                    $this->admin_notices[] = array('state'=>'success', 'text'=>__('Analytics search table successfully purged', 'ht-knowledge-base'));
                } else {
                    $this->admin_notices[] = array('state'=>'error', 'text'=>__('Unable to purge analytics search table', 'ht-knowledge-base'));
                }
            } else {
                $this->admin_notices[] = array('state'=>'info', 'text'=>__('Analytics search table does not exist', 'ht-knowledge-base'));
            }

            //wp_hkb_analytics_search_atomic
            $table_name = $wpdb->prefix . 'hkb_analytics_search_atomic';
            if ($wpdb->get_var("SHOW tables LIKE '$table_name'") == $table_name ) {
                $result = $wpdb->query( "TRUNCATE TABLE {$table_name}" );            
                if($result){
                    $this->admin_notices[] = array('state'=>'success', 'text'=>__('Analytics search atomic table successfully purged', 'ht-knowledge-base'));
                } else {
                    $this->admin_notices[] = array('state'=>'error', 'text'=>__('Unable to purge analytics search atomic table', 'ht-knowledge-base'));
                }
            } else {
                $this->admin_notices[] = array('state'=>'info', 'text'=>__('Analytics search atomic table does not exist', 'ht-knowledge-base'));
            }

            //wp_hkb_analytics_search_recent
            $table_name = $wpdb->prefix . 'hkb_analytics_search_recent';
            if ($wpdb->get_var("SHOW tables LIKE '$table_name'") == $table_name ) {
                $result = $wpdb->query( "TRUNCATE TABLE {$table_name}" );            
                if($result){
                    $this->admin_notices[] = array('state'=>'success', 'text'=>__('Analytics search recent table successfully purged', 'ht-knowledge-base'));
                } else {
                    $this->admin_notices[] = array('state'=>'error', 'text'=>__('Unable to purge analytics search recent table', 'ht-knowledge-base'));
                }
            } else {
                $this->admin_notices[] = array('state'=>'info', 'text'=>__('Analytics search recent table does not exist', 'ht-knowledge-base'));
            }
            

            
        }

        function hkb_data_tools_clear_all_voting_data(){
            global $wpdb;

            //hkb_voting
            $table_name = $wpdb->prefix . 'hkb_voting';
            if ($wpdb->get_var("SHOW tables LIKE '$table_name'") == $table_name ) {
                $result = $wpdb->query( "TRUNCATE TABLE {$table_name}" );            
                if($result){
                    $this->admin_notices[] = array('state'=>'success', 'text'=>__('Voting table successfully purged', 'ht-knowledge-base'));
                } else {
                    $this->admin_notices[] = array('state'=>'error', 'text'=>__('Unable to purge voting table', 'ht-knowledge-base'));
                }
            } else {
                $this->admin_notices[] = array('state'=>'info', 'text'=>__('Voting table does not exist', 'ht-knowledge-base'));
            }

            $this->hkb_data_tools_recalc_usefulness();

        }

        function hkb_data_tools_clear_all_exits_data(){
            global $wpdb;

            //hkb_exits
            $table_name = $wpdb->prefix . 'hkb_exits';
            if ($wpdb->get_var("SHOW tables LIKE '$table_name'") == $table_name ) {
                $result = $wpdb->query( "TRUNCATE TABLE {$table_name}" );            
                if($result){
                    $this->admin_notices[] = array('state'=>'success', 'text'=>__('Exits table successfully purged', 'ht-knowledge-base'));
                } else {
                    $this->admin_notices[] = array('state'=>'error', 'text'=>__('Unable to purge exits table', 'ht-knowledge-base'));
                }
            } else {
                $this->admin_notices[] = array('state'=>'info', 'text'=>__('Exits table does not exist', 'ht-knowledge-base'));
            }

        }

        function hkb_data_tools_clear_all_visits_data(){
            global $wpdb;

            //hkb_visits
            $table_name = $wpdb->prefix . 'hkb_visits';
            if ($wpdb->get_var("SHOW tables LIKE '$table_name'") == $table_name ) {
                $result = $wpdb->query( "TRUNCATE TABLE {$table_name}" );            
                if($result){
                    $this->admin_notices[] = array('state'=>'success', 'text'=>__('Visits table successfully purged', 'ht-knowledge-base'));
                } else {
                    $this->admin_notices[] = array('state'=>'error', 'text'=>__('Unable to purge visits table', 'ht-knowledge-base'));
                }
            } else {
                $this->admin_notices[] = array('state'=>'info', 'text'=>__('Visits table does not exist', 'ht-knowledge-base'));
            }
            

            //update viewcount meta 
            try {
                if(defined('HT_KB_POST_VIEW_COUNT_KEY')){
                    //get all ht_kb articles
                    $args = array(
                              'post_type' => 'ht_kb',
                              'posts_per_page' => -1,
                             );
                    $ht_kb_posts = get_posts( $args );

                    //loop and update views
                    foreach ( $ht_kb_posts as $post ) {
                        //set viewcount to 0
                       update_post_meta($post->ID, HT_KB_POST_VIEW_COUNT_KEY, 0);           
                    }
                    $this->admin_notices[] = array('state'=>'success', 'text'=>__('Article views successfully updated', 'ht-knowledge-base'));
                }  else {
                    $this->admin_notices[] = array('state'=>'info', 'text'=>__('View count functionality not found', 'ht-knowledge-base'));
                }
            } catch (Exception $e) {
                $this->admin_notices[] = array('state'=>'error', 'text'=>__('Something went wrong updating article views', 'ht-knowledge-base'));
            }

        }

        function hkb_data_tools_delete_votes_for_deleted_articles(){
            global $wpdb;

            //hkb_voting
            $table_name = $wpdb->prefix . 'hkb_voting';
            if ($wpdb->get_var("SHOW tables LIKE '$table_name'") == $table_name ) {
                $result = $wpdb->query( "DELETE FROM {$table_name} WHERE post_id NOT IN (SELECT id FROM {$wpdb->posts}) " );            
                if($result){
                    $this->admin_notices[] = array('state'=>'success', 'text'=>__('Deleted articles votes have been removed', 'ht-knowledge-base'));
                } else {
                    $this->admin_notices[] = array('state'=>'error', 'text'=>__('Nothing to clean up or unable to purge deleted articles votes', 'ht-knowledge-base'));
                }
            } else {
                $this->admin_notices[] = array('state'=>'info', 'text'=>__('Voting table does not exist', 'ht-knowledge-base'));
            }

            //recalc the usefulness of articles
            $this->hkb_data_tools_recalc_usefulness();

        }


        function hkb_data_tools_recalc_usefulness(){
            try {
                if(function_exists('ht_voting_update_article_usefulness')){
                    //get all ht_kb articles
                    $args = array(
                              'post_type' => 'ht_kb',
                              'posts_per_page' => -1,
                             );
                    $ht_kb_posts = get_posts( $args );

                    //loop and update usefulness
                    foreach ( $ht_kb_posts as $post ) {
                        //upgrade if required
                       ht_voting_update_article_usefulness( $post->ID );            
                    }
                    $this->admin_notices[] = array('state'=>'success', 'text'=>__('Article usefulness successfully updated', 'ht-knowledge-base'));
                }  else {
                    $this->admin_notices[] = array('state'=>'info', 'text'=>__('Voting functionality not found', 'ht-knowledge-base'));
                }
            } catch (Exception $e) {
                $this->admin_notices[] = array('state'=>'error', 'text'=>__('Something went wrong updating article usefulness', 'ht-knowledge-base'));
            }
        }

        function hkb_data_tools_recalc_visits(){
            try {
                if(class_exists('HT_Knowledge_Base_View_Count')){
                    //get all ht_kb articles
                    $args = array(
                              'post_type' => 'ht_kb',
                              'posts_per_page' => -1,
                             );
                    $ht_kb_posts = get_posts( $args );

                    //loop and update visits
                    foreach ( $ht_kb_posts as $post ) {
                        //upgrade if required
                       apply_filters('ht_kb_set_post_views', 0, $post->ID);           
                    }
                    $this->admin_notices[] = array('state'=>'success', 'text'=>__('Article views successfully updated', 'ht-knowledge-base'));
                }  else {
                    $this->admin_notices[] = array('state'=>'info', 'text'=>__('Views functionality not found', 'ht-knowledge-base'));
                }
            } catch (Exception $e) {
                $this->admin_notices[] = array('state'=>'error', 'text'=>__('Something went wrong updating article views', 'ht-knowledge-base'));
            }
        }

        /**
        * Add activation hooks
        */
        function ht_kb_activation_hook(){
            //add a daily data cleaner task
            if ( ! wp_next_scheduled( 'hkb_data_cleaner' ) ) {
                wp_schedule_event( time(), 'daily', 'hkb_data_cleaner' );
            }
        }

        /**
        * Data cleaner (may be run as WP Cron)
        */
        function hkb_data_cleaner(){

            //data retention filter - initially 5 years
            $hkb_data_cleaner_retention_cutoff_days = apply_filters('hkb_data_cleaner_retention_cutoff_days', 5 * 365 );

            //search
            $this->hkb_data_tools_clear_old_search_data($hkb_data_cleaner_retention_cutoff_days);
            //voting
            $this->hkb_data_tools_clear_old_voting_data($hkb_data_cleaner_retention_cutoff_days);
            //exits
            $this->hkb_data_tools_clear_old_exits_data($hkb_data_cleaner_retention_cutoff_days);
            //visits
            $this->hkb_data_tools_clear_old_visits_data($hkb_data_cleaner_retention_cutoff_days);
        }

        function hkb_data_tools_clear_old_search_data($days=3650){
            global $wpdb;
            //wp_hkb_analytics_search_atomic only has datetime field
            $table_name = $wpdb->prefix . 'hkb_analytics_search_atomic';
            if ($wpdb->get_var("SHOW tables LIKE '$table_name'") == $table_name ) {
                $days = intval($days);
                if( $days && $days > 1 ){
                    $cutoff = strtotime('-' . $days . ' days' );
                    $end_sql = date('Y-m-d', $cutoff);
                    $query = "DELETE FROM {$table_name} WHERE datetime < '{$end_sql}';";
                    $result = $wpdb->query( $query );
                }         
            } else {
                //table does not exist, no action required
            }
        }

        function hkb_data_tools_clear_old_voting_data($days=3650){
            global $wpdb;
            //hkb_voting
            $table_name = $wpdb->prefix . 'hkb_voting';
            if ($wpdb->get_var("SHOW tables LIKE '$table_name'") == $table_name ) {
                $days = intval($days);
                if( $days && $days > 1 ){
                    $cutoff = strtotime('-' . $days . ' days' );
                    $end_sql = date('Y-m-d', $cutoff);
                    $query = "DELETE FROM {$table_name} WHERE datetime < '{$end_sql}';";
                    $result = $wpdb->query( $query );
                }         
            } else {
                //table does not exist, no action required
            }
        }

        function hkb_data_tools_clear_old_exits_data($days=3650){
            global $wpdb;
            //hkb_exits
            $table_name = $wpdb->prefix . 'hkb_exits';
            if ($wpdb->get_var("SHOW tables LIKE '$table_name'") == $table_name ) {
                $days = intval($days);
                if( $days && $days > 1 ){
                    $cutoff = strtotime('-' . $days . ' days' );
                    $end_sql = date('Y-m-d', $cutoff);
                    $query = "DELETE FROM {$table_name} WHERE datetime < '{$end_sql}';";
                    $result = $wpdb->query( $query );
                }         
            } else {
                //table does not exist, no action required
            }
        }

        function hkb_data_tools_clear_old_visits_data($days=3650){
            global $wpdb;
            //hkb_visits
            $table_name = $wpdb->prefix . 'hkb_visits';
            if ($wpdb->get_var("SHOW tables LIKE '$table_name'") == $table_name ) {
                $days = intval($days);
                if( $days && $days > 1 ){
                    $cutoff = strtotime('-' . $days . ' days' );
                    $end_sql = date('Y-m-d', $cutoff);
                    $query = "DELETE FROM {$table_name} WHERE datetime < '{$end_sql}';";
                    $result = $wpdb->query( $query );
                }         
            } else {
                //table does not exist, no action required
            }
        }





    }//end class

}

//run the module
if( class_exists( 'HT_Knowledge_Base_Data_Tools' ) ){
    new HT_Knowledge_Base_Data_Tools();
}