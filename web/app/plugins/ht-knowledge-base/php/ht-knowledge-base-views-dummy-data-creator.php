<?php
/**
* Dummy data creator for views
*/

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if (!class_exists('HT_KB_Views_Dummy_Data_Creator')) {

    class HT_KB_Views_Dummy_Data_Creator {

        //constructor
        function __construct() {
            //add test data listener
            add_action( 'admin_init' , array( $this, 'add_test_data' ));
            //enqueue scripts and styles
            add_action( 'admin_head' , array( $this, 'enqueue_scripts_and_styles' ));
            //view only metabox
            add_action( 'add_meta_boxes', array( $this, 'ht_knowledge_base_add_dummy_views_meta_box' ) );
        }

        /**
        * Meta Box
        */
        function ht_knowledge_base_add_dummy_views_meta_box(){
            add_meta_box('ht_kb_dummy_views_mb', __('Dummy Views', 'ht-knowledge-base'), 
                array($this, 'ht_knowledge_base_render_dummy_views_meta_box'), 'ht_kb', 'side', 'default');
        }

        /**
        * Render meta box
        */
        function ht_knowledge_base_render_dummy_views_meta_box() {
            global $post;
            $add_dummy_data_url = admin_url('post.php?post=' . $post->ID . '&action=edit' . '&add_test_views=add' . '&nonce=' . wp_create_nonce( 'ht-kb-views-add-dummy' ) );
            ?>
                <input id="ht_kb_views_dummy_create__input" name="ht_kb_views_dummy_create__button" value="20" />
                <button id="ht_kb_views_dummy_create__button" href="<?php echo $add_dummy_data_url; ?>" data-challenge="<?php _e('Add dummy views?', 'ht-knowledge-base'); ?>"><?php _e('Add Views', 'ht-knowledge-base'); ?></button>
            <?php
        }

        /**
        * Testing / Debug function
        */
        function  add_test_data(){ 
            $action = (isset($_GET['add_test_views']) && $_GET['add_test_views']) ? $_GET['add_test_views'] : '';
            if('add'===$action){
                $nonce = array_key_exists('nonce', $_GET) ? $_GET['nonce'] : '';
                if ( ! wp_verify_nonce( $nonce, 'ht-kb-views-add-dummy' ) ) {
                        die( 'Security check' ); 
                }
                $count = (isset($_GET['count']) && $_GET['count']) ? sanitize_text_field( $_GET['count'] ) : '500';
                $count = intval($count);
                $post_id = (isset($_GET['post']) && $_GET['post']) ? sanitize_text_field( $_GET['post'] ) : null;
                
                if(!isset($post_id))
                    return;
                
                $post_id = intval($post_id);

                //create dummy views
                $this->create_dummy_views($post_id, $count);                
            }
        }

        /**
        * Testing / Debug function
        */
        function create_dummy_views($post_id, $number_of_views){
            //create a database controller
            $database_controller = new HT_Knowledge_Base_View_Count();
            $i = 0;

            for ($i=0; $i < $number_of_views ; $i++) { 
                $data = array(  'object_type' => 'ht_kb_article',
                                'object_id' => $post_id
                    );
                //add rows to database
                $database_controller->ht_kb_add_post_view_data_to_table($data);
            }
            //set the meta value
            $database_controller->ht_kb_set_post_views($post_id);
            
            //populate admin message
            $this->admin_notice_message = sprintf( __( 'Inserted %s dummy views on this article', 'ht-knowledge-base' ), $number_of_views );
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
            wp_enqueue_script( 'ht-kb-views-dummy-data', plugins_url( 'js/hkb-views-dummy-data-js.js', dirname( __FILE__ ) ), array(), HT_KB_VERSION_NUMBER, true );
        }



    }
} //end if class_exist

//run the module
if(class_exists('HT_KB_Views_Dummy_Data_Creator')){
    $ht_kb_views_dummy_data_creator_init = new HT_KB_Views_Dummy_Data_Creator();
}