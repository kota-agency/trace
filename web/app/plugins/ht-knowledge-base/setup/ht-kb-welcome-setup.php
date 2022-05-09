<?php
/**
 * Heroic Knowledge Base Welcome Setup
 */

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if (!class_exists('HT_Knowledge_Base_Welcome_Setup')) {

	class HT_Knowledge_Base_Welcome_Setup {

		function __construct(){			
			include_once('inc/ht-kb-welcome-setup-page.php');
		}

	}//end class

}

if (class_exists('HT_Knowledge_Base_Welcome_Setup')) {
	new HT_Knowledge_Base_Welcome_Setup();
}