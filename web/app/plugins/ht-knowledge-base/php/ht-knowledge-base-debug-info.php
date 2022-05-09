<?php
/**
 * 2.6.5 New Settings Page
 */

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if (!class_exists('HT_Knowledge_Base_Debug_Info')) {

    class HT_Knowledge_Base_Debug_Info {

        private $ht_kb_settings;
        private $reserved_terms;
        private $existing_post_names;

        //Constructor
        function __construct(){  
            global $ht_knowledge_base_settings;

            //get option
            $this->ht_kb_settings = get_option( 'ht_knowledge_base_settings' );

            $ht_knowledge_base_settings = $this->ht_kb_settings;

            //add settings page
            add_action('admin_menu', array($this, 'add_ht_kb_debug_info_page'), 10 ); 

            //remove submenu page from menu    
            add_action('admin_menu', array($this, 'remove_ht_kb_debug_info_page_from_menu'), 15 );  
        }

        function add_ht_kb_debug_info_page(){

            //add the submenu page
            add_submenu_page(
                    'edit.php?post_type=ht_kb',
                    __('Heroic Knowledge Debug Info', 'ht-knowledge-base'), 
                    __('Debug', 'ht-knowledge-base'), 
                    'manage_options', 
                    'ht_knowledge_base_debug_info', 
                   array($this, 'ht_kb_debug_info_display')
                );

        }

        function remove_ht_kb_debug_info_page_from_menu(){
            remove_submenu_page( 'edit.php?post_type=ht_kb', 'ht_knowledge_base_debug_info' );
        }

        function ht_kb_debug_info_display(){
            ?>
                <div class="hkb-admin-settings-page">
                    <h1><?php _e('Heroic Knowledge Base Debug Info', 'ht-knowledge-base'); ?></h1>
                    <div class="notice notice-info">
                        <p>                        
                            <?php printf( __('Debug Info is now available from the <a href="%s">Site Health Panel</a>.', 'ht-knowledge-base'), admin_url('site-health.php?tab=debug') ); ?>
                        </p>
                    </div>
                    <?php do_action('ht_kb_debug_info_display'); ?>
                </div><!-- /hkb-admin-settings-page -->

            <?php
        }



   

    }//end class

}

if (class_exists('HT_Knowledge_Base_Debug_Info')) {
    new HT_Knowledge_Base_Debug_Info();
}