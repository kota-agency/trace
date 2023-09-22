<?php
/**
 * Object responsible for organizing and constructing the form settings page.
 *
 * @package Gravity_Forms\Gravity_Forms_Akismet\Settings
 */

namespace Gravity_Forms\Gravity_Forms_Akismet\Settings;

use Gravity_Forms\Gravity_Forms_Akismet\GF_Akismet;
use GFAPI;
use GFFormsModel;

/**
 * Class Form_Settings
 *
 * @since   1.0
 * @package Gravity_Forms\Gravity_Forms_Akismet\Settings
 */
class Form_Settings {
	/**
	 * Add-on instance.
	 *
	 * @var GF_Akismet
	 */
	private $addon;

	/**
	 * Plugin_Settings constructor.
	 *
	 * @since 1.0
	 *
	 * @param GF_Akismet $addon GF_Akismet instance.
	 */
	public function __construct( $addon ) {
		$this->addon = $addon;
	}

	/**
	 * Get the form settings fields.
	 *
	 * @since 1.0
	 * @see   GF_Akismet::form_settings_fields()
	 *
	 * @param array $form The form data.
	 *
	 * @return array
	 */
	public function get_fields( $form ) {
		return array(
			array(
				'title'  => esc_html__( 'Akismet Settings', 'gravityformsakismet' ),
				'fields' => array(
					$this->get_akismet_enabled_field(),
					array(
						'name'  => 'comment_author_first_name',
						'label' => esc_html__( 'First Name', 'gravityformsakismet' ),
						'type'  => 'field_select',
						'args'  => array(
							'input_types' => array( 'name', 'text', 'hidden' ),
						),
					),
					array(
						'name'  => 'comment_author_last_name',
						'label' => esc_html__( 'Last Name', 'gravityformsakismet' ),
						'type'  => 'field_select',
						'args'  => array(
							'input_types' => array( 'name', 'text', 'hidden' ),
						),
					),
					array(
						'name'  => 'comment_author_email',
						'label' => esc_html__( 'Email', 'gravityformsakismet' ),
						'type'  => 'field_select',
						'args'  => array(
							'input_types' => array( 'email', 'text', 'hidden' ),
						),
					),
					array(
						'name'  => 'comment_author_url',
						'label' => esc_html__( 'Website', 'gravityformsakismet' ),
						'type'  => 'field_select',
						'args'  => array(
							'input_types' => array( 'website', 'text', 'hidden' ),
						),
					),
					array(
						'name'          => 'contact_form_subject',
						'label'         => esc_html__( 'Subject', 'gravityformsakismet' ),
						'type'          => 'text',
						'class'         => 'medium merge-tag-support mt-position-right mt-hide_all_fields',
						'default_value' => '{form_title}',
					),
					array(
						'name'          => 'comment_content',
						'label'         => esc_html__( 'Content', 'gravityformsakismet' ),
						'type'          => 'textarea',
						'class'         => 'medium merge-tag-support mt-position-right',
						'default_value' => $this->get_default_merge_tag( $form, 'comment_content' ),
					),
				),
			),
		);
	}

	/**
	 * Gets the field for controlling whether or not Gravity Forms Akismet is enabled for the given form.
	 *
	 * Newer versions of Gravity Forms use a toggle for this control, while earlier versions use a checkbox.
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	private function get_akismet_enabled_field() {
		$field_name          = 'enabled';
		$enabled_description = esc_html__( "Enable to protect this form's entries from spam using Akismet", 'gravityformsakismet' );

		if ( ! $this->addon->is_gravityforms_supported( '2.5' ) ) {
			return array(
				'label'       => esc_html__( 'Akismet enabled', 'gravityformsakismet' ),
				'description' => $enabled_description,
				'type'        => 'checkbox',
				'name'        => $field_name,
				'choices'     => array(
					array(
						'label'         => esc_html__( 'Enabled', 'gravityformsakismet' ),
						'name'          => $field_name,
						'default_value' => '1',
					),
				),
			);
		}

		return array(
			'label'         => $enabled_description,
			'type'          => 'toggle',
			'name'          => $field_name,
			'default_value' => '1',
		);
	}

	/**
	 * Get the defaults for the form settings fields.
	 *
	 * @since 1.0
	 *
	 * @param array $form  The form data.
	 * @param array $entry The entry data.
	 *
	 * @return array
	 */
	public function get_default_settings( $form, $entry ) {
		return array(
			'comment_author'       => $this->get_default_merge_tag( $form, 'comment_author', $entry ),
			'comment_author_email' => $this->get_default_merge_tag( $form, 'comment_author_email', $entry ),
			'comment_author_url'   => $this->get_default_merge_tag( $form, 'comment_author_url', $entry ),
			'contact_form_subject' => '{form_title}',
			'comment_content'      => '', // Can be empty when using the contact_form_field_ keys.
		);
	}

	/**
	 * Returns the merge tag to be used as the setting default value.
	 *
	 * @since 1.0
	 *
	 * @param array      $form  The current form.
	 * @param string     $name  The field name.
	 * @param null|array $entry Null when preparing the form settings or the entry currently being processed.
	 *
	 * @return string
	 */
	public function get_default_merge_tag( $form, $name, $entry = null ) {
		if ( $entry && empty( $entry['created_by'] ) ) {
			// Entry processing will loop through the fields so we don't need to do it here.
			return '';
		}

		$mapping = rgar( $this->default_mappings(), $name );

		// Using the created_by merge tag for forms where the user is logged in.
		if ( $entry || rgar( $form, 'requireLogin' ) ) {
			return $mapping['merge_tag'];
		}

		$fields = GFAPI::get_fields_by_type( $form, $mapping['field_type'], true );

		if ( empty( $fields ) ) {
			return '';
		}

		if ( $name === 'comment_author' ) {
			$first_input_id = $fields[0]->id . '.3';
			$last_input_id  = $fields[0]->id . '.6';

			return sprintf( '{%s:%s} {%s:%s}', GFFormsModel::get_label( $fields[0], $first_input_id ), $first_input_id, GFFormsModel::get_label( $fields[0], $last_input_id ), $last_input_id );
		}

		return sprintf( '{%s:%d}', GFFormsModel::get_label( $fields[0] ), $fields[0]->id );
	}

	/**
	 * Returns the default mappings configuration for the Akismet settings.
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public function default_mappings() {
		return array(
			'comment_author'       => array(
				'field_type' => array( 'name' ),
				'merge_tag'  => '{created_by:first_name} {created_by:last_name}',
			),
			'comment_author_email' => array(
				'field_type' => array( 'email' ),
				'merge_tag'  => '{created_by:user_email}',
			),
			'comment_author_url'   => array(
				'field_type' => array( 'website' ),
				'merge_tag'  => '{created_by:user_url}',
			),
			'comment_content'      => array(
				'field_type' => array( 'paragraph', 'post_content' ),
				'merge_tag'  => '',
			),
		);
	}
}
