<?php

namespace Gravity_Forms\Gravity_Forms_Akismet;

defined( 'ABSPATH' ) || die();

use GFForms;
use GFAddOn;
use GFCommon;
use GFAPI;
use GFFormsModel;
use Akismet;
use Gravity_Forms\Gravity_Forms_Akismet\Settings;

// Include the Gravity Forms Add-On Framework.
GFForms::include_addon_framework();

/**
 * Gravity Forms Akismet Add-On.
 *
 * @since     1.0
 * @package   GravityForms
 * @author    Gravity Forms
 * @copyright Copyright (c) 2020-2021, Gravity Forms
 */
class GF_Akismet extends GFAddOn {

	/**
	 * Contains an instance of this class, if available.
	 *
	 * @since  1.0
	 * @var    GF_Akismet $_instance If available, contains an instance of this class
	 */
	private static $_instance = null;

	/**
	 * Defines the version of the Gravity Forms Akismet Add-On.
	 *
	 * @since  1.0
	 * @var    string $_version Contains the version.
	 */
	protected $_version = GF_AKISMET_VERSION;

	/**
	 * Defines the minimum Gravity Forms version required.
	 *
	 * @since  1.0
	 * @var    string $_min_gravityforms_version The minimum version required.
	 */
	protected $_min_gravityforms_version = GF_AKISMET_MIN_GF_VERSION;

	/**
	 * Defines the plugin slug.
	 *
	 * @since  1.0
	 * @var    string $_slug The slug used for this plugin.
	 */
	protected $_slug = 'gravityformsakismet';

	/**
	 * Defines the main plugin file.
	 *
	 * @since  1.0
	 * @var    string $_path The path to the main plugin file, relative to the plugins folder.
	 */
	protected $_path = 'gravityformsakismet/akismet.php';

	/**
	 * Defines the full path to this class file.
	 *
	 * @since  1.0
	 * @var    string $_full_path The full path.
	 */
	protected $_full_path = __FILE__;

	/**
	 * Defines the URL where this add-on can be found.
	 *
	 * @since  1.0
	 * @var    string The URL of the Add-On.
	 */
	protected $_url = 'https://gravityforms.com';

	/**
	 * Defines the title of this add-on.
	 *
	 * @since  1.0
	 * @var    string $_title The title of the add-on.
	 */
	protected $_title = 'Gravity Forms Akismet Add-On';

	/**
	 * Defines the short title of the add-on.
	 *
	 * @since  1.0
	 * @var    string $_short_title The short title.
	 */
	protected $_short_title = 'Akismet';

	/**
	 * Defines if Add-On should use Gravity Forms servers for update data.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    bool
	 */
	protected $_enable_rg_autoupgrade = true;

	/**
	 * Defines the capabilities needed for the Gravity Forms Akismet Add-On
	 *
	 * @since  1.0
	 * @access protected
	 * @var    array $_capabilities The capabilities needed for the Add-On
	 */
	protected $_capabilities = array(
		'gravityforms_akismet',
		'gravityforms_akismet_uninstall',
	);

	/**
	 * Defines the capability needed to access the Add-On settings page.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_capabilities_settings_page The capability needed to access the Add-On settings page.
	 */
	protected $_capabilities_settings_page = 'gravityforms_akismet';

	/**
	 * Defines the capability needed to access the Add-On form settings page.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_capabilities_form_settings The capability needed to access the Add-On form settings page.
	 */
	protected $_capabilities_form_settings = 'gravityforms_akismet';

	/**
	 * Defines the capability needed to uninstall the Add-On.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_capabilities_uninstall The capability needed to uninstall the Add-On.
	 */
	protected $_capabilities_uninstall = 'gravityforms_akismet_uninstall';

	/**
	 * Instance of the object responsible for mapping Gravity Forms fields to the Akismet array.
	 *
	 * @since 1.0
	 *
	 * @var Akismet_Fields_Filter
	 */
	private $akismet_fields_filter;

	/**
	 * Wrapper class for form settings.
	 *
	 * @since 1.0
	 * @var Settings\Form_Settings
	 */
	private $form_settings;

	/**
	 * Returns an instance of this class, and stores it in the $_instance property.
	 *
	 * @since  1.0
	 *
	 * @return GF_Akismet $_instance An instance of the GF_Akismet class.
	 */
	public static function get_instance() {

		if ( self::$_instance == null ) {
			self::$_instance = new GF_Akismet();
		}

		return self::$_instance;

	}

	/**
	 * Set minimum requirements for the add-on.
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public function minimum_requirements() {
		return array(
			array( $this, 'check_minimum_requirements' ),
		);
	}

	/**
	 * Callback method to the `minimum_requirements` override.
	 *
	 * This method ensures we have all of the minimum requirements needed run the add-on.
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public function check_minimum_requirements() {
		$meets_requirements = true;
		$errors             = array();

		if ( ! GFCommon::has_akismet() ) {
			$meets_requirements = false;
			$errors[]           = esc_html__( 'The Akismet plugin is either inactive or not installed.', 'gravityformsakismet' );
		}

		if ( ! $this->is_enabled_global() ) {
			$meets_requirements = false;
			$errors[]           = esc_html__( 'To use this add-on, please visit the Forms -> Settings page to enable Akismet integration', 'gravityformsakismet' );
		}

		return $meets_requirements
			? array( 'meets_requirements' => true )
			: array(
				'meets_requirements' => false,
				'errors'             => $errors,
			);
	}

	/**
	 * Pre-initialize add-on services.
	 *
	 * @since 1.0
	 */
	public function pre_init() {
		require_once __DIR__ . '/includes/class-akismet-fields-filter.php';
		require_once __DIR__ . '/includes/settings/class-form-settings.php';

		$this->akismet_fields_filter = new Akismet_Fields_Filter( $this );
		$this->form_settings         = new Settings\Form_Settings( $this );

		parent::pre_init();
	}

	/**
	 * Register initialization hooks.
	 *
	 * @since  1.0
	 */
	public function init() {
		parent::init();

		if ( ! rgar( $this->check_minimum_requirements(), 'meets_requirements' ) ) {
			return;
		}

		add_filter( 'gform_akismet_enabled', array( $this, 'filter_akismet_enabled' ), 1, 2 );
		add_filter( 'gform_akismet_fields', array( $this, 'filter_akismet_fields' ), 1, 4 );

		if ( ! $this->is_gravityforms_supported( '2.5' ) ) {
			add_filter( 'gform_pre_replace_merge_tags', array( $this, 'filter_pre_replace_merge_tags' ), 10, 7 );
		}
	}

	/**
	 * Determines if the Akismet integration is enabled for the site on the Forms > Settings page.
	 *
	 * @since  1.0
	 *
	 * @return bool
	 */
	public function is_enabled_global() {
		$enabled = get_option( 'rg_gforms_enable_akismet' );

		return $enabled === false ? true : $enabled === '1';
	}

	/**
	 * Determines if the Akismet integration is enabled for the supplied form.
	 *
	 * @since  1.0
	 *
	 * @param array $form The current form.
	 *
	 * @return bool
	 */
	public function is_enabled_form( $form ) {
		$settings = $this->get_form_settings( $form );

		return empty( $settings ) || rgar( $settings, 'enabled' ) === '1';
	}

	/**
	 * Enables or disables Akismet based on the form settings.
	 *
	 * @since 1.0
	 *
	 * @param bool $enabled Indicates if Akismet is enabled.
	 * @param int  $form_id The ID of the form being processed.
	 *
	 * @return bool
	 */
	public function filter_akismet_enabled( $enabled, $form_id ) {
		if ( ! $enabled ) {
			return false;
		}

		if ( ! Akismet::get_api_key() ) {
			$this->log_debug( __METHOD__ . '(): Aborting; Akismet is not configured.' );

			return false;
		}

		$form = GFAPI::get_form( $form_id );

		if ( ! $form ) {
			return false;
		}

		return $this->is_enabled_form( $form );
	}

	/**
	 * Replaces the default Akismet field mappings with the new mappings based on the form specific configuration.
	 *
	 * @since 1.0
	 *
	 * @param array  $akismet_fields The data passed from Akismet to Gravity Forms.
	 * @param array  $form           The form which created the entry.
	 * @param array  $entry          The form which created the entry.
	 * @param string $action         The action triggering the Akismet request: submit, spam, or ham.
	 *
	 * @return array
	 */
	public function filter_akismet_fields( $akismet_fields, $form, $entry, $action ) {
		$this->log_debug( sprintf( '%s(): action: %s; form: %d; entry: %d.', __METHOD__, $action, rgar( $form, 'id' ), rgar( $entry, 'id' ) ) );

		$settings = $this->get_form_settings( $form );

		if ( empty( $settings ) ) {
			$this->log_debug( __METHOD__ . '(): settings not configured; using defaults.' );
			$settings = $this->form_settings->get_default_settings( $form, $entry );
		}

		$this->log_debug( __METHOD__ . '(): settings => ' . print_r( $settings, true ) );

		// Use this variable instead of reassigning $akismet_fields to make it clear that we never use the original data.
		$gf_akismet_fields = $this->akismet_fields_filter->get_fields( $settings, $form, $entry, $action, $akismet_fields );

		$this->log_debug( __METHOD__ . '(): $akismet_fields => ' . print_r( $gf_akismet_fields, true ) );

		add_action( 'http_api_debug', array( $this, 'handle_akismet_response' ), 10, 5 );

		return $gf_akismet_fields;
	}

	/**
	 * Handles any necessary processes after receiving a response from Akismet.
	 *
	 * @since 1.0
	 *
	 * @param array|WP_Error $response HTTP response or WP_Error object.
	 * @param string         $context  Context under which the hook is fired.
	 * @param string         $class    HTTP transport used.
	 * @param array          $args     HTTP request arguments.
	 * @param string         $url      The request URL.
	 */
	public function handle_akismet_response( $response, $context, $class, $args, $url ) {
		if ( ! $this->is_akismet_response( $response, $args, $url ) ) {
			return;
		}

		$this->log_debug( __METHOD__ . '(): request body => ' . $args['body'] );

		$response_body = wp_remote_retrieve_body( $response );

		$this->log_debug(
			__METHOD__ . '(): response => '
			. print_r(
				array(
					wp_remote_retrieve_headers( $response ),
					$response_body,
				),
				true
			)
		);

		$this->maybe_mark_as_spam( $response_body );

		remove_action( 'http_api_debug', array( $this, 'handle_akismet_response' ) );
	}

	/**
	 * Checks whether the current response being processed is for Akismet.
	 *
	 * @since 1.0
	 *
	 * @param array|WP_Error $response The API response.
	 * @param array          $args     HTTP request arguments.
	 * @param string         $url      The request URL.
	 *
	 * @return bool
	 */
	private function is_akismet_response( $response, $args, $url ) {
		return (
			rgar( $args, 'method' ) === 'POST'
			&& stripos( $url, 'rest.akismet.com' ) !== false
			&& ! is_wp_error( $response )
		);
	}

	/**
	 * Replaces the created_by merge tag.
	 *
	 * @since 1.0
	 *
	 * @param string $text       The current text in which merge tags are being replaced.
	 * @param array  $form       The current form object.
	 * @param array  $entry      The current entry object.
	 * @param bool   $url_encode Whether or not to encode any URLs found in the replaced value.
	 * @param bool   $esc_html   Whether or not to encode HTML found in the replaced value.
	 * @param bool   $nl2br      Whether or not to convert newlines to break tags.
	 * @param string $format     The format requested for the location the merge is being used. Possible values: html, text or url.
	 *
	 * @return string
	 */
	public function filter_pre_replace_merge_tags( $text, $form, $entry, $url_encode, $esc_html, $nl2br, $format ) {
		if ( strpos( $text, '{' ) === false ) {
			return $text;
		}

		preg_match_all( '/{created_by:(.*?)}/', $text, $matches, PREG_SET_ORDER );

		if ( empty( $matches ) ) {
			return $text;
		}

		$entry_creator = ! empty( $entry['created_by'] ) ? get_userdata( $entry['created_by'] ) : false;
		foreach ( $matches as $match ) {
			$full_tag = $match[0];
			$property = $match[1];

			if ( $entry_creator && $property !== 'user_pass' ) {
				$value = $entry_creator->get( $property );
				$value = $url_encode ? urlencode( $value ) : $value;
			} else {
				$value = '';
			}

			$text = str_replace( $full_tag, $value, $text );
		}

		return $text;
	}

	// # FORM SETTINGS -------------------------------------------------------------------------------------------------

	/**
	 * Define form settings fields.
	 *
	 * @since  1.0
	 *
	 * @param array $form The current form.
	 *
	 * @return array
	 */
	public function form_settings_fields( $form ) {
		return $this->form_settings->get_fields( $form );
	}

	/**
	 * The settings page icon.
	 *
	 * @since 1.0
	 * @return string
	 */
	public function get_menu_icon() {
		return 'gform-icon--akismet';
	}

	/**
	 * Adds additional actions for an entry if it needs to be marked as spam.
	 *
	 * @since 1.0
	 *
	 * @param array $response_body HTTP response body.
	 */
	private function maybe_mark_as_spam( $response_body ) {
		if ( $response_body !== 'true' ) {
			return;
		}

		add_action( 'gform_entry_created', array( $this, 'add_marked_as_spam_note_to_entry' ) );
	}

	/**
	 * Adds a note to an entry at the time that it is marked as spam.
	 *
	 * @since 1.0
	 *
	 * @param array $entry The entry data.
	 */
	public function add_marked_as_spam_note_to_entry( $entry ) {
		if ( rgar( $entry, 'status' ) !== 'spam' ) {
			return;
		}

		$this->log_debug( __METHOD__ . '(): marking entry as spam.' );

		$this->add_note( rgar( $entry, 'id' ), esc_html__( 'This entry has been marked as spam.', 'gravityformsakismet' ), 'success' );
	}
}
