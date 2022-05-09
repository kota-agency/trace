<?php
/**
* Utility functions for Heroic Knowledge Base
*/

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'HT_Knowledge_Base_Utilities' ) ){
	class HT_Knowledge_Base_Utilities {

		//Constructor
		function __construct(){
			//nothing here
		}

		/**
		* Get the current wordpress user ip
		* @return (String) The user IP (note - as a string)
		*/
		function hkb_get_user_ip(){
			$user_ip = 0;
			// Retrieve user IP address 
	        if(array_key_exists('REMOTE_ADDR', $_SERVER)) {
	            $ip = $_SERVER['REMOTE_ADDR']; 
	            $user_ip = $ip;
	        } else {
	            $user_ip = '';
	        }

	        if(HKB_TESTING_MODE){
	        	$user_ip = rand(0, 10000000000000) ;
	        }

	        //anonymise IP if required
	        if(  !empty( $user_ip ) && apply_filters('hkb_get_user_ip_anonymise', true)){
	        	if ( defined('NONCE_KEY') && is_string( constant('NONCE_KEY') ) && '' != trim( constant('NONCE_KEY') ) ) {
					$nonce_key = constant('NONCE_KEY');
	        		$user_ip = md5( $nonce_key . $user_ip );

	        		//we only need 15 characters to  emulate an IP address
	        		$user_ip = substr($user_ip, 0, 15); 
	        	} else {
	        		//silent fail
	        		$user_ip = '';
	        	}
	        }

	        return (String)$user_ip;	           
		}

		/**
		* Get the current wordpress user id
		* @return (String) The user ID (note - as a string)
		*/
		function hkb_get_current_user_id(){
			$user_id = '0';
			$current_user = wp_get_current_user();
	        if( is_a($current_user, 'WP_User') ) {
	            $user_id = $current_user->ID;
	        } else {
	            $user_id = '0';
	        }
	        return $user_id;
		}

		/**
		* Gets the queried object and associated data
		* * @return (Array) Queried object data
		*/
		function hkb_get_queried_object_data(){
			global $post;
            $queried_object = get_queried_object();
            $object_type = '';
            $object_id = 0;
            $object_slug = '';

            if(empty($queried_object)){
                //return if empty
                return;
            }
            //cases - ht_kb_archive, ht_kb_tag, ht_kb_category, ht_kb_article,
            if(is_a($queried_object, 'WP_Post')){
                //homepage or article
                if(property_exists($queried_object, 'post_type') && 'ht_kb' == $queried_object->post_type){
                    //article
                    $object_type = 'ht_kb_article';
                    $object_id = $queried_object->ID;
                    $object_slug = $queried_object->post_name;
                }

            } else {
                //archive, category or tag
                if(property_exists($queried_object, 'query_var') && 'ht_kb' == $queried_object->query_var){
                    //archive
                    $object_type = 'ht_kb_archive';
                    $object_id = 0;
   					$object_slug = ht_kb_get_cpt_slug();
                }
                if(property_exists($queried_object, 'taxonomy') && 'ht_kb_tag' == $queried_object->taxonomy){
                    //tag
                    $object_type = 'ht_kb_tag';
                    $object_id = $queried_object->term_id;
                    $object_slug = $queried_object->slug;
                }

                if(property_exists($queried_object, 'taxonomy') && 'ht_kb_category' == $queried_object->taxonomy){
                    //category
                    $object_type = 'ht_kb_category';
                    $object_id = $queried_object->term_id;
                    $object_slug = $queried_object->slug;
                }
            }
            
            if(empty($object_type)){
                //return if empty
                return;
            }

            //populate data
            $queried_object_data = array(  	'object_type' => $object_type, 
                            				'object_id' => $object_id,
                            				'object_slug' => $object_slug
                        			);

            return $queried_object_data;
        }


    }//end class
}//end class test

//run the module
if(class_exists('HT_Knowledge_Base_Utilities')){

	global $ht_knowledge_base_utilities_init;
	$ht_knowledge_base_utilities_init = new HT_Knowledge_Base_Utilities();
	
	function hkb_get_user_ip(){
		global $ht_knowledge_base_utilities_init;		
		return $ht_knowledge_base_utilities_init->hkb_get_user_ip();
	}

	function hkb_get_current_user_id(){
		global $ht_knowledge_base_utilities_init;		
		return $ht_knowledge_base_utilities_init->hkb_get_current_user_id();
	}

	function hkb_get_queried_object_data(){
		global $ht_knowledge_base_utilities_init;		
		return $ht_knowledge_base_utilities_init->hkb_get_queried_object_data();
	}
}