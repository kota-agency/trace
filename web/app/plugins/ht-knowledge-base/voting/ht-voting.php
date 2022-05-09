<?php
/**
* Voting module
*
*/

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists('HT_Voting') ){

	if(!defined('HT_VOTING_KEY')){
		define('HT_VOTING_KEY', '_ht_voting');
	}

	class HT_Voting {	

		private $add_script, $new_vote;	

		//constructor
		function __construct(){
			$this->add_script = false;

			add_action( 'init', array( $this, 'register_ht_voting_shortcode_scripts_and_styles' ) );
			add_action( 'wp_footer', array( $this, 'print_ht_voting_shortcode_scripts_and_styles' ) );
			add_action( 'ht_voting_post', array( $this , 'ht_voting_post_action' ) );

			//admin init hook
			//must be before scripts and styles are enqueued
        	add_action( 'admin_init', array( $this, 'admin_init' ), 5 );

			//display voting
			add_action( 'ht_kb_end_article', array($this, 'ht_voting_display_voting' ) );

			//ajax filters
        	add_action( 'wp_ajax_ht_voting', array( $this, 'ht_ajax_voting_callback' ) );
        	add_action( 'wp_ajax_nopriv_ht_voting', array( $this, 'ht_ajax_voting_callback' ) );
        	add_action( 'wp_ajax_ht_voting_update_feedback', array( $this, 'ht_ajax_voting_update_feedback_callback' ) );
        	add_action( 'wp_ajax_nopriv_ht_voting_update_feedback', array( $this, 'ht_ajax_voting_update_feedback_callback' ) );
			include_once('php/ht-vote-class.php');
			//meta-boxes
			include_once('php/ht-voting-meta-boxes.php');
			//voting backend
			include_once('php/ht-voting-backend.php');

			//database controller
			include_once('php/ht-voting-database.php');
			$this->voting_database = new HT_Voting_Database();

			//add activation action for table
            add_action( 'ht_kb_activate', array( $this, 'on_activate' ), 10, 1);

		}

        /** 
        * Admin init actions
        */
        function admin_init(){
            //dummy data creator, for testing
			if( apply_filters( 'hkb_debug_mode', false ) ){
				include_once('php/ht-voting-dummy-data-creator.php');
			}
        }

		/**
		* Activation functions
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
                    $this->ht_kb_voting_activation_upgrade_actions();
                    restore_current_blog();
                }
            } else {
                $this->ht_kb_voting_activation_upgrade_actions();
            }
        }


		/**
		* Function to loop through all the existing ht_kb articles a perform any upgrade actions
		*/
		function ht_kb_voting_activation_upgrade_actions(){
			//upgrade - set initial meta if required

			//get all ht_kb articles
			$args = array(
					  'post_type' => 'ht_kb',
					  'posts_per_page' => -1,
					 );
			$ht_kb_posts = get_posts( $args );

			//loop and ugrade
			foreach ( $ht_kb_posts as $post ) {
				//upgrade if required
			   ht_kb_voting_upgrade_votes( $post->ID );			   
			}
		}

		/**
		* Static function to perform upgrade actions on an individual article
		* @param (Int) $post_id ID of article to upgrade
		*/
		function ht_kb_voting_upgrade_votes($post_id){
			//get old votes
			$votes = get_post_meta($post_id, HT_VOTING_KEY);
			//delete old votes
			delete_post_meta($post_id, HT_VOTING_KEY);
			foreach ($votes as $key => $vote) {
				$key = md5( strval($vote->magnitude) . $vote->ip . $vote->time . $vote->user_id );
	            $vote->key = $key;
	            //initiate comments if not set
	            if(!property_exists($vote, 'comments')){
					$vote->comments = '';
	            }	            
	            //add vote
	            add_post_meta($post_id, HT_VOTING_KEY, $vote);
			}

		}

		/**
		* Voting post action
		*/
		function ht_voting_post_action(){
			global $post;
			//action used so scripts and styles required
			$this->add_script = true;
			//todo - allow needs hooking back in
			$this->ht_voting_post_display($post->ID);
		}

		/**
		* Display a vote
		* @param (Int) $post_id
		* @param (String) $allow
		* @param (String) $display
		*/
		function ht_voting_post_display($post_id, $allow='user', $display='standard', $vote=null){
				//cast post id
				$post_id = (int)$post_id;
				//strip $allow
				$allow = sanitize_text_field($allow);
				//strip $display
				$display = sanitize_text_field($display);
				//get votes so far
				$votes = $this->get_post_votes($post_id);
				//override allow (2.11.2)
				$allow = ht_kb_voting_enable_anonymous() ? 'anon' : $allow;
				//filter (2.11.2)
				$allow = apply_filters( 'ht_voting_post_display_allow', $allow, $post_id, $vote );
			?>
				<div class="ht-voting" id ="ht-voting-post-<?php echo $post_id ?>">
					<?php $this->ht_voting_post_render($post_id, $allow, $votes, $display, $vote); ?>
				</div>
			<?php
		}


		/**
		 * Get post votes
		 * @param (Int) $post_id The post id for the votes to fetch
		 * @return (Array) Vote objects array
		 */
		function get_post_votes($post_id){
			$votes = $this->voting_database->ht_voting_get_votes_as_objects($post_id);
			return $votes;
		}


		/**
		* Render the voting for a post
		* @param (Int) $post_id The post id
		* @param (String) $allow Whether to allow anonymous voting ('anon')
		* @param (Array) $votes An array of existing votes
		* @param (String) $display How the voting display should be rendered
		* @param (Object) $new_vote The vote that has just been made (or null if first render)
		*/
		function ht_voting_post_render($post_id, $allow, $votes, $display='standard', $new_vote=null){
			global $voting_nonce, $feedback_nonce;

			$GLOBALS['post_id'] = $post_id;
			$GLOBALS['new_vote'] = $new_vote;

			//enqueue script
			wp_enqueue_script( 'ht-voting-frontend-script' ); 
 

			$voting_nonce = ( $allow!='anon' && !is_user_logged_in() ) ? '' : wp_create_nonce('ht-voting-post-ajax-nonce');
			$feedback_nonce = ( $allow!='anon' && !is_user_logged_in() ) ? '' : wp_create_nonce('ht-voting-feedback-ajax-nonce');
			

			?>
			
			<?php hkb_get_template_part('hkb-voting-form'); ?>

			<?php
		}

		/**
		* Get the voting link
		* @param (String) $direction The direction up/down
		* @param (Int) $post_id The id of the post for the voting link
		* @param (String) $allow Whether to allow anonymous voting ('anon')
		*/
		function vote_post_link($direction, $post_id, $allow='anon'){
			$bookmark = 'ht-voting-post-'.$post_id;
			if($allow!='anon' && !is_user_logged_in())
				return '?' . '#' . $bookmark ;
			$security = wp_create_nonce( 'ht-post-vote' );
			return '?' . 'vote=' . $direction . '&post=' . $post_id . '&_htvotenonce=' . $security . '#' . $bookmark ;
		}


		/**
		* Get a post vote for a user
		* @param (Int) $post_id The post_id to get the user vote for
		* @param (Array) $votes Existing vote array object to search for first (otherwise load post meta)
		* @return (Object) Vote object
		*/
		function get_users_post_vote($post_id, $votes=null){
			//create a dummy vote to compare
			if(class_exists('HT_Vote_Up')){
				$comp_vote = new HT_Vote_Up();
			} else {
				return;
			}
			//get all votes
			$votes = $this->voting_database->ht_voting_get_votes_as_objects($post_id);
			//loop through and compare users vote
			if($votes && !empty($votes)){
				foreach ($votes as $key => $vote) {
					//if user id is same (and not 0), return vote
					if( $vote->user_id > 0 && $vote->user_id == $comp_vote->user_id )
						return $vote;
					//if user not logged in and ip is same, return vote
					if( $vote->user_id == 0 && $vote->ip == $comp_vote->ip )
						return $vote;
					//else try next one
					continue;
				}
			} else {
				return;
			}
		}

		/**
		* Get the direction of the post vote for the user
		* @param (Int) $post_id The post_id to get the user vote for
		* @return (String) Vote direction
		*/
		function get_users_post_vote_direction($post_id){
			$user_vote = $this->get_users_post_vote($post_id);

			$user_vote_direction = 'none';

			if( is_a( $user_vote, 'HT_Vote_Up' ) )
				$user_vote_direction = 'up';

			if( is_a( $user_vote, 'HT_Vote_Down' ) )
				$user_vote_direction = 'down';	

			return $user_vote_direction;
		}


		/**
		* Get the new vote just made
		* @return (Object) New vote
		*/
		function get_new_vote(){
			return $this->new_vote;	
		}

		/**
		* Whether to show the feedback form
		* @return (Bool) state
		*/
		function show_feedback_form(){
			if ( 	( is_a($this->new_vote, 'HT_Vote_Up') && ht_kb_voting_upvote_feedback() ) || 
            		( is_a($this->new_vote, 'HT_Vote_Down') && ht_kb_voting_downvote_feedback() ) 
            ) {
				return true;
            } else {
            	return false;
            }            
		}


		/**
		* Test whether the user has voted
		* @param (Int) $post_id The post_id to get the user vote for
		* @param (Array) $votes Existing vote array object to search for first (otherwise load post meta)
		* @return (Bool) True when user has already voted
		*/
		function has_user_voted($post_id, $votes=null){
			$user_vote = $this->get_users_post_vote( $post_id, $votes );
			$voted = (empty( $user_vote )) ? false : true;
			return $voted;
		}

		/**
	    * Register scripts and styles
	    */
	    public function register_ht_voting_shortcode_scripts_and_styles(){			

	    	if(SCRIPT_DEBUG){
				$ht_voting_frontend_js_src = 'js/ht-voting-frontend-js.js';
				wp_register_script( 'ht-voting-frontend-script', plugins_url( $ht_voting_frontend_js_src, __FILE__ ), array('jquery') , HT_KB_VERSION_NUMBER, true );
				$this->ht_knowledge_base_localize_voting_scripts('ht-voting-frontend-script');
			} else {
				wp_register_script('ht-kb-frontend-scripts', plugins_url( 'dist/ht-kb-frontend.min.js' , HT_KB_MAIN_PLUGIN_FILE ), array( 'jquery' ), HT_KB_VERSION_NUMBER, true);
				$this->ht_knowledge_base_localize_voting_scripts('ht-kb-frontend-scripts');
			}           
				
	    }

	    /**
	    * Localize scripts
	    */
	    function ht_knowledge_base_localize_voting_scripts($script_handle){
			wp_localize_script( $script_handle, 'voting', array( 
					'ajaxurl' => admin_url( 'admin-ajax.php' ), 
					'ajaxnonce' => wp_create_nonce('ht-voting-ajax-nonce') 
			    ));  
		}

	    /**
	    * Print scripts and styles
	    */
	    public function print_ht_voting_shortcode_scripts_and_styles(){
	    	global $ht_kb_frontend_scripts_loaded;
			if ( ! $this->add_script )
				return;

			if(SCRIPT_DEBUG){
				wp_print_styles( 'ht-voting-frontend-style' );
			} else {
				if(!$ht_kb_frontend_scripts_loaded){
					wp_print_scripts('ht-kb-frontend-scripts');
					$ht_kb_frontend_scripts_loaded = true;
				}
			}         
	    }

	   /**
	    * Ajax voting callback 
	    */
	    public function ht_ajax_voting_callback(){
	        global $_POST;
	    	$direction = array_key_exists('direction', $_POST) ? sanitize_text_field($_POST['direction']) : '';
	    	//type - either post or comment
	    	$type = array_key_exists('type', $_POST) ? sanitize_text_field($_POST['type']) : '';
	    	$nonce = isset($_POST['nonce']) ? $_POST['nonce'] : '';
	    	$post_id = array_key_exists('id', $_POST) ? sanitize_text_field($_POST['id']) : '';
	    	$vote_allow = array_key_exists('allow', $_POST) ? sanitize_text_field($_POST['allow']) : '';
	    	$vote_display = array_key_exists('display', $_POST) ? sanitize_text_field($_POST['display']) : '';

	        if(!empty($direction)){
	    			if( $type=='post' ){
	    				 if ( ! wp_verify_nonce( $nonce, 'ht-voting-post-ajax-nonce' ) ){
	    				 	die( 'Security check - voting callback' );
	    				 } else {
	    				 	//vote	post    		
			    			$this->new_vote = $this->vote_post($post_id, $direction);
							$this->ht_voting_post_display($post_id);

	    				 }	
			    	}		
	    	}	  
	        die(); // this is required to return a proper result
	    }

	   /**
	    * Ajax add feedback callback
	    */
	    public function ht_ajax_voting_update_feedback_callback(){
	        global $_POST;
	    	$vote_key = array_key_exists('key', $_POST) ? sanitize_text_field($_POST['key']) : '';
	    	$post_id = array_key_exists('id', $_POST) ? sanitize_text_field($_POST['id']) : '';
	    	$nonce = isset($_POST['nonce']) ? $_POST['nonce'] : '';
	    	$comment = array_key_exists('comment', $_POST) ? sanitize_text_field($_POST['comment']) : '';
	        if(!empty($vote_key)){
				 if ( ! wp_verify_nonce( $nonce, 'ht-voting-feedback-ajax-nonce' ) ){
				 	die( 'Security check - update feedback callback' );
				 } else {
				 	do_action('ht_ajax_voting_update_feedback');
				 	//add feedback to vote
				 	$this->ht_voting_add_vote_comment($vote_key, $post_id, $comment);
				 	_e('Thanks for your feedback', 'ht-knowledge-base');
				 }				    	
	    	}	  
	        die(); // this is required to return a proper result
	    }

	    /**
		* Add vote comments/feedback
		* Filterable ($comment) - 'ht_voting_add_vote_comment_filter', $comment, $vote, $post_id
		* Action hook - ht_voting_add_vote_comment_action
		* @param (String) $vote_key The vote key
		* @param (Int) $post_id The post id
		* @param (String) $comment Comments/Feedback to add to vote
		*/
		function ht_voting_add_vote_comment($vote_key, $post_id, $comment=''){
			$vote = $this->get_users_post_vote_by_key($post_id, null, $vote_key);
			if(isset($vote)){
				$comment = apply_filters('ht_voting_add_vote_comment_filter', $comment, $vote, $post_id);
				$this->voting_database->update_comments_for_vote($post_id, $vote_key, $comment);
				do_action('ht_voting_add_vote_comment_action', $comment, $vote, $post_id);
			} else {
				_e('Cannot retrieve vote', 'ht-knowledge-base');
				echo $vote_key;
			}
		}

	   /**
	    * Perform the voting action for a post
	    * @param (Int) $post_id The post id to add a vote to
	    * @param (String) $direction Direction of vote up/down/neutral
	    */
	    public function vote_post($post_id, $direction){
	    	//cast post id
	    	$post_id = (int)$post_id;
	    	//get the users vote and delete it
	    	$user_previous_vote = $this->get_users_post_vote($post_id);

	    	switch($direction){
	    		case 'up':
	    			if(class_exists('HT_Vote_Up')){
	    				$new_vote = new HT_Vote_Up();
	    			}
	    			break;
	    		case 'down':
	    			if(class_exists('HT_Vote_Down')){
	    				$new_vote = new HT_Vote_Down();
	    			}
	    			break;
	    		case 'neutral':
	    			if(class_exists('HT_Vote_Neutral')){
	    				$new_vote = new HT_Vote_Neutral();
	    			}
	    			break;
	    		default:
	    			//numeric value
	    			if(is_numeric($direction)&&class_exists('HT_Vote_Value')){
						$new_vote_val = intval($direction);
						$new_vote = new HT_Vote_Value( $new_vote_val );
	    			}
	    			break;
	    	}

	    	//set the vote
	    	if($user_previous_vote){
	    		$users_vote = $user_previous_vote;
	    		$user_previous_vote->magnitude = $new_vote->magnitude;
	    	} else {
	    		$users_vote = $new_vote;
	    	}

	    	//apply filters
	    	$users_vote = apply_filters('ht_voting_vote_post_filter', $users_vote, $post_id, $direction);

	    	//call database save_vote_for_article
	    	$users_vote = $this->voting_database->save_vote_for_article($post_id, $users_vote);

	    	//do actions
	    	do_action('ht_voting_vote_post_action', $users_vote, $post_id, $direction);

	    	//return the vote just made
	    	if(is_a($users_vote, 'HT_Vote')){
	    		return $users_vote;
	    	}
	    }

	    /**
		 * Get the article usefulness
		 * @param (Int) $post_id The post id
		 * @return (Int) The usefulness rating (dynamic)
		 */
	    function get_article_usefulness($post_id){
	    	return $this->voting_database->get_article_usefulness($post_id);
	    }

	    /**
        * Get the upvote count
        * @param (Int) $post_id Article ID
        * @return (Int) Article upvotes count
        */
        function get_article_upvotes_count($post_id){
            return $this->voting_database->get_article_upvotes_count($post_id);
        }

        /**
        * Get the downvote count
        * @param (Int) $post_id Article ID
        * @return (Int) Article downvotes count
        */
        function get_article_downvotes_count($post_id){
            return $this->voting_database->get_article_downvotes_count($post_id);
        }

        /**
        * Get the allvote count
        * @param (Int) $post_id Article ID
        * @return (Int) Article allvotes count
        */
        function get_article_allvotes_count($post_id){
            return $this->voting_database->get_article_allvotes_count($post_id);
        }

	    /**
		 * Upgrade the meta key values
		 * @param (Int) $post_id The post id being upgraded
		 */
		public static function ht_voting_upgrade_post_meta_fields($postID){
			//keys to be upgraded
			HT_Voting::ht_voting_upgrade_voting_meta_fields($postID, 'voting_checkbox');
			HT_Voting::ht_voting_upgrade_voting_meta_fields($postID, 'voting_reset');
			HT_Voting::ht_voting_upgrade_voting_meta_fields($postID, 'voting_reset_confirm');
		}

	    /**
		 * Upgrade a post meta field
		 * @param (String) $name The name of the meta field to be upgraded
		 */
		static function ht_voting_upgrade_voting_meta_fields($postID, $name){
			$old_prefix = '_ht_knowledge_base_';
			$new_prefix = '_ht_voting_';

			//get the old value
			$old_value = get_post_meta($postID, $old_prefix . $name, true);
			if(!empty($old_value)){
				//get the new value
				$new_value = get_post_meta($postID, $new_prefix . $name, true);
				if(empty($new_value)){
					//sync the new value to the old value
					update_post_meta($postID, $new_prefix . $name, $old_value);
				}
				
			}
			//delete old meta key
			delete_post_meta($postID, $old_prefix . $name);
		}

		/**
		* Display voting
		*/
		function ht_voting_display_voting(){
			$voting_disabled =  get_post_meta( get_the_ID(), '_ht_voting_voting_disabled', true );
			$allow_voting_on_this_article = $voting_disabled ? false : true;

			$allow_voting_on_this_article = apply_filters( 'ht_voting_display_voting_on_article', $allow_voting_on_this_article, get_the_ID() );
		
			// voting
			if( ht_kb_voting_enable_feedback() && $allow_voting_on_this_article ){ 
				hkb_get_template_part('hkb-voting-single');
			}
				
			
		}

		/**
		* Get a vote by key
		* @param (Int) $post_id The post_id to get the user vote for
		* @param (Array) $votes Existing vote array object to search for first (otherwise load post meta)
		* @param (Int) $vote_key The key of the vote to fetch
		* @return (Object) Vote object
		*/
		function get_users_post_vote_by_key($post_id, $votes=null, $vote_key=-1){
			return $this->voting_database->ht_voting_get_vote_by_key($post_id, $vote_key);
		}


		/**
		* Delete vote by vote_id
		* @param (String) $vote_id The vote key (changed to vote id in 2.2.1+)
		* @param (Int) $post_id The post id 
		*/
		function ht_voting_delete_vote($vote_id, $post_id){
			$this->voting_database->delete_vote_from_database($post_id, $vote_id);
		}    

		/**
		* Deletes all votes for a post
		* @param (Int) $post_id The post id
		*/
		function ht_voting_delete_all_post_votes($post_id){
			$this->voting_database->delete_all_article_votes_from_database($post_id);
		}

		/**
		* Update article usefulness
		* @param (Int) $post_id The post id
		*/
		function ht_voting_update_article_usefulness($post_id){
			$this->voting_database->update_article_usefulness($post_id);
		}

		/**
		* Has votes
		* @param (Int) $post_id The post id
		*/
		function ht_voting_has_votes($post_id){
			$this->voting_database->has_votes($post_id);
		}

	} //end class
} //end class exists

if(class_exists('HT_Voting')){
	global $ht_voting_init;

	$ht_voting_init = new HT_Voting();

	if(!function_exists('ht_voting_post')){
		function ht_voting_post( $post_id=null, $allow='user', $display='standard', $vote=null ){
			global $post, $ht_voting_init;
			$post_id = ( empty( $post_id ) ) ? $post->ID : $post_id;
			$ht_voting_init->ht_voting_post_display( $post_id );
		}
	}

	if(!function_exists('ht_usefulness')){
		function ht_usefulness( $post_id=null ){
			global $post, $ht_voting_init;
			//set the post id
			$post_id = ( empty( $post_id ) ) ? $post->ID : $post_id;
			//get the post usefulness
			$post_usefulness_int = $ht_voting_init->get_article_usefulness($post_id);
			//apply filters
			$post_usefulness_int = apply_filters( 'ht_usefulness', $post_usefulness_int, $post_id );
			//return as integer
			return $post_usefulness_int;
		}
	} 

	if(!function_exists('ht_upvotes_count')){
		function ht_upvotes_count( $post_id=null ){
			global $post, $ht_voting_init;
			//set the post id
			$post_id = ( empty( $post_id ) ) ? $post->ID : $post_id;
			//get the post usefulness
			$post_usefulness_int = $ht_voting_init->get_article_upvotes_count($post_id);
			//return as integer
			return $post_usefulness_int;
		}
	}

	if(!function_exists('ht_downvotes_count')){
		function ht_downvotes_count( $post_id=null ){
			global $post, $ht_voting_init;
			//set the post id
			$post_id = ( empty( $post_id ) ) ? $post->ID : $post_id;
			//get the post usefulness
			$post_usefulness_int = $ht_voting_init->get_article_downvotes_count($post_id);
			//return as integer
			return $post_usefulness_int;
		}
	}

	if(!function_exists('ht_allvotes_count')){
		function ht_allvotes_count( $post_id=null ){
			global $post, $ht_voting_init;
			//set the post id
			$post_id = ( empty( $post_id ) ) ? $post->ID : $post_id;
			//get the post usefulness
			$post_usefulness_int = $ht_voting_init->get_article_allvotes_count($post_id);
			//return as integer
			return $post_usefulness_int;
		}
	}

	if(!function_exists('ht_voting_get_post_votes')){
		function ht_voting_get_post_votes( $post_id=null ){
			global $ht_voting_init;
			
			return $ht_voting_init->get_post_votes($post_id);
		}
	}


	if(!function_exists('ht_voting_delete_vote')){
		function ht_voting_delete_vote( $vote_id, $post_id ){
			global $ht_voting_init;
			
			return $ht_voting_init->ht_voting_delete_vote($vote_id, $post_id);
		}
	}

	if(!function_exists('ht_voting_delete_all_post_votes')){
		function ht_voting_delete_all_post_votes( $post_id ){
			global $ht_voting_init;
			
			return $ht_voting_init->ht_voting_delete_all_post_votes( $post_id );
		}
	}

	if(!function_exists('ht_voting_update_article_usefulness')){
		function ht_voting_update_article_usefulness( $post_id ){
			global $ht_voting_init;
			
			return $ht_voting_init->ht_voting_update_article_usefulness( $post_id );
		}
	}

	if(!function_exists('ht_voting_has_votes')){
		function ht_voting_has_votes( $post_id ){
			global $ht_voting_init;
			
			return $ht_voting_init->ht_voting_has_votes( $post_id );
		}
	}

	if(!function_exists('ht_kb_voting_upgrade_votes')){
		function ht_kb_voting_upgrade_votes( $post_id ){
			global $ht_voting_init;
			
			return $ht_voting_init->ht_kb_voting_upgrade_votes( $post_id );
		}
	}

	if(!function_exists('ht_kb_voting_get_users_post_vote')){
		function ht_kb_voting_get_users_post_vote($post_id, $votes=null){
			global $ht_voting_init;

			return $ht_voting_init->get_users_post_vote($post_id, $votes=null);
		}
	}

	if(!function_exists('ht_kb_voting_get_users_post_vote_direction')){
		function ht_kb_voting_get_users_post_vote_direction($post_id){
			global $ht_voting_init;

			return $ht_voting_init->get_users_post_vote_direction($post_id);
		}
	}

	if(!function_exists('ht_kb_voting_get_new_vote')){
		function ht_kb_voting_get_new_vote(){
			global $ht_voting_init;

			return $ht_voting_init->get_new_vote();
		}
	}

	if(!function_exists('ht_kb_voting_show_feedback_form')){
		function ht_kb_voting_show_feedback_form(){
			global $ht_voting_init;

			return $ht_voting_init->show_feedback_form();
		}
	}

}