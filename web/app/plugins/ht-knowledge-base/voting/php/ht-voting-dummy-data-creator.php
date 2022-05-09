<?php
/**
* Voting module
* Dummy data creator for voting
*/

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if (!class_exists('HT_Voting_Dummy_Data_Creator')) {

    class HT_Voting_Dummy_Data_Creator {

        private $admin_notice_message;

        //constructor
        public function __construct() {

            //add test data listener
            add_action( 'admin_init' , array( $this, 'add_test_data' ) );
            //admin head
            add_action( 'admin_init', array( $this, 'enqueue_scripts_and_styles' ) );
            //view only metabox
            add_action( 'add_meta_boxes', array( $this, 'ht_knowledge_base_add_dummy_votes_meta_box' ) );
        }

        /**
        * Add dummy votes meta box
        */
        function ht_knowledge_base_add_dummy_votes_meta_box(){
            add_meta_box('ht_kb_dummy_votes_mb', __('Dummy Votes', 'ht-knowledge-base'), 
                array($this, 'ht_knowledge_base_render_dummy_votes_meta_box'), 'ht_kb', 'side', 'default');
        }

        /**
        * Render dummy votes meta box
        */
        function ht_knowledge_base_render_dummy_votes_meta_box() {
            global $post;
            $add_dummy_data_url = admin_url('post.php?post=' . $post->ID . '&action=edit' . '&add_test_votes=add' . '&nonce=' . wp_create_nonce( 'ht-voting-add-dummy' ) );
            ?>
                    <input id="kb_voting_dummy_create__input" name="kb_voting_dummy_create__button" value="20" />
                    <button id="kb_voting_dummy_create__button" href="<?php echo $add_dummy_data_url; ?>" data-challenge="<?php _e('Add dummy votes?', 'ht-knowledge-base'); ?>"><?php _e('Add Votes', 'ht-knowledge-base'); ?></button>
            <?php
        }

        /**
        * Testing / Debug function
        */
        function  add_test_data(){ 
            $action = (isset($_GET['add_test_votes']) && $_GET['add_test_votes']) ? $_GET['add_test_votes'] : '';
            if('add'===$action){
                $nonce = array_key_exists('nonce', $_GET) ? $_GET['nonce'] : '';
                if ( ! wp_verify_nonce( $nonce, 'ht-voting-add-dummy' ) ) {
                        die( 'Security check' ); 
                }
                $count = (isset($_GET['count']) && $_GET['count']) ? sanitize_text_field( $_GET['count'] ) : '500';
                $count = intval($count);
                $post_id = (isset($_GET['post']) && $_GET['post']) ? sanitize_text_field( $_GET['post'] ) : null;
                
                if(!isset($post_id))
                    return;
                
                $post_id = intval($post_id);
                //$this->create_dummy_votes($post_id, $count);
                $this->create_dummy_on_all_articles($count);
            }
        }

        function create_dummy_on_all_articles( $number_of_votes ){
            //get all ht_kb articles
            $args = array(
                      'post_type' => 'ht_kb',
                      'posts_per_page' => -1,
                     );
            $ht_kb_posts = get_posts( $args );

            //loop and upgrade
            foreach ( $ht_kb_posts as $post ) {
                //create dummy exit
               $this->create_dummy_votes( $post->ID, $number_of_votes );
            }
        }

        /**
        * Testing / Debug function
        */
        function create_dummy_votes($post_id, $number_of_votes){
            //create a database controller
            $database_controller = new HT_Voting_Database();
            $i = 0;

            $phrase_array = array('great', 'loved', 'brilliant', 'top', 'article', 'notch', 'amazing', 'bullish', 'marketing' );
            $phrase_array_length = count($phrase_array);

            for ($i=0; $i < $number_of_votes ; $i++) { 
                if (rand(0, 1)) { 
                    $vote = new HT_Vote_Down();
                } else {
                    $vote = new HT_Vote_Up();
                }
                $database_controller->save_vote_for_article($post_id, $vote);

                //don't add comments for 75% of votes
                $rand_int = rand(0, 100);
                if($rand_int < 25){
                    $rand_index_1 = rand(0, $phrase_array_length-1);
                    $rand_index_2 = rand(0, $phrase_array_length-1);
                    $rand_index_3 = rand(0, $phrase_array_length-1);
                    $comment_string = $phrase_array[$rand_index_1] . ' ' . $phrase_array[$rand_index_2] . ' ' . $phrase_array[$rand_index_3];
                    $database_controller->update_comments_for_vote($post_id, $vote->key, $comment_string );
                }
            }

            //populate admin message
            $this->admin_notice_message = sprintf( __( 'Inserted %s dummy votes on this article', 'ht-knowledge-base' ), $number_of_votes );
            //add hook
            add_action( 'admin_notices', array( $this, 'dummy_data_added_admin_notice') );

        }

        function dummy_data_added_admin_notice() {
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php echo $this->admin_notice_message; ?></p>
            </div>
            <?php
        }

        /**
        * Enqueue Scripts and Styles
        */
        function enqueue_scripts_and_styles(){
            wp_enqueue_script( 'ht-voting-dummy-data', plugins_url( 'js/ht-voting-dummy-data.js', dirname( __FILE__ ) ), array(), HT_KB_VERSION_NUMBER, true );
        }


    }

} //end if class_exist



//run the module
if(class_exists('HT_Voting_Dummy_Data_Creator')){
    $ht_voting_dummy_data_creator_init = new HT_Voting_Dummy_Data_Creator();
}