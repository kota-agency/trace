<?php
/**
* Knowledge Base Welcome Page
*/

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if (!class_exists('HT_Knowledge_Base_Welcome')) {

    class HT_Knowledge_Base_Welcome {
        //transient ->_ht_kb_just_installed

        function __construct(){
            //deprecated - replaced with setup/ht-kb-welcome-setup
            //add_action( 'admin_menu', array( $this, 'add_ht_kb_welcome_menus') );
            //add_action( 'admin_head', array( $this, 'ht_kb_remove_welcome_menus' ) );
            //add_action( 'admin_init', array( $this, 'ht_kb_welcome' ) );

             //enqueue scripts
            //add_action( 'admin_enqueue_scripts', array( $this, 'ht_kb_welcome_styles' ) );
        }


        /**
        * Add the knowledge base menu pages
        */
        function add_ht_kb_welcome_menus(){
            // About Page
            add_dashboard_page(
                __( 'Welcome to Heroic Knowledge Base', 'ht-knowledge-base' ),
                __( 'Welcome to Heroic Knowledge Base', 'ht-knowledge-base' ),
                'manage_options',
                'ht-kb-welcome',
                array( $this, 'ht_kb_welcome_screen' )
            );
        }

        /**
        * Remove welcome menus
        */
        public function ht_kb_remove_welcome_menus() {
            remove_submenu_page( 'index.php', 'ht-kb-welcome' );
        }

        /**
        * Welcome functionality
        */
        function ht_kb_welcome(){
            //check activation
            if ( ! get_transient( '_ht_kb_just_installed' ) )
                return;

            //delete the transient
            delete_transient( '_ht_kb_just_installed' );

            //don't run on multisite
            if ( is_network_admin() || isset( $_GET['activate-multi'] ) )
                return;

            //don't run if show_ht_kb_welcome_on_activation disabled (eg by theme)
            if( ! apply_filters('show_ht_kb_welcome_on_activation', true) )
                return;

            $upgrade = get_option( 'ht_kb_previous_version' );

            if( ! $upgrade ) {
                wp_safe_redirect( admin_url( 'index.php?page=ht-kb-welcome' ) ); 
                exit;
            } else { 
                wp_safe_redirect( admin_url( 'index.php?page=ht-kb-welcome' ) ); 
                exit;
            }
        }

       

        /**
        * Enqueue the styles
        */
        function ht_kb_welcome_styles(){
            $screen = get_current_screen();

            if(  $screen->base == 'dashboard_page_ht-kb-welcome' ) {
                wp_enqueue_style( 'hkb-style-admin', plugins_url( 'css/hkb-style-admin.css', dirname(__FILE__) ), array(), HT_KB_VERSION_NUMBER );             
            } 
        }

        /**
        * Render the welcome screen
        */
        function ht_kb_welcome_screen(){
            $current_user = wp_get_current_user();
            if ( !($current_user instanceof WP_User) ){
                return;
            }

            $ht_kb_license_status = get_option('ht_kb_license_status');
            $ht_kb_license_ok = ('valid'==$ht_kb_license_status) ? true : false;

            $articles = get_posts('post_type=ht_kb&posts_per_page=10');
            $article_count = count($articles);

            $ht_kb_show_delete_data_button = ($_GET && is_array($_GET) && array_key_exists( 'ht_kb_show_kb_delete', $_GET ) ) ? true : false;

            $ht_kb_state_text = ( $article_count > 0 ) ? __('and it looks like there are already a few articles', 'ht-knowledge-base') : __('though it does not have anything in it at the moment', 'ht-knowledge-base') ;

            ?>
            <!-- .about-wrap -->
            <div id="hkb-welcome" class="wrap about-wrap">

                <h1><?php _e('Welcome to Heroic Knowledge Base', 'ht-knowledge-base');  ?></h1>

                <div class="about-text">
                    <?php printf(__('Welcome to Heroic Knowledge Base %s, thanks for choosing this plugin, which adds a feature rich knowledge base to your WordPress powered site.', 'ht-knowledge-base'), HT_KB_VERSION_NUMBER); ?>
                </div>

                <h2 class="nav-tab-wrapper">
                    <a class="nav-tab nav-tab-active" href="#"><?php _e('Getting Started', 'ht-knowledge-base'); ?></a>
                </h2>

                <div class="hkb-grid">
                    <div class="hkb-grid__col hkb-grid__6">
                        <h3><?php _e('Knowledge Base basics', 'ht-knowledge-base'); ?></h3>
                        <ol>
                            <li>
                            <?php printf(__('Your Knowledge Base is now available from <a href="%s" target="_blank">The Knowledge Base Archive</a>, %s.', 'ht-knowledge-base') , 
                                get_permalink( ht_kb_get_kb_archive_page_id( 'default' ) ),
                                $ht_kb_state_text
                                ); ?>
                            </li>
                            <li>
                            <?php printf(__('<a href="%s" target="_blank">Add a new article</a> to create your first Knowledge Base article. These work like other WordPress posts.', 'ht-knowledge-base') , 
                                admin_url('post-new.php?post_type=ht_kb')
                                ); ?>
                            </li>
                            <li>
                            <?php printf(__('To get the most out of your Knowledge Base you should use several top-level <a href="%s" target="_blank">Knowledge Base categories</a> to organize your Knowledge Base.', 'ht-knowledge-base') , 
                                admin_url('edit-tags.php?taxonomy=ht_kb_category&post_type=ht_kb')
                                ); ?>
                            </li>
                            <li>
                            <?php printf(__('<a href="%s" target="_blank">Tags for the Knowledge Base</a> will help users find relevant and related content. ', 'ht-knowledge-base') , 
                                admin_url('edit-tags.php?taxonomy=ht_kb_tag&post_type=ht_kb')
                                ); ?>
                            </li>
                            <li>
                            <?php printf(__('You can control most options for the Knowledge Base from the <a href="%s" target="_blank">Knowledge Base Settings</a> page', 'ht-knowledge-base') , 
                                admin_url('edit.php?post_type=ht_kb&page=ht_knowledge_base_settings_page')
                                ); ?>
                            </li>
                            <li>
                            <?php _e('The in-built live search allows users to quickly find answers to their queries right from any page on the Knowledge Base. It also searches Knowledge Base categories and Knowledge Base tags.', 'ht-knowledge-base'); ?>
                            </li>
                            <li>
                                <?php printf( __('If you want to alter the default knowledge base templates, please read <a href="%s" target="_blank">the integration guide here</a>.', 'ht-knowledge-base'),  HT_HKB_INTEGRATION_GUIDE_URL ); ?>
                            </li>
                        </ol>
                    </div>
                    <div class="hkb-grid__col hkb-grid__6">
                        <h3><?php _e('Sample Content', 'ht-knowledge-base'); ?></h3>
                        <p><?php _e('Install sample articles to get an instant Knowledge Base, meaning you can experiment with example articles, categories and tags to get up and running quickly.', 'ht-knowledge-base'); ?></p>
                        <?php if($article_count>5): ?>
                            <a class="button disabled" href="#"><?php _e('Sample installed', 'ht-knowledge-base'); ?></a>     
                        <?php else: ?>
                            <a class="button" href="<?php echo wp_nonce_url( admin_url('?page=ht-kb-welcome&ht_kb_admin=install_sample'), 'add-ht-kb-sample-data' ); ?>"><?php _e('Set Up Sample Knowledge Base', 'ht-knowledge-base'); ?></a>
                        <?php endif; ?>
                        <?php if($ht_kb_show_delete_data_button): ?>
                             <a class="button" href="<?php echo wp_nonce_url( admin_url('?page=ht-kb-welcome&ht_kb_admin=delete_kb_data'), 'delete-ht-kb-data' ); ?>"><?php _e('Delete All Knowledge Base Data', 'ht-knowledge-base'); ?></a>
                        <?php endif; ?>
                        <h3><?php _e('Your Knowledge Base Archive', 'ht-knowledge-base'); ?></h3>
                        <input class="hkb-url-info disabled" type="text" value="<?php echo get_permalink( ht_kb_get_kb_archive_page_id( 'default' ) ); ?>" />
                        <p><?php printf( __('The %s knowledge base has automatically been created, it is available at the URL above, which can be used to link to your knowledge base from any menu, widget or text.', 'ht-knowledge-base') , get_bloginfo( 'name' ), HT_HKB_INTEGRATION_GUIDE_URL ); ?></p>
                    </div>

                    <div  class="hkb-grid__col hkb-grid__6">
                        <h3><?php _e('The Knowledge Base is just the start', 'ht-knowledge-base'); ?></h3>
                        <p><?php printf(__('Visit <a href="%s" target="_blank">HeroThemes</a>, or signup with the form below for more tips, guides and addons to harness the power of your new Knowledge Base', 'ht-knowledge-base') , 'https://herothemes.com'); ?></p>
                        <!-- Sign up form -->
                        <!-- Begin MailChimp Signup Form -->
                        <link href="//cdn-images.mailchimp.com/embedcode/classic-081711.css" rel="stylesheet" type="text/css" >
                        <style type="text/css">
                            #mc_embed_signup{clear:left; font:14px Helvetica,Arial,sans-serif; max-width: 600px; }
                            #mc_embed_signup .indicates-required, #mc_embed_signup .mc-field-group .asterisk {display: none;}
                            /* Add your own MailChimp form style overrides in your site stylesheet or in this style block.
                               We recommend moving this block and the preceding CSS link to the HEAD of your HTML file. */
                        </style>
                        <div id="mc_embed_signup">
                        <form action="//herothemes.us10.list-manage.com/subscribe/post?u=958c07d7ba2f4b21594564929&amp;id=db684b9928" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
                            <div id="mc_embed_signup_scroll">
                        <div class="indicates-required"><span class="asterisk">*</span> <?php _e('indicates required', 'ht-knowledge-base'); ?></div>
                        <div class="mc-field-group">
                            <label for="mce-EMAIL"><?php _e('Email Address', 'ht-knowledge-base'); ?>  <span class="asterisk">*</span>
                        </label>
                            <input type="email" value="<?php echo $current_user->user_email; ?>" name="EMAIL" class="required email" id="mce-EMAIL">
                            <input type="hidden" value="<?php echo $current_user->user_firstname; ?>" name="FNAME" class="" id="mce-FNAME">
                            <input type="hidden" value="<?php echo $current_user->user_lastname; ?>" name="LNAME" class="" id="mce-LNAME">
                            <input type="hidden" id="group_64" name="group[925][64]" value="1" /><!-- signup location = HKB Dashboard -->
                            <input type="hidden" name="SIGNUP" id="SIGNUP" value="hkb-welcome-screen" />
                        </div>
                            <div id="mce-responses" class="clear">
                                <div class="response" id="mce-error-response" style="display:none"></div>
                                <div class="response" id="mce-success-response" style="display:none"></div>
                            </div>    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
                            <div style="position: absolute; left: -5000px;"><input type="text" name="b_958c07d7ba2f4b21594564929_db684b9928" tabindex="-1" value=""></div>
                            <div class="clear"><input type="submit" value="<?php _e('Subscribe', 'ht-knowledge-base'); ?>" name="subscribe" id="mc-embedded-subscribe" class="button"></div>
                            </div>
                        </form>
                        </div>
                        <script type='text/javascript' src='//s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js'></script><script type='text/javascript'>(function($) {window.fnames = new Array(); window.ftypes = new Array();fnames[0]='EMAIL';ftypes[0]='email';fnames[1]='FNAME';ftypes[1]='text';fnames[2]='LNAME';ftypes[2]='text';}(jQuery));var $mcj = jQuery.noConflict(true);</script>
                        <!--End mc_embed_signup-->
                        </script>
                    </div>
                    <div class="hkb-grid__col hkb-grid__6">
                        <h3><?php _e('Support and Updates', 'ht-knowledge-base'); ?></h3>
                        <?php if($ht_kb_license_ok): ?>
                            <p><?php printf(__('Your license is up-to-date and valid, be sure to visit the <a href="%s" target="_blank">update page</a> regularly to keep your Knowledge Base plugin updated.', 'ht-knowledge-base'), admin_url('update-core.php')); ?></p>
                        <?php elseif( current_theme_supports('ht_kb_theme_managed_updates') || current_theme_supports('ht-kb-theme-managed-updates')  ): ?>
                            <p><?php printf(__('Your theme manages the updates and licensing for this plugin, be sure to visit the <a href="%s" target="_blank">update page</a> regularly to keep your theme updated.', 'ht-knowledge-base'), admin_url('update-core.php')); ?></p>
                        <?php else: ?>
                            <p><?php printf(__('<b>Important:</b> To enable automatic updates and support for this site, be sure to enter your license key on the <a href="%s" target="_blank">license and updates tab</a> of your Knowledge Base settings, this ensures you have the latest and supported version of the plugin.', 'ht-knowledge-base'), admin_url('edit.php?post_type=ht_kb&page=ht_knowledge_base_settings_page#license-section')); ?></p>
                        <?php endif; ?>
                        <p><?php printf(__('If you need any assistance in setting up your Knowledge Base, be sure to check out the documentation and knowledge base at the <a href="%s" target="_blank">Heroic Knowledge Base Documentation Page</a>.', 'ht-knowledge-base'), HT_KB_SUPPORT_URL); ?></p>
                    </div>
                </div>

            <!-- /.about-wrap -->
               </div>
            <?php
        }

    }
}

if (class_exists('HT_Knowledge_Base_Welcome')) {
    new HT_Knowledge_Base_Welcome();
}