<?php
/**
* Analytics module
* Test data creator for analytics
*
*/

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'HKB_Analytics_Test_Data' )) {
    class HKB_Analytics_Test_Data {
    
        function __construct() {
            add_action( 'admin_init' , array( $this, 'add_test_data' ));
        }

        function add_test_data(){
            global $wpdb;
            $action = (isset($_GET['add_test_data']) && $_GET['add_test_data']) ? sanitize_text_field($_GET['add_test_data']) : '';
            if('add'===$action){
                $count = (isset($_GET['count']) && $_GET['count']) ? intval($_GET['count']) : '500';
                $count = intval($count);
                $search_terms = array('test', 'search', 'string', 'great', 'nice', 'result', 'water', 'bottle', 'charm');
                $search_terms_len = count($search_terms);
                //id, terms, datetime, hits
                // Save search into the db
                for ($i=0; $i < $count; $i++) { 
                    $id = NULL;
                    $search_term_1_index = rand(0, $search_terms_len-1);
                    $search_term_1 = $search_terms[(int)$search_term_1_index];
                    $search_term_2_index = rand(0, $search_terms_len-1);
                    $search_term_2 = $search_terms[(int)$search_term_2_index];
                    $search_term = $search_term_1 . ' ' . $search_term_2;
                    $rand_days = rand(1, 365);
                    $timestamp = strtotime('-' . $rand_days .' days');
                    $rand_time = date( 'Y-m-d H:i:s', $timestamp);
                    $rand_hits = rand(0, 10);
                    $query = "INSERT INTO {$wpdb->prefix}hkb_analytics_search_atomic ( id ,  terms , datetime , hits )
                    VALUES (NULL, '{$search_term}', '{$rand_time}', {$rand_hits})";
                    //echo $query;
                    //echo "<br/><br/>";
                    $run_query = $wpdb->query($query);
                }

                echo 'inserted ' . $i . ' records';            
                    
            }
        }

        function add_test_votes(){

            //iterate kb articles
            //vote_post($post_id, $direction);

            //add X number of votes

            //add Y number of comments to votes
        }
    }
}

if( class_exists( 'HKB_Analytics_Test_Data' )) {
    //don't initialize for build
    $ht_hkb_test_data_init = new HKB_Analytics_Test_Data();
}