<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://iamankitpanchal.com/
 * @since      1.7.0
 *
 * @package    hab_Hide_Admin_Bar_Based_On_User_Roles
 * @subpackage hab_Hide_Admin_Bar_Based_On_User_Roles/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    hab_Hide_Admin_Bar_Based_On_User_Roles
 * @subpackage hab_Hide_Admin_Bar_Based_On_User_Roles/public
 * @author     Ankit Panchal <ankitmaru@live.in>
 */
class hab_Hide_Admin_Bar_Based_On_User_Roles_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.7.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.7.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.7.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.7.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in hab_Hide_Admin_Bar_Based_On_User_Roles_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The hab_Hide_Admin_Bar_Based_On_User_Roles_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/hide-admin-bar-based-on-user-roles-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.7.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in hab_Hide_Admin_Bar_Based_On_User_Roles_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The hab_Hide_Admin_Bar_Based_On_User_Roles_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/hide-admin-bar-based-on-user-roles-public.js', array( 'jquery' ), $this->version, false );

	}

	public function hab_hide_admin_bar(){
		global $wpdb;
		
		if( is_multisite() ) {
			$current_user_object = new WP_User(
			    get_current_user_id(),
			    get_current_blog_id()
			);
		} else {
			$current_user_object = wp_get_current_user();
		}

		$settings = [];

		if( is_multisite() ) {
			$settings_sa = get_network_option( 1, "hab_settings" );
			$settings = get_network_option( get_current_blog_id(), "hab_settings" );
		} else {
			$settings = get_option("hab_settings");	
		}

    	$plgUserRoles = ( isset($settings["hab_userRoles"]) ) ? $settings["hab_userRoles"] : "";
    	$hab_capabilities = ( isset($settings["hab_capabilities"]) && !empty($settings["hab_capabilities"]) )  ? explode(",",$settings["hab_capabilities"]) : "";
    	$hab_disableforall = ( isset($settings["hab_disableforall"]) ) ? $settings["hab_disableforall"] : "";
    	$hab_disableforallGuests = ( isset($settings["hab_disableforallGuests"]) ) ? $settings["hab_disableforallGuests"] : "";
    	$hab_super_admin = ( isset($settings["hab_super_admin"]) ) ? $settings["hab_super_admin"] : "";

    	$userCap = 0;
    	if( is_array($hab_capabilities) && count($hab_capabilities) > 0 ) {
	    	foreach( $hab_capabilities as $caps ){
		    	if( current_user_can( $caps ) ) { 
		    		$userCap = 1;
		    		break;
		    	}
	    	}
    	}

    	$flag = 0;

    	if( $hab_disableforall == 'yes' ){
    		$flag = 1;
    		show_admin_bar( false );
		}

		if( is_array($plgUserRoles) && array_intersect($plgUserRoles, $current_user_object->roles ) ) { 
			$flag = 1;
    		show_admin_bar( false );
    	}

    	if( $userCap == 1 ){
    		$flag = 1;
    		show_admin_bar( false );
    	}

    	if( strval($hab_disableforallGuests) == 'yes' && ! is_user_logged_in() ){
    		$flag = 1;
    		show_admin_bar( false );
    	}

    	if( $flag == 0 ){
    		show_admin_bar( true );
    	}

    	if( is_super_admin() && $hab_super_admin == 'yes' ){
			show_admin_bar( false );
		}

		if( isset($settings_sa['hab_disableforall']) && $settings_sa['hab_disableforall'] == 'yes' ){
			show_admin_bar( false );
		}
		
	}


}
