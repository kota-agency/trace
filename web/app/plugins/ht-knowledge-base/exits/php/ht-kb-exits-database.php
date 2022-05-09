<?php
/**
* Exits module
* Database controller for exits functionality
*/

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if (!class_exists('HT_KB_Exits_Database')) {

    if(!defined('HT_KB_EXITS_TABLE')){
        define('HT_KB_EXITS_TABLE', 'hkb_exits');
    }

    class HT_KB_Exits_Database {


        //constructor
        public function __construct() {

            //activation hooks
            add_action( 'ht_kb_activate', array( $this, 'on_activate' ), 10, 1);

        }

        /**
        * Add tracked exit
        * @param (Array) $data The data to add
        */
        function add_tracked_exit_to_db($data){
            global $wpdb;
            //new/insert
            $exit_id =  (isset($data['exit_id'])) ? $data['exit_id'] : 'NULL';
            $object_type = (isset($data['object_type'])) ? $data['object_type'] : 'undefined';
            $object_id = (isset($data['object_id'])) ? $data['object_id'] : 0;
            $source = (isset($data['source'])) ? $data['source'] : 'undefined';
            $datetime = (isset($data['datetime'])) ? $data['datetime'] : time();
            $url = (isset($data['url'])) ? $data['url'] : '';

            //user ip 
            $user_ip = hkb_get_user_ip();

            //user_id
            $user_id = hkb_get_current_user_id();
            

            //convert for sql
            $mysqltime = date ("Y-m-d H:i:s", $datetime);


            //exit_id,  object_type, object_id, user_id, user_ip, datetime, duration
            $query = "INSERT INTO {$wpdb->prefix}" . HT_KB_EXITS_TABLE .   " ( exit_id ,  object_type, object_id, source, user_id, user_ip, datetime, url)
            VALUES ($exit_id, '$object_type', $object_id, '$source', $user_id, '$user_ip', '$mysqltime', '$url')";

            //run the query
            $run_query = $wpdb->query($query);
        }

        /**
        * Create the hkb exits table
        */
        function ht_kb_exits_create_database_table() {
            //add the table into the database
            global $wpdb;
            $table_name = $wpdb->prefix . HT_KB_EXITS_TABLE;
            if ($wpdb->get_var("show tables like '$table_name'") != $table_name) {
              require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
              $create_hkb_exits_table_sql = "
                                                  CREATE TABLE {$table_name} (
                                                    exit_id BIGINT(20) unsigned NOT NULL AUTO_INCREMENT,
                                                    object_type VARCHAR(50) NOT NULL,
                                                    object_id bigint(20) unsigned NOT NULL,
                                                    source VARCHAR(50),
                                                    user_id BIGINT(20) unsigned NOT NULL,
                                                    user_ip VARCHAR(15) NOT NULL,
                                                    datetime DATETIME NOT NULL,
                                                    url VARCHAR(2083) NOT NULL,
                                                    PRIMARY KEY (exit_id)
                                                  )
                                                  CHARACTER SET utf8 COLLATE utf8_general_ci;
                                                  ";
              dbDelta($create_hkb_exits_table_sql);
            }
        }

        /**
        * On Active function, hook to ht_kb_activate
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
                    $this->ht_kb_exits_create_database_table();
                    restore_current_blog();
                }
            } else {
                $this->ht_kb_exits_create_database_table();
            }
        }


    }

} //end if class_exist