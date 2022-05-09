<?php
/**
 * Integration for Help Scout
 */

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

require_once dirname( HT_KB_INTEGRATIONS_MAIN_PLUGIN_FILE ) . '/vendor/autoload.php';

use HelpScoutApp\DynamicApp;
use HelpScout\Api\ApiClientFactory;

if (!class_exists('Knowledge_Base_Integration_Help_Scout_V2')) {

    if(!defined('HT_KB_INTEGRATION_HELP_SCOUT_V2_COMMUNICATION_OK_KEY')){
        define( 'HT_KB_INTEGRATION_HELP_SCOUT_V2_COMMUNICATION_OK_KEY', '_ht_kb_integration_help_scout_v2_communication_ok' );
    }

    class Knowledge_Base_Integration_Help_Scout_V2 {

         function __construct(){
            //ajax filters
            add_action( 'wp_ajax_ht_kb_help_scout_app', array( $this, 'ht_kb_ajax_integration_help_scout_app_callback' ) );
            add_action( 'wp_ajax_nopriv_ht_kb_help_scout_app', array( $this, 'ht_kb_ajax_integration_help_scout_app_callback' ) );

            //settings action
            add_action( 'integration_settings_section_fields', array( $this, 'ht_kb_integration_help_scout_settings_section_fields' ) );

            //add filter
            add_filter( 'ht_knowledge_base_settings_validate', array( $this, 'ht_kb_integration_help_scout_settings_validate'), 10, 2);

        }

        function ht_kb_integration_help_scout_settings_validate($output, $input){
            global $ht_knowledge_base_settings;

            //integration-help-scout-app-key
            if( isset($input['integration-help-scout-app-key']) ) {
                $output['integration-help-scout-app-key'] = esc_attr($input['integration-help-scout-app-key']);
            } else {
                $output['integration-help-scout-app-key'] = '';
            }

            //integration-help-scout-user-api-key
            //@deprecated, will be removed in upcoming versions
            if( isset($input['integration-help-scout-user-api-key']) ) {
                $output['integration-help-scout-user-api-key'] = esc_attr($input['integration-help-scout-user-api-key']);
            } else {
                $output['integration-help-scout-user-api-key'] = '';
            }

            //integration-help-scout-user-app-id
            if( isset($input['integration-help-scout-user-app-id']) ) {
                $output['integration-help-scout-user-app-id'] = esc_attr($input['integration-help-scout-user-app-id']);
            } else {
                $output['integration-help-scout-user-app-id'] = '';
            }

            //integration-help-scout-user-app-secret
            if( isset($input['integration-help-scout-user-app-secret']) ) {
                $output['integration-help-scout-user-app-secret'] = esc_attr($input['integration-help-scout-user-app-secret']);
            } else {
                $output['integration-help-scout-user-app-secret'] = '';
            }

            //remove the communication ok if option updated
            if($output['integration-help-scout-app-key'] != $ht_knowledge_base_settings['integration-help-scout-app-key']){
                delete_option(HT_KB_INTEGRATION_HELP_SCOUT_V2_COMMUNICATION_OK_KEY);
            }

            //remove the communication ok if option updated
            if($output['integration-help-scout-user-api-key'] != $ht_knowledge_base_settings['integration-help-scout-user-api-key']){
                delete_option(HT_KB_INTEGRATION_HELP_SCOUT_V2_COMMUNICATION_OK_KEY);
            }

            return $output;
        }

        function ht_kb_integration_help_scout_settings_section_fields(){
            add_settings_field('integration-help-scout-title-dummy', __('Help Scout', 'ht-kb-integrations'), array($this, 'integrations_help_scout_section_title_dummy_option_render'), 'ht_kb_settings_integrations_section', 'ht_knowledge_base_integrations_settings');

            add_settings_field('integration-help-scout-app-key', __('App Key', 'ht-kb-integrations'), array($this, 'integrations_help_scout_app_key_option_render'), 'ht_kb_settings_integrations_section', 'ht_knowledge_base_integrations_settings');

            add_settings_field('integration-help-scout-app-key-helper', __('Status', 'ht-kb-integrations'), array($this, 'integrations_help_scout_app_key_helper_option_render'), 'ht_kb_settings_integrations_section', 'ht_knowledge_base_integrations_settings');

            add_settings_field('integration-help-scout-user-api-key', __('User API Key', 'ht-kb-integrations'), array($this, 'integrations_help_scout_user_api_key_option_render'), 'ht_kb_settings_integrations_section', 'ht_knowledge_base_integrations_settings');

            add_settings_field('integration-help-scout-user-app-id', __('User App ID', 'ht-kb-integrations'), array($this, 'integrations_help_scout_user_app_id_option_render'), 'ht_kb_settings_integrations_section', 'ht_knowledge_base_integrations_settings');

            add_settings_field('integration-help-scout-user-app-secret', __('User App ID', 'ht-kb-integrations'), array($this, 'integrations_help_scout_user_app_secret_option_render'), 'ht_kb_settings_integrations_section', 'ht_knowledge_base_integrations_settings');

            add_settings_field('integration-help-scout-user-auth-helper', __('Status', 'ht-kb-integrations'), array($this, 'integrations_help_scout_user_auth_helper_option_render'), 'ht_kb_settings_integrations_section', 'ht_knowledge_base_integrations_settings');
        }

        function integrations_help_scout_section_title_dummy_option_render(){
            ?>
                <div>&nbsp;</div>              
            <?php            
        }

        function integrations_help_scout_app_key_option_render(){
            global $ht_knowledge_base_settings;
            global $kb_integration_help_scout_app_key;

            $kb_integration_help_scout_app_key = isset($ht_knowledge_base_settings['integration-help-scout-app-key'] ) && $ht_knowledge_base_settings['integration-help-scout-app-key'] ? $ht_knowledge_base_settings['integration-help-scout-app-key'] : $this->generateRandomStringHelper();
            if(!apply_filters('hkb_integrations_help_scout_app_key_option_render', true)){
                return;
            } ?>
                <input type="text" class="ht-knowledge-base-settings-integration-help-scout-app-key__input" name="ht_knowledge_base_settings[integration-help-scout-app-key]" value="<?php echo esc_attr($kb_integration_help_scout_app_key); ?>" style="width: 350px;" placeholder="xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"></input>
                <span class="hkb_setting_desc"><?php _e('Enter your Help Scout App Key', 'ht-kb-integrations'); ?></span>               
            <?php            
        }

        function integrations_help_scout_app_key_helper_option_render(){
            global $ht_knowledge_base_settings;
            global $kb_integration_help_scout_app_key;

            
            if(!apply_filters('hkb_integrations_help_scout_app_key_option_render', true)){
                return;
            } 

            if(get_option(HT_KB_INTEGRATION_HELP_SCOUT_V2_COMMUNICATION_OK_KEY, false)){
                _e('Help Scout App Key OK - Communicating with Help Scout', 'ht-kb-integrations');
            } else {
                printf(__('Create App from the <a href="%s" target="_blank">Help Scout Custom App Integration</a> dashboard', 'ht-kb-integrations'), 'https://secure.helpscout.net/apps/custom/');
                echo '<br/><br/>';
                printf(__('App Name: <strong>%s</strong>', 'ht-kb-integrations'), get_bloginfo('name') . __(' KB Integration', 'ht-kb-integrations'));
                echo '<br/><br/>';
                _e('Set Content Type to <strong>Dynamic Content</strong>', 'ht-kb-integrations');
                echo '<br/><br/>';
                printf(__('Callback Url: <strong>%s</strong>', 'ht-kb-integrations'), admin_url('admin-ajax.php?action=ht_kb_help_scout_app'));
                echo '<br/><br/>'; 
                printf(__('Secret Key: <strong>%s</strong>', 'ht-kb-integrations'), $kb_integration_help_scout_app_key );
            }
            

        }

        function integrations_help_scout_user_api_key_option_render(){
            global $ht_knowledge_base_settings;
            $kb_integration_help_scout_user_api_key = isset($ht_knowledge_base_settings['integration-help-scout-user-api-key']) ? $ht_knowledge_base_settings['integration-help-scout-user-api-key'] : '';
            if(!apply_filters('hkb_integrations_help_scout_user_api_key_option_render', true)){
                return;
            } ?>
                <input type="text" class="ht-knowledge-base-settings-integration-help-scout-user_api_key__input" name="ht_knowledge_base_settings[integration-help-scout-user-api-key]" value="<?php echo esc_attr($kb_integration_help_scout_user_api_key); ?>" style="width: 350px;" placeholder="xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"></input>
                <span class="hkb_setting_desc"><?php _e('<strong>Deprecated</strong> API key is no longer used, please create user app authentication as described below.', 'ht-kb-integrations'); ?></span>               
            <?php            
        }

        function integrations_help_scout_user_app_id_option_render(){
            global $ht_knowledge_base_settings;
            $kb_integration_help_scout_user_app_id = isset($ht_knowledge_base_settings['integration-help-scout-user-app-id']) ? $ht_knowledge_base_settings['integration-help-scout-user-app-id'] : '';
            if(!apply_filters('hkb_integrations_help_scout_user_app_id_option_render', true)){
                return;
            } ?>
                <input type="text" class="ht-knowledge-base-settings-integration-help-scout-user_app_id__input" name="ht_knowledge_base_settings[integration-help-scout-user-app-id]" value="<?php echo esc_attr($kb_integration_help_scout_user_app_id); ?>" style="width: 350px;" placeholder="xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"></input>
                <span class="hkb_setting_desc"><?php _e('Enter your Help Scout User App ID', 'ht-kb-integrations'); ?></span>               
            <?php            
        }

        function integrations_help_scout_user_app_secret_option_render(){
            global $ht_knowledge_base_settings;
            $kb_integration_help_scout_user_app_id = isset($ht_knowledge_base_settings['integration-help-scout-user-app-secret']) ? $ht_knowledge_base_settings['integration-help-scout-user-app-secret'] : '';
            if(!apply_filters('hkb_integrations_help_scout_user_app_id_option_render', true)){
                return;
            } ?>
                <input type="text" class="ht-knowledge-base-settings-integration-help-scout-user_app_id__input" name="ht_knowledge_base_settings[integration-help-scout-user-app-secret]" value="<?php echo esc_attr($kb_integration_help_scout_user_app_id); ?>" style="width: 350px;" placeholder="xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"></input>
                <span class="hkb_setting_desc"><?php _e('Enter your Help Scout User App Secret', 'ht-kb-integrations'); ?></span>               
            <?php            
        }

        function integrations_help_scout_user_auth_helper_option_render(){
            if(!apply_filters('hkb_integrations_help_scout_app_key_option_render', true)){
                return;
            } 

            if(get_option(HT_KB_INTEGRATION_HELP_SCOUT_V2_COMMUNICATION_OK_KEY, false)){
                _e('Help Scout User Authentication OK - Communicating with Help Scout', 'ht-kb-integrations');
            } else {
                _e('Goto your Help Scout Profile and select <strong>My Apps</strong>', 'ht-kb-integrations');
                echo '<br/><br/>'; 
                _e('Click <strong>Create My App</strong>', 'ht-kb-integrations');
                echo '<br/><br/>'; 
                printf(__('App Name: <strong>%s</strong>', 'ht-kb-integrations'), get_bloginfo('name') . __(' User App', 'ht-kb-integrations'));
                echo '<br/><br/>';
                printf(__('Callback Url: <strong>%s</strong>', 'ht-kb-integrations'), admin_url('admin-ajax.php?action=ht_kb_help_scout_app_v2') );
                echo '<br/><br/>'; 
                _e('Copy the generated <strong>App ID</strong> and <strong>App Secret</strong> into the boxes above', 'ht-kb-integrations');
            }
        }



        function ht_kb_ajax_integration_help_scout_app_callback(){
            global $ht_knowledge_base_settings;    

            $help_scout_app_key = isset($ht_knowledge_base_settings['integration-help-scout-app-key']) ? $ht_knowledge_base_settings['integration-help-scout-app-key'] : false;

            //new authentication method (HelpScout Mailbox API V2)
            $help_scout_user_app_id = isset($ht_knowledge_base_settings['integration-help-scout-user-app-id']) ? $ht_knowledge_base_settings['integration-help-scout-user-app-id'] : false;
            $help_scout_user_app_secret = isset($ht_knowledge_base_settings['integration-help-scout-user-app-secret']) ? $ht_knowledge_base_settings['integration-help-scout-user-app-secret'] : false;

            $html = array();

            $html[] = sprintf('Connected to %s <br/><br/>', site_url());
            
            try {               
            
                if( $help_scout_app_key ){

                    $app = new DynamicApp($help_scout_app_key);   
                           
                    if ( $app->isSignatureValid() ) {

                            
                            if( ($help_scout_user_app_id && $help_scout_user_app_secret ) ){                          
                                
                                $convo    = $app->getConversation();
                                //use new mail box API
                                $client = ApiClientFactory::createClient();                                

                                //use client credentials as authentication method
                                $client->useClientCredentials( $help_scout_user_app_id, $help_scout_user_app_secret );                        

                                $conversation = $client->conversations()->get( $convo->getId() );
                                
                                //todo check for authentication error
                                if(isset($conversation)){
                                    //set communication ok key
                                    update_option(HT_KB_INTEGRATION_HELP_SCOUT_V2_COMMUNICATION_OK_KEY, true); 

                                    $item = $api_response['item'];
                                    $html[] =   'Searching Knowledge Base with current conversation tags:<br/>';
                                    $tags = $conversation->getTags();

                                    if(count($tags) > 0){

                                        foreach ($tags as $key => $tag) {

                                            
                                            $query_args = array( 
                                                                's' => $tag->getName(), 
                                                                'post_type' => 'ht_kb',
                                                                'posts_per_page' => apply_filters('ht_kb_integration_help_scout_results_per_tag', '3') 
                                                            );
                                           
                                            $search_results = new WP_Query( $query_args );
                                            if($search_results->have_posts()){
                                                $html[] = sprintf( '<i class="icon-search"></i> Top 3 Results for <strong>%s</strong><br/>', $tag->getName() );
                                                while($search_results->have_posts()){
                                                  $search_results->the_post();  
                                                  $html[] = sprintf( '<i class="icon-doc"></i><a href="%s">%s</a><br/>', get_permalink(), get_the_title() );
                                                }
                                            } else {
                                                $html[] = sprintf( '<i class="icon-search muted"></i> No Results for <strong>%s</strong><br/>', $tag->getName() );
                                            }                                        
                                        }
                                    } else {
                                        $html[] = '<i class="icon-search muted"></i> No tags in current conversation<br/>';
                                    }                                    
                                    
                                    echo $app->getResponse($html);
                                    //all ok, can die here
                                    die();
                                } else {
                                    //set communication not OK key
                                    delete_option(HT_KB_INTEGRATION_HELP_SCOUT_V2_COMMUNICATION_OK_KEY);

                                    $html[] = 'Invalid Request, or Invalid Help Scout User API Key<br/>';
                                }

                                

                            } else {
                                //or need to update user app id  and user app secret

                                $html[] = 'Help Scout User App ID and/or App Secret not set, please see your WP Admin knowledge base integration settings tab<br/>';
                            }
                            
                    } else {
                            $html[] = 'Invalid Request, or Invalid App Key<br/>';
                    }

                } else {
                    $html[] = 'Help Scout App Key Not Set<br/>';
                }

            } catch (Exception $e) {
                $html[] = 'Could not proceed due to the following error:<br/>';
                $html[] = $e->getMessage();
            }
            
            //send any errors using wp_send_json
            wp_send_json( array( 'html'=>implode( $html ) ) );

            //required to ensure proper ajax request
            die();
        }

        function generateRandomStringHelper($length = 40) {
            $characters = '23456789abcdefghjkmpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        }

    }//end class

}

if (class_exists('Knowledge_Base_Integration_Help_Scout_V2')) {
    $ht_kb_integration_help_scout_init = new Knowledge_Base_Integration_Help_Scout_V2();
}