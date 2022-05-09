<?php
/**
* Stubs
* For functionality not implemented in this plugin, but elsewhere with additional plugins or filters
*/

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'HT_KB_Stubs' ) ){
    class HT_KB_Stubs {

        //constructor
        function __construct() {
            //todo - function to alert user when kb-integrations not active. Gravity Forms active, GF_Field_KB_Suggest missing
        }

        function ht_kb_gf_stub(){
            if(!function_exists('Knowledge_Base_Integration_Gravity_Forms')){
                //todo
            }
        }

    }
}

//run the module
if( class_exists( 'HT_KB_Stubs' ) ){
    new HT_KB_Stubs();
}