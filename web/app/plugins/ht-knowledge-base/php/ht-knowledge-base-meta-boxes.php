<?php
/**
* CMB
* Includes and setup custom metaboxes and fields
*/

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if (!class_exists('HT_Knowledge_Base_Meta_Boxes')) {

    class HT_Knowledge_Base_Meta_Boxes {

    	//Constructor
    	function __construct() {
    		if ( file_exists(  dirname( HT_KB_MAIN_PLUGIN_FILE ) . '/cmb290/init.php' ) ) {
				require_once  dirname( HT_KB_MAIN_PLUGIN_FILE ) . '/cmb290/init.php';
			}
		
    		//cmb2 data
    		add_filter( 'cmb2_init', array( $this, 'ht_knowledge_base_register_meta_boxes') );
    		//view only metabox
    		add_action( 'add_meta_boxes', array( $this, 'ht_knowledge_base_add_article_stats_meta_box' ) );
    		 //enqueue scripts
            add_action( 'admin_enqueue_scripts', array( $this, 'ht_knowledge_base_enqueue_meta_box_styles' ) );
    	 }

    	 /**
		 * Register meta boxes
		 * @uses the meta-boxes module
		 * @param (Array) $meta_boxes The exisiting metaboxes
		 * @param (Array) Filtered metaboxes
		 */
		function ht_knowledge_base_register_meta_boxes() {

			if(apply_filters('ht_kb_disable_article_options_metabox', false)){
				return;
			}

			//don't load for non CMB 2.9.0
			if(!class_exists('CMB2_Bootstrap_290')){
				return;
			}

			$prefix = '_ht_knowledge_base_';

			$ht_kb_article_options_metabox = new_cmb2_box( array(
				'id'           => $prefix . 'metabox',
				'title' 		=> __( 'Article Options', 'ht-knowledge-base' ),
				'object_types' => array( 'ht_kb', ), // Post type
				'context'      => 'normal',
				'priority'     => 'high',
				'show_names'   => true, // Show field names on the left

			) );

			$ht_kb_article_options_metabox->add_field( array(
				'name' => 'update_dummy',
				'id'   => $prefix .'updade_dummy',
				'type' => 'title',
				'show_on_cb' => array( $this, 'maybe_upgrade_meta_fields' ),
			) );

			$ht_kb_article_options_metabox->add_field( array(
				'name' => __( 'Attachments', 'ht-knowledge-base' ),
				'description' => __( 'Add attachments to this article', 'ht-knowledge-base' ),
				'id'   => $prefix .'file_advanced',
				'type' => 'file_list',
				'max_file_uploads' => 4,
				'mime_type' => '', // Leave blank for all file types
			) );

			$ht_kb_article_options_metabox->add_field( array(
				'name' => __( 'Attachments in New Window', 'ht-knowledge-base' ),
				'description' => __( 'Open attachments in a new window', 'ht-knowledge-base' ),
				'id'   => $prefix .'file_new_window',
				'type' => 'checkbox'
			) );

			/*
			* @deprecated, view count no longer editable 
			$ht_kb_article_options_metabox->add_field( array(
				'name' => __( 'View Count', 'ht-knowledge-base' ),
				'description' => __( 'View count for this article', 'ht-knowledge-base' ),
				'id'   => HT_KB_POST_VIEW_COUNT_KEY,
				'type' => 'text',
				'default' => 1,
				'sanitization_cb' => array($this, 'santize_view_count_field'), // custom sanitization callback parameter
			) );
			*/
		}

		/**
		* Santize view count field
		* @param (String) $new_value The new value 
		* @param (Array) $args The argument array
		* @param (Object) $field The field object
		* @return (String) The santized value 
		*/
		function santize_view_count_field($new_value, $args, $field){
			$old_value = $field->value();
			if( preg_match('/^\d+$/', $new_value ) ){
				return (int) $new_value;
			} else {
				return $old_value;
			}			
		}

		/**
		 * Upgrade the meta key values.
		 */
		function maybe_upgrade_meta_fields(){
			ht_kb_upgrade_article_meta_fields( get_the_ID() );
			//return a false so the dummy does not display
			return false;
		}

		/**
		* Stats Meta Box
		*/
		function ht_knowledge_base_add_article_stats_meta_box(){
			add_meta_box('ht_kb_article_stats_mb', __( 'Article Stats', 'ht-knowledge-base' ), 
				array($this, 'ht_knowledge_base_render_article_stats_meta_box'), 'ht_kb', 'side', 'default');
		}

		/**
		* Render Stats Meta Box
		*/
		function ht_knowledge_base_render_article_stats_meta_box() {
			global $post;
			?>
				<div class="hkb-articlestats">
					<div class="hkb-articlestats__views">
						<span class="hkb-articlestats__views-label"><?php _e( 'Views:', 'ht-knowledge-base' ); ?></span>
						<span class="hkb-articlestats__views-value"><?php echo ht_kb_view_count($post->ID); ?></span>
					</div>
					<div class="hkb-articlestats__rating">
						<span class="hkb-articlestats__rating-label"><?php _e( 'Rating:', 'ht-knowledge-base' ); ?></span>
						<span class="hkb-articlestats__rating-value"><?php echo ht_usefulness($post->ID); ?></span>
					</div>
					<div class="hkb-articlestats__attachments">
						<span class="hkb-articlestats__attachments-label"><?php _e( 'Attachments:', 'ht-knowledge-base' ); ?></span>
						<span class="hkb-articlestats__attachments-value"><?php echo hkb_count_attachments($post->ID); ?></span>
					</div>
				</div>
			<?php 

		}

		/**
        * Enqueue the javascript and styles for sorting functionality
        */
        function ht_knowledge_base_enqueue_meta_box_styles(){
            $screen = get_current_screen();

            if( $screen->base == 'post' && $screen->id == 'ht_kb' ) {
                wp_enqueue_style( 'hkb-style-admin', plugins_url( 'css/hkb-style-admin.css', dirname(__FILE__) ), array(), HT_KB_VERSION_NUMBER );      
            } 
        }

    } //end class
}//end class exists

//run the module
if(class_exists('HT_Knowledge_Base_Meta_Boxes')){
	new HT_Knowledge_Base_Meta_Boxes();
}