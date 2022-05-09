<?php
/**
 * Integrations loader
 */

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if (!class_exists('Knowledge_Base_Integration_Loader')) {

    class Knowledge_Base_Integration_Loader {

        //Constructor
        function __construct(){  

        	//settings
        	include_once('ht-knowledge-base-integrations-settings.php');

            //Help Scout
            //include_once('help-scout/integration-help-scout.php');

            //Help Scout v2
            include_once('help-scout-v2/integration-help-scout-v2.php');

            //Gravity Forms
            include_once('gravity-forms/integration-gravity-forms.php');

            //Slack
            include_once('slack/integration-slack.php');  

        }   

    }//end class

}

if (class_exists('Knowledge_Base_Integration_Loader')) {
    $ht_kb_integration_loader_init = new Knowledge_Base_Integration_Loader();
}