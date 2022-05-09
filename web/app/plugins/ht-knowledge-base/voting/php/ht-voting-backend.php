<?php
/**
* Voting module
* Backend functionality metaboxes etc
*/

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if (!class_exists('HT_Voting_Backend')) {

    class HT_Voting_Backend {

        //constructor
        public function __construct() {
            add_action( 'add_meta_boxes_ht_kb', array( $this, 'adding_article_votes_meta_boxes' ) );
            add_action( 'admin_head', array( $this, 'ht_voting_backend_head' ) );

            //ajax filters
            add_action( 'wp_ajax_ht_voting_delete_vote', array( $this, 'ht_ajax_voting_delete_vote_callback' ) );
        }

        /**
        * Add the meta boxes
        */
        function adding_article_votes_meta_boxes( $post ) {
            //enqueue script
            wp_enqueue_script( 'data-tables-script', plugins_url( 'js/jquery.dataTables.js', dirname( __FILE__ ) ), array('jquery'), '1.10.8', true );
            $ht_voting_backend_js_src = (HKB_DEBUG_SCRIPTS) ? 'js/ht-voting-backend-js.js' : 'js/ht-voting-backend-js.min.js';
            wp_enqueue_script( 'ht-voting-backend', plugins_url( $ht_voting_backend_js_src, dirname( __FILE__ ) ), array('jquery', 'data-tables-script'), HT_KB_VERSION_NUMBER, true );
            //localize
            wp_localize_script( 'ht-voting-backend', 'voting', array( 
                        'spinner' => admin_url( 'images/wpspin_light.gif' ),
                        'ajaxurl' => admin_url( 'admin-ajax.php' ), 
                        'ajaxnonce' => wp_create_nonce('ht-voting-ajax-nonce'),
                        'deleteChallenge' => __('Are you sure you want to delete this vote?', 'ht-knowledge-base')
                    ));

            //add meta box
            add_meta_box( 
                'ht-voting-display',
                __( 'Votes and Feedback', 'ht-knowledge-base'),
                array($this, 'render_votes_and_feedback'),
                'ht_kb',
                'normal',
                'high'
            );

        }

        /**
        * Render the votes and feedback metaboxes
        */
        function render_votes_and_feedback($post){
            $this->render_article_votes($post);
            $this->render_article_vote_actions($post);
        }

        /**
        * HT Voting Backend Head
        */
        public function ht_voting_backend_head(){
            global $_GET, $post;

            $action = array_key_exists('ht-voting-action', $_GET) ? $_GET['ht-voting-action'] : '';
            $nonce = array_key_exists('nonce', $_GET) ? $_GET['nonce'] : '';

             if('recalc'==$action){
                if ( ! wp_verify_nonce( $nonce, 'ht-voting-recalc' ) ) {
                    die( 'Security check' ); 
                } else {
                    ht_voting_update_article_usefulness($post->ID);
                    return;
                }                
            } 

            if('deleteall'==$action){
                if ( ! wp_verify_nonce( $nonce, 'ht-voting-deleteall' ) ) {
                    die( 'Security check' ); 
                } else {
                    //re-add this 
                    ht_voting_delete_all_post_votes($post->ID);
                    return;
                }
            }       
        }

        /**
        * Render the votes for an article
        * @param (Object) $post The post/article to render the vote for
        */
        function render_article_votes($post){
            $post_id = $post->ID;

            //get votes
            echo '<table class="ht-voting-backend-vote-list wp-list-table widefat fixed striped">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>' . __('Rating', 'ht-knowledge-base') . '</th>';
            echo '<th>' . __('Date', 'ht-knowledge-base') . '</th>';
            echo '<th>' . __('User', 'ht-knowledge-base') . '</th>';
            echo '<th>' . __('Comments', 'ht-knowledge-base') . '</th>';
            echo '<th>' . __('Options', 'ht-knowledge-base') . '</th>';
            echo '</tr>';
            echo '</thead>';
            

            //upgrade check
            $votes = ht_voting_get_post_votes($post_id);
            foreach ($votes as $key => $vote) {
                if(property_exists($vote, 'key') && $vote->key){
                    //no upgrade required
                } else {
                    //perform upgrade
                    ht_kb_voting_upgrade_votes($post_id);
                    break;

                }
            }

            //reassign
            $votes = ht_voting_get_post_votes($post_id);            
            foreach ($votes as $key => $vote) {
                if(property_exists($vote, 'key') && $vote->key){
                    $key = $vote->key;
                } else {
                    delete_post_meta($post_id, HT_VOTING_KEY, $vote);
                    $vote = $this->upgrade_vote($key, $post_id, $vote);
                    $key = $vote->key;
                }
                $vote_id = $vote->vote_id;
                $key = $vote->key;
                $rating = $vote->magnitude;
                $delete_link = admin_url('post.php?post=' . $post->ID . '&action=edit');
                $delete_href = '<button class="ht-voting-delete-vote button" data-vote-id="' . $vote_id . '" data-vote-key="' . $key . '" data-post-id="' . $post_id . '" href="' . $delete_link . '">' . __('Delete This Vote', 'ht-knowledge-base') . '</button>';
                if($rating==10){
                    $rating = __('Up', 'ht-knowledge-base');
                } else {
                    $rating = __('Down', 'ht-knowledge-base');
                }
                $date_order = $vote->time;
                $date = date('H:i d/m/Y',$vote->time);
                $user_id = $vote->user_id;
                if(apply_filters('hkb_get_user_ip_anonymise', true)){
                    $user = __('Guest', 'ht-knowledge-base');
                } else {
                    $user = $vote->ip;
                }                
                if(''!=$user_id && 0!=$user_id){
                    $user_info = get_userdata($user_id);
                    $user_name = $user_info->user_login;
                    $user = '<a href="' . get_edit_user_link($user_id) . '" target="_blank">' . $user_name . '</a>';
                }
                $comments = '';
                if(property_exists($vote, 'comments') && $vote->comments){
                    $comments = $vote->comments;
                }
                echo '<tr>';
                echo '<td>' . $rating . '</td>';
                echo '<td data-order="' . $date_order .'">' . $date . '</td>';
                echo '<td>' . $user . '</td>';
                echo '<td>' . stripslashes( $comments ) . '</td>';
                echo '<td>' . $delete_href . '</td>';
                echo '</tr>';
            }

            echo '</table>';
        }

        /*
        * @deprecated - now using activation hook
        */
        function upgrade_vote($key, $post_id, $vote){
            //function to upgrade vote
            //delete old vote
            delete_post_meta($post_id, HT_VOTING_KEY, $vote);
            //set key and comments
            $key = md5( strval($vote->magnitude) . $vote->ip . $vote->time . $vote->user_id );
            $vote->key = $key;
            $vote->comments = '';
            //add vote
            add_post_meta($post_id, HT_VOTING_KEY, $vote);

        }


        /**
        * Delete vote ajax callback
        */
        function ht_ajax_voting_delete_vote_callback(){
            global $_POST;
            $vote_id = array_key_exists('vid', $_POST) ? sanitize_text_field($_POST['vid']) : '';
            //@depected -now only needs vote_id
            $vote_key = array_key_exists('key', $_POST) ? sanitize_text_field($_POST['key']) : '';
            $post_id = array_key_exists('pid', $_POST) ? sanitize_text_field($_POST['pid']) : '';
            $nonce = isset($_POST['nonce']) ? $_POST['nonce'] : '';
            
            if(!empty($vote_key) && !empty($post_id) && wp_verify_nonce($nonce, 'ht-voting-ajax-nonce' )){
               //vote delete
               ht_voting_delete_vote($vote_id, $post_id); 
               echo 'ok';
            } else {
                echo 'Security check';
            }
            
            die(); // this is required to return a proper result
        }


        /**
        * Article voting actions
        */
        function render_article_vote_actions($post){
            //re-calculate usefulness
            $recalc_usefulness_url = admin_url('post.php?post=' . $post->ID . '&action=edit' . '&ht-voting-action=recalc' . '&nonce=' . wp_create_nonce( 'ht-voting-recalc' ) );
            //delete all votes
            $delete_all_votes_url = admin_url('post.php?post=' . $post->ID . '&action=edit' . '&ht-voting-action=deleteall' . '&nonce=' . wp_create_nonce( 'ht-voting-deleteall' ) );
            echo '<div class="ht-voting-article-voting-actions">';
                echo '<button class="ht-voting-recalc-article-usefulness button" href="' . $recalc_usefulness_url . '" data-challenge="' . __('Are you sure you want to recalculate the article usefulness, this will override any manual setting?', 'ht-knowledge-base') .'" style="margin-right: 5px;">' . __('Recalculate Usefulness', 'ht-knowledge-base') . '</button>';
                echo '<button class="ht-voting-delete-all-votes button" href="' . $delete_all_votes_url . '" data-challenge="' . __('Are you sure you want to delete all votes?', 'ht-knowledge-base') .'">' . __('Delete All Votes', 'ht-knowledge-base') . '</button>';
            echo '</div> <!-- /ht-voting-article-voting-actions -->';
        }

    }

}

//run the module
if(class_exists('HT_Voting_Backend')){
    $ht_voting_backend_init = new HT_Voting_Backend();
}