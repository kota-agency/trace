<?php
/**
* Plugin updater
*/

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

//debug option for testing update functionality
//set_site_transient( 'update_plugins', null );

//HeroThemes site url and product name
define( 'HT_STORE_URL', 'https://www.herothemes.com/?nocache' );
define( 'HT_KB_ITEM_NAME', 'Heroic Knowledge Base WordPress Plugin' ); 

if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
    // load our custom updater
    include( dirname(dirname( __FILE__ )) . '/sl-updater/EDD_SL_Plugin_Updater.php' );
}

if (!class_exists('HT_Knowledge_Base_Updater')) {

    class HT_Knowledge_Base_Updater {

        //Constructor
        function __construct(){
            //init updater
            add_action( 'admin_init', array($this, 'ht_kb_updater' ), 0 );
            //admin notices
            add_filter( 'admin_notices', array( $this, 'ht_kb_license_nag' ) );
            //cron jobs
            register_activation_hook( HT_KB_MAIN_PLUGIN_FILE, array( $this, 'ht_kb_activation_hook' ) );
            //cron action hooks
            add_action( 'ht_kb_license_check', array( $this, 'ht_kb_license_check' ) );
        }

        /**
        * Create the updater
        */
        function ht_kb_updater() {
            if( ( current_theme_supports('ht_kb_theme_managed_updates') || current_theme_supports('ht-kb-theme-managed-updates') ) ){
                return;
            }

            // retrieve our license key from the DB
            $license_key = trim( get_option( 'ht_kb_license_key' ) );
            // setup the updater
            $edd_updater = new EDD_SL_Plugin_Updater( HT_STORE_URL, HT_KB_MAIN_PLUGIN_FILE, array( 
                    'version'   => HT_KB_VERSION_NUMBER,               // current version number
                    'license'   => $license_key,        // license key (used get_option above to retrieve from DB)
                    'item_name' => HT_KB_ITEM_NAME,    // name of this plugin
                    'author'    => 'HeroThemes'  // author of this plugin
                )
            );
        }
    

        /**
        * License nag
        */
        function ht_kb_license_nag(){
            if( ( current_theme_supports('ht_kb_theme_managed_updates') || current_theme_supports('ht-kb-theme-managed-updates') ) ){
                //theme manages licenses updates
                return;
            }
            elseif('valid'==get_option('ht_kb_license_status')){
                //license valid
                return;
            } else {

                $screen = get_current_screen();

                //only display on options page
                if(is_admin() && is_object($screen) && ('ht_kb_page_ht_knowledge_base_settings_page' == $screen->base) ){  
                    ?>
                        <div class="error">
                            <p><?php _e( 'You have not entered a valid license key for automatic updates and support, be sure to do this in the <b>License and Updates</b> section now', 'ht-knowledge-base' ); ?></p>
                        </div>
                    <?php 
                }
            }
        }

        /**
        * Add activation hooks
        */
        function ht_kb_activation_hook(){
            //add a daily license key check
            if ( ! wp_next_scheduled( 'ht_kb_license_check' ) ) {
                wp_schedule_event( time(), 'daily', 'ht_kb_license_check' );
            }
        }

        /**
        * Get the current license key and check it
        */
        function ht_kb_license_check(){
            //don't check the license if theme managed updates (the theme should do it's own if required)
            if( ( current_theme_supports('ht_kb_theme_managed_updates') || current_theme_supports('ht-kb-theme-managed-updates') ) ){
                return;
            }

            //get the license key
            $license_key = trim( get_option( 'ht_kb_license_key' ) );

            if(!empty($license_key)){
                //perform license key check
                HT_Knowledge_Base_Updater::check_license($license_key);    
            }            
        }
        

        /**
        * Attempt to activate license
        * @param $sections (String) The license key to activate
        */
        public static function activate_license($key=''){
            if(empty($key)){
                return;
            }

            $license_key = $key;

            // data to send in our API request
            $api_params = array( 
                'edd_action'=> 'activate_license', 
                'license'   => $license_key, 
                'item_name' => urlencode( HT_KB_ITEM_NAME ) // the name of our product in EDD
            );
            
            // call custom EDD API
            $response = wp_remote_get( add_query_arg( $api_params, HT_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

            // make sure the response came back okay
            if ( is_wp_error( $response ) ){
                $error = true;
            }                

            // decode the license data
            $license_data = json_decode( wp_remote_retrieve_body( $response ) );
            
            // $license_data->license will be either "valid" or "invalid"
            update_option( 'ht_kb_license_status', $license_data->license );
            //update the license key in the database
            update_option( 'ht_kb_license_key', $license_key );
            //if valid, check if an update is required

            //herothemes mod - set license price_id
            if ( $license_data && isset( $license_data->price_id ) ) {
                update_option( 'hkb_license_price_id', $license_data->price_id );
            }

            //notify 
            do_action( 'ht_kb_activate_license', $license_data );

            return;

        }

        /** 
        * Same as function above but retrieves key automatically - used in ajax welcome setup (3.3.0)
        */
        public static function ht_kb_activate_license(){
            $key = trim( get_option( 'ht_kb_license_key' ) );
            return self::activate_license( $key );
        }

        /**
        * Attempt to deactivate license
        * @param $key (String)  The license key to deactivate
        */
        public static function deactivate_license($key=''){
            if(empty($key)){
                return;
            }

            $license_key = $key;

            // data to send in our API request
            $api_params = array( 
                'edd_action'=> 'deactivate_license', 
                'license'   => $license_key, 
                'item_name' => urlencode( HT_KB_ITEM_NAME ) // the name of our product in EDD
            );

            
            // call custom EDD API
            $response = wp_remote_get( add_query_arg( $api_params, HT_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

            // make sure the response came back okay
            if ( is_wp_error( $response ) )
                return false;

            // decode the license data
            $license_data = json_decode( wp_remote_retrieve_body( $response ) );
            
            // $license_data->license will be either "deactivated" or "failed"
            if( $license_data->license == 'deactivated' ){
                delete_option( 'ht_kb_license_status' );
            } else {
                //remove license status, even on failed response
                delete_option( 'ht_kb_license_status' );
            }

            if(empty($license_key)){
                //remove license key from db if blank
                delete_option( 'ht_kb_license_key' );
            }

            //herothemes mod - set license price_id
            if ( $license_data && isset( $license_data->price_id ) ) {
                delete_option( 'hkb_license_price_id' );
            }

            //notify 
            do_action( 'ht_kb_deactivate_license', $license_data );

            return;    
        }

        /** 
        * Same as function above but retrieves key automatically - used in ajax welcome setup (3.3.0)
        */
        public static function ht_kb_deactivate_license(){
            $key = trim( get_option( 'ht_kb_license_key' ) );
            return self::deactivate_license( $key );
        }

        /*
        * Check license validity
        * @param (String) $key  The license key to check
        */
        public static function check_license($key='') {
            global $wp_version;

            if(empty($key)){
                return;
            }

            $license_key = $key;
                
            $api_params = array( 
                'edd_action' => 'check_license', 
                'license' => $license_key, 
                'item_name' => urlencode( HT_KB_ITEM_NAME ),
                'url'       => home_url()
            );

            // call custom EDD API
            $response = wp_remote_get( add_query_arg( $api_params, HT_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

            if ( is_wp_error( $response ) )
                return false;

            $license_data = json_decode( wp_remote_retrieve_body( $response ) );

            if( $license_data->license == 'valid' ) { 
                // this license is still valid, ensure key is correct in db
                update_option( 'ht_kb_license_status', $license_data->license );
                update_option( 'ht_kb_license_key', $license_key );
                //herothemes mod - set license price_id
                if ( $license_data && isset( $license_data->price_id ) ) {
                    update_option( 'hkb_license_price_id', $license_data->price_id );
                }
            } else {
                // this license is no longer valid, delete status
                delete_option( 'ht_kb_license_status' );
                
            }

            if(empty($license_key)){
                //remove license key from db if blank
                delete_option( 'ht_kb_license_key' );
            }

            //notify 
            do_action( 'ht_kb_check_license', $license_data );

            return;
        }

        /** 
        * Same as function above but retrieves key automatically - used in ajax welcome setup (3.3.0)
        */
        public static function ht_kb_check_license(){
            $key = trim( get_option( 'ht_kb_license_key' ) );
            return self::check_license( $key );
        }

    }//end class 

}//end class exists


if (class_exists('HT_Knowledge_Base_Updater')) {
    new HT_Knowledge_Base_Updater();
}