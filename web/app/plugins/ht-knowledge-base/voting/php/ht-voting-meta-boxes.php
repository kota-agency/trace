<?php
/**
* Voting module
* Custom metaboxes and fields setup
*/

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( file_exists( dirname( HT_KB_MAIN_PLUGIN_FILE ) . '/cmb2/init.php' ) ) {
	require_once dirname( HT_KB_MAIN_PLUGIN_FILE ) . '/cmb2/init.php';
} elseif ( file_exists(  dirname( HT_KB_MAIN_PLUGIN_FILE ) . '/CMB2/init.php' ) ) {
	require_once  dirname( HT_KB_MAIN_PLUGIN_FILE ) . '/CMB2/init.php';
}

if (!class_exists('HT_Voting_Meta_Boxes')) {

    class HT_Voting_Meta_Boxes {

    	//Constructor
    	public function __construct() {
    		add_filter( 'cmb2_init', array( $this, 'ht_kb_voting_meta_boxes') );
    	 }

    	 /**
		 * Register meta boxes
		 * @uses the meta-boxes module
		 */
		function ht_kb_voting_meta_boxes() {

			if(apply_filters('ht_kb_disable_voting_options_metabox', false)){
				return;
			}

			$prefix = '_ht_voting_';

			$voting_metabox = new_cmb2_box( array(
				'id'           => $prefix . 'metabox',
				'title'        => __( 'Voting Options', 'ht-knowledge-base' ),
				'object_types' => array( 'ht_kb', ), // Post type
				'context'      => 'normal',
				'priority'     => 'high',
				'show_names'   => true, // Show field names on the left

			) );
			
			//dummy to upgrade fields
			$voting_metabox->add_field( array(
				'name'       => 'update_dummy',
				'id'         => $prefix .'updade_dummy',
				'type'       => 'title',
				'show_on_cb' => array( $this, 'maybe_upgrade_meta_fields' ),
			) );

			//voting enable
			$voting_metabox->add_field( array(
				'name' => __( 'Disable Voting', 'ht-knowledge-base' ),
				'description' => __( 'Disable voting on this article', 'ht-knowledge-base' ),
				'id'   => $prefix .'voting_disabled',
				'type' => 'checkbox',
				'show_on_cb' => array( $this, 'cmb_only_show_for_voting_enabled' ),
			) );
			/*
			//voting reset - no longer used - see ht-voting-backend instead
			$voting_metabox->add_field( array(
				'name' => __( 'Reset Voting', 'ht-knowledge-base' ),
				'description' => __( 'Check this box to reset all votes for this article on update', 'ht-knowledge-base' ),
				'id'   => $prefix .'voting_reset',
				'type' => 'checkbox',
				'default' => false,
				'sanitization_cb' => array( $this, 'santize_reset_field' ), 
				'show_on_cb' => array( $this, 'cmb_only_show_for_votes' ),
			) );
			*/

			/* @deprecated - use main voting display 
			//voting reset confirmation
			$voting_metabox->add_field( array(
				'name' => __( 'No Votes', 'ht-knowledge-base' ),
				'description' => __( 'There are currently no votes or votes have been reset', 'ht-knowledge-base' ),
				'id'   => $prefix .'voting_reset_confirm',
				'type' => 'checkbox',
				'default' => true,
				'sanitization_cb' => array( $this, 'santize_reset_confirm_field' ),
				'show_on_cb' => array( $this, 'cmb_only_show_for_no_votes' ),
			) );
			*/

			/* @deprecated - no longer user editable
			//usefulness
			$voting_metabox->add_field( array(
				'name' => __( 'Usefulness', 'ht-knowledge-base' ),
				'description' => __( 'Set the usefulness for this article (editing may cause inconsistencies with voting)', 'ht-knowledge-base' ),
				'id'   => '_ht_kb_usefulness',
				'type' => 'text',
				// custom sanitization callback parameter
				//none
			) );
			*/
			

		}


		function cmb_only_show_for_voting_enabled(){

			return ( ht_kb_voting_enable_feedback() );
		}

		function cmb_only_show_for_votes(){
			return ($this->cmb_only_show_for_voting_enabled() && $this->cmb_only_show_for_no_votes()==false);
		}

		function cmb_only_show_for_no_votes(){
			//return false if no voting
			if($this->cmb_only_show_for_voting_enabled() == false)
				return false;


			$votes = get_post_meta( get_the_ID(), HT_VOTING_KEY, true );
			return empty($votes);

		}

		function santize_reset_field($new_value, $args, $field){
			if($new_value=='on'){
				//clear votes
				delete_post_meta( get_the_ID(), HT_VOTING_KEY );
				delete_post_meta( get_the_ID(), HT_USEFULNESS_KEY );
				update_post_meta( get_the_ID(), HT_USEFULNESS_KEY, 0 );
			}
			return false;			
		}

		function santize_reset_confirm_field($new_value, $args, $field){
			return true;			
		}


		//upgrade 1.3 -> 1.4
		//transfer _ht_knowledge_base_ prefixed options to _ht_voting_
		/**
		 * Upgrade the meta key values.
		 */
		function maybe_upgrade_meta_fields(){
			HT_Voting::ht_voting_upgrade_post_meta_fields( get_the_ID() );
			//return a false so the dummy does not display
			return false;
		}


		function get_voting_enabled_option(){
			return ht_kb_voting_enable_feedback();
			
		}

		




    } //end class

}//end class exists


//run the module
if(class_exists('HT_Voting_Meta_Boxes')){
	$ht_voting_meta_boxes_init = new HT_Voting_Meta_Boxes();
}
