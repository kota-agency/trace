<?php
/**
 * Heroic Knowledge Base Frontend Embed
 */

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if (!class_exists('Knowledge_Base_Frontend_Embed')) {

	// this file.
	if ( ! defined( 'HT_KB_FE_EMBED_MAIN_FILE' ) ) {
		define( 'HT_KB_FE_EMBED_MAIN_FILE', __FILE__ );
	}

	if ( ! defined( 'HT_KB_FE_EMBED_MAIN_VERSION' ) ) {
		define( 'HT_KB_FE_EMBED_MAIN_VERSION', '1.0.0' );
	}

	class Knowledge_Base_Frontend_Embed {

		 function __construct(){
			//Loader
            include_once('inc/loader.php'); 

            //Embed page
            include_once('inc/embed-page.php'); 

            //Settings
            include_once('inc/settings.php'); 

		}


	}//end class

}

if (class_exists('Knowledge_Base_Frontend_Embed')) {
	new Knowledge_Base_Frontend_Embed();
}