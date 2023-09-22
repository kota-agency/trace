<?php

namespace Gravity_Forms\Gravity_Forms_Akismet;

use Akismet;
use GFCommon;
use GFFormsModel;
use GF_Field;
use GFAPI;

/**
 * Class Akismet_Fields_Filter
 *
 * This class handles all of the logic for looking up and mapping Gravity Forms fields to their Akismet equivalents.
 *
 * @since   1.0
 *
 * @see GF_Akismet::filter_akismet_fields()
 *
 * @package Gravity_Forms\Gravity_Forms_Akismet
 */
class Akismet_Fields_Filter {
	/**
	 * Instance of the GF_Akismet add-on.
	 *
	 * @since 1.0
	 *
	 * @var GF_Akismet
	 */
	private $addon;

	/**
	 * Add-on settings.
	 *
	 * @since 1.0
	 *
	 * @var array
	 */
	private $settings;

	/**
	 * Array of required Akismet values that are missing from the form settings.
	 *
	 * @since 1.0
	 *
	 * @var array
	 */
	private $missing_keys = array();

	/**
	 * IP address of the submission.
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	private $ip;

	/**
	 * Fields prepared for Akismet.
	 *
	 * @since 1.0
	 *
	 * @var array
	 */
	private $prepared_fields = array();

	/**
	 * Akismet_Fields_Filter constructor.
	 *
	 * @since 1.0
	 *
	 * @param GF_Akismet $addon Instance of the GF_Akismet add-on.
	 */
	public function __construct( $addon ) {
		$this->addon = $addon;
	}

	/**
	 * Hydrate this object with its provided data.
	 *
	 * @since 1.0
	 *
	 * @param array  $settings The add-on settings.
	 * @param array  $form     Gravity Forms form data.
	 * @param array  $entry    Gravity Forms entry data.
	 * @param string $action   Akismet action.
	 */
	private function hydrate( $settings, $form, $entry, $action ) {
		$this->settings = $settings;
		$this->form     = $form;
		$this->entry    = $entry;
		$this->action   = $action;
		$this->set_ip_address();
	}

	/**
	 * Set the IP address for this submission.
	 *
	 * @since 1.0
	 */
	private function set_ip_address() {
		$ip = $this->action === 'submit' && rgars( $this->form, 'personalData/preventIP' ) ? GFFormsModel::get_ip() : rgar( $this->entry, 'ip' );

		if ( ! empty( $ip ) ) {
			$ip = preg_replace( '/[^0-9A-F:.]/i', '', $ip );
		}

		$this->ip = $ip;
	}

	/**
	 * Gets the array of data in the structure required by Akismet.
	 *
	 * @since 1.0
	 *
	 * @param array  $settings       The add-on settings.
	 * @param array  $form           Gravity Forms form data.
	 * @param array  $entry          Gravity Forms entry data.
	 * @param string $action         Akismet action.
	 * @param array  $akismet_fields Fields from the Akismet add-on. Set to optional because we don't actually use them.
	 *
	 * @return array
	 */
	public function get_fields( $settings, $form, $entry, $action, $akismet_fields = array() ) {
		$this->hydrate( $settings, $form, $entry, $action );

		// Use this variable instead of reassigning $akismet_fields to make it clear that we never use the original data.
		$initial_prepared_fields = $this->initialize_akismet_fields();
		$additional_fields       = array(
			'comment_author_IP' => $this->ip,
			'user_ip'           => $this->ip,
			'permalink'         => rgar( $this->entry, 'source_url' ),
			'user_agent'        => rgar( $this->entry, 'user_agent' ),
			'referrer'          => $this->action === 'submit' ? rgar( $_SERVER, 'HTTP_REFERER' ) : '',
			'blog'              => get_option( 'home' ),
			'blog_lang'         => get_locale(),
			'blog_charset'      => get_option( 'blog_charset' ),
		);

		if ( $this->action !== 'submit' ) {
			$additional_fields['comment_date_gmt'] = rgar( $this->entry, 'date_created' );
		}

		if ( Akismet::is_test_mode() || stripos( $additional_fields['permalink'], 'gf_page=preview&id=' . rgar( $this->form, 'id' ) ) !== false ) {
			// Prevent test submissions training the Akismet filters.
			$additional_fields['is_test'] = 'true';
		} elseif ( ! empty( $this->entry['created_by'] ) ) {
			// Akismet will return false for admins.
			$additional_fields['user_role'] = Akismet::get_user_roles( $this->entry['created_by'] );
		}

		$this->prepared_fields = array_merge( $initial_prepared_fields, $additional_fields );

		return $this->prepared_fields;
	}

	/**
	 * Initializes Akismet data for processing based on values from the from settings.
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	private function initialize_akismet_fields() {
		$mapped_entry_data = $this->get_mapped_field_entry_data();
		$akismet_fields    = $this->normalize_entry_data( $mapped_entry_data );

		$this->set_missing_keys( $akismet_fields );

		return $this->missing_keys
			? $this->populate_missing_fields_with_fallbacks( $this->form, $this->entry, $akismet_fields )
			: $akismet_fields;
	}

	/**
	 * Get the entry data mapped to the form settings.
	 *
	 * Iterates through the form settings to apply values from field data or merge tags, respectively.
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	private function get_mapped_field_entry_data() {
		$akismet_fields = array(
			'comment_type' => 'gravity_form',
		);

		foreach ( $this->get_form_settings_values() as $key => $value ) {
			$akismet_fields[ $key ] = $this->get_mapped_form_settings_value_from_entry( $value );
		}

		return $akismet_fields;
	}

	/**
	 * Converts a value saved in the form settings into the actual value from the submitted form data.
	 *
	 * This method parses the field maps and text values from the form settings page in order to convert them
	 * into their actual values from the form submission. Form settings values might be individual field IDs,
	 * merge tags, or other field map types.
	 *
	 * @since 1.0
	 *
	 * @param string $form_settings_value The value saved to the form settings.
	 *
	 * @return array|mixed|string|null
	 */
	private function get_mapped_form_settings_value_from_entry( $form_settings_value ) {
		// Form setting is a merge tag.
		if ( 1 === GFCommon::has_merge_tag( $form_settings_value ) ) {
			return trim( GFCommon::replace_variables( $form_settings_value, $this->form, $this->entry, false, false, false, 'text' ) );
		}

		// Form setting is either a GF_Field or something like an entry property.
		return $this->addon->get_field_value( $this->form, $this->entry, $form_settings_value );
	}

	/**
	 * Gets all of the values saved in the form settings.
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	private function get_form_settings_values() {
		return array_filter(
			$this->settings,
			function( $key ) {
				return $key !== 'enabled';
			},
			ARRAY_FILTER_USE_KEY
		);
	}

	/**
	 * Normalizes the raw field data into the Akismet structure.
	 *
	 * This method uses the data from the form settings fields and maps the first and last name values to the comment
	 * author, if necessary.
	 *
	 * @since 1.0
	 *
	 * @param array $akismet_fields The Akismet field data.
	 *
	 * @return array
	 */
	private function normalize_entry_data( $akismet_fields ) {
		if (
			! empty( $akismet_fields['comment_author'] )
			|| ! isset( $akismet_fields['comment_author_first_name'], $akismet_fields['comment_author_last_name'] )
		) {
			return $akismet_fields;
		}

		$normalized_fields = array_merge(
			$akismet_fields,
			array(
				'comment_author' => trim( "{$akismet_fields['comment_author_first_name']} {$akismet_fields['comment_author_last_name']}" ),
			)
		);

		unset( $normalized_fields['comment_author_first_name'] );
		unset( $normalized_fields['comment_author_last_name'] );

		return $normalized_fields;
	}

	/**
	 * Set missing keys on this object.
	 *
	 * @since 1.0
	 *
	 * @param array $akismet_fields Processed Akismet field data.
	 */
	private function set_missing_keys( $akismet_fields ) {
		$this->missing_keys = array_filter(
			array(
				'author'  => empty( $akismet_fields['comment_author'] ),
				'email'   => empty( $akismet_fields['comment_author_email'] ),
				'website' => empty( $akismet_fields['comment_author_url'] ),
				'content' => empty( $akismet_fields['comment_content'] ),
			)
		);
	}

	/**
	 * Checks if the Akismet data is missing required fields and populates it with data from another matching field.
	 *
	 * @since 1.0
	 *
	 * @param array $form           The form data.
	 * @param array $entry          The entry data.
	 * @param array $akismet_fields The Akismet field data.
	 *
	 * @return array
	 */
	private function populate_missing_fields_with_fallbacks( $form, $entry, $akismet_fields ) {
		$gf_akismet_fields = $akismet_fields;

		$process_types = $this->get_fallback_types_to_process();

		if ( empty( $process_types ) ) {
			return $gf_akismet_fields;
		}

		/** @var GF_Field $field */
		foreach ( $form['fields'] as $field ) {
			if ( empty( $this->missing_keys ) ) {
				break;
			}

			$field_type = $field->get_input_type();

			if ( $field->is_administrative() || ! in_array( $field_type, $process_types ) ) {
				continue;
			}

			$value = $field->get_value_export( $entry );
			if ( empty( $value ) ) {
				continue;
			}

			if ( isset( $this->missing_keys['author'] ) && $field_type === 'name' ) {
				$this->addon->log_debug( sprintf( '%s(): comment_author is empty; using value from field #%d.', __METHOD__, $field->id ) );
				$gf_akismet_fields['comment_author'] = $value;
				unset( $this->missing_keys['author'] );
				continue;
			}

			if ( isset( $this->missing_keys['email'] ) && $field_type === 'email' ) {
				$this->addon->log_debug( sprintf( '%s(): comment_author_email is empty; using value from field #%d.', __METHOD__, $field->id ) );
				$gf_akismet_fields['comment_author_email'] = $value;
				unset( $this->missing_keys['email'] );
				continue;
			}

			if ( isset( $this->missing_keys['website'] ) && $field_type === 'website' ) {
				$this->addon->log_debug( sprintf( '%s(): comment_author_url is empty; using value from field #%d.', __METHOD__, $field->id ) );
				$gf_akismet_fields['comment_author_url'] = $value;
				unset( $this->missing_keys['website'] );
				continue;
			}

			if ( ! isset( $this->missing_keys['content'] ) || in_array( $value, $gf_akismet_fields ) ) {
				continue;
			}

			$key = $this->get_key( $field );

			$gf_akismet_fields[ $key ] = $value;
		}

		return $gf_akismet_fields;
	}

	/**
	 * Generate a key from a field's ID and label.
	 *
	 * @since 1.0
	 *
	 * @param object $field The field object.
	 *
	 * @return string $key
	 */
	private function get_key( $field ) {
		$key = sprintf(
			'contact_form_field_%d_%s',
			$field->id,
			// Normalize the label into a slug. See https://github.com/Automattic/jetpack/blob/43fee1286315992b343dd91601d5afad6c0f0d0f/modules/contact-form/grunion-contact-form.php#L2588.
			trim( // Strip all leading/trailing dashes.
				preg_replace(   // Normalize everything to a-z0-9_-.
					'/[^a-z0-9_]+/',
					'-',
					strtolower( GFFormsModel::get_label( $field, 0, false, false ) )
				),
				'-'
			)
		);

		return $key;
	}

	/**
	 * Determines which field types are needed for fallback data to send to Akismet.
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	private function get_fallback_types_to_process() {
		if ( rgar( $this->missing_keys, 'content' ) ) {
			return array(
				'address',
				'email',
				'hidden',
				'list',
				'name',
				'number',
				'phone',
				'post_content',
				'post_excerpt',
				'post_tags',
				'post_title',
				'text',
				'textarea',
				'website',
			);
		}

		$process_types = array();

		if ( rgar( $this->missing_keys, 'author' ) ) {
			$process_types[] = 'name';
		}

		if ( rgar( $this->missing_keys, 'email' ) ) {
			$process_types[] = 'email';
		}

		if ( rgar( $this->missing_keys, 'website' ) ) {
			$process_types[] = 'website';
		}

		return $process_types;
	}
}
