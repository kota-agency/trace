<?php
/**
* Voting module
* Database controller for voting functionality
*/

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if (!class_exists('HT_Voting_Database')) {

    if(!defined('HT_VOTING_TABLE')){
        define('HT_VOTING_TABLE', 'hkb_voting');
    }

    class HT_Voting_Database {


        //constructor
        public function __construct() {

            //activation hooks
            add_action( 'ht_kb_activate', array( $this, 'on_activate' ), 10, 1);
        }


        /**
        * Gets votes for a given article
        * @param (Int) $post_id The article post id
        * @return (Array) An array of vote objects
        */
        function ht_voting_get_votes_as_objects($post_id){
            $votes_object_array = array();

            $votes_array = $this->ht_voting_get_votes($post_id);

            foreach ($votes_array as $key => $vote_array) {
                //convert the vote array to vote object
                $vote_object = $this->create_vote_object_from_vote_array($vote_array);
                //push the vote object into the votes object array
                array_push($votes_object_array, $vote_object);
            }

            return $votes_object_array;
        }


        /**
        * Gets votes for a given article
        * @param (Int) $post_id The article post id
        * @return (Array) An array of votes (as arrays, not objects)
        */
        function ht_voting_get_votes($post_id){
            global $wpdb;

            //recast variable to help avoid injections
            $post_id = (int)$post_id;

            $get_votes_query = "SELECT *
                                FROM {$wpdb->prefix}" . HT_VOTING_TABLE  .
                                " WHERE post_id = '{$post_id}'
                                ";
            $get_votes_results = $wpdb->get_results($get_votes_query);
            $votes = array();

            foreach($get_votes_results as $vote_result) {
                $vote = array();
                //populate vote
                $vote['vote_id'] = $vote_result->vote_id;
                $vote['vote_key'] = $vote_result->vote_key;
                $vote['post_id'] = $vote_result->post_id;
                $vote['user_id'] = $vote_result->user_id;
                $vote['user_ip'] = $vote_result->user_ip;
                $vote['datetime'] = $vote_result->datetime;
                $vote['magnitude'] = $vote_result->magnitude;
                $vote['feedback'] = $vote_result->feedback;
                //add vote
                array_push($votes, $vote);
            }

            return $votes;
        }

        /**
        * Gets recent votes for all articles as objects
        * @param (Int) $limt The number of recent comments to fetch (default 10)
        * @param (Bool) $commented_only (default false)
        * @return (Array) An array of vote objects
        */
        function ht_voting_get_recent_votes_as_objects($limit=10, $commented_only=false){
            $votes_object_array = array();

            $votes_array = $this->ht_voting_get_recent_votes($limit, $commented_only);

            foreach ($votes_array as $key => $vote_array) {
                //convert the vote array to vote object
                $vote_object = $this->create_vote_object_from_vote_array($vote_array);
                //push the vote object into the votes object array
                array_push($votes_object_array, $vote_object);
            }

            return $votes_object_array;
        }

        /**
        * Gets recent votes for all articles
        * @param (Int) $limt The number of recent comments to fetch (default 10)
        * @param (Bool) $commented_only (default false)
        * @return (Array) An array of votes (as arrays, not objects)
        */
        function ht_voting_get_recent_votes($limit=10, $commented_only=false){
            global $wpdb;

            //recast variable to help avoid injections
            $limit = (int)$limit;
            $commented_only = (bool)$commented_only;

            $limit_clause = " LIMIT {$limit} ";
            $comment_clause = ($commented_only) ? " AND feedback <>  '' " : "";

            $get_votes_query = "SELECT *
                                FROM {$wpdb->prefix}" . HT_VOTING_TABLE  .
                                " WHERE 1 {$comment_clause} {$limit_clause}
                                ";
            $get_votes_results = $wpdb->get_results($get_votes_query);
            $votes = array();

            foreach($get_votes_results as $vote_result) {
                $vote = array();
                //populate vote
                $vote['vote_id'] = $vote_result->vote_id;
                $vote['vote_key'] = $vote_result->vote_key;
                $vote['post_id'] = $vote_result->post_id;
                $vote['user_id'] = $vote_result->user_id;
                $vote['user_ip'] = $vote_result->user_ip;
                $vote['datetime'] = $vote_result->datetime;
                $vote['magnitude'] = $vote_result->magnitude;
                $vote['feedback'] = $vote_result->feedback;
                //add vote
                array_push($votes, $vote);
            }

            return $votes;
        }

        /**
        * Gets votes for a given article
        * @param (Int) $post_id The article post id
        * @return (Array) An array of votes (as arrays, not objects)
        */
        function ht_voting_get_vote_by_key($post_id, $key){
            global $wpdb;

            //recast variable to help avoid injections
            $post_id = (int)$post_id;

            //review sql vulns - safe due to use of wpdb::prepare
            //manual indicates regex replacements should be unquoted
            $get_vote_query = $wpdb->prepare(
                                "SELECT *, COUNT(*) as count 
                                    FROM {$wpdb->prefix}" . HT_VOTING_TABLE  .
                                    " WHERE post_id = %d
                                    AND vote_key = %s
                                ",
                                $post_id,
                                $key
                            );

            $get_votes_results = $wpdb->get_row($get_vote_query, ARRAY_A);

            if(is_array($get_votes_results)){
                return $this->create_vote_object_from_vote_array($get_votes_results);
            } else {
                return null;
            }
            

        }

        /**
        * Create a HT_Vote object from an vote array
        * @param (Array) $vote_array Vote object as arrray
        * @return (Object) HT_Vote (or subclass)
        */
        function create_vote_object_from_vote_array($vote_array){
            //set variables
            $vote_id = $vote_array['vote_id'];
            $vote_key = $vote_array['vote_key'];
            $post_id = $vote_array['post_id'];
            $user_id = $vote_array['user_id'];
            $user_ip = $vote_array['user_ip'];
            $datetime = $vote_array['datetime']; //note this is in mysql date time 
            //convert to unix time stamp
            $unixdate = strtotime($datetime);
            $magnitude = $vote_array['magnitude'];
            $feedback = $vote_array['feedback'];

            $vote_object = null;

            if($magnitude==10){
                $vote_object = new HT_Vote_Up();
            } else {
                $vote_object = new HT_Vote_Down();
            }

            //set $vote_id, $key, $ip, $time, $user_id, $comments

            $vote_object->vote_id = $vote_id;
            $vote_object->key = $vote_key;
            $vote_object->user_id = $user_id;
            $vote_object->ip = $user_ip;
            $vote_object->time = $unixdate;
            $vote_object->comments = $feedback;

            return $vote_object;

        }

        /**
        * Deletes a particular vote
        * @param (Int) $post_id Article ID
        * @param (String) $vote_id The vote key (changed to vote id in 2.2.1+)
        */
        function delete_vote_from_database($post_id, $vote_id){
            global $wpdb;

            //review sql vulns - safe due to use of wpdb::prepare
            $wpdb->query( $wpdb->prepare(
                                "DELETE
                                    FROM {$wpdb->prefix}" . HT_VOTING_TABLE  .
                                    " WHERE post_id = '%d'
                                    AND vote_id = '%d'
                                ",
                                $post_id,
                                $vote_id
                            ) );

            $this->update_article_usefulness($post_id);
        }

        /**
        * Deletes all votes for an article
        * @param (Int) $post_id Article ID
        */
        function delete_all_article_votes_from_database($post_id){
            global $wpdb;
            
            //review sql vulns - safe due to use of wpdb::prepare
            $wpdb->query( $wpdb->prepare(
                                "DELETE
                                    FROM {$wpdb->prefix}" . HT_VOTING_TABLE  .
                                    " WHERE post_id = '%d'
                                ",
                                $post_id
                            ) );

            $this->update_article_usefulness($post_id);
        }


        /**
        * Create the hkb voting table
        */
        function ht_voting_create_table() {
            //add the table into the database
            global $wpdb;
            $table_name = $wpdb->prefix . HT_VOTING_TABLE;
            if ($wpdb->get_var("show tables like '$table_name'") != $table_name) {
              require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
              $create_hkb_voting_table_sql = "
                                                  CREATE TABLE {$table_name} (
                                                    vote_id BIGINT(20) unsigned NOT NULL AUTO_INCREMENT,
                                                    vote_key VARCHAR(32) NOT NULL,
                                                    post_id BIGINT(20) unsigned NOT NULL,
                                                    user_id BIGINT(20) unsigned NOT NULL,
                                                    user_ip VARCHAR(15) NOT NULL,
                                                    datetime DATETIME NOT NULL,
                                                    magnitude SMALLINT NOT NULL,
                                                    feedback TEXT,
                                                    PRIMARY KEY (vote_id)
                                                  )
                                                  CHARACTER SET utf8 COLLATE utf8_general_ci;
                                                  ";
              dbDelta($create_hkb_voting_table_sql);
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
                    $this->ht_voting_create_table();
                    $this->upgrade_voting();
                    restore_current_blog();
                }
            } else {
                $this->ht_voting_create_table();
                $this->upgrade_voting();
            }
        }

        /**
        * Get the usefulness of an article
        * @param (Int) $post_id Article ID
        * @return (Int) Article usefulness
        */
        function get_article_usefulness($post_id){
            return $this->get_article_voting_meta_val($post_id, 'usefulness');
        }

        /**
        * Get the upvote count
        * @param (Int) $post_id Article ID
        * @return (Int) Article upvotes count
        */
        function get_article_upvotes_count($post_id){
            return $this->get_article_voting_meta_val($post_id, 'upvotes');
        }

        /**
        * Get the downvote count
        * @param (Int) $post_id Article ID
        * @return (Int) Article downvotes count
        */
        function get_article_downvotes_count($post_id){
            return $this->get_article_voting_meta_val($post_id, 'downvotes');
        }

        /**
        * Get the allvote count
        * @param (Int) $post_id Article ID
        * @return (Int) Article allvotes count
        */
        function get_article_allvotes_count($post_id){
            return $this->get_article_voting_meta_val($post_id, 'sum');
        }



        /**
        * Get the article voting meta value for an article
        * @param (Int) $post_id Article ID
        * @param (String) $item meta to return
        * @return (Int) Article usefulness
        */
        function get_article_voting_meta_val($post_id, $item='usefulness'){
            global $wpdb;

            //recast variable to help avoid injections
            $post_id = (int)$post_id;

            $get_usefulness_query = "SELECT 
                                        SUM(magnitude) AS mag_total,
                                        COUNT(*) AS sum,
                                        SUM(CASE WHEN magnitude > 0 THEN 1 ELSE 0 END) AS upvotes,
                                        SUM(CASE WHEN magnitude = 0 THEN 1 ELSE 0 END) AS downvotes
                                        FROM {$wpdb->prefix}" . HT_VOTING_TABLE  .
                                    " WHERE post_id = '{$post_id}'
                                ";
            $mag_total =  $wpdb->get_var( $get_usefulness_query, 0 );
            $sum = $wpdb->get_var( $get_usefulness_query, 1 );
            $upvotes = $wpdb->get_var( $get_usefulness_query, 2 );
            $downvotes = $wpdb->get_var( $get_usefulness_query, 3 );

            //return 

            switch ($item) {
                case 'usefulness':
                    //calculate usefulness value
                    $usefulness = $upvotes-$downvotes;
                    return (int)$usefulness;
                    break;
                case 'upvotes':
                    //return upvotes count
                    return (int)$upvotes;
                    break;
                 case 'downvotes':
                    //return downvotes count
                    return (int)$downvotes;
                    break;
                    break;
                case 'mag':
                    //return magnitude calc
                    return (int)$mag;
                    break;
                case 'sum':
                    //return sum val
                    return (int)$sum;
                    break;
                default:
                    return 0;
                    break;
            }

            
        }

        /**
        * Updates article usefulness
        * Required to overcome issues with sorting, still need meta value on post
        * @param (Int) $post_id Article ID
        */
        function update_article_usefulness($post_id){
            update_post_meta($post_id, HT_USEFULNESS_KEY, $this->get_article_usefulness($post_id));
        }

        /**
        * Counts article votes
        * @param (Int) $post_id Article ID
        * @return (Int) number of votes
        */
        function article_votes($post_id){
            //recast variable to help avoid injections
            $post_id = (int)$post_id;
            
            $total_article_votes = "SELECT 
                                        COUNT(*) AS sum
                                        FROM {$wpdb->prefix}" . HT_VOTING_TABLE  .
                                    " WHERE post_id = '{$post_id}'
                                ";
            $count =  $wpdb->get_var( $total_article_votes, 0 );
            return (int)$count;
        }

        /**
        * Checks whether an article has votes
        * @param (Int) $post_id Article ID
        * @return (Bool) true if article has votes
        */
        function has_votes($post_id){
            $count = $this->article_votes($post_id);
            return  $count>0;
        }

        /**
        * Updates a vote with comments
        * @param (Int) $post_id Article ID
        * @param (String) $vote_key The vote key
        * @param (String) $comments Comments to add
        */
        function update_comments_for_vote($post_id, $vote_key, $comments=''){
            $vote = $this->ht_voting_get_vote_by_key($post_id, $vote_key);
            if($vote){
                //update comments
                $vote->comments = $comments;
                //save 
                $this->save_vote_for_article($post_id, $vote);
            } else {
                echo "something went wrong updating the comments for " . $vote_key;
            }
        }


        /**
        * Create a HT_Vote object from an vote array
        * @param (Int) $post_id Article ID
        * @param (Object) $vote Vote object
        */
        function save_vote_for_article($post_id, $vote=null){
            global $wpdb;

            //check vote is valid
            if(empty($vote)){
                return;
            }                

            //santize
            $vote_id =  (isset($vote->vote_id)) ? $vote->vote_id : 'NULL';
            $user_id =  (isset($vote->user_id)) ? $vote->user_id : 0;
            $user_ip =  (isset($vote->ip)) ? $vote->ip : 'none';
            //datetime
            $datetime = (isset($vote->time)) ? $vote->time : time();
            //convert for sql
            $mysqltime = date ("Y-m-d H:i:s", $datetime);
            $magnitude = (isset($vote->magnitude)) ? $vote->magnitude : 0;
            $feedback = (isset($vote->comments)) ? $vote->comments : '';
            //compute vote_key, careful this duplicates functionality in vote class
            $vote_key = (isset($vote->key)) ? $vote->key : md5( strval($magnitude) . $user_ip . $datetime . $user_id );


            //$key, $magnitude, $ip, $time, $user_id, $comments
            //Save vote into the db
            //review sql vulns - safe due to use of wpdb::prepare
            $query = "";
            if($vote_id=='NULL'){
                //new/insert
                $prepared_query = $wpdb->prepare("INSERT INTO {$wpdb->prefix}" . HT_VOTING_TABLE .   " ( vote_id ,  vote_key, post_id, user_id, user_ip, datetime, magnitude, feedback )
                VALUES ('%d', '%s', '%d', '%s', '%s', '%s', '%d', '%s')",
                    $vote_id,
                    $vote_key,
                    $post_id,
                    $user_id,
                    $user_ip,
                    $mysqltime,
                    $magnitude,
                    $feedback
                );
            } else {
                //update
                $prepared_query = $wpdb->prepare( "UPDATE {$wpdb->prefix}" . HT_VOTING_TABLE .   "
                SET  vote_key = '%s', post_id = '%d', user_id = '%s', user_ip = '%s', datetime = '%s', magnitude = '%d', feedback = '%s'
                WHERE vote_id = '%d'",
                    $vote_key,
                    $post_id,
                    $user_id,
                    $user_ip,
                    $mysqltime,
                    $magnitude,
                    $feedback,
                    $vote_id
                );
            }
            
            $run_query = $wpdb->query($prepared_query);

            $this->update_article_usefulness($post_id);

            return $vote;
        }


        /**
        * Convert post meta to table entities
        * @param (Int) $post_id Article ID
        */
        function convert_article_votes($post_id){
            //get existing votes
            $votes = (array) get_post_meta( $post_id, HT_VOTING_KEY, false);

            foreach ($votes as $key => $vote) {
                $this->save_vote_for_article($post_id, $vote);
            }
        }

        /**
        * Upgrade article
        * @param (Int) $post_id Article ID
        */
        function upgrade_article($post_id){
            //convert
            $this->convert_article_votes($post_id);

            //delete old postmeta
            delete_post_meta( $post_id, HT_VOTING_KEY);
        }


        /**
        * Upgrade Knowledge Base install - hook to ht_kb_activate
        * @param (Int) $post_id Article ID
        */
        function upgrade_voting(){
            $kb_articles_args = array( 'post_type' => 'ht_kb', 'posts_per_page' => -1, 'post_status' => 'any' );
            $kb_articles = get_posts($kb_articles_args);

            foreach ($kb_articles as $key => $article) {
               $this->upgrade_article($article->ID);
            }
        }

    }

} //end if class_exist