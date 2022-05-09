<?php
/**
 * Create a Channel in Slack where the messages will go (or use an existing channel)
 * Click Settings (cog icon) then Add an app
 * Search for Incoming Webhooks
 * Click Add Configuration
 * Select the Channel from the dropdown and click Add Incoming Webhook Integration
 * Copy the Webhook URL into Knowledge Base > Settings > Integrations (tab) > Slack (section) > Webhook URL
 * Additionally, select all the knowledge base notifications you wish to send to Slack.
 */

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


if (!class_exists('Knowledge_Base_Integration_Slack')) {

    if( !defined('HT_KB_INTEGRATION_SLACK_COUNTER_KEY')){
        define('HT_KB_INTEGRATION_SLACK_COUNTER_KEY', '_ht_kb_integration_slack_ncount_');
    }

    class Knowledge_Base_Integration_Slack {

        private $notifications;

        //constructor
        function __construct(){

            //init actions
            add_action( 'init', array( $this, 'ht_kb_integration_slack_init' ) );

            //settings action
            add_action( 'integration_settings_section_fields', array( $this, 'ht_kb_integration_slack_settings_section_fields' ) );

            //add filter
            add_filter( 'ht_knowledge_base_settings_validate', array( $this, 'ht_kb_integration_slack_settings_validate'), 10, 2 );

            //use a filter for the slack notify specific option
            add_filter( 'ht_knowledge_base_integration_slack_notify_option', array( $this, 'ht_knowledge_base_integration_slack_notify_option'), 10, 2 ); 

            //new article hook
            add_filter( 'publish_ht_kb', array( $this, 'ht_knowledge_base_integration_slack_publish_ht_kb'), 10, 2 );   
            
            //article updated hook
            add_filter( 'post_updated', array( $this, 'ht_knowledge_base_integration_slack_post_updated'), 10, 3 ); 

            //article deleted hook   
            add_filter( 'delete_post', array( $this, 'ht_knowledge_base_integration_slack_delete_post'), 10, 1 ); 
            add_filter( 'trash_ht_kb', array( $this, 'ht_knowledge_base_integration_slack_delete_post'), 10, 2 );        

            //upvote/downvote action hook
            add_action( 'ht_voting_vote_post_action', array( $this, 'ht_knowledge_base_integration_slack_vote_post' ), 10, 3 );

            //feedback action hook
            add_action( 'ht_voting_add_vote_comment_action', array( $this, 'ht_knowledge_base_integration_slack_add_vote_comment' ), 10, 3 );

            //comment action hook
            add_action('comment_post', array( $this, 'ht_knowledge_base_integration_slack_comment_post' ), 10, 3 ); 

            //notification sent action hook
            add_action('ht_knowledge_base_integration_slack_notification_sent', array( $this, 'ht_knowledge_base_integration_slack_notification_counter_increment' ), 10, 3 );
        }

        function ht_kb_integration_slack_init(){
            //inside init fuction for translation
            $this->notifications = array(   'article-published' => __('New Articles', 'ht-kb-integrations'),
                                            'article-updated' => __('Updated Articles', 'ht-kb-integrations'),
                                            'article-deleted' => __('Deleted Articles', 'ht-kb-integrations'),
                                            'article-upvotes' => __('Article Upvotes', 'ht-kb-integrations'),
                                            'article-downvotes' => __('Article Downvotes', 'ht-kb-integrations'),
                                            'article-feedback' => __('Article Feedback', 'ht-kb-integrations'),
                                            'article-comments' => __('Article Comments', 'ht-kb-integrations'),
                                        );
        }

        /**
        * Validate the slack settings
        */
        function ht_kb_integration_slack_settings_validate($output, $input){
            global $ht_knowledge_base_settings;

            //integration-slack-webhook-url
            if( isset($input['integration-slack-webhook-url']) ) {
                $output['integration-slack-webhook-url'] = esc_attr($input['integration-slack-webhook-url']);
            } else {
                $output['integration-slack-webhook-url'] = '';
            }

            //remove the options if webhook url updated
            if($output['integration-slack-webhook-url'] != $ht_knowledge_base_settings['integration-slack-webhook-url']){
                $this->ht_knowledge_base_integration_slack_remove_notification_counter_wp_options();
            }
            
            foreach ($this->notifications as $key => $label) {
                $value =  isset($input['integration-slack-notifications'][$key]) ? 1 : 0;
                $output['integration-slack-notifications'][$key] = $value;
            } 



            return $output;
        }

        /**
        * Render the slack settings tab in knowledge base settings
        */
        function ht_kb_integration_slack_settings_section_fields(){
            add_settings_field('integration-slack-title-dummy', __('Slack', 'ht-kb-integrations'), array($this, 'integrations_slack_section_title_dummy_option_render'), 'ht_kb_settings_integrations_section', 'ht_knowledge_base_integrations_settings');
            add_settings_field('integration-slack-webhook-url', __('Webhook URL', 'ht-kb-integrations'), array($this, 'integrations_slack_webhook_url_option_render'), 'ht_kb_settings_integrations_section', 'ht_knowledge_base_integrations_settings');
            add_settings_field('integration-slack-webhook-url-helper', __('Status', 'ht-kb-integrations'), array($this, 'integrations_slack_webhook_url_helper_option_render'), 'ht_kb_settings_integrations_section', 'ht_knowledge_base_integrations_settings');
            add_settings_field('integration-slack-notifications', __('Notifications', 'ht-kb-integrations'), array($this, 'integrations_slack_notifications_option_render'), 'ht_kb_settings_integrations_section', 'ht_knowledge_base_integrations_settings');
        }

        /**
        * Dummy section title render
        */
        function integrations_slack_section_title_dummy_option_render(){
            ?>
                <div>&nbsp;</div>              
            <?php            
        }

        /**
        * Slack webhook url setting option render
        */
        function integrations_slack_webhook_url_option_render(){
            global $ht_knowledge_base_settings;

            $kb_integration_slack_webhook_url = $this->get_kb_integration_slack_webhook_url();
            if(!apply_filters('hkb_integrations_slack_webhook_url_option_render', true)){
                return;
            } ?>
                <input type="text" class="ht-knowledge-base-settings-integration-slack-webhook-url__input" name="ht_knowledge_base_settings[integration-slack-webhook-url]" value="<?php echo esc_attr($kb_integration_slack_webhook_url); ?>" style="width: 585px;" placeholder="https://hooks.slack.com/services/XXXXXXXXX/XXXXXXXXX/xxxxxxxxxxxxxxxxxxxxxxxx"></input>
                <span class="hkb_setting_desc"><?php _e('Enter your full Slack Incoming Webhook Integration URL', 'ht-kb-integrations'); ?></span>               
            <?php            
        }

        /**
        * Slack webhook url status option render
        */
        function integrations_slack_webhook_url_helper_option_render(){
            global $ht_knowledge_base_settings;

            if(!apply_filters('hkb_integrations_slack_webhook_url_option_render', true)){
                return;
            } 

            $total_notifications = (int) $this->ht_knowledge_base_integration_slack_notification_total_get();

            if($total_notifications > 0 ){
                printf(__('A total of %s notifications have been sent to this Slack channel', 'ht-kb-integrations'), $total_notifications);
                echo '<br/><br/>';
            } else {
                _e('No notifications have been sent to this Slack channel', 'ht-kb-integrations');
                echo '<br/><br/>';
                printf(__('Add a new <a href="%s" target="_blank">Incoming Webhooks</a> integration to your Slack channel', 'ht-kb-integrations'), 'https://slack.com/apps/A0F7XDUAZ-incoming-webhooks');
                echo '<br/><br/>';
                _e('Paste the <strong>Webhook URL</strong> from Slack into the box above', 'ht-kb-integrations');
                echo '<br/><br/>';
            }
                     
        }

        /**
        * Slack notification options render
        */
        function integrations_slack_notifications_option_render(){
            global $ht_knowledge_base_settings;
            if(!apply_filters('hkb_integrations_slack_notifications_option_render', true)){
                return;
            } ?>
                <?php foreach ($this->notifications as $key => $label): ?>
                    <?php $current_value = isset($ht_knowledge_base_settings['integration-slack-notifications'][$key]) ? $ht_knowledge_base_settings['integration-slack-notifications'][$key] : 1; ?>
                    <input type="checkbox" class="ht-knowledge-base-settings-integration-slack-notifications__input" name="ht_knowledge_base_settings[integration-slack-notifications][<?php echo $key; ?>]" value="1" <?php checked($current_value, 1); ?> ></input>
                    <span class="hkb_setting_desc--inline"><?php echo $label; ?></span>
                    <br/>                     
                <?php endforeach ?>   
            <?php            
        }

        /**
        * Get the webhook url setting
        */
        function get_kb_integration_slack_webhook_url(){
            global $ht_knowledge_base_settings;
            $kb_integration_slack_webhook_url = isset($ht_knowledge_base_settings['integration-slack-webhook-url']) ? $ht_knowledge_base_settings['integration-slack-webhook-url'] : '';

            return $kb_integration_slack_webhook_url;

        }

        /**
        * Get the notification option
        */
        function ht_knowledge_base_integration_slack_notify_option($key='', $option=false){
            global $ht_knowledge_base_settings;

            //if no key specified return option
            if(''===$key ){
                return $option;    
            }         

            //if the webhook url not specified return option
            if( ''===$this->get_kb_integration_slack_webhook_url() ){
                return $option;    
            }

            //get the value
            $option_value = isset($ht_knowledge_base_settings['integration-slack-notifications'][$key]) ? $ht_knowledge_base_settings['integration-slack-notifications'][$key] : $option;

            return $option_value;

        }  

        /**
        * Get display name of current WP user
        */
        function get_wp_current_user_display_name(){
            if ( is_user_logged_in() ) {
                global $current_user;
                wp_get_current_user();
                $display_name = $current_user->display_name;
            } else {
                $display_name = __('An anonymous user', 'ht-kb-integrations');
            }

            return $display_name;
        }

        /**
        * Get article title wrapper
        */
        function article_title( $post_id = 0 ){
            //get the title
            $title = esc_attr( get_the_title( $post_id ) );
            //replace apostrophes, do we need to do other characters?
            $title = str_replace( "&#8217;", "'", $title ); 
            return $title;
        }

        /**
        * Format a comment for use in slack
        */
        function format_comment( $comment = "" ){
            //replace apostrophes, do we need to do other characters?
            $comment = str_replace( "&#8217;", "'", $comment );
            $comment = str_replace( "\&quot;", "'", $comment ); 
            return $comment;
        }


        /**
        * Publish ht_kb action hook
        * publish_ht_kb = $post_id, $post
        */
        function ht_knowledge_base_integration_slack_publish_ht_kb( $post_id, $post ){

            $notification_type = 'article-published';

            //exit if not notify option
            if( !apply_filters( 'ht_knowledge_base_integration_slack_notify_option', $notification_type ) ){
                return;
            }

            $title = sprintf( __('%s Article Published', 'ht-kb-integrations'),  $this->article_title( $post_id )  );
           
            $display_name = $this->get_wp_current_user_display_name();
            
            $message = sprintf( __( '*%s* has published <%s|%s>', 'ht-kb-integrations'), $display_name, get_post_permalink( $post_id ), $this->article_title( $post_id )  );

            $color = '#7fff7f';
            $icon_emoji = ':blue_book:';
 
            $this->post_message_to_slack( $title, $notification_type, $message, $color, $icon_emoji );

        } 

        /**
        * Post Updated action hook
        * post_updated - $post_id, $post, $post_before
        */
        function ht_knowledge_base_integration_slack_post_updated( $post_id, $post, $post_before ){
            //only fire on ht_kb
            if( 'ht_kb' != $post->post_type ){
                return;
            }

            $notification_type = 'article-updated';

            //exit if not notify option
            if( !apply_filters( 'ht_knowledge_base_integration_slack_notify_option', $notification_type ) ){
                return;
            }

            //only notify is post and post before status is published
            if( ! ( $post->post_status == $post_before->post_status && $post->post_status == 'publish' ) ){
                return;
            }
             

            $title = sprintf( __('%s Article Updated', 'ht-kb-integrations'),  $this->article_title( $post_id ) );
           
            $display_name = $this->get_wp_current_user_display_name();
            
            $message = sprintf( __( '*%s* has edited <%s|%s>', 'ht-kb-integrations'), $display_name, get_post_permalink( $post_id ), $this->article_title( $post_id )  );

            $color = '#daff19';
            $icon_emoji = ':pencil:';
 
            $this->post_message_to_slack( $title, $notification_type, $message, $color, $icon_emoji );



        }

        /**
        * Delete and Trash action hook
        * delete_post - $post_id
        * trash_ht_kb - $post_id, $post
        */
        function ht_knowledge_base_integration_slack_delete_post( $post_id, $post = null ){
            //get post if not set
            if( !$post || empty($post) ){
                $post = get_post($post_id);
            }

            //only fire on ht_kb
            if( 'ht_kb' != $post->post_type ){
                return;
            }

            $notification_type = 'article-deleted';

            //exit if not notify option
            if( !apply_filters( 'ht_knowledge_base_integration_slack_notify_option', $notification_type ) ){
                return;
            }

            //trashed if post object set else false
            $trashed = isset($post) ? true : false;

            if($trashed){
                $title = sprintf( __('%s Article Trashed', 'ht-kb-integrations'),  $this->article_title( $post_id )  );
            } else {
                $title = sprintf( __('%s Article Deleted', 'ht-kb-integrations'),  $this->article_title( $post_id )  );
            }            
           
            $display_name = $this->get_wp_current_user_display_name();
            
            $message = sprintf( __( '*%s* has trashed or deleted <%s|%s>', 'ht-kb-integrations'), $display_name, get_post_permalink( $post_id ), $this->article_title( $post_id )  );

            if($trashed){
                $color = 'warning';
                $icon_emoji = ':recycle:';
            } else {
                $color = '#ff0000';
                $icon_emoji = ':x:';
            }            
 
            $this->post_message_to_slack( $title, $notification_type, $message, $color, $icon_emoji );

        }

        /**
        * HT Voting Post action hook
        * When user makes a vote
        * ht_voting_vote_post_action - $users_vote, $post_id, $direction
        */
        function ht_knowledge_base_integration_slack_vote_post($users_vote, $post_id, $direction){ 

            $notification_type = ('up'===$direction) ? 'article-upvotes' : 'article-downvotes';

            //exit if upvote and not set as notify option
            if( 'up'===$direction && !apply_filters( 'ht_knowledge_base_integration_slack_notify_option', $notification_type ) ){
                return;
            }

            //exit if downvote and not set as notify option
            if( 'down'===$direction && !apply_filters( 'ht_knowledge_base_integration_slack_notify_option', $notification_type ) ){
                return;
            }

            $praise = ( 'up'===$direction ) ? __( 'helpful', 'ht-kb-integrations' ) : __( 'unhelpful', 'ht-kb-integrations' );

            $title = sprintf( __('Voted %s - %s', 'ht-kb-integrations'), $praise,  $this->article_title( $post_id ) );
           
            $display_name = $this->get_wp_current_user_display_name();
            
            $message = sprintf( __( '*%s* rated *%s* <%s|%s>', 'ht-kb-integrations'), $display_name, $praise, get_post_permalink( $post_id ), $this->article_title( $post_id )  );

            $color = ( 'up'===$direction ) ? 'good' : 'danger';
            $icon_emoji = ( 'up'===$direction ) ? ':arrow_up:' : ':arrow_down:';
 
            $this->post_message_to_slack( $title, $notification_type, $message, $color, $icon_emoji );
        }

        /**
        * HT Voting Post action hook
        * When voting comment submitted
        * ht_voting_vote_comment_action - $comment, $vote, $post_id
        */
        function ht_knowledge_base_integration_slack_add_vote_comment($comment, $vote, $post_id){ 

            $notification_type = 'article-feedback';

            //exit if not notify option
            if( !apply_filters( 'ht_knowledge_base_integration_slack_notify_option', $notification_type ) ){
                return;
            }

            $praise = ( is_a( $vote, 'HT_Vote_Up' ) ) ? __('positive', 'ht-kb-integrations' ) : __('negative', 'ht-kb-integrations' );

            $title = sprintf( __('New %s feedback on   %s', 'ht-kb-integrations'), esc_attr($praise),  $this->article_title( $post_id )  );

            $display_name = $this->get_wp_current_user_display_name();

            $message = sprintf( __( '*%s* left the following feedback on   <%s|%s>', 'ht-kb-integrations'), $display_name, get_post_permalink( $post_id ), $this->article_title( $post_id )  );
            $message .= "\n";
            $message .= apply_filters( 'ht_knowledge_base_integration_slack_add_vote_comment_pre_comment', '>> ' );
            $message .= $this->format_comment( $comment );

            $color = ( 'positive'===$praise ) ? 'good' : 'danger';
            $icon_emoji = ':speech_balloon:';
 
            $this->post_message_to_slack( $title, $notification_type, $message, $color, $icon_emoji );
        }

        /**
        * Comment Post action hook
        * comment_post - $comment_id, $comment_approved, $commentdata
        */
        function ht_knowledge_base_integration_slack_comment_post( $comment_id, $comment_approved, $commentdata ){

            $notification_type = 'article-comments';

            //exit if not notify option
            if( !apply_filters( 'ht_knowledge_base_integration_slack_notify_option', $notification_type ) ){
                return;
            }

            //@todo - may need additional check on post comment is being made on to ensure post is ht_kb type

            $post_id = $commentdata['comment_post_ID'];

            $title = sprintf( __('New comment on %s', 'ht-kb-integrations'),  $this->article_title( $post_id ) );

            $display_name = $commentdata['comment_author'];

            $message = sprintf( __( '*%s* left the following comment on  <%s|%s>', 'ht-kb-integrations'), $display_name, get_post_permalink( $post_id ), $this->article_title( $post_id )  );
            $message .= "\n";
            $message .= apply_filters( 'ht_knowledge_base_integration_slack_comment_post_pre_comment', '>> ' );
            $comment = $commentdata['comment_content'];
            $message .= $this->format_comment( $comment );

            $color =  '#0000FF';
            $icon_emoji = ':speech_balloon:';
 
            $this->post_message_to_slack( $title, $notification_type, $message, $color, $icon_emoji );

        }

        /**
        * Increment notifications count by one
        */
        function ht_knowledge_base_integration_slack_notification_counter_increment($notification_type='undefined', $title, $message){

            $current_count = $this->ht_knowledge_base_integration_slack_notification_counter_get($notification_type);
            $new_count = (int) $current_count + 1;
            $sucess = $this->ht_knowledge_base_integration_slack_notification_counter_update($notification_type, $new_count);

            //update totals
            $this->ht_knowledge_base_integration_slack_notification_total_increment();

            return $sucess;
        }

        /**
        * Get a notification count
        */
        function ht_knowledge_base_integration_slack_notification_counter_get($notification_type='undefined'){
            $count = (int) get_option( HT_KB_INTEGRATION_SLACK_COUNTER_KEY . $notification_type, 0 );

            return $count;
        }

        /**
        * Update a notification count
        */
        function ht_knowledge_base_integration_slack_notification_counter_update($notification_type='undefined', $new_value=0){
            $updated = update_option( HT_KB_INTEGRATION_SLACK_COUNTER_KEY . $notification_type, $new_value );

            return $updated;
        }

        /**
        * Increment the total notifications count
        */
        function ht_knowledge_base_integration_slack_notification_total_increment(){
            $current_count = $this->ht_knowledge_base_integration_slack_notification_total_get();
            $new_count = (int) $current_count + 1;
            $sucess = $this->ht_knowledge_base_integration_slack_notification_counter_update('total', $new_count);

            return $sucess;
        }

        /**
        * Get the total notifications count
        */
        function ht_knowledge_base_integration_slack_notification_total_get(){
            $current_count = $this->ht_knowledge_base_integration_slack_notification_counter_get('total');
            return $current_count;
        }

        /**
        * Remove all notification counter wp options
        */
        function ht_knowledge_base_integration_slack_remove_notification_counter_wp_options(){
            //delete all options
            foreach ($this->notifications as $key => $value) {
                delete_option(HT_KB_INTEGRATION_SLACK_COUNTER_KEY . $key);
            }

            //total
            delete_option(HT_KB_INTEGRATION_SLACK_COUNTER_KEY . 'total');
        }


        /**
        * Post message to slack
        */
        function post_message_to_slack($title='notification title', $notification_type='undefined', $message='notification message', $color='good', $icon_emoji=':arrow_right:', $username=false){

            global $ht_knowledge_base_settings;

            $kb_integration_slack_webhook_url = $this->get_kb_integration_slack_webhook_url();

            //exit if no webhook url specified
            if(''===$kb_integration_slack_webhook_url){
                return;
            }

            //declare attachment array
            $attachment = array();

            //apply attachment filters
            $title = apply_filters('ht_kb_slack_title', $title );
            $message = apply_filters('ht_kb_slack_message', $message );
            $color = apply_filters('ht_kb_slack_color', $color );

            $attachment[] = array(
                'fallback'  => __('Slack notification', 'ht-kb-integrations'),
                'title'     => $title,
                'text'      => $message,
                'color'     => $color,
                'mrkdwn_in' => array( 'text' ),
            );

            if(!$username){
                $username =  get_bloginfo( 'name' ) . ' ' . __('Knowledge Base Notifcation', 'ht-kb-integrations') ;
            }

            //apply payload filters
            $username = apply_filters('ht_kb_slack_username', $username );
            $attachment = apply_filters('ht_kb_slack_attachment', $attachment );
            $icon_emoji = apply_filters('ht_kb_slack_icon_emoji', $icon_emoji );
            

            $payload = array(
                'username'      => $username,
                'attachments'   => $attachment,
                'icon_emoji'    => $icon_emoji,
                /*'channel'       => HT_SN_SLACK_CHANNEL,*/
            );

            $args = array(
                'body'      => json_encode( $payload ),
                'timeout'   => 30
            );

            $response = wp_remote_post( $kb_integration_slack_webhook_url, $args );

            do_action('ht_knowledge_base_integration_slack_notification_sent', $notification_type, $title, $message);
            
            return;
        }

    }//end class

}

if (class_exists('Knowledge_Base_Integration_Slack')) {
    $ht_kb_integration_Slack_init = new Knowledge_Base_Integration_Slack();
}