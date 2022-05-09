<?php
/**
* Extension to enable enable sorting of knowledge base article
*/

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'HT_Knowledge_Base_Custom_Article_Order' ) ){
    class HT_Knowledge_Base_Custom_Article_Order {

        //Constructor
        function __construct(){

            //add order column  - currently not required
            //add_filter( 'manage_edit-ht_kb_article_columns',  array( $this,  'add_ht_kb_article_order_column' ) );

            //add order column data  - currently not required
            //add_action( 'manage_ht_kb_article_custom_column' , array( $this,  'data_ht_kb_article_column' ), 10, 3 );

            //add the admin menu
            add_action ( 'admin_menu', array( $this,  'add_ht_kb_article_ordering_menu' ), 20 );

            //enqueue scripts
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_ht_kb_article_ordering_scripts_and_styles' ) );

            //add ajax action to save new order
            add_action( 'wp_ajax_save_ht_kb_article_order', array( $this, 'ajax_save_ht_kb_article_order' ) );

            //admin notices
            add_filter( 'admin_notices', array( $this, 'ht_kb_sort_by_custom_check' ) );
        }

        /**
        * Add category ordering page to menu
        */
        function add_ht_kb_article_ordering_menu(){
            $page_title = __('Article Order', 'ht-knowledge-base');
            $menu_title = __('Article Ordering', 'ht-knowledge-base');
            add_submenu_page( 'edit.php?post_type=ht_kb', $page_title, $menu_title, apply_filters( 'ht_kb_article_ordering_page_capability', 'manage_options' ), 'ht_kb_article_ordering_page', array($this, 'display_ht_kb_article_ordering_page') ); 
        }

        /**
        * Renderer for category ordering page
        */
        function display_ht_kb_article_ordering_page(){
            global $wpdb, $wp_locale;
            
            $taxonomy = 'ht_kb_category';
            $post_type = 'ht_kb';
                       
            $post_type_data = get_post_type_object($post_type);
            
            if (!taxonomy_exists($taxonomy))
                $taxonomy = '';

            //get all the categories with the get_ht_kb_categories function, note the orderby not yet working correctly
            $all_cats = get_ht_kb_categories(null, 'slug');

            //sort the categories by slug, to overcome issue with get_ht_kb_categories not sorting correctly
            $sorted_categories = array();

            //add each category to sorted categories array, using the slug as the key
            foreach ($all_cats as $key => $cat) {
                 $sorted_categories[$cat->slug] = $cat;
            }

            //perform key sort
            ksort($sorted_categories);


            ?>

            <div class="wrap">
                <h2><?php _e( 'Article Ordering', 'ht-knowledge-base' ) ?></h2>

                <noscript>
                    <div class="error message">
                        <p><?php _e( 'Javascript must be enabled to use this page', 'ht-knowledge-base' ) ?></p>
                    </div>
                </noscript>

                <div id="ajax-response"></div>    

                <div id="ht-kb-ordering">

                <div class="hkb-ordering__header">
                    <span><?php _e( 'Category:', 'ht-knowledge-base' ) ?></span>
                    <select class="hkb-cat-selector-adm">
                        <?php foreach ($sorted_categories as $key => $category): ?>
                            <option value="<?php echo $category->term_id; ?>"><?php echo $category->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="hkb-ordering__content">


                    <form action="edit.php" method="get" id="ht-kb-ordering-form"> 
                        <?php foreach ($sorted_categories as $key => $category): ?>
                            <ul class="sortable hkb-category-article-list hkb-category-article-list-<?php echo $category->term_id; ?>" data-term-id="<?php echo $category->term_id; ?>">
                                <?php 
                                    $category_articles = hkb_get_archive_articles($category, null, null, 'article_ordering'); 
                                    $order  = 10;
                                ?>
                                <?php if(empty($category_articles)): ?>
                                    <li><?php _e('No articles in this category', 'ht-knowledge-base'); ?></li>
                                <?php else: ?> 
                                    <?php foreach ($category_articles as $key => $article) : ?>
                                        <?php 
                                            $custom_order = hkb_get_custom_article_order($article->ID, $category->term_id);
                                            $order = empty($custom_order) ? $order : intval($custom_order);
                                        ?>
                                        <li data-article-id="<?php echo $article->ID; ?>" data-term-id="<?php echo $category->term_id; ?>" data-order="<?php echo $order; ?>">
                                            <div class="item">
                                                <?php echo $article->post_title; ?>
                                            </div>
                                        </li>
                                        <?php $order = $order + 10; ?>
                                    <?php endforeach; ?> 
                                <?php endif; ?>
                            </ul>
                        <?php endforeach; ?>
                    </form>

                </div>

                <div class="hkb-ordering__footer">
                    <a href="javascript:;" class="save-order button-primary"><?php _e( "Save Order", 'ht-knowledge-base' ) ?></a>
                </div>

                </div>
            </div>

            <?php
            
        }


        /**
        * Save the new order when called by ajax post
        */
        function ajax_save_ht_kb_article_order(){
            global $wpdb;

            try {
                //check security
                check_ajax_referer( 'ht-kb-article-ordering-ajax-nonce', 'security' );

                //get the new order
                $items = $_POST['items'];

                foreach ($items as $key => $item) {
                    $article_id = (int) $item['articleID'];
                    $term_id = (int) $item['termID'];
                    $order = (int) $item['order'];
                    hkb_set_custom_article_order($article_id, $term_id, $order);
                                  
                }
                //return success message
                $response_text = __('Article Order updated sucessfully', 'ht-knowledge-base');
                $response = array('state' => 'success', 'message' => $response_text);
                
            } catch (Exception $e) {
                //return failure message
                $response_text = __('Article Order cannot be updated', 'ht-knowledge-base');
                $response = array('state' => 'failure', 'message' => $response_text);
                
            }       
            echo json_encode($response);
            die(); // this is required to return a proper result
        }


        /**
        * Sort by not custom
        */
        function ht_kb_sort_by_custom_check(){

            $screen = get_current_screen();

            //get the sort by order
            $user_sort_by = hkb_archive_sortby();


            //only display on this page and when the order is not custom
            if(is_admin() && is_object($screen) && ('ht_kb_page_ht_kb_article_ordering_page' == $screen->base) && 'custom' != $user_sort_by ){  
                ?>
                    <div class="error">
                        <p><?php  _e( 'You must set the Knowledge Base <strong>Sort By</strong> to <strong>Custom</strong> to enable article ordering. ' , 'ht-knowledge-base' );
                                  printf( __('You can do this now from the <a href="%s">Knowledge Base General Settings page</a>.', 'ht-knowledge-base' ), 
                                        admin_url('edit.php?post_type=ht_kb&page=ht_knowledge_base_settings_page#general-section' ) ); ?></p>
                    </div>
                <?php 
            }

        }

        /**
        * Enqueue the javascript and styles for sorting functionality
        */
        function enqueue_ht_kb_article_ordering_scripts_and_styles(){
            $screen = get_current_screen();
            $ajax_error_string = __('Error saving orders', 'ht-knowledge-base');

            if(  $screen->base == 'ht_kb_page_ht_kb_article_ordering_page' ) {
                wp_enqueue_style( 'hkb-style-admin', plugins_url( 'css/hkb-style-admin.css', dirname(__FILE__) ), array(), HT_KB_VERSION_NUMBER );             
                $hkb_admin_article_ordering_js_src = (HKB_DEBUG_SCRIPTS) ? 'js/hkb-admin-article-ordering-js.js' : 'js/hkb-admin-article-ordering-js.min.js';
                wp_enqueue_script( 'ht-kb-article-ordering-script', plugins_url( $hkb_admin_article_ordering_js_src, dirname(__FILE__) ), array( 'jquery' , 'jquery-effects-core', 'jquery-ui-draggable', 'jquery-ui-widget', 'jquery-ui-mouse', 'jquery-ui-sortable' ), HT_KB_VERSION_NUMBER, true );              
                wp_localize_script( 'ht-kb-article-ordering-script', 'framework', array( 'ajaxnonce' => wp_create_nonce('ht-kb-article-ordering-ajax-nonce'), 'ajaxerror' => $ajax_error_string ) );
            }  elseif(  $screen->id == 'widgets' ) {
                wp_enqueue_style( 'hkb-style-admin', plugins_url( 'css/hkb-style-admin.css', dirname(__FILE__) ), array(), HT_KB_VERSION_NUMBER );             
            } elseif(  $screen->id == 'edit-ht_kb_category' ) {
                wp_enqueue_style( 'hkb-style-admin', plugins_url( 'css/hkb-style-admin.css', dirname(__FILE__) ), array(), HT_KB_VERSION_NUMBER );             
            } 
        }

    }//end class
} //end class test


//run the module
if(class_exists('HT_Knowledge_Base_Custom_Article_Order')){
    new HT_Knowledge_Base_Custom_Article_Order();
}