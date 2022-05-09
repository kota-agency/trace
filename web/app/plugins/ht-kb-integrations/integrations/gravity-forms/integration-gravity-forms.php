<?php
/**
 * Integration for Gravity Forms
 */

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


if (!class_exists('Knowledge_Base_Integration_Gravity_Forms')) {

    class Knowledge_Base_Integration_Gravity_Forms {

        //constructor
        function __construct(){

            //init function
            add_action('init', array($this, 'kb_suggest_init'), 10);            
        }

        /**
        * Init plugin
        * Required to check for Gravity Forms and Heroic Knowledge Base
        */
        function kb_suggest_init(){
            //check for gravity forms
            if ( ! class_exists( 'HT_Knowledge_Base' ) ) {
                //heroic knowledge base must be active
                add_action( 'admin_notices', array($this, 'kb_suggest_activate_hkb_admin_message' ), 0 );
            } elseif ( ! class_exists( 'GFForms' ) ) {
                //gravity forms must be active
                add_action( 'admin_notices', array($this, 'kb_suggest_activate_gforms_admin_message' ), 0 );
            } else {
                //include the KB suggest class
                include_once('php/class-gf-kb-suggest.php');

                //add to the settings
                add_action( 'gform_field_standard_settings', array( $this, 'kb_suggest_settings' ), 10, 2 );

                //tooltips
                add_filter( 'gform_tooltips', array( $this, 'kb_suggest_add_setting_tooltips' ) );

                //add the js to the field
                add_action( 'gform_editor_js', array( $this, 'kb_suggest_field_editor_script' ) );

                //set the defaults
                add_action( 'gform_editor_js_set_default_values', array( $this, 'kb_suggest_set_defaults' ) );

                //enqueue scripts
                add_action( 'gform_enqueue_scripts', array( $this, 'hkb_suggest_field_enqueue_custom_script' ), 10, 2 );   

            }

            //js
            add_action( 'admin_enqueue_scripts', array( $this, 'ht_kb_integration_gravity_forms_enqueue_backend_scripts' ) );

            //ajax dismiss
            add_action( 'wp_ajax_ht_kb_gf_dismiss_inactive_warning', array( $this, 'ht_kb_integration_gravity_forms_dismiss_inactive_warning' ) );

            //settings action
            add_action( 'integration_settings_section_fields', array( $this, 'ht_kb_integration_gravity_forms_settings_section_fields' ) );

            //add filter
            add_filter( 'ht_knowledge_base_settings_validate', array( $this, 'ht_kb_integration_gravity_forms_settings_validate'), 10, 2);
        }

        /**
        * Display admin warning message when gravity forms inactive
        */
        function kb_suggest_activate_gforms_admin_message(){
            global $ht_knowledge_base_settings;
            $screen = get_current_screen();

            $hide_gforms_warning_setting = isset($ht_knowledge_base_settings['integration-gravity-forms-hide-inactive-warning']) && $ht_knowledge_base_settings['integration-gravity-forms-hide-inactive-warning'] ? true : false;
            $hide_gravity_forms_activation_warning = apply_filters( 'kb_suggest_hide_gravity_forms_activation_warning', $hide_gforms_warning_setting );
            if( is_a($screen, 'WP_Screen')  && !$hide_gravity_forms_activation_warning ):
                ?>
                <div class="notice notice-info inactive-gf-warning is-dismissible">
                    <p>
                        <?php _e( 'Gravity Forms is inactive. It is required to use the Gravity Forms Heroic Knowledge Base integration.', 'ht-kb-integrations' ); ?> 
                        <a href="<?php echo admin_url( 'plugins.php?plugin_status=inactive' ); ?>"><?php _e( 'Please Activate Gravity Forms', 'ht-kb-integrations' ); ?></a>                 
                    </p>
                </div>
                <?php
            endif;
        }

        /**
        * Display admin warning message when heroic knowledge base inactive
        */
        function kb_suggest_activate_hkb_admin_message(){
            $screen = get_current_screen();

            $hide_hkb_activation_warning = apply_filters( 'kb_suggest_hide_hkb_activation_warning', false );
            if( is_a($screen, 'WP_Screen')  && !$hide_hkb_activation_warning ):
                ?>
                <div class="notice notice-info inactive-hkb-warning is-dismissible">
                    <p>
                        <?php _e( 'Heroic Knowledge Base is inactive. It is required to use the Gravity Forms Heroic Knowledge Base integration.', 'ht-kb-integrations' ); ?> 
                        <a href="<?php echo admin_url( 'plugins.php?plugin_status=inactive' ); ?>"><?php _e( 'Please Activate Heroic Knowledge Base', 'ht-kb-integrations' ); ?></a>                 
                    </p>
                </div>
                <?php
            endif;
        }


        /**
        * Add the additional setting fields to GF_Field_KB_Suggest
        * gform_field_standard_settings
        */
        function kb_suggest_settings( $position, $form_id ){
            //create settings on position 25 (right after Field Label)
    
            if ( $position == 25 ) {   
            ?>
                <li class="kb_suggest_max_results field_setting">
                    <label for="kb_suggest_max_results_value" class="section_label">
                        <?php esc_html_e( 'Maximum Results', 'ht-kb-integrations' ); ?>
                        <?php gform_tooltip( 'kb_suggest_max_results_value' ) ?>
                    </label>
                    <select id="kb_suggest_max_results_value">
                        <option value="1"><?php _e('1', 'ht-kb-integrations'); ?></option>
                        <option value="2"><?php _e('2', 'ht-kb-integrations'); ?></option>
                        <option value="3"><?php _e('3', 'ht-kb-integrations'); ?></option>
                        <option value="4"><?php _e('4', 'ht-kb-integrations'); ?></option>
                        <option value="5"><?php _e('5', 'ht-kb-integrations'); ?></option>
                        <option value="6"><?php _e('6', 'ht-kb-integrations'); ?></option>
                        <option value="7"><?php _e('7', 'ht-kb-integrations'); ?></option>
                        <option value="8"><?php _e('8', 'ht-kb-integrations'); ?></option>
                        <option value="9"><?php _e('9', 'ht-kb-integrations'); ?></option>
                        <option value="10"><?php _e('10', 'ht-kb-integrations'); ?></option>
                    </select>
            <?php
            }
            
        }

        /*
        * Add the tooltips for the custom description
        * gform_tooltips hooks
        */
        function kb_suggest_add_setting_tooltips( $tooltips ){
            $tooltips['kb_suggest_max_results_value'] = __('<h6>Maximum Results</h6>The maximum number of search results to return', 'ht-kb-integrations');
            return $tooltips;
        }

        /**
        * Custom field display initialization
        * gform_editor_js hook
        */
        function kb_suggest_field_editor_script(){
            ?>
            <script type='text/javascript'>

                //adding setting to fields of type "text"
                fieldSettings.kbsuggest = fieldSettings.kbsuggest  + ', .kb_suggest_max_results';
                
                //remove .input_mask_setting, 
                fieldSettings.kbsuggest = fieldSettings.kbsuggest.replace('.input_mask_setting,', '');

                //binding to the load field settings event to initialize the items
                jQuery(document).bind('gform_load_field_settings', function(event, field, form){

                    jQuery('#kb_suggest_max_results_value').val(field.maxResults);
                    jQuery('#kb_suggest_max_results_value').on('change',function(){
                        var newValue = jQuery(this).val();
                        SetFieldProperty('maxResults', newValue);
                    });
   
                });
            </script>
            <?php
        }

        /*
        * Defaults for entries
        */
        function kb_suggest_set_defaults(){
            $placeholder_text = __('Search the Knowledge Base', 'ht-kb-integrations'); 
             ?>
                //this hook is fired in the middle of a switch statement,
                //so we need to add a case for our new field type
                case "kbsuggest" :
                    field.placeholder = "<?php echo $placeholder_text ;?>";
                    field.maxResults = 5;
                    break;
            <?php
        }

        /**
        * Enqueue custom scripts and styles
        * gform_enqueue_scripts
        */
        function hkb_suggest_field_enqueue_custom_script( $form, $is_ajax ) {
                $load_hkb_suggest_script = false;
                if(is_array($form['fields'])){
                    foreach ($form['fields'] as $key => $field) {
                        if(isset($field['type']) && 'kbsuggest' == $field['type']){
                            //load
                            $load_hkb_suggest_script = true;
                        }
                    }
                }

                //do not load the scripts and styles if no kbsuggest field
                if(!$load_hkb_suggest_script){
                    return; 
                }

                $hkb_integration_gravity_forms_script = (HKB_DEBUG_SCRIPTS) ? 'js/integration-gravity-forms-js.js' : 'js/integration-gravity-forms-js.min.js';
                //$hkb_integration_gravity_forms_script = 'js/integration-gravity-forms-js.js';

                wp_enqueue_script( 'hkb-integration-gravity-forms-script', plugins_url( $hkb_integration_gravity_forms_script, __FILE__ ), array( 'jquery', 'backbone', 'underscore', 'wp-api'  ), 1.0, true );    

                $hkb_integration_gravity_forms_style = 'css/integration-gravity-forms.css';
                wp_enqueue_style( 'hkb-integration-gravity-forms-style', plugins_url( $hkb_integration_gravity_forms_style, __FILE__ ) );
        }

        function ht_kb_integration_gravity_forms_enqueue_backend_scripts(){
            //no need for backend scripts if gravity forms loaded
            if ( class_exists( 'GFForms' ) ) {
                return;
            } else {
                $hkb_integration_gravity_forms_backend_backend_script = (HKB_DEBUG_SCRIPTS) ? 'js/integration-gravity-forms-backend-js.js' : 'js/integration-gravity-forms-backend-js.min.js';
               //$hkb_integration_gravity_forms_backend_backend_script = 'js/integration-gravity-forms-backend-js.js';            

                wp_enqueue_script( 'hkb-integration-gravity-forms-backend-script', plugins_url( $hkb_integration_gravity_forms_backend_backend_script, __FILE__ ), array( 'jquery' ), 1.0, true );  
            }
        }

        function ht_kb_integration_gravity_forms_dismiss_inactive_warning(){
            $ht_knowledge_base_settings = get_option('ht_knowledge_base_settings');
            $ht_knowledge_base_settings['integration-gravity-forms-hide-inactive-warning'] = '1';
            update_option('ht_knowledge_base_settings', $ht_knowledge_base_settings); 
        }

        function ht_kb_integration_gravity_forms_settings_validate($output, $input){
            //integration-gravity-forms-hide-inactive-warning
            if( isset($input['integration-gravity-forms-hide-inactive-warning']) ) {
                $output['integration-gravity-forms-hide-inactive-warning'] = esc_attr($input['integration-gravity-forms-hide-inactive-warning']);
            } else {
                $output['integration-gravity-forms-hide-inactive-warning'] = false;
            }

            return $output;
        }

        function ht_kb_integration_gravity_forms_settings_section_fields(){
            add_settings_field('integration-gravity-forms-title-dummy', __('Gravity Forms', 'ht-kb-integrations'), array($this, 'integrations_gravity_forms_section_title_dummy_option_render'), 'ht_kb_settings_integrations_section', 'ht_knowledge_base_integrations_settings');
            add_settings_field('integration-gravity-forms-status-warning', __('Status', 'ht-kb-integrations'), array($this, 'integrations_gravity_forms_status_option_render'), 'ht_kb_settings_integrations_section', 'ht_knowledge_base_integrations_settings');
            //check for gravity forms
            if ( ! class_exists( 'GFForms' ) ) {
            add_settings_field('integration-gravity-forms-hide-inactive-warning', __('Hide Inactive Warning', 'ht-kb-integrations'), array($this, 'integrations_gravity_forms_hide_inactive_warning_option_render'), 'ht_kb_settings_integrations_section', 'ht_knowledge_base_integrations_settings');
            }
        }

        function integrations_gravity_forms_section_title_dummy_option_render(){
            ?>
                <div>&nbsp;</div>              
            <?php            
        }

        function integrations_gravity_forms_status_option_render(){
            //check for gravity forms
            if ( ! class_exists( 'GFForms' ) ) {
                //gravity forms not active
                _e('Gravity Forms is not installed or active', 'ht-kb-integrations');
            } else {
                //gravity forms active
                printf( __('Gravity Forms is active, you can now add KB search fields to <a href="%s">your forms</a>', 'ht-kb-integrations'), admin_url('admin.php?page=gf_edit_forms') );
            }
        }

        function integrations_gravity_forms_hide_inactive_warning_option_render(){
            global $ht_knowledge_base_settings;

            $kb_integration_gravity_forms_hide_inactive_warning = isset($ht_knowledge_base_settings['integration-gravity-forms-hide-inactive-warning']) ? $ht_knowledge_base_settings['integration-gravity-forms-hide-inactive-warning'] : false;
            if(!apply_filters('hkb_integrations_gravity_forms_app_key_option_render', true)){
                return;

            } ?>

                <input type="checkbox" class="ht-knowledge-base-settings-integration-gravity-forms-hide-inactive-warning__input" name="ht_knowledge_base_settings[integration-gravity-forms-hide-inactive-warning]" value="1" <?php checked( $kb_integration_gravity_forms_hide_inactive_warning, 1 ); ?> />
                <span class="hkb_setting_desc--inline"><?php _e('Disable Gravity Forms inactive warning in dashboard', 'ht-kb-integrations'); ?></span>               
            <?php            
        }




    }//end class

}

if( class_exists('Knowledge_Base_Integration_Gravity_Forms') ) {
    $ht_kb_integration_gravity_forms_init = new Knowledge_Base_Integration_Gravity_Forms();
}