<?php

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'acf_field_font_awesome' ) ) :

	class acf_field_font_awesome extends acf_field
	{
		private $icons = false;
		private $version;

		public function __construct( $settings )
		{
			$this->version = 'v' . ACFFA_MAJOR_VERSION;
			$this->name = 'font-awesome';	
			$this->label = __( 'Font Awesome Icon', 'acf-font-awesome');
			$this->category = 'content';
			$this->settings = $settings;

			$this->defaults = [
				'enqueue_fa' 		=>	0,
				'allow_null' 		=>	0,
				'show_preview'		=>	1,
				'save_format'		=>  'element',
				'default_value'		=>	'',
				'default_label'		=>	'',
				'fa_live_preview'	=>	'',
				'choices'			=>	[]
			];

			parent::__construct();

			if ( apply_filters( 'ACFFA_always_enqueue_fa', false ) ) {
				add_action( 'wp_enqueue_scripts', [ $this, 'frontend_enqueue_scripts' ] );
			} else {
				add_filter( 'acf/load_field', [ $this, 'maybe_enqueue_font_awesome' ] );
			}

			add_filter( 'ACFFA_v5_upgrade_compat_selected_field_sets', [ $this, 'v5_upgrade_compat_selected_field_sets' ], 5, 1 );
			add_filter( 'ACFFA_v5_upgrade_compat_format_value', [ $this, 'v5_upgrade_compat_format_value' ], 5, 2 );
		}
		
		public function render_field_settings( $field )
		{
			if ( apply_filters( 'ACFFA_show_fontawesome_pro_blurbs', true ) ) {
				acf_render_field_setting( $field, [
					'label'			=> __( 'Get FontAwesome Pro', 'acf-font-awesome' ),
					'message'		=> '<p>' . __( 'Support this plugin and get more icons across more styles plus helpful services, regular updates, a lifetime license, and actual human support.', 'acf-font-awesome' ) . '</p>' . '<a class="get-acfpro-btn" target="_blank" href="https://fontawesome.com/referral?a=0032f3e781"><i class="fa-solid fa-carrot"></i>' . __( 'Upgrade to Font Awesome Pro!', 'acf-font-awesome' ) . '</a>',
					'type'			=> 'message',
					'name'			=> 'get-fontawesome-pro',
					'class'			=> 'get-fontawesome-pro'
				] );
			}

			$icon_sets_args = [
				'label'			=> __( 'Icon Sets', 'acf-font-awesome' ),
				'instructions'	=> __( 'Specify which icon set(s) to load', 'acf-font-awesome' ),
				'type'			=> 'checkbox',
				'name'			=> 'icon_sets',
			];

			$icon_sets_args['choices'] = [
				'solid'		=> __( 'Solid', 'acf-font-awesome' ),
				'regular'	=> __( 'Regular', 'acf-font-awesome' ),
				'light'		=> __( 'Light', 'acf-font-awesome' ) . ' (' . '<a target="_blank" href="https://fontawesome.com/referral?a=0032f3e781">' . __( 'FontAwesome Pro License Required', 'acf-font-awesome' ) . '</a>)',
				'thin'		=> __( 'Thin', 'acf-font-awesome' ) . ' (' . '<a target="_blank" href="https://fontawesome.com/referral?a=0032f3e781">' . __( 'FontAwesome Pro License Required', 'acf-font-awesome' ) . '</a>)',
				'duotone'	=> __( 'Duotone', 'acf-font-awesome' ) . ' (' . '<a target="_blank" href="https://fontawesome.com/referral?a=0032f3e781">' . __( 'FontAwesome Pro License Required', 'acf-font-awesome' ) . '</a>)',
				'fak'		=> __( 'Uploaded Icons', 'acf-font-awesome' ) . ' (' . '<a target="_blank" href="https://fontawesome.com/referral?a=0032f3e781">' . __( 'FontAwesome Pro License Required', 'acf-font-awesome' ) . '</a>)',
				'brands'	=> __( 'Brands', 'acf-font-awesome' ),
				'custom'	=> __( 'Custom Icon Set', 'acf-font-awesome' )
			];

			$selected_field_sets	= ! empty( $field['icon_sets'] ) ? $field['icon_sets'] : [ 'solid', 'regular', 'brands' ];
			$selected_field_sets	= apply_filters( 'ACFFA_v5_upgrade_compat_selected_field_sets', $selected_field_sets );

			$icon_sets_args['value'] = $selected_field_sets;


			acf_render_field_setting( $field, $icon_sets_args );

			$custom_icon_set_choices = get_option( 'ACFFA_custom_icon_sets_list' );
			if ( isset( $custom_icon_set_choices[ $this->version ] ) && ! empty( $custom_icon_set_choices[ $this->version ] ) ) {
				$custom_icon_set_choices = $custom_icon_set_choices[ $this->version ];
			} else {
				$custom_icon_set_choices = [ __( 'No custom icon set(s) found', 'acf-font-awesome' ) ];
			}

			acf_render_field_setting( $field, [
				'label'			=> __( 'Custom Icon Set', 'acf-font-awesome' ),
				'instructions'	=> sprintf( __( 'Create custom icon sets in the <a href="%s">FontAwesome Settings page</a>.', 'acf-font-awesome' ), admin_url( '/edit.php?post_type=acf-field-group&page=fontawesome-settings' ) ),
				'type'			=> 'select',
				'name'			=> 'custom_icon_set',
				'class'	  		=> 'custom-icon-set',
				'choices'		=> $custom_icon_set_choices,
				'value'			=> isset( $field['custom_icon_set'] ) ? $field['custom_icon_set'] : false,
				'placeholder'	=> 'Choose an icon set',
				'allow_null'	=> 1
			] );

			acf_render_field_setting( $field, [
				'label'			=> __( 'Icon Preview', 'acf-font-awesome' ),
				'instructions'	=> '',
				'type'			=> 'message',
				'name'			=> 'fa_live_preview',
				'class'			=> 'live-preview'
			] );

			acf_render_field_setting( $field, [
				'label'			=> __( 'Default Label', 'acf-font-awesome' ),
				'instructions'	=> 'Used internally to store the select label for the default icon. For performance reasons.',
				'type'			=> 'text',
				'name'			=> 'default_label',
				'value'			=> ! empty ( $field['default_label'] ) ? $field['default_label'] : $field['default_value'],
				'class'			=> 'default_value'
			] );

			acf_render_field_setting( $field, [
				'label'			=> __( 'Default Icon', 'acf-font-awesome' ),
				'instructions'	=> '',
				'type'			=> 'select',
				'name'			=> 'default_value',
				'class'	  		=> 'select2-fontawesome fontawesome-create',
				'choices'		=>  ! empty( $field['default_label'] ) ? [ $field['default_value'] => html_entity_decode( $field['default_label'] ) ] : [ $field['default_value'] => $field['default_value'] ],
				'value'			=> $field['default_value'],
				'placeholder'	=> 'Choose a default icon (optional)',
				'ui'			=> 1,
				'allow_null'	=> 1,
				'ajax'			=> 1,
				'ajax_action'	=> 'acf/fields/font-awesome/query'
			] );

			acf_render_field_setting( $field, [
				'label'			=> __( 'Return Value', 'acf-font-awesome' ),
				'instructions'	=> __( 'Specify the returned value on front end', 'acf-font-awesome' ),
				'type'			=> 'radio',
				'name'			=> 'save_format',
				'choices'	=>	[
					'element'	=>	__( 'Icon Element', 'acf-font-awesome' ),
					'class'		=>	__( 'Icon Class', 'acf-font-awesome' ),
					'unicode'	=>	__( 'Icon Unicode', 'acf-font-awesome' ),
					'object'	=>	__( 'Icon Object', 'acf-font-awesome' ),
				]
			] );

			acf_render_field_setting( $field, [
				'label'			=> __( 'Allow Null?', 'acf-font-awesome' ),
				'instructions'	=> '',
				'type'			=> 'radio',
				'name'			=> 'allow_null',
				'choices'	=>	[
					1	=>	__( 'Yes', 'acf-font-awesome' ),
					0	=>	__( 'No', 'acf-font-awesome' )
				]
			] );

			acf_render_field_setting( $field, [
				'label'			=> __( 'Show Icon Preview', 'acf-font-awesome' ),
				'instructions'	=> __( 'Set to \'Yes\' to include a larger icon preview on any admin pages using this field.', 'acf-font-awesome' ),
				'type'			=> 'radio',
				'name'			=> 'show_preview',
				'choices'	=>	[
					1	=>	__( 'Yes', 'acf-font-awesome' ),
					0	=>	__( 'No', 'acf-font-awesome' )
				]
			] );

			if ( ! apply_filters( 'ACFFA_always_enqueue_fa', false ) ) {
				acf_render_field_setting( $field, [
					'label'			=> __( 'Enqueue FontAwesome?', 'acf-font-awesome' ),
					'instructions'	=> __( 'Set to \'Yes\' to enqueue FA in the footer on any pages using this field.', 'acf-font-awesome' ),
					'type'			=> 'radio',
					'name'			=> 'enqueue_fa',
					'choices'	=>	[
						1	=>	__( 'Yes', 'acf-font-awesome' ),
						0	=>	__( 'No', 'acf-font-awesome' )
					]
				] );
			}
		}

		public function render_field( $field )
		{	
			if ( $field['allow_null'] ) {
				$select_value = $field['value'];
			} else {
				$select_value = ( 'null' != $field['value'] ) ? $field['value'] : $field['default_value'];
			}

			$v5_icon_preselected = false;

			$field['type']		= 'select';
			$field['ui']		= 1;
			$field['ajax']		= 1;
			$field['choices']	= [];
			$field['multiple']	= false;
			$field['class']		= $v5_icon_preselected ? 'v5_icon_preselected' : '';
			if ( ! empty( $field['icon_sets'] ) && in_array( 'custom', $field['icon_sets'] ) && ! empty( $field['custom_icon_set'] ) ) {
				$field['class'] .= ' fa6 select2-fontawesome fontawesome-edit custom-icon-set';
			} else {
				$field['class'] .= ' fa6 select2-fontawesome fontawesome-edit';
			}

			if ( $select_value ) :
				$icon_info = json_decode( $select_value );
				if ( is_object( $icon_info ) ) {
					if ( 'fak' == $icon_info->style ) {
						$field['choices'][ $select_value ] = '<i class="' . $icon_info->style . ' fa-' . $icon_info->id . ' fa-fw"></i> ' . $icon_info->label;
					} else {
						$field['choices'][ $select_value ] = '<i class="fa-' . $icon_info->style . ' fa-' . $icon_info->id . ' fa-fw"></i> ' . $icon_info->label;
					}
				} else {
					$v5_icon_preselected	= true;
					$options				= get_option( 'acffa_settings' );
					$label					= isset( $options['acffa_v5_compatibility_mode'] ) && $options['acffa_v5_compatibility_mode'] ? '[v5-compat-lookup]' : false;

					$field['choices'][ $select_value ] = $label;
				}
			endif;

			if ( $field['show_preview'] ) :
				if ( $v5_icon_preselected ) :
					?>
					<div class="icon_preview v5-compat-alert show-alert">
						<i class="fas fa-exclamation-circle"></i>
					</div>
					<?php
				else :
					?>
					<div class="icon_preview"></div>
					<?php
				endif;
			endif;

			if ( $v5_icon_preselected ) :
				$previous_icon_info = $this->get_previous_icon_info( $select_value );
				?>
				<div class="v5-compat-message" aria-label="<?php _e( 'This FontAwesome v5 Pro icon cannot be automatically translated to its v6 equivalent and will need to be reselected before saving this post/page.', 'acf-font-awesome' ); ?>" data-microtip-size="large" data-microtip-position="top" role="tooltip">
					<?php echo sprintf (__( 'Please reselect your FontAwesome Icon.', 'acf-font-awesome' ), 'SOLID', 'COFFEE' ); ?> <i class="fas fa-question-circle"></i>
					<?php
						if ( isset( $previous_icon_info['style'] ) && ! empty( $previous_icon_info['style'] ) ) :
							?>
							<em><?php _e( 'Style:', 'acf-font-awesome' ); ?></em> <strong><?php echo $previous_icon_info['style']; ?></strong>
							<?php
						endif;
						if ( isset( $previous_icon_info['name'] ) && ! empty( $previous_icon_info['name'] ) ) :
							?>
							<em><?php _e( 'Name:', 'acf-font-awesome' ); ?></em> <strong><?php echo $previous_icon_info['name']; ?></strong>
							<?php
						endif;
					?>
				</div>
				<?php
			endif;

			acf_render_field( $field );
		}

		public function input_admin_enqueue_scripts()
		{
			$version		= $this->settings['version'];
			$options		= get_option( 'acffa_settings' );
			$latest_version	= apply_filters( 'ACFFA_get_latest_version', '6.0.0' );

			if ( isset( $options['acffa_v5_compatibility_mode'] ) && $options['acffa_v5_compatibility_mode'] ) {
				wp_enqueue_script( 'acffa_fontawesome-js-api', "https://use.fontawesome.com/releases/v$latest_version/js/all.js", [], $latest_version );
			}
			wp_enqueue_script( 'acf-input-font-awesome', ACFFA_PUBLIC_PATH . "assets/js/input-v6.js", [ 'acf-input' ], $version );
			wp_localize_script( 'acf-input-font-awesome', 'ACFFA', [
				'major_version'		=> ACFFA_MAJOR_VERSION,
				'v5_compat_mode'	=> isset( $options['acffa_v5_compatibility_mode'] ) && $options['acffa_v5_compatibility_mode'] ? true : false
			] );

			wp_enqueue_style( 'acf-input-microtip', ACFFA_PUBLIC_PATH . "assets/inc/microtip/microtip.min.css", [], '1.0.0' );
			wp_enqueue_style( 'acf-input-font-awesome', ACFFA_PUBLIC_PATH . "assets/css/input.css", [ 'acf-input' ], $version );

			if ( apply_filters( 'ACFFA_admin_enqueue_fa', true ) ) {
				$fa_url = apply_filters( 'ACFFA_get_fa_url', '' );
				if ( stristr( $fa_url, 'https://kit.fontawesome.com/' ) ) {
					wp_enqueue_script( 'acffa_font-awesome-kit', $fa_url );
				} else {
					wp_enqueue_style( 'acffa_font-awesome', $fa_url, [ 'acf-input' ], $latest_version );
				}
			}
		}

		public function maybe_enqueue_font_awesome( $field )
		{
			if ( 'font-awesome' == $field['type'] && $field['enqueue_fa'] ) {
				add_action( 'wp_footer', [ $this, 'frontend_enqueue_scripts' ] );
			}

			return $field;
		}

		public function frontend_enqueue_scripts()
		{
			$fa_url = apply_filters( 'ACFFA_get_fa_url', '' );
			if ( stristr( $fa_url, 'https://kit.fontawesome.com/' ) ) {
				wp_enqueue_script( 'acffa_font-awesome-kit', $fa_url );
			} else {
				$latest_version	= apply_filters( 'ACFFA_get_latest_version', '6.0.0' );
				wp_enqueue_style( 'acffa_font-awesome', $fa_url, [], $latest_version );
			}
		}
	
		public function format_value( $value, $post_id, $field )
		{
			if ( 'null' == $value ) {
				return false;
			}

			if ( empty( $value ) ) {
				return $value;
			}

			$icon_json = json_decode( $value );

			if ( is_object( $icon_json ) ) {
				if ( 'fak' == $icon_json->style ) {
					$class	= $icon_json->style . ' fa-' . $icon_json->id;
					$prefix	= $icon_json->style;
				} else {
					$class	= 'fa-' . $icon_json->style . ' fa-' . $icon_json->id;
					$prefix	= 'fa-' . $icon_json->style;
				}

				switch ( $field['save_format'] ) {
					case 'element':
						$value = '<i class="' . $class . '" aria-hidden="true"></i>';
						break;

					case 'unicode':
						$value = '&#x' . $icon_json->unicode . ';';
						break;

					case 'class':
						$value = $class;
						break;

					case 'object':
						$object_data = [
							'element'	=> '<i class="' . $class . '" aria-hidden="true"></i>',
							'class'		=> $class,
							'id'		=> $icon_json->id,
							'prefix'	=> $prefix,
							'style'		=> $icon_json->style,
							'hex'		=> '\\' . $icon_json->unicode,
							'unicode'	=> '&#x' . $icon_json->unicode . ';'
						];

						if ( 'fak' == $icon_json->style ) {
							$svg_data = [
								'element'	=> '<svg class="svg-inline--fa" viewBox="0 0 ' . $icon_json->width . ' ' . $icon_json->height . '"><path d="' . $icon_json->path . '" /></svg>',
								'path'		=> $icon_json->path,
								'height'	=> $icon_json->height,
								'width'		=> $icon_json->width
							];
							$object_data['svg'] = ( object ) $svg_data;
						}

						$value = ( object ) $object_data;
						break;
				}
			} else {
				$value = apply_filters( 'ACFFA_v5_upgrade_compat_format_value', $value, $field['save_format'] );
			}

			return $value;
		}

		public function v5_upgrade_compat_format_value( $value, $save_format )
		{
			if ( 'false' == $value ) {
				return;
			}

			$icons		= get_option( 'ACFFA_icon_data' );
			$version	= get_option( 'ACFFA_current_version', '5.15.4' );

			if ( ! $icons || ! isset( $icons[ $version ] ) ) {
				return $value;
			}

			$icons = $icons[ $version ];

			if ( version_compare( $version, 5, '<' ) ) {
				$icon = isset( $icons['details'][ $value ] ) ? $icons['details'][ $value ] : false;
			} else {
				$prefix = substr( $value, 0, 3 );
				$icon = isset( $icons['details'][ $prefix ][ $value ] ) ? $icons['details'][ $prefix ][ $value ] : false;
			}

			if ( $icon ) {
				switch ( $save_format ) {
					case 'element':
						if ( version_compare( $version, 5, '<' ) ) {
							$value = '<i class="fa ' . $value . '" aria-hidden="true"></i>';
						} else {
							$value = '<i class="' . $value . '" aria-hidden="true"></i>';
						}
						break;

					case 'unicode':
						$value = $icon['unicode'];
						break;

					case 'object':
						$object_data = array(
							'element' => '<i class="' . $value . '" aria-hidden="true"></i>',
							'class' => $value,
							'hex' => $icon['hex'],
							'unicode' => $icon['unicode']
						);

						if ( version_compare( $version, 5, '>=' ) ) {
							$object_data['prefix'] = $prefix;
						}

						$value = ( object ) $object_data;
						break;
				}
			}

			return $value;
		}

		public function v5_upgrade_compat_selected_field_sets( $selected_field_sets )
		{
			if ( is_array( $selected_field_sets ) && ! empty( $selected_field_sets ) ) {
				foreach ( $selected_field_sets as $key => $field_set ) {
					switch ( $field_set ) {
						case 'fas':
							unset( $selected_field_sets[ $key ] );
							$selected_field_sets[] = 'solid';
							break;

						case 'far':
							unset( $selected_field_sets[ $key ] );
							$selected_field_sets[] = 'regular';
							break;

						case 'fal':
							unset( $selected_field_sets[ $key ] );
							$selected_field_sets[] = 'light';
							break;

						case 'fad':
							unset( $selected_field_sets[ $key ] );
							$selected_field_sets[] = 'duotone';
							break;

						case 'fab':
							unset( $selected_field_sets[ $key ] );
							$selected_field_sets[] = 'brands';
							break;
					}
				}
			}

			return $selected_field_sets;
		}

		private function get_previous_icon_info( $previous_icon )
		{
			if ( ! $previous_icon || 'false' == $previous_icon ) {
				return;
			}

			if ( 0 === strpos( $previous_icon, 'fa-' ) ) {
				$icon_info	= [
					'name'	=> preg_replace('/-o$/', '', substr( $previous_icon, 3 ) )
				];
			} else {
				$icon_info	= [
					'style'	=> '',
					'name'	=> substr( $previous_icon, 7 )
				];

				$prefix = substr( $previous_icon, 0, 3 );

				switch ( $prefix ) {
					case 'fas':
						$icon_info['style'] = 'solid';
						break;

					case 'far':
						$icon_info['style'] = 'regular';
						break;

					case 'fal':
						$icon_info['style'] = 'light';
						break;

					case 'fad':
						$icon_info['style'] = 'duotone';
						break;

					case 'fab':
						$icon_info['style'] = 'brands';
						break;
				}
			}

			return $icon_info;
		}

	}

	new acf_field_font_awesome( $this->settings );

endif;
