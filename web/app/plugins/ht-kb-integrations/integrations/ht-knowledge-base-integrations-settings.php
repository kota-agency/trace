<?php
/**
 * Integrations settings
 */

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if (!class_exists('Knowledge_Base_Integration_Settings')) {

    class Knowledge_Base_Integration_Settings {

        //Constructor
        function __construct(){ 
            add_action( 'add_ht_kb_settings_page_additional_settings', array( $this, 'integrations_settings_section' ) );
            add_action( 'ht_kb_settings_display_tabs', array( $this, 'integrations_settings_tab' ) );
            add_action( 'ht_kb_settings_display_additional_sections', array( $this, 'integrations_settings_section_display' ) );       

        }

        //integration settings section
        function integrations_settings_section(){
            //integrations section
            add_settings_section('ht_knowledge_base_integrations_settings', __('Integrations', 'ht-kb-integrations'), array($this, 'ht_kb_settings_integrations_section_description'), 'ht_kb_settings_integrations_section'); 

            do_action('integration_settings_section_fields');
        }

        //section header 
        function ht_kb_settings_integrations_section_description(){
            ?>
                <div class="hkb-settings-integrations-section-start"></div>
            <?php
        }

        //general integrations tab
        function integrations_settings_tab(){
            if(apply_filters('hkb_add_integrations_settings_section', true)): ?>
                <li><a href="#integrations-section" id="integrations-section-link" data-section="integrations"><?php _e('Integrations', 'ht-kb-integrations'); ?></a></li>
            <?php endif;
        }

        //the integrations settings section render
        function integrations_settings_section_display(){
            if(apply_filters('hkb_add_integrations_settings_section', true)): ?>
                <div id="integrations-section" class="hkb-settings-section" style="display:none;">
                    <?php do_settings_sections('ht_kb_settings_integrations_section'); ?>   
                </div>
            <?php endif; 
        }   

    }//end class

}

if (class_exists('Knowledge_Base_Integration_Settings')) {
    $ht_kb_integration_settings_init = new Knowledge_Base_Integration_Settings();
}