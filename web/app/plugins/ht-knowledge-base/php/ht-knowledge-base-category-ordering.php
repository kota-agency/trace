<?php
/**
* Extension to enable enable sorting of knowledge base categories
*/

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'HT_Knowledge_Base_Custom_Tax_Order' ) ){
	class HT_Knowledge_Base_Custom_Tax_Order {

		//Constructor
		function __construct(){
			
			//enable the taxonomy ordering
			add_filter('get_terms', array($this, 'sort_kb_categories'), 10, 4);

			//add order column  - currently not required
			//add_filter( 'manage_edit-ht_kb_category_columns',  array( $this,  'add_ht_kb_category_order_column' ) );

			//add order column data  - currently not required
			//add_action( 'manage_ht_kb_category_custom_column' , array( $this,  'data_ht_kb_category_column' ), 10, 3 );

			//add article category filter to article list
			add_action( 'restrict_manage_posts', array( $this,  'add_ht_kb_category_filter_to_posts_list' ) );

			//apply any article category filtering to article list
			add_filter( 'parse_query', array( $this,  'perform_ht_kb_category_filter_to_posts_list' )  );

			//add the ordering page menu to admin menu
			add_action ('admin_menu', array( $this,  'add_ht_kb_ordering_menu' ), 25 );

			//enqueue scripts
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_ht_kb_category_ordering_scripts_and_styles' ) );

			//add ajax action to dave new order
			add_action( 'wp_ajax_save_ht_kb_category_order', array( $this, 'ajax_save_ht_kb_category_order' ) );

		}


		/**
		* Enable sorting of the terms by their order when get_terms is called
		* @param (Array) $terms Array of found terms
		* @param (Array) $taxonomies An array of taxonomies
		* @param (Array) $args An array of get_terms() arguments.
		* @param (WP_Term_Query) $term_query The WP_Term_Query object
		* @return (Array) Filtered terms
		*/
		function sort_kb_categories( $terms, $taxonomies, $args, $term_query){

            //fix for category widget sort
            $custom_category_sort = apply_filters('ht_kb_allow_custom_category_sort', true);

            //only apply for args fields, prevent infinite loop on some installs (added in 2.20.0)
			if($args['fields'] == apply_filters('sort_kb_categories_fields_exclude_string' , 'id=>parent')) {
				return $terms;
			}

            //only apply for ht_kb_category
            if( !in_array( 'ht_kb_category', $taxonomies ) ){
            	return $terms;
            }

            //reindex terms
            $terms = array_values($terms);
		
			if( $custom_category_sort &&
                isset($terms) &&
				is_array($terms) && 
				count($terms) > 0 &&
				array_key_exists(0, $terms) &&  
				is_object($terms[0]) && 
				property_exists($terms[0], 'taxonomy') &&
				$terms[0]->taxonomy == 'ht_kb_category' ){ 
					//term order detected now order by term order
					usort($terms, array($this, 'category_sort'));
					return $terms;
			} else {
				return $terms;
			}			
		}

		/**
		* Custom usort function for sorting terms by their term_order
		*/
		function category_sort($a, $b){
			//get term orders
			$a_term_order = get_term_meta( $a->term_id, 'term_order', true );
			$b_term_order = get_term_meta( $b->term_id, 'term_order', true );
			if ( $a_term_order ==  $b_term_order ) {
				return 0;
			} else if ( $a_term_order < $b_term_order ) {
				return -1;
			} else {
				return 1;
			}
		}

		/**
		* Add article category filter to articles list
		*/
		function add_ht_kb_category_filter_to_posts_list(){
				$screen = get_current_screen();
				global $wp_query;
				if ( 'ht_kb' == $screen->post_type ) {
					wp_dropdown_categories( array(
						'show_option_all' => __( 'Show All Categories', 'ht-knowledge-base' ),
						'taxonomy' => 'ht_kb_category',
						'name' => 'ht_kb_category',
						'orderby' => 'name',
						'selected' => ( isset( $wp_query->query['ht_kb_category'] ) ? $wp_query->query['ht_kb_category'] : '' ),
						'hierarchical' => false,
						'depth' => 3,
						'show_count' => false,
						'hide_empty' => true,
					) );
				}
		}

		/**
		 * Perform the article category query filter on the posts list 
		 * @param (Object) $query WordPress query
		 * @return (Object) $query Modified query
		 */
		function perform_ht_kb_category_filter_to_posts_list( $query ){
			if( is_admin() ){
				$query_variables = $query->query_vars;
				if ( isset( $query_variables['ht_kb_category'] ) && is_numeric( $query_variables['ht_kb_category'] ) ) {
						$term = get_term_by( 'id', $query_variables['ht_kb_category'], 'ht_kb_category' );
						if(isset($term) && isset($term->slug)){
							$query->query_vars['ht_kb_category'] = $term->slug;	
						}
				}
			}
			
			return $query;
		}

		/**
		 * Add kb post view count column
		 * @param (Array) $columns Current columns on the list post
		 * @return (Array) $columns Filtered columns on the list post
		 */
		function add_ht_kb_category_order_column( $columns ) {
			$column_name = __('Order', 'ht-knowledge-base');
		 	$column_meta = array( 'ht_kb_order' => $column_name );
			$columns = array_slice( $columns, 0, 4, true ) + $column_meta + array_slice( $columns, 4, NULL, true );
			return $columns;
		}

		/**
		 * Add kb post view count data
		 * @param (String) $out The output (unused)
		 * @param (String) $column_name The name of the current column
		 * @param (Int) $term_id The current term ID
		 */
		function data_ht_kb_category_column( $out, $column_name, $term_id ) {
		    switch ( $column_name ) {
		      case 'ht_kb_order':
		      	$order = get_term_meta( $term_id, 'term_order', true );
		      	echo $order;
		        break;
		    }
		}

		/**
		* Add category ordering page to menu
		*/
		function add_ht_kb_ordering_menu(){
			$page_title = __('Category Order', 'ht-knowledge-base');
			$menu_title = __('Category Ordering', 'ht-knowledge-base');
			add_submenu_page( 'edit.php?post_type=ht_kb', $page_title, $menu_title, apply_filters( 'ht_kb_category_ordering_page_capability', 'manage_options' ), 'ht_kb_ordering_page', array($this, 'display_ht_kb_ordering_page') ); 
		}

		/**
		* Renderer for category ordering page
		*/
		function display_ht_kb_ordering_page(){
			global $wpdb, $wp_locale;
            
            $taxonomy = 'ht_kb_category';
            $post_type = 'ht_kb';
	                   
            $post_type_data = get_post_type_object($post_type);
            
            if (!taxonomy_exists($taxonomy))
                $taxonomy = '';

            ?>
            <div class="wrap">
                <h2><?php _e( 'Article Category Ordering', 'ht-knowledge-base' ) ?></h2>

                <noscript>
                    <div class="error message">
                        <p><?php _e( 'Javascript must be enabled to use this page', 'ht-knowledge-base' ) ?></p>
                    </div>
                </noscript>

                <div id="ajax-response"></div>

                <div id="ht-kb-ordering">

                    <div class="hkb-ordering__header">
                        <h3><?php _e( 'Category Order', 'ht-knowledge-base' ) ?></h3>
                    </div>                

                    <div class="hkb-ordering__content">

                        <p>
                        	<?php _e('Drag and drop the categories to re-order how they appear in the knowledge base archives', 'ht-knowledge-base'); ?>
                        </p>
                        
                        <form action="edit.php" method="get" id="ht-kb-ordering-form"> 
        	                <div id="order-terms">    
        	                    <div id="post-body">                
        	                            <ul class="sortable">
        	                                <?php 
        	                                    $this->list_ht_kb_category_terms(); 
        	                                ?>
        	                            </ul>
        	                            <div class="clear"></div>
        	                    </div>
                           
        	                </div> 
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
		* List the terms for the taxonomy
		*/
		function list_ht_kb_category_terms() {
                $args = array(
							'orderby' 		=> 'meta_value_num slug',
							'meta_query' => array(
													'relation' => 'OR', //default AND
													array(
														'key' => 'term_order',
														'compare' => 'NOT EXISTS'
													),
													array(
														'key' => 'term_order',
														'value' => '',
														'compare' => '!='
													)
											),
							//ensure order is asc for custom order
							'order' 		=> 'asc',
                            'depth'         =>  0,
                            'child_of'      => 	0,
                            'hide_empty'    =>  0
                );

                $legacy_args = array(
						'taxonomy' => 'ht_kb_category',
						'hide_empty' => false,
						//cache use OK
						//'update_term_meta_cache' => false,
				);  
				//@since 3.0 updated get_terms call 
				//get all the terms for the ht_kb_category taxonomy
				$taxonomy_terms = get_terms( array_merge( $legacy_args, $args ) );

                $output = '';
                if (count($taxonomy_terms) > 0)
                    {
                    	//walk the terms
                        $output = $this->ht_kb_category_terms_walk($taxonomy_terms, $args['depth'], $args);    
                    }
                echo $output;                
        }       
        
        /**
		* Walk the taxonomy terms
		*/
        function ht_kb_category_terms_walk($taxonomy_terms, $depth, $r) {
                $walker = new HT_KB_Category_Terms_Walker; 
                $args = array($taxonomy_terms, $depth, $r);
                return call_user_func_array(array(&$walker, 'walk'), $args);
        }

        /**
		* Save the new order when called by ajax post
		*/
        function ajax_save_ht_kb_category_order(){
        	global $wpdb;

        	try {
        		//check security
    			check_ajax_referer( 'ht-kb-ordering-ajax-nonce', 'security' );

    			//get the new order
				$new_orders = $_POST['order'];

				foreach ($new_orders as $key => $value) {
					$term_id = (int) $key;
					$term_order = (int) $value;


					//set new orders NEED term_relationships also?
					if(is_int($term_id) && is_int($term_order)){
						//update term order 
						update_term_meta($term_id, 'term_order', $term_order);
						//set the new order for each term in the database
						//review sql vulns - safe due to use of wpdb::prepare
						/*
						$wpdb->query( $wpdb->prepare(
								"UPDATE $wpdb->terms SET term_order = '%d' WHERE term_id ='%d'",
								$term_order,
								$term_id
							) );
							*/
					}				
				}
				//return success message
				$response_text = __('Category Order updated sucessfully', 'ht-knowledge-base');
	    		$response = array('state' => 'success', 'message' => $response_text);
				
			} catch (Exception $e) {
				//return failure message
				$response_text = __('Category Order cannot be updated', 'ht-knowledge-base');
	    		$response = array('state' => 'failure', 'message' => $response_text);
				
			}		
			echo json_encode($response);
			die(); // this is required to return a proper result
		}

		/**
		* Enqueue the javascript and styles for sorting functionality
		*/
		function enqueue_ht_kb_category_ordering_scripts_and_styles(){
			$screen = get_current_screen();
			$ajax_error_string = __('Error saving orders', 'ht-knowledge-base');

			if(  $screen->base == 'ht_kb_page_ht_kb_ordering_page' ) {
				wp_enqueue_style( 'hkb-style-admin', plugins_url( 'css/hkb-style-admin.css', dirname(__FILE__) ), array(), HT_KB_VERSION_NUMBER );	
                $hkb_admin_category_ordering_js_src = (HKB_DEBUG_SCRIPTS) ? 'js/hkb-admin-category-ordering-js.js' : 'js/hkb-admin-category-ordering-js.min.js';			
				wp_enqueue_script( 'ht-kb-category-ordering-script', plugins_url( $hkb_admin_category_ordering_js_src, dirname(__FILE__) ), array( 'jquery' , 'jquery-effects-core', 'jquery-ui-draggable', 'jquery-ui-widget', 'jquery-ui-mouse', 'jquery-ui-sortable' ), HT_KB_VERSION_NUMBER, true );				
				wp_localize_script( 'ht-kb-category-ordering-script', 'framework', array( 'ajaxnonce' => wp_create_nonce('ht-kb-ordering-ajax-nonce'), 'ajaxerror' => $ajax_error_string ) );
			}  elseif(  $screen->id == 'widgets' ) {
                wp_enqueue_style( 'hkb-style-admin', plugins_url( 'css/hkb-style-admin.css', dirname(__FILE__) ), array(), HT_KB_VERSION_NUMBER );             
            } 
		}

	}//end class
} //end class test

/**
* Custom walker class for the category terms
*/
 class HT_KB_Category_Terms_Walker extends Walker {

	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id');


	function start_lvl(&$output, $depth = 0, $args = array() ){
		extract($args, EXTR_SKIP);
			
		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent<ul class='children sortable'>\n";
    }


    function end_lvl(&$output, $depth = 0, $args = array()){
		extract($args, EXTR_SKIP);
	
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
    }


    function start_el(&$output, $term, $depth = 0, $args = array(), $current_object_id = 0){
	    if(!isset($term))
            return;

        if ( $depth )
			$indent = str_repeat("\t", $depth);
	    else
			$indent = '';

	    $taxonomy = get_taxonomy($term->term_taxonomy_id);

        $order = get_term_meta( $term->term_id, 'term_order', true ); 

	    $output .= $indent . '<li class="term_type_li" id="item_'.$term->term_id.'" data-term-id="'.$term->term_id.
	                '" data-term-order="'.$order.'"><div class="item"><span>'.
	                apply_filters( 'the_title', $term->name, $term->term_id ).' </span>'.'</div>';
    }


    function end_el(&$output, $object, $depth = 0, $args = array()) {
        $output .= "</li>\n";
    }

}//end class

//run the module
if(class_exists('HT_Knowledge_Base_Custom_Tax_Order')){
	new HT_Knowledge_Base_Custom_Tax_Order();
}