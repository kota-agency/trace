<?php
/**
*   Plugin Name: Heroic Knowledge Base Integrations
*   Plugin URI:  https://herothemes.com/plugins/heroic-wordpress-knowledge-base
*   Description: Provides integrations for the Heroic Knowledge Base plugin
*   Author: HeroThemes
*   Version: 1.1.0
*   Author URI: https://herothemes.com/
*   Text Domain: ht-kb-integrations
*/

if( !class_exists( 'HT_KB_Integrations' ) ){

    // main plugin file.
    if ( ! defined( 'HT_KB_INTEGRATIONS_MAIN_PLUGIN_FILE' ) ) {
        define( 'HT_KB_INTEGRATIONS_MAIN_PLUGIN_FILE', __FILE__ );
    }

    class HT_KB_Integrations {

        /**
        * Constructor - Used when the plugin is initialized
        */
        function __construct(){

            //load plugin textdomain
            add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );            

            //loader
            include_once('integrations/ht-knowledge-base-integrations.php');
        }

        /**
        * Load plugin textdomain
        */
        function load_textdomain(){
            load_plugin_textdomain( 'ht-kb-integrations', false, basename( dirname( __FILE__ ) ) . '/languages' );
        }
        
    }

}

//Initialize the plugin
if( class_exists( 'HT_KB_Integrations' ) ){
    $ht_kb_integrations_init = new HT_KB_Integrations();
}