<?php

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Cookie_Notice_Welcome_API class.
 *
 * @class Cookie_Notice_Welcome_API
 */
class Cookie_Notice_Welcome_API {

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		// actions
		add_action( 'init', [ $this, 'check_cron' ] );
		add_action( 'cookie_notice_get_app_analytics', [ $this, 'get_app_analytics' ] );
		add_action( 'cookie_notice_get_app_config', [ $this, 'get_app_config' ] );
		add_action( 'wp_ajax_cn_api_request', [ $this, 'api_request' ] );
	}

	/**
	 * Ajax API request.
	 *
	 * @return void
	 */
	public function api_request() {
		// check capabilities
		if ( ! current_user_can( apply_filters( 'cn_manage_cookie_notice_cap', 'manage_options' ) ) )
			wp_die( __( 'You do not have permission to access this page.', 'cookie-notice' ) );

		// check main nonce
		if ( ! check_ajax_referer( 'cookie-notice-welcome', 'nonce' ) )
			wp_die( __( 'You do not have permission to access this page.', 'cookie-notice' ) );

		// get request
		$request = isset( $_POST['request'] ) ? sanitize_key( $_POST['request'] ) : '';

		// no valid request?
		if ( ! in_array( $request, [ 'register', 'login', 'configure', 'select_plan', 'payment', 'get_bt_init_token', 'use_license' ], true ) )
			wp_die( __( 'You do not have permission to access this page.', 'cookie-notice' ) );

		$special_actions = [ 'register', 'login', 'configure', 'payment' ];

		// payment nonce
		if ( $request === 'payment' )
			$nonce = isset( $_POST['cn_payment_nonce'] ) ? sanitize_key( $_POST['cn_payment_nonce'] ) : '';
		// special nonce
		elseif ( in_array( $request, $special_actions, true ) )
			$nonce = isset( $_POST['cn_nonce'] ) ? sanitize_key( $_POST['cn_nonce'] ) : '';

		// check additional nonce
		if ( in_array( $request, $special_actions, true ) && ! wp_verify_nonce( $nonce, 'cn_api_' . $request ) )
			wp_die( __( 'You do not have permission to access this page.', 'cookie-notice' ) );

		$errors = [];
		$response = false;

		// get main instance
		$cn = Cookie_Notice();

		// get site language
		$locale = get_locale();
		$locale_code = explode( '_', $locale );

		// check network
		$network = $cn->is_network_admin();

		// get app token data
		if ( $network )
			$data_token = get_site_transient( 'cookie_notice_app_token' );
		else
			$data_token = get_transient( 'cookie_notice_app_token' );

		$admin_email = ! empty( $data_token->email ) ? $data_token->email : '';
		$app_id = $cn->options['general']['app_id'];

		$params = [];

		switch ( $request ) {
			case 'use_license':
				$subscriptionID = isset( $_POST['subscriptionID'] ) ? (int) $_POST['subscriptionID'] : 0;

				$result = $this->request(
					'assign_subscription',
					[
						'AppID'				=> $app_id,
						'subscriptionID'	=> $subscriptionID
					]
				);

				// errors?
				if ( ! empty( $result->message ) ) {
					$response = [ 'error' => $result->message ];
					break;
				} else
					$response = $result;

				break;

			case 'get_bt_init_token':
				$result = $this->request( 'get_token' );

				// is token available?
				if ( ! empty( $result->token ) )
					$response = [ 'token' => $result->token ];
				break;

			case 'payment':
				$error = [ 'error' => esc_html__( 'Unexpected error occurred. Please try again later.', 'cookie-notice' ) ];

				// empty data?
				if ( empty( $_POST['payment_nonce'] ) || empty( $_POST['plan'] ) || empty( $_POST['method'] ) ) {
					$response = $error;
					break;
				}

				// validate plan and payment method
				$available_plans = [
					'compliance_monthly_notrial',
					'compliance_monthly_5',
					'compliance_monthly_10',
					'compliance_monthly_20',
					'compliance_yearly_notrial',
					'compliance_yearly_5',
					'compliance_yearly_10',
					'compliance_yearly_20'
				];

				$available_payment_methods = [
					'credit_card',
					'paypal'
				];

				$plan = sanitize_key( $_POST['plan'] );

				if ( ! in_array( $_POST['plan'], $available_plans, true ) )
					$plan = false;

				$method = sanitize_key( $_POST['method'] );

				if ( ! in_array( $_POST['method'], $available_payment_methods, true ) )
					$method = false;

				// valid plan and payment method?
				if ( empty( $plan ) || empty( $method ) ) {
					$response = [ 'error' => esc_html__( 'Empty plan or payment method data.', 'cookie-notice' ) ];
					break;
				}

				$result = $this->request(
					'get_customer',
					[
						'AppID'		=> $app_id,
						'PlanId'	=> $plan
					]
				);

				// user found?
				if ( ! empty( $result->id ) ) {
					$customer = $result;
				// create user
				} else {
					$result = $this->request(
						'create_customer',
						[
							'AppID'					=> $app_id,
							'AdminID'				=> $admin_email, // remove later - AdminID from API response
							'PlanId'				=> $plan,
							'paymentMethodNonce'	=> sanitize_key( $_POST['payment_nonce'] )
						]
					);

					if ( ! empty( $result->success ) )
						$customer = $result->customer;
					else
						$customer = $result;
				}

				// user created/received?
				if ( empty( $customer->id ) ) {
					$response = [ 'error' => esc_html__( 'Unable to create customer data.', 'cookie-notice' ) ];
					break;
				}

				// selected payment method
				$payment_method = false;

				// get payment identifier (email or 4 digits)
				$identifier = isset( $_POST['cn_payment_identifier'] ) ? sanitize_text_field( $_POST['cn_payment_identifier'] ) : '';

				// customer available payment methods
				$payment_methods = ! empty( $customer->paymentMethods ) ? $customer->paymentMethods : [];

				// try to find payment method
				if ( ! empty( $payment_methods ) && is_array( $payment_methods ) ) {
					foreach ( $payment_methods as $pm ) {
						// paypal
						if ( isset( $pm->email ) && $pm->email === $identifier )
							$payment_method = $pm;
						// credit card
						elseif ( isset( $pm->last4 ) && $pm->last4 === $identifier )
							$payment_method = $pm;
					}
				}

				// if payment method was not identified, create it
				if ( ! $payment_method ) {
					$result = $this->request(
						'create_payment_method',
						[
							'AppID'					=> $app_id,
							'paymentMethodNonce'	=> sanitize_key( $_POST['payment_nonce'] )
						]
					);

					// payment method created successfully?
					if ( ! empty( $result->success ) ) {
						$payment_method = $result->paymentMethod;
					} else {
						$response = [ 'error' => esc_html__( 'Unable to create payment mehotd.', 'cookie-notice' ) ];
						break;
					}
				}

				if ( ! isset( $payment_method->token ) ) {
					$response = [ 'error' => esc_html__( 'No payment method token.', 'cookie-notice' ) ];
					break;
				}

				// @todo: check if subscription exists
				$subscription = $this->request(
					'create_subscription',
					[
						'AppID'					=> $app_id,
						'PlanId'				=> $plan,
						'paymentMethodToken'	=> $payment_method->token
					]
				);

				// subscription assigned?
				if ( ! empty( $subscription->error ) ) {
					$response = $subscription->error;
					break;
				}

				$status_data = $cn->defaults['data'];

				// update app status
				if ( $network ) {
					$status_data = get_site_option( 'cookie_notice_status', $status_data );
					$status_data['subscription'] = 'pro';

					update_site_option( 'cookie_notice_status', $status_data );
				} else {
					$status_data = get_option( 'cookie_notice_status', $status_data );
					$status_data['subscription'] = 'pro';

					update_option( 'cookie_notice_status', $status_data );
				}

				$response = $app_id;
				break;

			case 'register':
				// check terms
				$terms = isset( $_POST['terms'] );

				// no terms?
				if ( ! $terms ) {
					$response = [ 'error' => esc_html__( 'Please accept the Terms of Service to proceed.', 'cookie-notice' ) ];
					break;
				}

				// check email
				$email = isset( $_POST['email'] ) ? is_email( $_POST['email'] ) : false;

				// empty email?
				if ( ! $email ) {
					$response = [ 'error' => esc_html__( 'Email is not allowed to be empty.', 'cookie-notice' ) ];
					break;
				}

				// check passwords
				$pass = ! empty( $_POST['pass'] ) ? stripslashes( $_POST['pass'] ) : '';
				$pass2 = ! empty( $_POST['pass2'] ) ? stripslashes( $_POST['pass2'] ) : '';

				// empty password?
				if ( ! $pass || ! is_string( $pass ) ) {
					$response = [ 'error' => esc_html__( 'Password is not allowed to be empty.', 'cookie-notice' ) ];
					break;
				}

				// invalid password?
				if ( preg_match( '/^(?=.*[A-Z])(?=.*\d)[\w !"#$%&\'()*\+,\-.\/:;<=>?@\[\]^\`\{\|\}\~\\\\]{8,}$/', $pass ) !== 1 ) {
					$response = [ 'error' => esc_html__( 'The password contains illegal characters or does not meet the conditions.', 'cookie-notice' ) ];
					break;
				}

				// no match?
				if ( $pass !== $pass2 ) {
					$response = [ 'error' => esc_html__( 'Passwords do not match.', 'cookie-notice' ) ];
					break;
				}

				$params = [
					'AdminID'	=> $email,
					'Password'	=> $pass,
					'Language'	=> ! empty( $_POST['language'] ) ? sanitize_key( $_POST['language'] ) : 'en'
				];

				$response = $this->request( 'register', $params );

				// errors?
				if ( ! empty( $response->error ) )
					break;

				// errors?
				if ( ! empty( $response->message ) ) {
					$response->error = $response->message;
					break;
				}

				// ok, so log in now
				$params = [
					'AdminID'	=> $email,
					'Password'	=> $pass
				];

				$response = $this->request( 'login', $params );

				// errors?
				if ( ! empty( $response->error ) )
					break;

				// errors?
				if ( ! empty( $response->message ) ) {
					$response->error = $response->message;
					break;
				}

				// token in response?
				if ( empty( $response->data->token ) ) {
					$response = [ 'error' => esc_html__( 'Unexpected error occurred. Please try again later.', 'cookie-notice' ) ];
					break;
				}

				// set token
				if ( $network )
					set_site_transient( 'cookie_notice_app_token', $response->data, DAY_IN_SECONDS );
				else
					set_transient( 'cookie_notice_app_token', $response->data, DAY_IN_SECONDS );

				// multisite?
				if ( is_multisite() ) {
					switch_to_blog( 1 );
					$site_title = get_bloginfo( 'name' );
					$site_url = network_site_url();
					$site_description = get_bloginfo( 'description' );
					restore_current_blog();
				} else {
					$site_title = get_bloginfo( 'name' );
					$site_url = get_home_url();
					$site_description = get_bloginfo( 'description' );
				}

				// create new app, no need to check existing
				$params = [
					'DomainName'	=> $site_title,
					'DomainUrl'		=> $site_url
				];

				if ( ! empty( $site_description ) )
					$params['DomainDescription'] = $site_description;

				$response = $this->request( 'app_create', $params );

				// errors?
				if ( ! empty( $response->message ) ) {
					$response->error = $response->message;
					break;
				}

				// data in response?
				if ( empty( $response->data->AppID ) || empty( $response->data->SecretKey ) ) {
					$response = [ 'error' => esc_html__( 'Unexpected error occurred. Please try again later.', 'cookie-notice' ) ];
					break;
				} else {
					$app_id = $response->data->AppID;
					$secret_key = $response->data->SecretKey;
				}

				// update options: app id and secret key
				$cn->options['general'] = wp_parse_args( [ 'app_id' => $app_id, 'app_key' => $secret_key ], $cn->options['general'] );

				if ( $network ) {
					$cn->options['general']['global_override'] = true;

					update_site_option( 'cookie_notice_options', $cn->options['general'] );

					// get options
					$app_config = get_site_transient( 'cookie_notice_app_quick_config' );
				} else {
					update_option( 'cookie_notice_options', $cn->options['general'] );

					// get options
					$app_config = get_transient( 'cookie_notice_app_quick_config' );
				}

				// create quick config
				$params = ! empty( $app_config ) && is_array( $app_config ) ? $app_config : [];

				// cast to objects
				if ( $params ) {
					$new_params = [];

					foreach ( $params as $key => $array ) {
						$object = new stdClass();

						foreach ( $array as $subkey => $value ) {
							$new_params[$key] = $object;
							$new_params[$key]->{$subkey} = $value;
						}
					}

					$params = $new_params;
				}

				$params['AppID'] = $app_id;

				// @todo When mutliple default languages are supported
				$params['DefaultLanguage'] = 'en';

				if ( ! array_key_exists( 'text', $params ) )
					$params['text'] = new stdClass();

				// add privacy policy url
				$params['text']->privacyPolicyUrl = get_privacy_policy_url();

				// add translations if needed
				if ( $locale_code[0] !== 'en' )
					$params['Languages'] = [ $locale_code[0] ];

				$response = $this->request( 'quick_config', $params );
				$status_data = $cn->defaults['data'];

				if ( $response->status === 200 ) {
					// notify publish app
					$params = [
						'AppID'	=> $app_id
					];

					$response = $this->request( 'notify_app', $params );

					if ( $response->status === 200 ) {
						$response = true;
						$status_data['status'] = 'active';

						// update app status
						if ( $network )
							update_site_option( 'cookie_notice_status', $status_data );
						else
							update_option( 'cookie_notice_status', $status_data );
					} else {
						$status_data['status'] = 'pending';

						// update app status
						if ( $network )
							update_site_option( 'cookie_notice_status', $status_data );
						else
							update_option( 'cookie_notice_status', $status_data );

						// errors?
						if ( ! empty( $response->error ) )
							break;

						// errors?
						if ( ! empty( $response->message ) ) {
							$response->error = $response->message;
							break;
						}
					}
				} else {
					$status_data['status'] = 'pending';

					// update app status
					if ( $network )
						update_site_option( 'cookie_notice_status', $status_data );
					else
						update_option( 'cookie_notice_status', $status_data );

					// errors?
					if ( ! empty( $response->error ) ) {
						$response->error = $response->error;
						break;
					}

					// errors?
					if ( ! empty( $response->message ) ) {
						$response->error = $response->message;
						break;
					}
				}

				break;

			case 'login':
				// check email
				$email = isset( $_POST['email'] ) ? is_email( $_POST['email'] ) : false;

				// invalid email?
				if ( ! $email ) {
					$response = [ 'error' => esc_html__( 'Email is not allowed to be empty.', 'cookie-notice' ) ];
					break;
				}

				// check password
				$pass = ! empty( $_POST['pass'] ) ? preg_replace( '/[^\w !"#$%&\'()*\+,\-.\/:;<=>?@\[\]^\`\{\|\}\~\\\\]/', '', $_POST['pass'] ) : '';

				// empty password?
				if ( ! $pass ) {
					$response = [ 'error' => esc_html__( 'Password is not allowed to be empty.', 'cookie-notice' ) ];
					break;
				}

				$params = [
					'AdminID'	=> $email,
					'Password'	=> $pass
				];

				$response = $this->request( $request, $params );

				// errors?
				if ( ! empty( $response->error ) )
					break;

				// errors?
				if ( ! empty( $response->message ) ) {
					$response->error = $response->message;
					break;
				}

				// token in response?
				if ( empty( $response->data->token ) ) {
					$response = [ 'error' => esc_html__( 'Unexpected error occurred. Please try again later.', 'cookie-notice' ) ];
					break;
				}

				// set token
				if ( $network )
					set_site_transient( 'cookie_notice_app_token', $response->data, DAY_IN_SECONDS );
				else
					set_transient( 'cookie_notice_app_token', $response->data, DAY_IN_SECONDS );

				// get apps and check if one for the current domain already exists
				$response = $this->request( 'list_apps', [] );

				// errors?
				if ( ! empty( $response->message ) ) {
					$response->error = $response->message;
					break;
				}

				$apps_list = [];
				$app_exists = false;

				// multisite?
				if ( is_multisite() ) {
					switch_to_blog( 1 );
					$site_title = get_bloginfo( 'name' );
					$site_url = network_site_url();
					$site_description = get_bloginfo( 'description' );
					restore_current_blog();
				} else {
					$site_title = get_bloginfo( 'name' );
					$site_url = get_home_url();
					$site_description = get_bloginfo( 'description' );
				}

				// apps added, check if current one exists
				if ( ! empty( $response->data ) ) {
					$apps_list = (array) $response->data;

					foreach ( $apps_list as $index => $app ) {
						$site_without_http = trim( str_replace( [ 'http://', 'https://' ], '', $site_url ), '/' );

						if ( $app->DomainUrl === $site_without_http ) {
							$app_exists = $app;

							continue;
						}
					}
				}

				// if no app, create one
				if ( ! $app_exists ) {
					// create new app
					$params = [
						'DomainName'	=> $site_title,
						'DomainUrl'		=> $site_url,
					];

					if ( ! empty( $site_description ) )
						$params['DomainDescription'] = $site_description;

					$response = $this->request( 'app_create', $params );

					// errors?
					if ( ! empty( $response->message ) ) {
						$response->error = $response->message;
						break;
					}

					$app_exists = $response->data;
				}

				// check if we have the valid app data
				if ( empty( $app_exists->AppID ) || empty( $app_exists->SecretKey ) ) {
					$response = [ 'error' => esc_html__( 'Unexpected error occurred. Please try again later.', 'cookie-notice' ) ];
					break;
				}

				// get subscriptions
				$subscriptions = [];

				$params = [
					'AppID' => $app_exists->AppID
				];

				$response = $this->request( 'get_subscriptions', $params );

				// errors?
				if ( ! empty( $response->error ) ) {
					$response->error = $response->error;
					break;
				} else
					$subscriptions = map_deep( (array) $response->data, 'sanitize_text_field' );

				// set subscriptions data
				if ( $network )
					set_site_transient( 'cookie_notice_app_subscriptions', $subscriptions, DAY_IN_SECONDS );
				else
					set_transient( 'cookie_notice_app_subscriptions', $subscriptions, DAY_IN_SECONDS );

				// update options: app ID and secret key
				$cn->options['general'] = wp_parse_args( [ 'app_id' => $app_exists->AppID, 'app_key' => $app_exists->SecretKey ], $cn->options['general'] );

				if ( $network ) {
					$cn->options['general']['global_override'] = true;

					update_site_option( 'cookie_notice_options', $cn->options['general'] );
				} else {
					update_option( 'cookie_notice_options', $cn->options['general'] );
				}

				// create quick config
				$params = [
					'AppID'				=> $app_exists->AppID,
					'DefaultLanguage'	=> 'en',
					'text'				=> new stdClass()
				];

				// add privacy policy url
				$params['text']->privacyPolicyUrl = get_privacy_policy_url();

				// add translations if needed
				if ( $locale_code[0] !== 'en' )
					$params['Languages'] = [ $locale_code[0] ];

				$response = $this->request( 'quick_config', $params );
				$status_data = $cn->defaults['data'];

				if ( $response->status === 200 ) {
					// @todo notify publish app
					$params = [
						'AppID' => $app_exists->AppID
					];

					$response = $this->request( 'notify_app', $params );

					if ( $response->status === 200 ) {
						$response = true;
						$status_data['status'] = 'active';

						// update app status
						if ( $network )
							update_site_option( 'cookie_notice_status', $status_data );
						else
							update_option( 'cookie_notice_status', $status_data );
					} else {
						$status_data['status'] = 'pending';

						// update app status
						if ( $network )
							update_site_option( 'cookie_notice_status', $status_data );
						else
							update_option( 'cookie_notice_status', $status_data );

						// errors?
						if ( ! empty( $response->error ) )
							break;

						// errors?
						if ( ! empty( $response->message ) ) {
							$response->error = $response->message;
							break;
						}
					}
				} else {
					$status_data['status'] = 'pending';

					// update app status
					if ( $network )
						update_site_option( 'cookie_notice_status', $status_data );
					else
						update_option( 'cookie_notice_status', $status_data );

					// errors?
					if ( ! empty( $response->error ) ) {
						$response->error = $response->error;
						break;
					}

					// errors?
					if ( ! empty( $response->message ) ) {
						$response->error = $response->message;
						break;
					}
				}

				// all ok, return subscriptions
				$response = (object) [];
				$response->subscriptions = $subscriptions;
				break;

			case 'configure':
				$fields = [
					'cn_position',
					'cn_color_primary',
					'cn_color_background',
					'cn_color_border',
					'cn_color_text',
					'cn_color_heading',
					'cn_color_button_text',
					'cn_laws',
					'cn_naming',
					'cn_privacy_paper',
					'cn_privacy_contact'
				];

				$options = [];

				// loop through potential config form fields
				foreach ( $fields as $field ) {
					switch ( $field ) {
						case 'cn_position':
							// sanitize position
							$position = isset( $_POST[$field] ) ? sanitize_key( $_POST[$field] ) : '';

							// valid position?
							if ( in_array( $position, [ 'bottom', 'top', 'left', 'right', 'center' ], true ) )
								$options['design']['position'] = $position;
							else
								$options['design']['position'] = 'bottom';
							break;

						case 'cn_color_primary':
							// sanitize color
							$color = isset( $_POST[$field] ) ? sanitize_hex_color( $_POST[$field] ) : '';

							// valid color?
							if ( empty( $color ) )
								$options['design']['primaryColor'] = '#20c19e';
							else
								$options['design']['primaryColor'] = $color;
							break;

						case 'cn_color_background':
							// sanitize color
							$color = isset( $_POST[$field] ) ? sanitize_hex_color( $_POST[$field] ) : '';

							// valid color?
							if ( empty( $color ) )
								$options['design']['bannerColor'] = '#ffffff';
							else
								$options['design']['bannerColor'] = $color;
							break;

						case 'cn_color_border':
							// sanitize color
							$color = isset( $_POST[$field] ) ? sanitize_hex_color( $_POST[$field] ) : '';

							// valid color?
							if ( empty( $color ) )
								$options['design']['borderColor'] = '#5e6a74';
							else
								$options['design']['borderColor'] = $color;
							break;

						case 'cn_color_text':
							// sanitize color
							$color = isset( $_POST[$field] ) ? sanitize_hex_color( $_POST[$field] ) : '';

							// valid color?
							if ( empty( $color ) )
								$options['design']['textColor'] = '#434f58';
							else
								$options['design']['textColor'] = $color;
							break;

						case 'cn_color_heading':
							// sanitize color
							$color = isset( $_POST[$field] ) ? sanitize_hex_color( $_POST[$field] ) : '';

							// valid color?
							if ( empty( $color ) )
								$options['design']['headingColor'] = '#434f58';
							else
								$options['design']['headingColor'] = $color;
							break;

						case 'cn_color_button_text':
							// sanitize color
							$color = isset( $_POST[$field] ) ? sanitize_hex_color( $_POST[$field] ) : '';

							// valid color?
							if ( empty( $color ) )
								$options['design']['btnTextColor'] = '#ffffff';
							else
								$options['design']['btnTextColor'] = $color;
							break;

						case 'cn_laws':
							$new_options = [];

							// any data?
							if ( ! empty( $_POST[$field] ) && is_array( $_POST[$field] ) ) {
								$options['laws'] = array_map( 'sanitize_text_field', $_POST[$field] );

								foreach ( $options['laws'] as $law ) {
									if ( in_array( $law, [ 'gdpr', 'ccpa' ], true ) )
										$new_options[$law] = true;
								}
							}

							$options['laws'] = $new_options;

							// GDPR
							if ( array_key_exists( 'gdpr', $options['laws'] ) )
								$options['config']['privacyPolicyLink'] = true;
							else
								$options['config']['privacyPolicyLink'] = false;

							// CCPA
							if ( array_key_exists( 'ccpa', $options['laws'] ) )
								$options['config']['dontSellLink'] = true;
							else
								$options['config']['dontSellLink'] = false;
							break;

						case 'cn_naming':
							$naming = isset( $_POST[$field] ) ? (int) $_POST[$field] : 1;
							$naming = in_array( $naming, [ 1, 2, 3 ] ) ? $naming : 1;

							// english only for now
							$level_names = [
								1 => [
									1 => 'Silver',
									2 => 'Gold',
									3 => 'Platinum'
								],
								2 => [
									1 => 'Private',
									2 => 'Balanced',
									3 => 'Personalized'
								],
								3 => [
									1 => 'Reject All',
									2 => 'Accept Some',
									3 => 'Accept All'
								]
							];

							$options['text'] = [
								'levelNameText_1'	=> $level_names[$naming][1],
								'levelNameText_2'	=> $level_names[$naming][2],
								'levelNameText_3'	=> $level_names[$naming][3]
							];
							break;

						case 'cn_privacy_paper':
							$options['config']['privacyPaper'] = false; // isset( $_POST[$field] );
							break;

						case 'cn_privacy_contact':
							$options['config']['privacyContact'] = false; // isset( $_POST[$field] );
							break;
					}
				}

				// set options
				if ( $network )
					set_site_transient( 'cookie_notice_app_quick_config', $options, DAY_IN_SECONDS );
				else
					set_transient( 'cookie_notice_app_quick_config', $options, DAY_IN_SECONDS );
				break;

			case 'select_plan':
				break;
		}

		echo wp_json_encode( $response );
		exit;
	}

	/**
	 * API request.
	 *
	 * @param string $request The requested action.
	 * @param array $params Parameters for the API action.
	 * @return string|array
	 */
	private function request( $request = '', $params = [] ) {
		// get main instance
		$cn = Cookie_Notice();

		$api_args = [
			'timeout'	=> 60,
			'sslverify'	=> false,
			'headers'	=> [
				'x-api-key'	=> $cn->get_api_key()
			]
		];
		$api_params = [];
		$json = false;

		// check network
		$network = $cn->is_network_admin();

		// get app token data
		if ( $network )
			$data_token = get_site_transient( 'cookie_notice_app_token' );
		else
			$data_token = get_transient( 'cookie_notice_app_token' );

		$api_token = ! empty( $data_token->token ) ? $data_token->token : '';

		switch ( $request ) {
			case 'register':
				$api_url = $cn->get_url( 'account_api', '/api/account/account/registration' );
				$api_args['method'] = 'POST';
				break;

			case 'login':
				$api_url = $cn->get_url( 'account_api', '/api/account/account/login' );
				$api_args['method'] = 'POST';
				break;

			case 'list_apps':
				$api_url = $cn->get_url( 'account_api', '/api/account/app/list' );
				$api_args['method'] = 'GET';
				$api_args['headers'] = array_merge(
					$api_args['headers'],
					[
						'Authorization' => 'Bearer ' . $api_token
					]
				);
				break;

			case 'app_create':
				$api_url = $cn->get_url( 'account_api', '/api/account/app/add' );
				$api_args['method'] = 'POST';
				$api_args['headers'] = array_merge(
					$api_args['headers'],
					[
						'Authorization' => 'Bearer ' . $api_token
					]
				);
				break;

			case 'get_analytics':
				$api_url = $cn->get_url( 'transactional_api', '/api/transactional/analytics/analytics-data' );
				$api_args['method'] = 'GET';

				$diff_data = $cn->settings->get_analytics_app_data();

				if ( ! empty( $diff_data ) ) {
					$app_data = [
						'app-id'			=> $diff_data['id'],
						'app-secret-key'	=> $diff_data['key']
					];
				} else {
					$app_data = [
						'app-id'			=> $cn->options['general']['app_id'],
						'app-secret-key'	=> $cn->options['general']['app_key']
					];
				}

				$api_args['headers'] = array_merge( $api_args['headers'], $app_data );
				break;

			case 'get_consent_logs_by_date':
				$api_url = $cn->get_url( 'transactional_api', '/api/transactional/analytics/consent-logs' );
				$api_args['method'] = 'POST';
				break;

			case 'get_config':
				$api_url = $cn->get_url( 'designer_api', '/api/designer/user-design-live' );
				$api_args['method'] = 'GET';
				break;

			case 'quick_config':
				$json = true;
				$api_url = $cn->get_url( 'designer_api', '/api/designer/user-design/quick' );
				$api_args['method'] = 'POST';
				$api_args['headers'] = array_merge(
					$api_args['headers'],
					[
						'Authorization'	=> 'Bearer ' . $api_token,
						'Content-Type'	=> 'application/json; charset=utf-8'
					]
				);
				break;

			case 'notify_app':
				$json = true;
				$api_url = $cn->get_url( 'account_api', '/api/account/app/notifyAppPublished' );
				$api_args['method'] = 'POST';
				$api_args['headers'] = array_merge(
					$api_args['headers'],
					[
						'Authorization'	=> 'Bearer ' . $api_token,
						'Content-Type'	=> 'application/json; charset=utf-8'
					]
				);
				break;

			// braintree init token
			case 'get_token':
				$api_url = $cn->get_url( 'account_api', '/api/account/braintree' );
				$api_args['method'] = 'GET';
				$api_args['headers'] = array_merge(
					$api_args['headers'],
					[
						'Authorization' => 'Bearer ' . $api_token
					]
				);
				break;

			// braintree get customer
			case 'get_customer':
				$json = true;
				$api_url = $cn->get_url( 'account_api', '/api/account/braintree/findcustomer' );
				$api_args['method'] = 'POST';
				$api_args['data_format'] = 'body';
				$api_args['headers'] = array_merge(
					$api_args['headers'],
					[
						'Authorization'	=> 'Bearer ' . $api_token,
						'Content-Type'	=> 'application/json; charset=utf-8'
					]
				);
				break;

			// braintree create customer in vault
			case 'create_customer':
				$json = true;
				$api_url = $cn->get_url( 'account_api', '/api/account/braintree/createcustomer' );
				$api_args['method'] = 'POST';
				$api_args['headers'] = array_merge(
					$api_args['headers'],
					[
						'Authorization'	=> 'Bearer ' . $api_token,
						'Content-Type'	=> 'application/json; charset=utf-8'
					]
				);
				break;

			// braintree get subscriptions
			case 'get_subscriptions':
				$json = true;
				$api_url = $cn->get_url( 'account_api', '/api/account/braintree/subscriptionlists' );
				$api_args['method'] = 'POST';
				$api_args['headers'] = array_merge(
					$api_args['headers'],
					[
						'Authorization'	=> 'Bearer ' . $api_token,
						'Content-Type'	=> 'application/json; charset=utf-8'
					]
				);
				break;

			// braintree create subscription
			case 'create_subscription':
				$json = true;
				$api_url = $cn->get_url( 'account_api', '/api/account/braintree/createsubscription' );
				$api_args['method'] = 'POST';
				$api_args['headers'] = array_merge(
					$api_args['headers'],
					[
						'Authorization'	=> 'Bearer ' . $api_token,
						'Content-Type'	=> 'application/json; charset=utf-8'
					]
				);
				break;

			// braintree assign subscription
			case 'assign_subscription':
				$json = true;
				$api_url = $cn->get_url( 'account_api', '/api/account/braintree/assignsubscription' );
				$api_args['method'] = 'POST';
				$api_args['headers'] = array_merge(
					$api_args['headers'],
					[
						'Authorization'	=> 'Bearer ' . $api_token,
						'Content-Type'	=> 'application/json; charset=utf-8'
					]
				);
				break;

			// braintree create payment method
			case 'create_payment_method':
				$json = true;
				$api_url = $cn->get_url( 'account_api', '/api/account/braintree/createpaymentmethod' );
				$api_args['method'] = 'POST';
				$api_args['headers'] = array_merge(
					$api_args['headers'],
					[
						'Authorization'	=> 'Bearer ' . $api_token,
						'Content-Type'	=> 'application/json; charset=utf-8'
					]
				);
				break;
		}

		if ( ! empty( $params ) && is_array( $params ) ) {
			foreach ( $params as $key => $param ) {
				if ( is_object( $param ) )
					$api_params[$key] = $param;
				elseif ( is_array( $param ) )
					$api_params[$key] = array_map( 'sanitize_text_field', $param );
				elseif ( $key === 'Password' && ( $request === 'register' || $request === 'login' ) )
					$api_params[$key] = preg_replace( '/[^\w !"#$%&\'()*\+,\-.\/:;<=>?@\[\]^\`\{\|\}\~\\\\]/', '', $param );
				else
					$api_params[$key] = sanitize_text_field( $param );
			}

			if ( $json )
				$api_args['body'] = wp_json_encode( $api_params );
			else
				$api_args['body'] = $api_params;
		}

		$response = wp_remote_request( $api_url, $api_args );

		if ( is_wp_error( $response ) )
			$result = [ 'error' => $response->get_error_message() ];
		else {
			$content_type = wp_remote_retrieve_header( $response, 'Content-Type' );

			// html response, means error
			if ( $content_type == 'text/html' )
				$result = [ 'error' => esc_html__( 'Unexpected error occurred. Please try again later.', 'cookie-notice' ) ];
			else {
				$result = wp_remote_retrieve_body( $response );

				// detect json or array
				$result = is_array( $result ) ? $result : json_decode( $result );
			}
		}

		return $result;
	}

	/**
	 * Check whether WP Cron needs to add new task.
	 *
	 * @return void
	 */
	public function check_cron() {
		// get main instance
		$cn = Cookie_Notice();

		if ( is_multisite() && $cn->is_plugin_network_active() && $cn->network_options['global_override'] ) {
			$app_id = $cn->network_options['app_id'];
			$app_key = $cn->network_options['app_key'];
		} else {
			$app_id = $cn->options['general']['app_id'];
			$app_key = $cn->options['general']['app_key'];
		}

		// compliance active only
		if ( $app_id !== '' && $app_key !== '' ) {
			if ( $cn->get_status() === 'active' )
				$recurrence = 'daily';
			else
				$recurrence = 'hourly';

			// set schedule if needed
			if ( ! wp_next_scheduled( 'cookie_notice_get_app_analytics' ) )
				wp_schedule_event( time(), 'hourly', 'cookie_notice_get_app_analytics' );

			// set schedule if needed
			if ( ! wp_next_scheduled( 'cookie_notice_get_app_config' ) )
				wp_schedule_event( time(), $recurrence, 'cookie_notice_get_app_config' );
		} else {
			// remove schedule if needed
			if ( wp_next_scheduled( 'cookie_notice_get_app_analytics' ) )
				wp_clear_scheduled_hook( 'cookie_notice_get_app_analytics' );

			// remove schedule if needed
			if ( wp_next_scheduled( 'cookie_notice_get_app_config' ) )
				wp_clear_scheduled_hook( 'cookie_notice_get_app_config' );
		}
	}

	/**
	 * Get consent logs.
	 *
	 * @param string $date
	 * @return string|array
	 */
	public function get_consent_logs_by_date( $date ) {
		// get main instance
		$cn = Cookie_Notice();

		// get consent logs for specific date
		$result = $this->request(
			'get_consent_logs_by_date',
			[
				'AppID'			=> $cn->options['general']['app_id'],
				'AppSecretKey'	=> $cn->options['general']['app_key'],
				'Date'			=> $date
			]
		);

		// message?
		if ( ! empty( $result->message ) )
			$result = $result->message;
		// error?
		elseif ( ! empty( $result->error ) )
			$result = $result->error;
		// valid data?
		elseif ( ! empty( $result->data ) )
			$result = $result->data;
		else
			$result = [];

		return $result;
	}

	/**
	 * Get app analytics.
	 *
	 * @param string $app_id
	 * @param bool $force_update
	 * @param bool $force_action
	 * @return void
	 */
	public function get_app_analytics( $app_id = '', $force_update = false, $force_action = true ) {
		// get main instance
		$cn = Cookie_Notice();

		$allow_one_cron_per_hour = false;

		if ( is_multisite() && $cn->is_plugin_network_active() && $cn->network_options['global_override'] ) {
			if ( empty( $app_id ) )
				$app_id = $cn->network_options['app_id'];

			$network = true;
			$allow_one_cron_per_hour = true;
		} else {
			if ( empty( $app_id ) )
				$app_id = $cn->options['general']['app_id'];

			$network = false;
		}

		// in global override mode allow only one cron per hour
		if ( $allow_one_cron_per_hour && ! $force_update ) {
			$analytics = get_site_option( 'cookie_notice_app_analytics', [] );

			// analytics data?
			if ( ! empty( $analytics ) ) {
				$updated = strtotime( $analytics['lastUpdated'] );

				// last updated less than an hour?
				if ( $updated !== false && current_time( 'timestamp', true ) - $updated < 3600 )
					return;
			}
		}

		$response = $this->request(
			'get_analytics',
			[
				'AppID' => $app_id
			]
		);

		// get analytics
		if ( ! empty( $response->data ) ) {
			$result = map_deep( (array) $response->data, 'sanitize_text_field' );

			// add time updated
			$result['lastUpdated'] = date( 'Y-m-d H:i:s', current_time( 'timestamp', true ) );

			// get default status data
			$status_data = $cn->defaults['data'];

			// update status
			$status_data['status'] = $cn->get_status();

			// update subscription
			$status_data['subscription'] = $cn->get_subscription();

			if ( $status_data['status'] === 'active' && $status_data['subscription'] === 'basic' ) {
				$threshold = ! empty( $result['cycleUsage']->threshold ) ? (int) $result['cycleUsage']->threshold : 0;
				$visits = ! empty( $result['cycleUsage']->visits ) ? (int) $result['cycleUsage']->visits : 0;

				if ( $visits >= $threshold && $threshold > 0 )
					$status_data['threshold_exceeded'] = true;
			}

			if ( $network ) {
				update_site_option( 'cookie_notice_app_analytics', $result );
				update_site_option( 'cookie_notice_status', $status_data );
			} else {
				update_option( 'cookie_notice_app_analytics', $result, false );
				update_option( 'cookie_notice_status', $status_data, false );
			}

			// update status data
			$cn->set_status_data();

			if ( $force_action )
				do_action( 'cn_configuration_updated', 'analytics' );
		}
	}

	/**
	 * Get app config.
	 *
	 * @param string $app_id
	 * @param bool $force_update
	 * @param bool $force_action
	 * @return void|array
	 */
	public function get_app_config( $app_id = '', $force_update = false, $force_action = true ) {
		// get main instance
		$cn = Cookie_Notice();

		$allow_one_cron_per_hour = false;

		if ( is_multisite() && $cn->is_plugin_network_active() && $cn->network_options['global_override'] ) {
			if ( empty( $app_id ) )
				$app_id = $cn->network_options['app_id'];

			$network = true;
			$allow_one_cron_per_hour = true;
		} else {
			if ( empty( $app_id ) )
				$app_id = $cn->options['general']['app_id'];

			$network = false;
		}

		// in global override mode allow only one cron per hour
		if ( $allow_one_cron_per_hour && ! $force_update ) {
			$blocking = get_site_option( 'cookie_notice_app_blocking', [] );

			// analytics data?
			if ( ! empty( $blocking ) ) {
				$updated = strtotime( $blocking['lastUpdated'] );

				// last updated less than an hour?
				if ( $updated !== false && current_time( 'timestamp', true ) - $updated < 3600 )
					return;
			}
		}

		// get config
		$response = $this->request(
			'get_config',
			[
				'AppID' => $app_id
			]
		);

		// get status data
		$status_data = $cn->defaults['data'];

		// get config
		if ( ! empty( $response->data ) ) {
			// sanitize data
			$result_raw = map_deep( (array) $response->data, 'sanitize_text_field' );

			// set status
			$status_data['status'] = 'active';

			// check subscription
			if ( ! empty( $result_raw['SubscriptionType'] ) )
				$status_data['subscription'] = $cn->check_subscription( strtolower( $result_raw['SubscriptionType'] ) );

			if ( $status_data['subscription'] === 'basic' ) {
				// get analytics data options
				if ( $network )
					$analytics = get_site_option( 'cookie_notice_app_analytics', [] );
				else
					$analytics = get_option( 'cookie_notice_app_analytics', [] );

				if ( ! empty( $analytics ) ) {
					$threshold = ! empty( $analytics['cycleUsage']->threshold ) ? (int) $analytics['cycleUsage']->threshold : 0;
					$visits = ! empty( $analytics['cycleUsage']->visits ) ? (int) $analytics['cycleUsage']->visits : 0;

					if ( $visits >= $threshold && $threshold > 0 )
						$status_data['threshold_exceeded'] = true;
				}
			}

			// process blocking data
			$result = [
				'categories'				=> ! empty( $result_raw['DefaultCategoryJSON'] ) && is_array( $result_raw['DefaultCategoryJSON'] ) ? $result_raw['DefaultCategoryJSON'] : [],
				'providers'					=> ! empty( $result_raw['DefaultProviderJSON'] ) && is_array( $result_raw['DefaultProviderJSON'] ) ? $result_raw['DefaultProviderJSON'] : [],
				'patterns'					=> ! empty( $result_raw['DefaultCookieJSON'] ) && is_array( $result_raw['DefaultCookieJSON'] ) ? $result_raw['DefaultCookieJSON'] : [],
				'google_consent_default'	=> [],
				'lastUpdated'				=> date( 'Y-m-d H:i:s', current_time( 'timestamp', true ) )
			];

			if ( ! empty( $result_raw['BannerConfigJSON'] ) && is_object( $result_raw['BannerConfigJSON'] ) ) {
				$gcm = isset( $result_raw['BannerConfigJSON']->googleConsentMode ) ? (int) $result_raw['BannerConfigJSON']->googleConsentMode : 0;

				// is google consent mode enabled?
				if ( $gcm === 1 ) {
					$result['google_consent_default']['ad_storage'] = isset( $result_raw['BannerConfigJSON']->googleConsentMapAdStorage ) ? (int) $result_raw['BannerConfigJSON']->googleConsentMapAdStorage : 4;
					$result['google_consent_default']['analytics_storage'] = isset( $result_raw['BannerConfigJSON']->googleConsentMapAnalytics ) ? (int) $result_raw['BannerConfigJSON']->googleConsentMapAnalytics : 3;
					$result['google_consent_default']['functionality_storage'] = isset( $result_raw['BannerConfigJSON']->googleConsentMapFunctionality ) ? (int) $result_raw['BannerConfigJSON']->googleConsentMapFunctionality : 2;
					$result['google_consent_default']['personalization_storage'] = isset( $result_raw['BannerConfigJSON']->googleConsentMapPersonalization ) ? (int) $result_raw['BannerConfigJSON']->googleConsentMapPersonalization : 2;
					$result['google_consent_default']['security_storage'] = isset( $result_raw['BannerConfigJSON']->googleConsentMapSecurity ) ? (int) $result_raw['BannerConfigJSON']->googleConsentMapSecurity : 2;
					$result['google_consent_default']['ad_personalization'] = isset( $result_raw['BannerConfigJSON']->googleConsentMapAdPersonalization ) ? (int) $result_raw['BannerConfigJSON']->googleConsentMapAdPersonalization : 4;
					$result['google_consent_default']['ad_user_data'] = isset( $result_raw['BannerConfigJSON']->googleConsentMapAdUserData ) ? (int) $result_raw['BannerConfigJSON']->googleConsentMapAdUserData : 4;
				}
			}

			if ( $network )
				update_site_option( 'cookie_notice_app_blocking', $result );
			else
				update_option( 'cookie_notice_app_blocking', $result, false );
		} else {
			if ( ! empty( $response->error ) ) {
				if ( $response->error == 'App is not puplised yet' )
					$status_data['status'] = 'pending';
				else
					$status_data['status'] = '';
			}
		}

		if ( $network )
			update_site_option( 'cookie_notice_status', $status_data );
		else
			update_option( 'cookie_notice_status', $status_data, false );

		// update status data
		$cn->set_status_data();

		if ( $force_action )
			do_action( 'cn_configuration_updated', 'config' );

		return $status_data;
	}
}
