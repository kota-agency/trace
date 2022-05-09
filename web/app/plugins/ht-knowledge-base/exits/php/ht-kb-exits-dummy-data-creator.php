<?php
/**
* Exits module
* Dummy data creator for exits
*/

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if (!class_exists('HT_KB_Exits_Dummy_Data_Creator')) {

    class HT_KB_Exits_Dummy_Data_Creator {

        private $exits_database, $admin_notice_message;

        //constructor
        public function __construct() {
            //add test data listener
            add_action( 'admin_init' , array( $this, 'add_test_data' ));
            //admin head
            add_action('admin_init', array($this, 'enqueue_scripts_and_styles'));
            //view only metabox
            add_action( 'add_meta_boxes', array( $this, 'ht_knowledge_base_add_dummy_exits_meta_box' ) );

            include_once('ht-kb-exits-database.php');
            $this->exits_database = new HT_KB_Exits_Database();
        }

        /**
        * Add dummy exits meta box
        */
        function ht_knowledge_base_add_dummy_exits_meta_box(){
            add_meta_box('ht_kb_dummy_exits_mb', __('Dummy Exits', 'ht-knowledge-base'), 
                array($this, 'ht_knowledge_base_render_dummy_exits_meta_box'), 'ht_kb', 'side', 'default');
        }

        /**
        * Render dummy exits meta box
        */
        function ht_knowledge_base_render_dummy_exits_meta_box() {
            global $post;
            $add_dummy_data_url = admin_url('post.php?post=' . $post->ID . '&action=edit' . '&add_test_exits=add' . '&nonce=' . wp_create_nonce( 'ht-kb-exits-add-dummy' ) );
            ?>
                    <select id="kb_exits_dummy_type__input" name="kb_exits_dummy_type__select">
                      <option value="article"><?php _e( 'This article', 'ht-knowledge-base' ); ?></option>
                      <option value="articles"><?php _e( 'All articles', 'ht-knowledge-base' ); ?></option>
                      <option value="categories"><?php _e( 'All categories', 'ht-knowledge-base' ); ?></option>
                      <option value="archive"><?php _e( 'HKB archive', 'ht-knowledge-base' ); ?></option>
                    </select>
                    <input id="kb_exits_dummy_create__input" name="kb_exits_dummy_create__button" value="20" />
                    <button id="kb_exits_dummy_create__button" href="<?php echo $add_dummy_data_url; ?>" data-challenge="<?php _e('Add dummy exits?', 'ht-knowledge-base'); ?>"><?php _e('Add Exits', 'ht-knowledge-base'); ?></button>
            <?php
        }



        /**
        * Testing / Debug function
        */
        function  add_test_data(){            
            //add test exits?
            $action = (isset($_GET['add_test_exits']) && $_GET['add_test_exits']) ? sanitize_text_field( $_GET['add_test_exits'] ) : '';

            //no action set - return
            if( empty( $action ) ){
                return;
            }                

            //security check
            $nonce = array_key_exists('nonce', $_GET) ? $_GET['nonce'] : '';
            if ( ! wp_verify_nonce( $nonce, 'ht-kb-exits-add-dummy' ) ) {
                die( 'Security check' ); 
            }

            //object to add to 
            $object = (isset($_GET['object']) && $_GET['object']) ? sanitize_text_field($_GET['object']) : '';

            //how many exits to add
            $count = (isset($_GET['count']) && $_GET['count']) ? (int) $_GET['count'] : 5;
            //post id
            $post_id = (isset($_GET['post']) && $_GET['post']) ? (int)$_GET['post'] : 0;
            if('article'===$object && $post_id > 0){
                //create dummy exits on article
                $this->create_dummy_exits_on_object($post_id, 'ht_kb_article', $count);               
            }
            if('articles'===$object){
                //create dummy exits on all kb posts
                $this->create_dummy_exits_on_all_kb_posts($count);                
            }
            if('categories'===$object){  
                //create dummy exits on all kb categories
                $this->create_dummy_exits_on_all_kb_categories($count);                
            }
            if('archive'===$object){
                //create dummy exits on kb archive
                $this->create_dummy_exits_on_kb_archive($count);                
            }
            
            //populate admin message
            $this->admin_notice_message = sprintf( __( 'Inserted %s dummy exits on %s', 'ht-knowledge-base' ), $count, $object );
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
        * Testing / Debug function
        */
        function create_dummy_exits_on_all_kb_posts($number_of_exits_per_post=10){
            //get all ht_kb articles
            $args = array(
                      'post_type' => 'ht_kb',
                      'posts_per_page' => -1,
                     );
            $ht_kb_posts = get_posts( $args );

            //loop and upgrade
            foreach ( $ht_kb_posts as $post ) {
                //create dummy exit
               $this->create_dummy_exits_on_object($post->ID, 'ht_kb_article', $number_of_exits_per_post);
            }
        }

        /**
        * Testing / Debug function
        */
        function create_dummy_exits_on_all_kb_categories($number_of_exits_per_cat=10){
            //get all ht_kb articles
            $ht_kb_categories = get_ht_kb_categories();

            //loop and upgrade
            foreach ( $ht_kb_categories as $key => $cat) {
                //create dummy exit
               $this->create_dummy_exits_on_object($cat->term_id, 'ht_kb_category', $number_of_exits_per_cat);
            }
        }

        /**
        * Testing / Debug function
        */
        function create_dummy_exits_on_kb_archive($number_of_exits=10){
            $this->create_dummy_exits_on_object(0, 'ht_kb_archive', $number_of_exits);
        }


        /**
        * Testing / Debug function
        */
        function create_dummy_exits_on_object($object_id, $type, $number_of_exits){

            
            for ($i=0; $i < $number_of_exits; $i++) { 
                //polpulate data array
                $data = array(
                            'object_type' => $type,
                            'object_id' => $object_id,
                            'source' => $this->source_spinner(),
                            'url' => 'http://example.com/testdummy',
                           // 'datetime' => rand(EPOCH1_START, now()),

                    );
                //add data
                $this->exits_database->add_tracked_exit_to_db($data);
            }
            
        
            
        }

        //return either widget, shortcode or end
        function source_spinner(){
            $value = rand ( 0, 2 );

            $source = 'shortcode';

            switch ($value) {
                case 0:
                     $source = 'shortcode';
                    break;
                case 1:
                     $source = 'widget';
                    break;
                case 2:
                     $source = 'end';
                    break;
                
                default:
                    # code...
                    break;
            }

            return $source;
        }

        /**
        * Enqueue Scripts and Styles
        */
        function enqueue_scripts_and_styles(){
            wp_enqueue_script( 'ht-exits-dummy-data', plugins_url( 'js/ht-exits-dummy-data.js', dirname( __FILE__ ) ), array(), HT_KB_VERSION_NUMBER, true );
        }





    }
} //end if class_exist

//run the module
if(class_exists('HT_KB_Exits_Dummy_Data_Creator')){
    $ht_kb_exits_dummy_data_creator_init = new HT_KB_Exits_Dummy_Data_Creator();
}