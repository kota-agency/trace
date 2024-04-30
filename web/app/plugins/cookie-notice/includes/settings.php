<?php
// exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Cookie_Notice_Settings class.
 *
 * @class Cookie_Notice_Settings
 */
class Cookie_Notice_Settings {

	public $parameters = [];
	public $operators = [];
	public $conditional_display_types = [];
	public $positions = [];
	public $styles = [];
	public $revoke_opts = [];
	public $links = [];
	public $link_targets = [];
	public $link_positions = [];
	public $colors = [];
	public $times = [];
	public $effects = [];
	public $script_placements = [];
	public $level_names = [];
	public $text_strings = [];
	private $analytics_app_data = [];

	/**
	 * Class constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		// actions
		add_action( 'admin_menu', [ $this, 'admin_menu_options' ] );
		add_action( 'network_admin_menu', [ $this, 'admin_menu_options' ] );
		add_action( 'after_setup_theme', [ $this, 'load_defaults' ] );
		add_action( 'plugins_loaded', [ $this, 'load_modules' ], 0 );
		add_action( 'admin_init', [ $this, 'validate_network_options' ], 9 );
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
		add_action( 'admin_print_styles', [ $this, 'admin_print_styles' ] );
		add_action( 'wp_ajax_cn_purge_cache', [ $this, 'ajax_purge_cache' ] );
		add_action( 'wp_ajax_cn-get-group-rules-values', [ $this, 'get_group_rule_values' ] );
		add_action( 'admin_notices', [ $this, 'settings_errors' ] );
		add_action( 'network_admin_notices', [ $this, 'settings_errors' ] );
	}

	/**
	 * Check whether caching compatibility is enabled. Also just before saving settings.
	 *
	 * @return bool
	 */
	public function is_caching_compatibility() {
		// get current value from database
		$db_cc = Cookie_Notice()->options['general']['caching_compatibility'];

		// if it is enabled allow immediately
		if ( $db_cc )
			return true;

		// check caching compatibility before it is saved, needed when we change caching_compatibility from false to true
		if ( ! ( isset( $_POST['save_cookie_notice_options'], $_POST['action'], $_POST['_wpnonce'], $_POST['option_page'], $_POST['cookie_notice_options'] ) && $_POST['option_page'] === 'cookie_notice_options' && wp_verify_nonce( $_POST['_wpnonce'], 'cookie_notice_options-options' ) !== false ) )
			return false;

		// check availability of caching compatibility itself
		if ( ! isset( $_POST['cookie_notice_options']['caching_compatibility'] ) )
			return false;

		// get active caching plugins
		$active_plugins = cn_get_active_caching_plugins();

		// return caching compatibility on the fly
		return ! empty( $active_plugins );
	}

	/**
	 * Load additional modules.
	 *
	 * @return void
	 */
	public function load_modules() {
		// caching compatibility enabled?
		if ( $this->is_caching_compatibility() ) {
			// wp fastest cache
			if ( cn_is_plugin_active( 'wpfastestcache' ) )
				include_once( COOKIE_NOTICE_PATH . 'includes/modules/wp-fastest-cache/wp-fastest-cache.php' );

			// wp-optimize
			if ( cn_is_plugin_active( 'wpoptimize' ) )
				include_once( COOKIE_NOTICE_PATH . 'includes/modules/wp-optimize/wp-optimize.php' );

			// hummingbird
			if ( cn_is_plugin_active( 'hummingbird' ) )
				include_once( COOKIE_NOTICE_PATH . 'includes/modules/hummingbird/hummingbird.php' );
		}
	}

	/**
	 * Load plugin defaults.
	 *
	 * @return void
	 */
	public function load_defaults() {
		$this->parameters = [
			'page_type'			=> __( 'Page Type', 'cookie-notice' ),
			'page'				=> __( 'Page', 'cookie-notice' ),
			'post_type'			=> __( 'Post Type', 'cookie-notice' ),
			'post_type_archive'	=> __( 'Post Type Archive', 'cookie-notice' ),
			'user_type'			=> __( 'User Type', 'cookie-notice' )
		];

		$this->operators = [
			'equal'		=> __( 'is equal to', 'cookie-notice' ),
			'not_equal'	=> __( 'is not equal to', 'cookie-notice' )
		];

		$this->conditional_display_types = [
			'hide'	=> __( 'Hide the banner', 'cookie-notice' ),
			'show'	=> __( 'Show the banner', 'cookie-notice' )
		];

		$this->positions = [
			'top'		=> __( 'Top', 'cookie-notice' ),
			'bottom'	=> __( 'Bottom', 'cookie-notice' )
		];

		$this->styles = [
			'none'			=> __( 'None', 'cookie-notice' ),
			'wp-default'	=> __( 'Light', 'cookie-notice' ),
			'bootstrap'		=> __( 'Dark', 'cookie-notice' )
		];

		$this->revoke_opts = [
			'automatic'	=> __( 'Automatic', 'cookie-notice' ),
			'manual'	=> __( 'Manual', 'cookie-notice' )
		];

		$this->links = [
			'page'		=> __( 'Page link', 'cookie-notice' ),
			'custom'	=> __( 'Custom link', 'cookie-notice' )
		];

		$this->link_targets = [ '_blank', '_self' ];

		$this->link_positions = [
			'banner'	=> __( 'Banner', 'cookie-notice' ),
			'message'	=> __( 'Message', 'cookie-notice' )
		];

		$this->colors = [
			'text'		=> __( 'Text color', 'cookie-notice' ),
			'button'	=> __( 'Button color', 'cookie-notice' ),
			'bar'		=> __( 'Bar color', 'cookie-notice' )
		];

		$this->times = apply_filters(
			'cn_cookie_expiry',
			[
				'hour'		=> [ __( 'An hour', 'cookie-notice' ), 3600 ],
				'day'		=> [ __( '1 day', 'cookie-notice' ), 86400 ],
				'week'		=> [ __( '1 week', 'cookie-notice' ), 604800 ],
				'month'		=> [ __( '1 month', 'cookie-notice' ), 2592000 ],
				'3months'	=> [ __( '3 months', 'cookie-notice' ), 7862400 ],
				'6months'	=> [ __( '6 months', 'cookie-notice' ), 15811200 ],
				'year'		=> [ __( '1 year', 'cookie-notice' ), 31536000 ],
				'infinity'	=> [ __( 'infinity', 'cookie-notice' ), 2147483647 ]
			]
		);

		$this->effects = [
			'none'	=> __( 'None', 'cookie-notice' ),
			'fade'	=> __( 'Fade', 'cookie-notice' ),
			'slide'	=> __( 'Slide', 'cookie-notice' )
		];

		$this->script_placements = [
			'header'	=> __( 'Header', 'cookie-notice' ),
			'footer'	=> __( 'Footer', 'cookie-notice' )
		];

		$this->level_names = [
			1 => [
				1 => __( 'Silver', 'cookie-notice' ),
				2 => __( 'Gold', 'cookie-notice' ),
				3 => __( 'Platinum', 'cookie-notice' )
			],
			2 => [
				1 => __( 'Private', 'cookie-notice' ),
				2 => __( 'Balanced', 'cookie-notice' ),
				3 => __( 'Personalized', 'cookie-notice' )
			],
			3 => [
				1 => __( 'Reject All', 'cookie-notice' ),
				2 => __( 'Accept Some', 'cookie-notice' ),
				3 => __( 'Accept All', 'cookie-notice' )
			]
		];

		$this->text_strings = [
			'saveBtnText'		=> __( 'Save my preferences', 'cookie-notice' ),
			'privacyBtnText'	=> __( 'Privacy policy', 'cookie-notice' ),
			'dontSellBtnText'	=> __( 'Do Not Sell', 'cookie-notice' ),
			'customizeBtnText'	=> __( 'Preferences', 'cookie-notice' ),
			'headingText'		=> __( "We believe your data is your property and support your right to privacy and transparency.", 'cookie-notice' ),
			'bodyText'			=> __( "Select a Data Access Level and Duration to choose how we use and share your data.", 'cookie-notice' ),
			'levelBodyText_1'	=> __( 'Highest level of privacy. Data accessed for necessary site operations only. Data shared with 3rd parties to ensure the site is secure and works on your device.', 'cookie-notice' ),
			'levelBodyText_2'	=> __( 'Balanced experience. Data accessed for content personalisation and site optimisation. Data shared with 3rd parties may be used to track and store your preferences for this site.', 'cookie-notice' ),
			'levelBodyText_3'	=> __( 'Highest level of personalisation. Data accessed to make ads and media more relevant. Data shared with 3rd parties may be use to track you on this site and other sites you visit.', 'cookie-notice' ),
			'levelNameText_1'	=> $this->level_names[1][1],
			'levelNameText_2'	=> $this->level_names[1][2],
			'levelNameText_3'	=> $this->level_names[1][3],
			'monthText'			=> __( 'month', 'cookie-notice' ),
			'monthsText'		=> __( 'months', 'cookie-notice' )
		];

		// get main instance
		$cn = Cookie_Notice();

		// set default text strings
		$cn->defaults['general']['message_text'] = __( 'We use cookies to ensure that we give you the best experience on our website. If you continue to use this site we will assume that you are happy with it.', 'cookie-notice' );
		$cn->defaults['general']['accept_text'] = __( 'Ok', 'cookie-notice' );
		$cn->defaults['general']['refuse_text'] = __( 'No', 'cookie-notice' );
		$cn->defaults['general']['revoke_message_text'] = __( 'You can revoke your consent any time using the Revoke consent button.', 'cookie-notice' );
		$cn->defaults['general']['revoke_text'] = __( 'Revoke consent', 'cookie-notice' );
		$cn->defaults['general']['see_more_opt']['text'] = __( 'Privacy policy', 'cookie-notice' );

		// set translation strings on plugin activation
		if ( $cn->options['general']['translate'] === true ) {
			$cn->options['general']['translate'] = false;

			$cn->options['general']['message_text'] = $cn->defaults['general']['message_text'];
			$cn->options['general']['accept_text'] = $cn->defaults['general']['accept_text'];
			$cn->options['general']['refuse_text'] = $cn->defaults['general']['refuse_text'];
			$cn->options['general']['revoke_message_text'] = $cn->defaults['general']['revoke_message_text'];
			$cn->options['general']['revoke_text'] = $cn->defaults['general']['revoke_text'];
			$cn->options['general']['see_more_opt']['text'] = $cn->defaults['general']['see_more_opt']['text'];

			if ( $cn->is_network_admin() )
				update_site_option( 'cookie_notice_options', $cn->options['general'] );
			else
				update_option( 'cookie_notice_options', $cn->options['general'] );
		}

		// WPML >= 3.2
		if ( defined( 'ICL_SITEPRESS_VERSION' ) && version_compare( ICL_SITEPRESS_VERSION, '3.2', '>=' ) ) {
			$this->register_wpml_strings();
		// WPML and Polylang compatibility
		} elseif ( function_exists( 'icl_register_string' ) ) {
			icl_register_string( 'Cookie Notice', 'Message in the notice', $cn->options['general']['message_text'] );
			icl_register_string( 'Cookie Notice', 'Button text', $cn->options['general']['accept_text'] );
			icl_register_string( 'Cookie Notice', 'Refuse button text', $cn->options['general']['refuse_text'] );
			icl_register_string( 'Cookie Notice', 'Revoke message text', $cn->options['general']['revoke_message_text'] );
			icl_register_string( 'Cookie Notice', 'Revoke button text', $cn->options['general']['revoke_text'] );
			icl_register_string( 'Cookie Notice', 'Privacy policy text', $cn->options['general']['see_more_opt']['text'] );
			icl_register_string( 'Cookie Notice', 'Custom link', $cn->options['general']['see_more_opt']['link'] );
		}
	}

	/**
	 * Add submenu.
	 *
	 * @return void
	 */
	public function admin_menu_options() {
		if ( current_action() === 'network_admin_menu' && ! Cookie_Notice()->is_plugin_network_active() )
			return;

		add_menu_page( __( 'Cookie Notice', 'cookie-notice' ), __( 'Cookies', 'cookie-notice' ), apply_filters( 'cn_manage_cookie_notice_cap', 'manage_options' ), 'cookie-notice', [ $this, 'options_page' ], 'none', '99.300' );
		add_submenu_page( 'cookie-notice', __( 'Cookie Notice - Settings', 'cookie-notice' ), __( 'Settings', 'cookie-notice' ), apply_filters( 'cn_manage_cookie_notice_cap', 'manage_options' ), 'cookie-notice', [ $this, 'options_page' ] );
		add_submenu_page( 'cookie-notice', __( 'Cookie Notice - Consent Logs', 'cookie-notice' ), __( 'Consent Logs', 'cookie-notice' ), apply_filters( 'cn_manage_cookie_notice_cap', 'manage_options' ), 'cookie-notice&tab=consent-logs', [ $this, 'options_page' ] );

		// highlight submenus
		add_filter( 'submenu_file', [ $this, 'submenu_file' ], 10, 2 );
	}

	/**
	 * Highlight submenu items.
	 *
	 * @param string|null $submenu_file
	 * @param string $parent_file
	 * @return string|null
	 */
	public function submenu_file( $submenu_file, $parent_file ) {
		if ( $parent_file === 'cookie-notice' ) {
			$tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'settings';

			if ( $tab !== 'settings' )
				return 'cookie-notice&tab=' . $tab;
		}

		return $submenu_file;
	}

	/**
	 * Options page output.
	 *
	 * @return void
	 */
	public function options_page() {
		// get main instance
		$cn = Cookie_Notice();

		// get cookie compliance status
		$status = $cn->get_status();

		echo '
		<div class="wrap">
			<h2>' . esc_html__( 'Cookie Notice & Compliance for GDPR/CCPA', 'cookie-notice' ) . '</h2>';

		// set tabs
		$tabs = [
			'settings'		=> __( 'Settings', 'cookie-notice' ),
			'consent-logs'	=> __( 'Consent Logs', 'cookie-notice' )
		];

		// reset tabs
		reset( $tabs );

		// get first default tab
		$first_tab = key( $tabs );

		// sanitize current tab
		$tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : $first_tab;

		// get current tab
		$tab = ! empty( $tab ) && array_key_exists( $tab, $tabs ) ? $tab : $first_tab;

		echo '
			<h2 class="nav-tab-wrapper cn-nav-tab-wrapper">';

		foreach ( $tabs as $key => $label ) {
			if ( $cn->is_network_admin() )
				$tab_url = network_admin_url( 'admin.php?page=cookie-notice&tab=' . $key );
			else
				$tab_url = admin_url( 'admin.php?page=cookie-notice&tab=' . $key );

			echo '
			<a class="nav-tab' . ( $tab === $key ? ' nav-tab-active' : '' ) . '" href="' . esc_url( $tab_url ) . '">' . esc_html( $label ) . '</a>';
		}

		echo '
			</h2>';

		if ( $tab === 'consent-logs' ) {
			echo '
			<div class="cookie-notice-settings">';

			$this->display_options_sidebar();

			echo '
				<form action="#">
					<div class="cn-options">';

			do_settings_sections( 'cookie_notice_consent_logs' );

			if ( $cn->is_network_admin() )
				$allow_consent_logs = true;
			elseif ( $cn->is_plugin_network_active() && $cn->network_options['global_override'] )
				$allow_consent_logs = false;
			else
				$allow_consent_logs = true;

			if ( $allow_consent_logs ) {
				if ( $status === 'active' ) {
					// include wp list table class if needed
					if ( ! class_exists( 'WP_List_Table' ) )
						include_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

					// include consent logs list table
					include_once( COOKIE_NOTICE_PATH . '/includes/consent-logs-list-table.php' );

					// initialize list table
					$list_table = new Cookie_Notice_Consent_Logs_List_Table();

					$list_table->views();
					$list_table->prepare_items();
					$list_table->display();
				} else {
					if ( $cn->is_network_admin() )
						$upgrade_url = network_admin_url( 'admin.php?page=cookie-notice&welcome=1' );
					else
						$upgrade_url = admin_url( 'admin.php?page=cookie-notice&welcome=1' );

					echo '
						<div id="cn-consent-logs-disabled">
							<img id="cn-consent-logs-bg" src="' . esc_url( COOKIE_NOTICE_URL ) . '/img/consent-logs.png" alt="Consent Logs" />
							<div id="cn-consent-logs-upgrade">
								<div id="cn-consent-logs-modal">
									<h2>' . esc_html__( 'Record and view Consent Log entries inside WordPress', 'cookie-notice' ) . '</h2>
									<p>' . esc_html__( 'Automatically store a record of each consent.', 'cookie-notice' ) . '</p>
									<p>' . esc_html__( 'Monitor consent activity directly in your WordPress dashboard.', 'cookie-notice' ) . '</p>
									<p><a href="' . esc_url( $upgrade_url ) . '" class="button button-primary button-hero cn-button">' . esc_html__( 'Upgrade to Cookie Compliance', 'cookie-notice' ) . '</a></p>
								</div>
							</div>
						</div>';
				}
			}

			echo '
					</div>
				</form>
			</div>';
		} elseif ( $tab === 'settings' ) {
			echo '
				<div class="cookie-notice-settings">';

			$this->display_options_sidebar();

			// multisite?
			if ( is_multisite() ) {
				// network admin?
				if ( $cn->is_network_admin() ) {
					$form_class = ( $cn->is_plugin_network_active() && ! $cn->options['general']['global_override'] ? 'cn-options-disabled' : '' );
					$form_page = 'admin.php?page=cookie-notice';
					$hidden_input = '<input type="hidden" name="cn-network-settings" value="true" />';
				// single network site
				} else {
					$form_class = ( $cn->is_plugin_network_active() && $cn->network_options['global_override'] ? 'cn-options-disabled cn-options-submit-disabled' : '' );
					$form_page = 'options.php';
					$hidden_input = '';
				}
			// single site
			} else {
				$form_class = '';
				$form_page = 'options.php';
				$hidden_input = '';
			}

			echo '
					<form action="' . esc_attr( $form_page ) . '" method="post"' . ( $form_class !== '' ? ' class="' . esc_attr( $form_class ) . '"' : '' ) . '>';

			settings_fields( 'cookie_notice_options' );

			if ( $hidden_input !== '' ) {
				$allowed_html = [
					'input'	=> [
						'type'	=> true,
						'name'	=> true,
						'value'	=> true
					]
				];

				echo wp_kses( $hidden_input, $allowed_html );
			}

			echo '
						<div class="cn-options">';

			do_settings_sections( 'cookie_notice_options' );

			echo '		</div>
						<p class="submit">';
			submit_button( '', 'primary', 'save_cookie_notice_options', false );

			echo ' ';

			submit_button( esc_html__( 'Reset to defaults', 'cookie-notice' ), 'secondary', 'reset_cookie_notice_options', false );
			echo '
						</p>
					</form>
				</div>
				<div class="clear"></div>
			</div>';
		}
	}

	/**
	 * Display options sidebar HTML.
	 *
	 * @return void
	 */
	public function display_options_sidebar() {
		// get main instance
		$cn = Cookie_Notice();

		// get cookie compliance status
		$status = $cn->get_status();

		// get subscription
		$subscription = $cn->get_subscription();

		echo '
		<div class="cookie-notice-sidebar">
			<div class="cookie-notice-credits">
				<div class="inside">
					<div class="inner">';

		// compliance enabled
		if ( $status === 'active' ) {
			echo '
						<div class="cn-pricing-info">
							<div class="cn-pricing-head">
								<p>' . esc_html__( 'Your Cookie Compliance plan:', 'cookie-notice' ) . '</p>
								<h2>' . esc_html( $subscription === 'pro' ? __( 'Professional', 'cookie-notice' ) : __( 'Basic', 'cookie-notice' ) ) . '</h2>
							</div>
							<div class="cn-pricing-body">
								<p class="cn-active"><span class="cn-icon"></span>' . esc_html__( 'GDPR, CCPA, LGPD, PECR requirements', 'cookie-notice' ) . '</p>
								<p class="cn-active"><span class="cn-icon"></span>' . esc_html__( 'Consent Analytics Dashboard', 'cookie-notice' ) . '</p>
								<p class="' . ( $subscription === 'pro' ? 'cn-active' : 'cn-inactive' ) . '"><span class="cn-icon"></span>' . sprintf( esc_html__( '%sUnlimited%s visits', 'cookie-notice' ), '<b>', '</b>' ) . '</p>
								<p class="' . ( $subscription === 'pro' ? 'cn-active' : 'cn-inactive' ) . '"><span class="cn-icon"></span>' . sprintf( esc_html__( '%sLifetime%s consent storage', 'cookie-notice' ), '<b>', '</b>' ) . '</p>
								<p class="' . ( $subscription === 'pro' ? 'cn-active' : 'cn-inactive' ) . '"><span class="cn-icon"></span>' . sprintf( esc_html__( '%sGoogle & Facebook%s consent modes', 'cookie-notice' ), '<b>', '</b>' ) . '</p>
								<p class="' . ( $subscription === 'pro' ? 'cn-active' : 'cn-inactive' ) . '"><span class="cn-icon"></span>' . sprintf( esc_html__( '%sGeolocation%s support', 'cookie-notice' ), '<b>', '</b>' ) . '</p>
								<p class="' . ( $subscription === 'pro' ? 'cn-active' : 'cn-inactive' ) . '"><span class="cn-icon"></span>' . sprintf( esc_html__( '%sUnlimited%s languages', 'cookie-notice' ), '<b>', '</b>' ) . '</p>
								<p class="' . ( $subscription === 'pro' ? 'cn-active' : 'cn-inactive' ) . '"><span class="cn-icon"></span>' . sprintf( esc_html__( '%sPriority%s Support', 'cookie-notice' ), '<b>', '</b>' ) . '</p>
							</div>';

			if ( $subscription !== 'pro' ) {
				echo '
							<div class="cn-pricing-footer">
								<a href="' . esc_url( $cn->get_url( 'host', '?utm_campaign=upgrade+to+pro&utm_source=wordpress&utm_medium=link#/en/cc/dashboard?app-id=' . $cn->options['general']['app_id'] . '&open-modal=payment' ) ) . '" class="button button-secondary button-hero cn-button" target="_blank">' . esc_html__( 'Upgrade to Pro', 'cookie-notice' ) . '</a>
							</div>';
			}

			echo '
						</div>';
		// compliance disabled
		} else {
				echo '
						<h1><b>' . esc_html__( 'Protect your business', 'cookie-notice' ) . '</b></h1>
						<h2>' . esc_html__( 'with Cookie Compliance&trade;', 'cookie-notice' ) . '</h2>
						<div class="cn-lead">
							<p>' . esc_html__( 'Deliver better consent experiences and comply with GDPR, CCPA and other data privacy laws more effectively.', 'cookie-notice' ) . '</p>
						</div>
						<img alt="' . esc_html__( 'Cookie Compliance dashboard', 'cookie-notice' ) . '" src="' . esc_url( COOKIE_NOTICE_URL ) . '/img/screen-compliance.png">
						<p><a href="https://cookie-compliance.co/?utm_campaign=learn+more&utm_source=wordpress&utm_medium=banner" class="button button-secondary button-hero cn-button" target="_blank">' . esc_html__( 'Learn more', 'cookie-notice' ) . '</a></p>';
		}

		echo '
					</div>
				</div>
			</div>';

		echo '
			<div class="cookie-notice-faq">
				<h2>' . esc_html__( 'F.A.Q.', 'cookie-notice' ) . '</h2>
				<div class="cn-toggle-container">
					<label for="cn-faq-1" class="cn-toggle-item">
						<input id="cn-faq-1" type="checkbox" />
						<span class="cn-toggle-heading">' . esc_html__( 'Does the Cookie Notice make my site fully compliant with GDPR/CCPA and other privacy regulations?', 'cookie-notice' ) . '</span>
						<span class="cn-toggle-body">' . esc_html__( 'It is not possible to provide the required technical compliance features using only a WordPress plugin. Features like consent record storage, purpose categories and script blocking that bring your site into full compliance with privacy regulations are only available through the Cookie Compliance integration.', 'cookie-notice' ) . '
					</label>
					<label for="cn-faq-2" class="cn-toggle-item">
						<input id="cn-faq-2" type="checkbox" />
						<span class="cn-toggle-heading">' . esc_html__( 'Does the Cookie Compliance integration make my site fully compliant with GDPR/CCPA?', 'cookie-notice' ) . '</span>
						<span class="cn-toggle-body">' . esc_html__( 'Yes! The plugin + web application version includes technical compliance features to meet requirements for over 100 countries and legal jurisdictions.', 'cookie-notice' ) . '</span>
					</label>
					<label for="cn-faq-3" class="cn-toggle-item">
						<input id="cn-faq-3" type="checkbox" />
						<span class="cn-toggle-heading">' . esc_html__( 'Is Cookie Compliance free?', 'cookie-notice' ) . '</span>
						<span class="cn-toggle-body">' . esc_html__( 'Yes, but with limits. Cookie Compliance includes both free and paid plans to choose from depending on your needs and your website monthly traffic.', 'cookie-notice' ) . '</span>
					</label>
					<label for="cn-faq-4" class="cn-toggle-item">
						<input id="cn-faq-4" type="checkbox" />
						<span class="cn-toggle-heading">' . esc_html__( 'Where can I find pricing options?', 'cookie-notice' ) . '</span>
						<span class="cn-toggle-body">' . esc_html__( 'You can learn more about the features and pricing by visiting the Cookie Compliance website here:', 'cookie-notice' ) . ' <a href="https://cookie-compliance.co/?utm_campaign=pricing+options&utm_source=wordpress&utm_medium=textlink" target="_blank">https://cookie-compliance.co</a></span>
					</label>
				</div>
			</div>';

		echo '
		</div>';
	}

	/**
	 * Register plugin settings.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting( 'cookie_notice_options', 'cookie_notice_options', [ $this, 'validate_options' ] );

		// get main instance
		$cn = Cookie_Notice();

		$status = $cn->get_status();

		if ( $cn->is_network_admin() )
			$cb = '';
		elseif ( $cn->is_plugin_network_active() && $cn->network_options['global_override'] )
			$cb = [ $this, 'cn_network_section' ];
		else
			$cb = '';

		add_settings_section( 'cookie_notice_consent_logs', esc_html__( 'Consent Logs', 'cookie-notice' ), $cb, 'cookie_notice_consent_logs', [ 'before_section' => '<div class="%s">', 'after_section' => '</div>', 'section_class' => 'cn-section-container logs-section' ] );

		// multisite?
		if ( is_multisite() ) {
			// network admin?
			if ( $cn->is_network_admin() ) {
				// network section
				add_settings_section( 'cookie_notice_network', esc_html__( 'Network Settings', 'cookie-notice' ), '', 'cookie_notice_options', [ 'before_section' => '<div class="%s">', 'after_section' => '</div>', 'section_class' => 'cn-section-container network-section' ] );
				add_settings_field( 'cn_global_override', esc_html__( 'Global Settings Override', 'cookie-notice' ), [ $this, 'cn_global_override' ], 'cookie_notice_options', 'cookie_notice_network' );
				add_settings_field( 'cn_global_cookie', esc_html__( 'Global Cookie', 'cookie-notice' ), [ $this, 'cn_global_cookie' ], 'cookie_notice_options', 'cookie_notice_network' );
			} elseif ( $cn->is_plugin_network_active() && $cn->network_options['global_override'] ) {
				// network section
				add_settings_section( 'cookie_notice_network', esc_html__( 'Network Settings', 'cookie-notice' ), [ $this, 'cn_network_section' ], 'cookie_notice_options', [ 'before_section' => '<div class="%s">', 'after_section' => '</div>', 'section_class' => 'cn-section-container network-section' ] );
				add_settings_field( 'cn_dummy', '', '__return_empty_string', 'cookie_notice_options', 'cookie_notice_network' );

				// get default status data
				$default_data = $cn->defaults['data'];

				// get real status of current site, not network since global_override is on
				$status_data = get_option( 'cookie_notice_status', $default_data );

				// old status format?
				if ( ! is_array( $status_data ) ) {
					// old value saved as string
					if ( is_string( $status_data ) && $cn->check_status( $status_data ) ) {
						// update status
						$default_data['status'] = $status_data;
					}

					// set data
					$status_data = $default_data;
				}

				// get valid status
				$status = $cn->check_status( $status_data['status'] );
			}
		}

		// compliance enabled
		if ( $status === 'active' ) {
			// compliance section
			add_settings_section( 'cookie_notice_compliance', esc_html__( 'Compliance Integration', 'cookie-notice' ), '', 'cookie_notice_options', [ 'before_section' => '<div class="%s">', 'after_section' => '</div>', 'section_class' => 'cn-section-container compliance-section' ] );
			add_settings_field( 'cn_app_status', esc_html__( 'Compliance Status', 'cookie-notice' ), [ $this, 'cn_app_status' ], 'cookie_notice_options', 'cookie_notice_compliance' );
			add_settings_field( 'cn_app_id', esc_html__( 'App ID', 'cookie-notice' ), [ $this, 'cn_app_id' ], 'cookie_notice_options', 'cookie_notice_compliance' );
			add_settings_field( 'cn_app_key', esc_html__( 'App Key', 'cookie-notice' ), [ $this, 'cn_app_key' ], 'cookie_notice_options', 'cookie_notice_compliance' );

			// configuration section
			add_settings_section( 'cookie_notice_configuration', esc_html__( 'Compliance Settings', 'cookie-notice' ), '', 'cookie_notice_options', [ 'before_section' => '<div class="%s">', 'after_section' => '</div>', 'section_class' => 'cn-section-container misc-section' ] );
			add_settings_field( 'cn_app_blocking', esc_html__( 'Autoblocking', 'cookie-notice' ), [ $this, 'cn_app_blocking' ], 'cookie_notice_options', 'cookie_notice_configuration' );
			add_settings_field( 'cn_refuse_code', esc_html__( 'Scripts', 'cookie-notice' ), [ $this, 'cn_refuse_code' ], 'cookie_notice_options', 'cookie_notice_configuration' );
			add_settings_field( 'cn_caching_compatibility', esc_html__( 'Caching Compatibility', 'cookie-notice' ), [ $this, 'cn_caching_compatibility' ], 'cookie_notice_options', 'cookie_notice_configuration' );
			add_settings_field( 'cn_app_purge_cache', esc_html__( 'Purge Cache', 'cookie-notice' ), [ $this, 'cn_app_purge_cache' ], 'cookie_notice_options', 'cookie_notice_configuration' );
			add_settings_field( 'cn_conditional_display', esc_html__( 'Conditional Display', 'cookie-notice' ), [ $this, 'cn_conditional_display' ], 'cookie_notice_options', 'cookie_notice_configuration' );
			add_settings_field( 'cn_bot_detection', esc_html__( 'Bot Detection', 'cookie-notice' ), [ $this, 'cn_bot_detection' ], 'cookie_notice_options', 'cookie_notice_configuration' );
			add_settings_field( 'cn_amp_support', esc_html__( 'AMP Support', 'cookie-notice' ), [ $this, 'cn_amp_support' ], 'cookie_notice_options', 'cookie_notice_configuration' );
			add_settings_field( 'cn_debug_mode', esc_html__( 'Debug Mode', 'cookie-notice' ), [ $this, 'cn_debug_mode' ], 'cookie_notice_options', 'cookie_notice_configuration' );
			add_settings_field( 'cn_deactivation_delete', esc_html__( 'Deactivation', 'cookie-notice' ), [ $this, 'cn_deactivation_delete' ], 'cookie_notice_options', 'cookie_notice_configuration' );
		// compliance disabled
		} else {
			// compliance section
			add_settings_section( 'cookie_notice_compliance', esc_html__( 'Compliance Integration', 'cookie-notice' ), '', 'cookie_notice_options', [ 'before_section' => '<div class="%s">', 'after_section' => '</div>', 'section_class' => 'cn-section-container compliance-section' ] );
			add_settings_field( 'cn_app_status', esc_html__( 'Compliance status', 'cookie-notice' ), [ $this, 'cn_app_status' ], 'cookie_notice_options', 'cookie_notice_compliance' );
			add_settings_field( 'cn_app_id', esc_html__( 'App ID', 'cookie-notice' ), [ $this, 'cn_app_id' ], 'cookie_notice_options', 'cookie_notice_compliance' );
			add_settings_field( 'cn_app_key', esc_html__( 'App Key', 'cookie-notice' ), [ $this, 'cn_app_key' ], 'cookie_notice_options', 'cookie_notice_compliance' );

			// configuration section
			add_settings_section( 'cookie_notice_configuration', esc_html__( 'Notice Settings', 'cookie-notice' ), '', 'cookie_notice_options', [ 'before_section' => '<div class="%s">', 'after_section' => '</div>', 'section_class' => 'cn-section-container notice-section' ] );
			add_settings_field( 'cn_message_text', esc_html__( 'Message', 'cookie-notice' ), [ $this, 'cn_message_text' ], 'cookie_notice_options', 'cookie_notice_configuration' );
			add_settings_field( 'cn_accept_text', esc_html__( 'Button text', 'cookie-notice' ), [ $this, 'cn_accept_text' ], 'cookie_notice_options', 'cookie_notice_configuration' );
			add_settings_field( 'cn_see_more', esc_html__( 'Privacy policy', 'cookie-notice' ), [ $this, 'cn_see_more' ], 'cookie_notice_options', 'cookie_notice_configuration' );
			add_settings_field( 'cn_refuse_opt', esc_html__( 'Refuse consent', 'cookie-notice' ), [ $this, 'cn_refuse_opt' ], 'cookie_notice_options', 'cookie_notice_configuration' );
			add_settings_field( 'cn_revoke_opt', esc_html__( 'Revoke consent', 'cookie-notice' ), [ $this, 'cn_revoke_opt' ], 'cookie_notice_options', 'cookie_notice_configuration' );
			add_settings_field( 'cn_refuse_code', esc_html__( 'Script blocking', 'cookie-notice' ), [ $this, 'cn_refuse_code' ], 'cookie_notice_options', 'cookie_notice_configuration' );
			add_settings_field( 'cn_redirection', esc_html__( 'Reloading', 'cookie-notice' ), [ $this, 'cn_redirection' ], 'cookie_notice_options', 'cookie_notice_configuration' );
			add_settings_field( 'cn_on_scroll', esc_html__( 'On scroll', 'cookie-notice' ), [ $this, 'cn_on_scroll' ], 'cookie_notice_options', 'cookie_notice_configuration' );
			add_settings_field( 'cn_on_click', esc_html__( 'On click', 'cookie-notice' ), [ $this, 'cn_on_click' ], 'cookie_notice_options', 'cookie_notice_configuration' );
			add_settings_field( 'cn_time', esc_html__( 'Accepted expiry', 'cookie-notice' ), [ $this, 'cn_time' ], 'cookie_notice_options', 'cookie_notice_configuration' );
			add_settings_field( 'cn_time_rejected', esc_html__( 'Rejected expiry', 'cookie-notice' ), [ $this, 'cn_time_rejected' ], 'cookie_notice_options', 'cookie_notice_configuration' );
			add_settings_field( 'cn_conditional_display', esc_html__( 'Conditional display', 'cookie-notice' ), [ $this, 'cn_conditional_display' ], 'cookie_notice_options', 'cookie_notice_configuration' );
			add_settings_field( 'cn_script_placement', esc_html__( 'Script placement', 'cookie-notice' ), [ $this, 'cn_script_placement' ], 'cookie_notice_options', 'cookie_notice_configuration' );
			add_settings_field( 'cn_deactivation_delete', esc_html__( 'Deactivation', 'cookie-notice' ), [ $this, 'cn_deactivation_delete' ], 'cookie_notice_options', 'cookie_notice_configuration' );

			// design section
			add_settings_section( 'cookie_notice_design', esc_html__( 'Notice Design', 'cookie-notice' ), '', 'cookie_notice_options', [ 'before_section' => '<div class="%s">', 'after_section' => '</div>', 'section_class' => 'cn-section-container design-section' ] );
			add_settings_field( 'cn_position', esc_html__( 'Position', 'cookie-notice' ), [ $this, 'cn_position' ], 'cookie_notice_options', 'cookie_notice_design' );
			add_settings_field( 'cn_hide_effect', esc_html__( 'Animation', 'cookie-notice' ), [ $this, 'cn_hide_effect' ], 'cookie_notice_options', 'cookie_notice_design' );
			add_settings_field( 'cn_colors', esc_html__( 'Colors', 'cookie-notice' ), [ $this, 'cn_colors' ], 'cookie_notice_options', 'cookie_notice_design' );
			add_settings_field( 'cn_css_class', esc_html__( 'Button class', 'cookie-notice' ), [ $this, 'cn_css_class' ], 'cookie_notice_options', 'cookie_notice_design' );
		}
	}

	/**
	 * Network settings override option.
	 *
	 * @return void
	 */
	public function cn_global_override() {
		echo '
		<div id="cn_global_override">
			<label><input type="checkbox" name="cookie_notice_options[global_override]" value="1" ' . checked( true, Cookie_Notice()->options['general']['global_override'], false ) . ' />' . esc_html__( 'Enable global network settings override.', 'cookie-notice' ) . '</label>
			<p class="description">' . esc_html__( 'Every site in the network will use the same settings. Site administrators will not be able to change them.', 'cookie-notice' ) . '</p>
		</div>';
	}

	/**
	 * Network cookie acceptance option.
	 *
	 * @return void
	 */
	public function cn_global_cookie() {
		$multi_folders = is_multisite() && ! is_subdomain_install();

		// multisite with path-based network?
		if ( $multi_folders )
			$desc = __( 'This option works only for domain-based networks.', 'cookie-notice' );
		else
			$desc = '';

		echo '
		<div id="cn_global_cookie">
			<label><input type="checkbox" name="cookie_notice_options[global_cookie]" value="1" ' . checked( true, Cookie_Notice()->options['general']['global_cookie'], false ) . ' ' . disabled( $multi_folders, true, false ) . ' />' . esc_html__( 'Enable global network cookie consent.', 'cookie-notice' ) . '</label>
			<p class="description">' . esc_html__( 'Cookie consent in one of the network sites results in a consent in all of the sites on the network.', 'cookie-notice' ) . ( $desc !== '' ? ' ' . esc_html( $desc ) : '' ) . '</p>
		</div>';
	}

	/**
	 * Network settings section.
	 *
	 * @return void
	 */
	public function cn_network_section() {
		echo '
		<p>' . esc_html__( 'Global network settings override is active. Every site will use the same network settings. Please contact super administrator if you want to have more control over the settings.', 'cookie-notice' ) . '</p>';
	}

	/**
	 * Compliance status.
	 *
	 * @return void
	 */
	public function cn_app_status() {
		// get main instance
		$cn = Cookie_Notice();

		// get cookie compliance status
		$app_status = $cn->get_status();

		// get threshold status
		$threshold_exceeded = $cn->threshold_exceeded();

		switch ( $app_status ) {
			case 'active':
				echo '
				<div id="cn_app_status">
					<div class="cn_compliance_status"><span class="cn-status-label">' . esc_html__( 'Notice', 'cookie-notice' ) . '</span>: <span class="cn-status cn-active"><span class="cn-icon"></span> ' . esc_html__( 'Active', 'cookie-notice' ) . '</span></div>
					<div class="cn_compliance_status"><span class="cn-status-label">' . esc_html__( 'Autoblocking', 'cookie-notice' ) . '</span>: <span class="cn-status ' . ( $threshold_exceeded ? 'cn-pending' : 'cn-active' ) . '"><span class="cn-icon"></span> ' . esc_html__( 'Active', 'cookie-notice' ) . '</span></div>
					<div class="cn_compliance_status"><span class="cn-status-label">' . esc_html__( 'Cookie Categories', 'cookie-notice' ) . '</span>: <span class="cn-status cn-active"><span class="cn-icon"></span> ' . esc_html__( 'Active', 'cookie-notice' ) . '</span></div>
					<div class="cn_compliance_status"><span class="cn-status-label">' . esc_html__( 'Proof-of-Consent', 'cookie-notice' ) . '</span>: <span class="cn-status cn-active"><span class="cn-icon"></span> ' . esc_html__( 'Active', 'cookie-notice' ) . '</span></div>
				</div>
				<div id="cn_app_actions">
					<a href="' . esc_url( $cn->get_url( 'host', '?utm_campaign=configure&utm_source=wordpress&utm_medium=button#/en/cc/login' ) ) . '" class="button button-primary button-hero cn-button" target="_blank">' . esc_html__( 'Log in & Configure', 'cookie-notice' ) . '</a>
					<p class="description">' . esc_html__( 'Log into the Cookie Compliance&trade; web application to configure the appearance and functionality of the banner.', 'cookie-notice' ) . '</p>
				</div>';
				break;

			case 'pending':
				echo '
				<div id="cn_app_status">
					<div class="cn_compliance_status"><span class="cn-status-label">' . esc_html__( 'Notice', 'cookie-notice' ) . '</span>: <span class="cn-status cn-active"><span class="cn-icon"></span> ' . esc_html__( 'Active', 'cookie-notice' ) . '</span></div>
					<div class="cn_compliance_status"><span class="cn-status-label">' . esc_html__( 'Autoblocking', 'cookie-notice' ) . '</span>: <span class="cn-status cn-pending"><span class="cn-icon"></span> ' . esc_html__( 'Pending', 'cookie-notice' ) . '</span></div>
					<div class="cn_compliance_status"><span class="cn-status-label">' . esc_html__( 'Cookie Categories', 'cookie-notice' ) . '</span>: <span class="cn-status cn-pending"><span class="cn-icon"></span> ' . esc_html__( 'Pending', 'cookie-notice' ) . '</span></div>
					<div class="cn_compliance_status"><span class="cn-status-label">' . esc_html__( 'Proof-of-Consent', 'cookie-notice' ) . '</span>: <span class="cn-status cn-pending"><span class="cn-icon"></span> ' . esc_html__( 'Pending', 'cookie-notice' ) . '</span></div>
				</div>
				<div id="cn_app_actions">
					<a href="' . esc_url( $cn->get_url( 'host', '?utm_campaign=configure&utm_source=wordpress&utm_medium=button#/en/cc/login' ) ) . '" class="button button-primary button-hero cn-button" target="_blank">' . esc_html__( 'Log in & configure', 'cookie-notice' ) . '</a>
					<p class="description">' . esc_html__( 'Log into the Cookie Compliance&trade; web application and complete the setup process.', 'cookie-notice' ) . '</p>
				</div>';
				break;

			default:
				if ( $cn->is_network_admin() )
					$url = network_admin_url( 'admin.php?page=cookie-notice' );
				else
					$url = admin_url( 'admin.php?page=cookie-notice' );

				echo '
				<div id="cn_app_status">
					<div class="cn_compliance_status"><span class="cn-status-label">' . esc_html__( 'Notice', 'cookie-notice' ) . '</span>: <span class="cn-status cn-active"><span class="cn-icon"></span> ' . esc_html__( 'Active', 'cookie-notice' ) . '</span></div>
					<div class="cn_compliance_status"><span class="cn-status-label">' . esc_html__( 'Autoblocking', 'cookie-notice' ) . '</span>: <span class="cn-status cn-inactive"><span class="cn-icon"></span> ' . esc_html__( 'Inactive', 'cookie-notice' ) . '</span></div>
					<div class="cn_compliance_status"><span class="cn-status-label">' . esc_html__( 'Cookie Categories', 'cookie-notice' ) . '</span>: <span class="cn-status cn-inactive"><span class="cn-icon"></span> ' . esc_html__( 'Inactive', 'cookie-notice' ) . '</span></div>
					<div class="cn_compliance_status"><span class="cn-status-label">' . esc_html__( 'Proof-of-Consent', 'cookie-notice' ) . '</span>: <span class="cn-status cn-inactive"><span class="cn-icon"></span> ' . esc_html__( 'Inactive', 'cookie-notice' ) . '</span></div>
				</div>
				<div id="cn_app_actions">
					<a href="' . esc_url( $url ) . '" class="button button-primary button-hero cn-button cn-run-welcome">' . esc_html__( 'Add Compliance features', 'cookie-notice' ) . '</a>
					<p class="description">' . sprintf( esc_html__( 'Sign up to %s and add GDPR, CCPA and other international data privacy laws compliance features.', 'cookie-notice' ), '<a href="https://cookie-compliance.co/?utm_campaign=sign-up&utm_source=wordpress&utm_medium=textlink" target="_blank">Cookie Compliance&trade;</a>' ) . '</p>
				</div>';
				break;
		}
	}

	/**
	 * App ID option.
	 *
	 * @return void
	 */
	public function cn_app_id() {
		echo '
		<div id="cn_app_id">
			<input type="text" class="regular-text" name="cookie_notice_options[app_id]" value="' . esc_attr( Cookie_Notice()->options['general']['app_id'] ) . '" />
			<p class="description">' . esc_html__( 'Enter your Cookie Compliance&trade; application ID.', 'cookie-notice' ) . '</p>
		</div>';
	}

	/**
	 * App key option.
	 *
	 * @return void
	 */
	public function cn_app_key() {
		echo '
		<div id="cn_app_key">
			<input type="password" class="regular-text" name="cookie_notice_options[app_key]" value="' . esc_attr( Cookie_Notice()->options['general']['app_key'] ) . '" />
			<p class="description">' . esc_html__( 'Enter your Cookie Compliance&trade; application secret key.', 'cookie-notice' ) . '</p>
		</div>';
	}

	/**
	 * App autoblocking option.
	 *
	 * @return void
	 */
	public function cn_app_blocking() {
		// get main instance
		$cn = Cookie_Notice();

		$threshold_exceeded = $cn->threshold_exceeded();

		echo '
		<div id="cn_app_blocking"' . ( $threshold_exceeded ? ' class="cn-option-disabled"' : '' ) . '>
			<label><input type="checkbox" name="cookie_notice_options[app_blocking]" value="1" ' . checked( true, $cn->options['general']['app_blocking'], false ) . ' ' . disabled( $threshold_exceeded, true, false ) . ' />' . esc_html__( 'Enable to automatically block 3rd party scripts before user consent is set.', 'cookie-notice' ) . '</label>' .
			( $threshold_exceeded ? '<p class="description"><span class="cn-warning">*</span> ' . esc_html__( 'This option has been temporarily disabled because your website has reached the usage limit for the Cookie Compliance Basic plan. It will become available again when the current visits cycle resets or you upgrade your website to a Professional plan.', 'cookie-notice' ) . '</p>' : '' ) .
		'</div>';
	}

	/**
	 * Purge cache option.
	 *
	 * @return void
	 */
	public function cn_app_purge_cache() {
		echo '
		<div id="cn_app_purge_cache">
			<div class="cn-button-container">
				<a href="#" class="button button-secondary">' . esc_html__( 'Purge Cache', 'cookie-notice' ) . '</a>
			</div>
			<p class="description">' . esc_html__( 'Click the Purge Cache button to refresh the app configuration.', 'cookie-notice' ) . '</p>
		</div>';
	}

	/**
	 * Conditional display option.
	 *
	 * @return void
	 */
	public function cn_conditional_display() {
		// get main instance
		$cn = Cookie_Notice();

		echo '
		<fieldset id="cn_conditional_display">
			<label><input id="cn_conditional_display_opt" type="checkbox" name="cookie_notice_options[conditional_active]" value="1" ' . checked( true, $cn->options['general']['conditional_active'], false ) . ' />' . esc_html__( 'Enable conditional display of the banner.', 'cookie-notice' ) . '</label>
			<div id="cn_conditional_display_opt_container"' . ( $cn->options['general']['conditional_active'] === false ? ' style="display: none"' : '' ) . ' class="cn_fieldset_content">
				<div>
					<select name="cookie_notice_options[conditional_display]">';

			foreach ( $this->conditional_display_types as $type => $label ) {
				echo '
						<option value="' . esc_attr( $type ) . '" ' . selected( $type, $cn->options['general']['conditional_display'] ) . '>' . esc_html( $label ) . '</option>';
			}

			echo '
					</select>
					<p class="description">' . esc_html__( 'Determine what should happen when the following conditions are met.', 'cookie-notice' ) . '</p>
				</div>';

		// get allowed html
		$allowed_html = wp_kses_allowed_html( 'post' );
		$allowed_html['select'] = [
			'name'	=> true,
			'class'	=> true
		];
		$allowed_html['option'] = [
			'value'		=> true,
			'selected'	=> true
		];

		add_filter( 'safe_style_css', [ $this, 'allow_style_attributes' ] );

		echo wp_kses( $this->conditional_display( $cn->options['general']['conditional_rules'] ), $allowed_html );

		remove_filter( 'safe_style_css', [ $this, 'allow_style_attributes' ] );

		echo '
			</div>
		</fieldset>';
	}

	/**
	 * Debug mode option.
	 *
	 * @return void
	 */
	public function cn_debug_mode() {
		echo '
		<div id="cn_debug_mode">
			<label><input type="checkbox" name="cookie_notice_options[debug_mode]" value="1" ' . checked( true, Cookie_Notice()->options['general']['debug_mode'], false ) . ' />' . esc_html__( 'Enable to run the consent banner in debug mode.', 'cookie-notice' ) . '</label>
		</div>';
	}

	/**
	 * AMP support option.
	 *
	 * @return void
	 */
	public function cn_amp_support() {
		$amp_enabled = cn_is_plugin_active( 'amp' );

		echo '
		<div id="cn_amp_support">
			<label><input type="checkbox" name="cookie_notice_options[amp_support]" value="1" ' . checked( true, Cookie_Notice()->options['general']['amp_support'] && $amp_enabled, false ) . ' ' . disabled( ! $amp_enabled, true, false ) . ' />' . esc_html__( 'Enable to support AMP.', 'cookie-notice' ) . '</label>
			<p class="description">' . ( ! $amp_enabled ? esc_html__( 'No compatible Google AMP plugins found.', 'cookie-notice' ) : esc_html__( 'Allows you to activate consent banner support for Google AMP.', 'cookie-notice' ) ) . '</p>
		</div>';
	}

	/**
	 * Bot detection option.
	 *
	 * @return void
	 */
	public function cn_bot_detection() {
		echo '
		<div id="cn_bot_detection">
			<label><input type="checkbox" name="cookie_notice_options[bot_detection]" value="1" ' . checked( true, Cookie_Notice()->options['general']['bot_detection'], false ) . ' />' . esc_html__( 'Enable to activate bot detection and reduce the number of calculated website visits.', 'cookie-notice' ) . '</label>
		</div>';
	}

	/**
	 * Caching compatibility option.
	 *
	 * @return void
	 */
	public function cn_caching_compatibility() {
		// get main instance
		$cn = Cookie_Notice();

		$plugins_html = '';

		// get active caching plugins
		$active_plugins = cn_get_active_caching_plugins();

		if ( ! empty( $active_plugins ) ) {
			$active_plugins_html = [];

			$plugins_html .= '<p class="description">' . esc_html__( 'Currently detected active caching plugins', 'cookie-notice' ) . ': ';

			foreach ( $active_plugins as $plugin ) {
				$active_plugins_html[] = '<code>' . esc_html( $plugin ) . '</code>';
			}

			$plugins_html .= implode( ', ', $active_plugins_html ) . '.</p>';
		} else
			$plugins_html .= '<p class="description">' . esc_html__( 'No compatible cache plugins found.', 'cookie-notice' ) . '</p>';

		echo '
		<div id="cn_caching_compatibility">
			<label><input type="checkbox" name="cookie_notice_options[caching_compatibility]" value="1" ' . checked( true, $cn->options['general']['caching_compatibility'] && ! empty( $active_plugins ), false ) . ' ' . disabled( empty( $active_plugins ), true, false ) . ' />' . esc_html__( 'Enable to apply changes improving compatibility with caching plugins.', 'cookie-notice' ) . '</label>' . $plugins_html . '
		</div>';
	}

	/**
	 * Cookie notice message option.
	 *
	 * @return void
	 */
	public function cn_message_text() {
		echo '
		<div id="cn_message_text">
			<textarea name="cookie_notice_options[message_text]" class="large-text" cols="50" rows="5">' . esc_textarea( Cookie_Notice()->options['general']['message_text'] ) . '</textarea>
			<p class="description">' . esc_html__( 'Enter the cookie notice message.', 'cookie-notice' ) . '</p>
		</div>';
	}

	/**
	 * Accept cookie label option.
	 *
	 * @return void
	 */
	public function cn_accept_text() {
		echo '
		<div id="cn_accept_text">
			<input type="text" class="regular-text" name="cookie_notice_options[accept_text]" value="' . esc_attr( Cookie_Notice()->options['general']['accept_text'] ) . '" />
			<p class="description">' . esc_html__( 'The text of the option to accept the notice and make it disappear.', 'cookie-notice' ) . '</p>
		</div>';
	}

	/**
	 * Toggle third party non functional cookies option.
	 *
	 * @return void
	 */
	public function cn_refuse_opt() {
		// get main instance
		$cn = Cookie_Notice();

		echo '
		<fieldset>
			<label><input id="cn_refuse_opt" type="checkbox" name="cookie_notice_options[refuse_opt]" value="1" ' . checked( true, $cn->options['general']['refuse_opt'], false ) . ' />' . esc_html__( 'Enable to give to the user the possibility to refuse third party non functional cookies.', 'cookie-notice' ) . '</label>
			<div id="cn_refuse_opt_container"' . ( $cn->options['general']['refuse_opt'] === false ? ' style="display: none"' : '' ) . ' class="cn_fieldset_content">
				<div id="cn_refuse_text">
					<input type="text" class="regular-text" name="cookie_notice_options[refuse_text]" value="' . esc_attr( $cn->options['general']['refuse_text'] ) . '" />
					<p class="description">' . esc_html__( 'The text of the button to refuse the consent.', 'cookie-notice' ) . '</p>
				</div>
			</div>
		</fieldset>';
	}

	/**
	 * Non functional cookies code option.
	 *
	 * @return void
	 */
	public function cn_refuse_code() {
		// get main instance
		$cn = Cookie_Notice();

		$active = ! empty( $cn->options['general']['refuse_code'] ) && empty( $cn->options['general']['refuse_code_head'] ) ? 'body' : 'head';

		echo '
		<div id="cn_refuse_code">
			<div id="cn_refuse_code_fields">
				<h2 class="nav-tab-wrapper">
					<a id="refuse_head-tab" class="nav-tab' . ( $active === 'head' ? ' nav-tab-active' : '' ) . '" href="#refuse_head">' . esc_html__( 'Head', 'cookie-notice' ) . '</a>
					<a id="refuse_body-tab" class="nav-tab' . ( $active === 'body' ? ' nav-tab-active' : '' ) . '" href="#refuse_body">' . esc_html__( 'Body', 'cookie-notice' ) . '</a>
				</h2>
				<div id="refuse_head" class="refuse-code-tab' . ( $active === 'head' ? ' active' : '' ) . '">
					<p class="description">' . esc_html__( 'The code to be used in your site header, before the closing head tag.', 'cookie-notice' ) . '</p>
					<textarea name="cookie_notice_options[refuse_code_head]" class="large-text" cols="50" rows="8">' . html_entity_decode( trim( wp_kses( $cn->options['general']['refuse_code_head'], $cn->get_allowed_html( 'head' ) ) ) ) . '</textarea>
				</div>
				<div id="refuse_body" class="refuse-code-tab' . ( $active === 'body' ? ' active' : '' ) . '">
					<p class="description">' . esc_html__( 'The code to be used in your site footer, before the closing body tag.', 'cookie-notice' ) . '</p>
					<textarea name="cookie_notice_options[refuse_code]" class="large-text" cols="50" rows="8">' . html_entity_decode( trim( wp_kses( $cn->options['general']['refuse_code'], $cn->get_allowed_html( 'body' ) ) ) ) . '</textarea>
				</div>
			</div>
			<p class="description">' . esc_html__( 'Enter non functional cookies Javascript code here (for e.g. Google Analitycs) to be used after the visitor consent is given.', 'cookie-notice' ) . '</p>
		</div>';
	}

	/**
	 * Revoke cookies option.
	 *
	 * @return void
	 */
	public function cn_revoke_opt() {
		// get main instance
		$cn = Cookie_Notice();

		echo '
		<fieldset id="cn_revoke_opt">
			<label><input id="cn_revoke_cookies" type="checkbox" name="cookie_notice_options[revoke_cookies]" value="1" ' . checked( true, $cn->options['general']['revoke_cookies'], false ) . ' />' . sprintf( esc_html__( 'Enable to give to the user the possibility to revoke their consent %s(requires "Refuse consent" option enabled)%s.', 'cookie-notice' ), '<i>', '</i>' ) . '</label>
			<div id="cn_revoke_opt_container"' . ( $cn->options['general']['revoke_cookies'] ? '' : ' style="display: none"' ) . ' class="cn_fieldset_content">
				<textarea name="cookie_notice_options[revoke_message_text]" class="large-text" cols="50" rows="2">' . esc_textarea( $cn->options['general']['revoke_message_text'] ) . '</textarea>
				<p class="description">' . esc_html__( 'Enter the revoke message.', 'cookie-notice' ) . '</p>
				<input type="text" class="regular-text" name="cookie_notice_options[revoke_text]" value="' . esc_attr( $cn->options['general']['revoke_text'] ) . '" />
				<p class="description">' . esc_html__( 'The text of the button to revoke the consent.', 'cookie-notice' ) . '</p>';

		foreach ( $this->revoke_opts as $value => $label ) {
			echo '
				<label><input id="cn_revoke_cookies-' . esc_attr( $value ) . '" type="radio" name="cookie_notice_options[revoke_cookies_opt]" value="' . esc_attr( $value ) . '" ' . checked( $value, $cn->options['general']['revoke_cookies_opt'], false ) . ' />' . esc_html( $label ) . '</label>';
		}

		echo '
				<p class="description">' . sprintf( esc_html__( 'Select the method for displaying the revoke button - automatic (in the banner) or manual using %s[cookies_revoke]%s shortcode.', 'cookie-notice' ), '<code>', '</code>' ) . '</p>
			</div>
		</fieldset>';
	}

	/**
	 * Redirection on cookie accept option.
	 *
	 * @return void
	 */
	public function cn_redirection() {
		echo '
		<div id="cn_redirection">
			<label><input type="checkbox" name="cookie_notice_options[redirection]" value="1" ' . checked( true, Cookie_Notice()->options['general']['redirection'], false ) . ' />' . esc_html__( 'Enable to reload the page after the notice is accepted.', 'cookie-notice' ) . '</label>
		</div>';
	}

	/**
	 * Privacy policy link option.
	 *
	 * @global string $wp_version
	 *
	 * @return void
	 */
	public function cn_see_more() {
		// get main instance
		$cn = Cookie_Notice();

		// get published pages
		$pages = get_pages(
			[
				'sort_order'	=> 'ASC',
				'sort_column'	=> 'post_title',
				'hierarchical'	=> 0,
				'child_of'		=> 0,
				'parent'		=> -1,
				'offset'		=> 0,
				'post_type'		=> 'page',
				'post_status'	=> 'publish'
			]
		);

		echo '
		<fieldset>
			<label><input id="cn_see_more" type="checkbox" name="cookie_notice_options[see_more]" value="1" ' . checked( true, $cn->options['general']['see_more'], false ) . ' />' . esc_html__( 'Enable privacy policy link.', 'cookie-notice' ) . '</label>
			<div id="cn_see_more_opt"' . ( $cn->options['general']['see_more'] === false ? ' style="display: none"' : '' ) . ' class="cn_fieldset_content">
				<input type="text" class="regular-text" name="cookie_notice_options[see_more_opt][text]" value="' . esc_attr( $cn->options['general']['see_more_opt']['text'] ) . '" />
				<p class="description">' . esc_html__( 'The text of the privacy policy button.', 'cookie-notice' ) . '</p>
				<div id="cn_see_more_opt_custom_link">';

		foreach ( $this->links as $value => $label ) {
			echo '
					<label><input id="cn_see_more_link-' . esc_attr( $value ) . '" type="radio" name="cookie_notice_options[see_more_opt][link_type]" value="' . esc_attr( $value ) . '" ' . checked( $value, $cn->options['general']['see_more_opt']['link_type'], false ) . ' />' . esc_html( $label ) . '</label>';
		}

		echo '
				</div>
				<p class="description">' . esc_html__( 'Select where to redirect user for more information.', 'cookie-notice' ) . '</p>
				<div id="cn_see_more_opt_page"' . ( $cn->options['general']['see_more_opt']['link_type'] === 'custom' ? ' style="display: none"' : '' ) . '>
					<select name="cookie_notice_options[see_more_opt][id]">
						<option value="0" ' . selected( 0, $cn->options['general']['see_more_opt']['id'], false ) . '>' . esc_html__( '-- select page --', 'cookie-notice' ) . '</option>';

		if ( $pages ) {
			foreach ( $pages as $page ) {
				echo '
						<option value="' . esc_attr( $page->ID ) . '" ' . selected( $page->ID, $cn->options['general']['see_more_opt']['id'], false ) . '>' . esc_html( $page->post_title ) . '</option>';
			}
		}

		echo '
					</select>
					<p class="description">' . esc_html__( 'Select from one of your site\'s pages.', 'cookie-notice' ) . '</p>';

		global $wp_version;

		if ( version_compare( $wp_version, '4.9.6', '>=' ) ) {
			echo '
						<label><input id="cn_see_more_opt_sync" type="checkbox" name="cookie_notice_options[see_more_opt][sync]" value="1" ' . checked( true, $cn->options['general']['see_more_opt']['sync'], false ) . ' />' . esc_html__( 'Synchronize with WordPress Privacy Policy page.', 'cookie-notice' ) . '</label>';
		}

		echo '
				</div>
				<div id="cn_see_more_opt_link"' . ( $cn->options['general']['see_more_opt']['link_type'] === 'page' ? ' style="display: none"' : '' ) . '>
					<input type="text" class="regular-text" name="cookie_notice_options[see_more_opt][link]" value="' . esc_attr( $cn->options['general']['see_more_opt']['link'] ) . '" />
					<p class="description">' . esc_html__( 'Enter the full URL starting with http(s)://', 'cookie-notice' ) . '</p>
				</div>
				<div id="cn_see_more_link_target">';

		foreach ( $this->link_targets as $target ) {
			echo '
					<label><input id="cn_see_more_link_target-' . esc_attr( $target ) . '" type="radio" name="cookie_notice_options[link_target]" value="' . esc_attr( $target ) . '" ' . checked( $target, $cn->options['general']['link_target'], false ) . ' />' . esc_html( $target ) . '</label>';
		}

		echo '
					<p class="description">' . esc_html__( 'Select the privacy policy link target.', 'cookie-notice' ) . '</p>
				</div>
				<div id="cn_see_more_link_position">';

		foreach ( $this->link_positions as $position => $label ) {
			echo '
					<label><input id="cn_see_more_link_position-' . esc_attr( $position ) . '" type="radio" name="cookie_notice_options[link_position]" value="' . esc_attr( $position ) . '" ' . checked( $position, $cn->options['general']['link_position'], false ) . ' />' . esc_html( $label ) . '</label>';
		}

		echo '
					<p class="description">' . esc_html__( 'Select the privacy policy link position.', 'cookie-notice' ) . '</p>
				</div>
			</div>
		</fieldset>';
	}

	/**
	 * Expiration time option.
	 *
	 * @return void
	 */
	public function cn_time() {
		echo '
		<div id="cn_time">
			<select name="cookie_notice_options[time]">';

		foreach ( $this->times as $time => $arr ) {
			echo '
				<option value="' . esc_attr( $time ) . '" ' . selected( $time, Cookie_Notice()->options['general']['time'] ) . '>' . esc_html( $arr[0] ) . '</option>';
		}

		echo '
			</select>
			<p class="description">' . esc_html__( 'The amount of time that the cookie should be stored for when user accepts the notice.', 'cookie-notice' ) . '</p>
		</div>';
	}

	/**
	 * Expiration time option.
	 *
	 * @return void
	 */
	public function cn_time_rejected() {
		echo '
		<div id="cn_time_rejected">
			<select name="cookie_notice_options[time_rejected]">';

		foreach ( $this->times as $time => $arr ) {
			echo '
				<option value="' . esc_attr( $time ) . '" ' . selected( $time, Cookie_Notice()->options['general']['time_rejected'] ) . '>' . esc_html( $arr[0] ) . '</option>';
		}

		echo '
			</select>
			<p class="description">' . esc_html__( 'The amount of time that the cookie should be stored for when the user doesn\'t accept the notice.', 'cookie-notice' ) . '</p>
		</div>';
	}

	/**
	 * Script placement option.
	 *
	 * @return void
	 */
	public function cn_script_placement() {
		echo '
		<div id="cn_script_placement">';

		foreach ( $this->script_placements as $value => $label ) {
			echo '
			<label><input id="cn_script_placement-' . esc_attr( $value ) . '" type="radio" name="cookie_notice_options[script_placement]" value="' . esc_attr( $value ) . '" ' . checked( $value, Cookie_Notice()->options['general']['script_placement'], false ) . ' />' . esc_html( $label ) . '</label>';
		}

		echo '
			<p class="description">' . esc_html__( 'Select where all the plugin scripts should be placed.', 'cookie-notice' ) . '</p>
		</div>';
	}

	/**
	 * Position option.
	 *
	 * @return void
	 */
	public function cn_position() {
		echo '
		<div id="cn_position">';

		foreach ( $this->positions as $value => $label ) {
			echo '
			<label><input id="cn_position-' . esc_attr( $value ) . '" type="radio" name="cookie_notice_options[position]" value="' . esc_attr( $value ) . '" ' . checked( $value, Cookie_Notice()->options['general']['position'], false ) . ' />' . esc_html( $label ) . '</label>';
		}

		echo '
			<p class="description">' . esc_html__( 'Select location for the notice.', 'cookie-notice' ) . '</p>
		</div>';
	}

	/**
	 * Animation effect option.
	 *
	 * @return void
	 */
	public function cn_hide_effect() {
		echo '
		<div id="cn_hide_effect">';

		foreach ( $this->effects as $value => $label ) {
			echo '
			<label><input id="cn_hide_effect-' . esc_attr( $value ) . '" type="radio" name="cookie_notice_options[hide_effect]" value="' . esc_attr( $value ) . '" ' . checked( $value, Cookie_Notice()->options['general']['hide_effect'], false ) . ' />' . esc_html( $label ) . '</label>';
		}

		echo '
			<p class="description">' . esc_html__( 'Select the animation style.', 'cookie-notice' ) . '</p>
		</div>';
	}

	/**
	 * On scroll option.
	 *
	 * @return void
	 */
	public function cn_on_scroll() {
		// get main instance
		$cn = Cookie_Notice();

		echo '
		<fieldset>
			<label><input id="cn_on_scroll" type="checkbox" name="cookie_notice_options[on_scroll]" value="1" ' . checked( true, $cn->options['general']['on_scroll'], false ) . ' />' . esc_html__( 'Enable to accept the notice when user scrolls.', 'cookie-notice' ) . '</label>
			<div id="cn_on_scroll_offset"' . ( $cn->options['general']['on_scroll'] === false || $cn->options['general']['on_scroll'] == false ? ' style="display: none"' : '' ) . ' class="cn_fieldset_content">
				<input type="number" min="0" class="small-text" name="cookie_notice_options[on_scroll_offset]" value="' . esc_attr( $cn->options['general']['on_scroll_offset'] ) . '" /> <span>px</span>
				<p class="description">' . esc_html__( 'Number of pixels user has to scroll to accept the notice and make it disappear.', 'cookie-notice' ) . '</p>
			</div>
		</fieldset>';
	}

	/**
	 * On click option.
	 *
	 * @return void
	 */
	public function cn_on_click() {
		echo '
		<div id="cn_on_click">
			<label><input type="checkbox" name="cookie_notice_options[on_click]" value="1" ' . checked( true, Cookie_Notice()->options['general']['on_click'], false ) . ' />' . esc_html__( 'Enable to accept the notice on any click on the page.', 'cookie-notice' ) . '</label>
		</div>';
	}

	/**
	 * Delete plugin data on deactivation option.
	 *
	 * @return void
	 */
	public function cn_deactivation_delete() {
		echo '
		<div id="cn_deactivation_delete">
			<label><input type="checkbox" name="cookie_notice_options[deactivation_delete]" value="1" ' . checked( true, Cookie_Notice()->options['general']['deactivation_delete'], false ) . '/>' . esc_html__( 'Enable if you want all plugin data to be deleted on deactivation.', 'cookie-notice' ) . '</label>
		</div>';
	}

	/**
	 * CSS style option.
	 *
	 * @return void
	 */
	public function cn_css_class() {
		echo '
		<div id="cn_css_class">
			<input type="text" class="regular-text" name="cookie_notice_options[css_class]" value="' . esc_attr( Cookie_Notice()->options['general']['css_class'] ) . '" />
			<p class="description">' . esc_html__( 'Enter additional button CSS classes separated by spaces.', 'cookie-notice' ) . '</p>
		</div>';
	}

	/**
	 * Colors option.
	 *
	 * @return void
	 */
	public function cn_colors() {
		// get main instance
		$cn = Cookie_Notice();

		echo '
		<fieldset>
			<div id="cn_colors">';

		foreach ( $this->colors as $value => $label ) {
			echo '
				<div id="cn_colors-' . esc_attr( $value ) . '"><label>' . esc_html( $label ) . '</label><br />
					<input class="cn_color" type="text" name="cookie_notice_options[colors][' . esc_attr( $value ) . ']" value="' . esc_attr( $cn->options['general']['colors'][$value] ) . '" />
				</div>';
		}

		echo '
				<div id="cn_colors-bar_opacity"><label>' . esc_html__( 'Bar opacity', 'cookie-notice' ) . '</label><br />
					<div><input id="cn_colors_bar_opacity_range" class="cn_range" type="range" min="50" max="100" step="1" name="cookie_notice_options[colors][bar_opacity]" value="' . (int) $cn->options['general']['colors']['bar_opacity'] . '" onchange="cn_colors_bar_opacity_text.value = cn_colors_bar_opacity_range.value" /><input id="cn_colors_bar_opacity_text" class="small-text" type="number" onchange="cn_colors_bar_opacity_range.value = cn_colors_bar_opacity_text.value" min="50" max="100" value="' . (int) $cn->options['general']['colors']['bar_opacity'] . '" /></div>
				</div>';

		echo '
			</div>
		</fieldset>';
	}

	/**
	 * Validate options.
	 *
	 * @param array $input
	 * @return array
	 */
	public function validate_options( $input ) {
		if ( ! current_user_can( apply_filters( 'cn_manage_cookie_notice_cap', 'manage_options' ) ) )
			return $input;

		// get main instance
		$cn = Cookie_Notice();

		$is_network = $cn->is_network_admin();

		if ( isset( $_POST['save_cookie_notice_options'] ) ) {
			// app id
			$input['app_id'] = isset( $input['app_id'] ) ? sanitize_key( $input['app_id'] ) : $cn->defaults['general']['app_id'];

			// app key
			$input['app_key'] = isset( $input['app_key'] ) ? sanitize_key( $input['app_key'] ) : $cn->defaults['general']['app_key'];

			// set app status
			if ( ! empty( $input['app_id'] ) && ! empty( $input['app_key'] ) ) {
				$app_data = $cn->welcome_api->get_app_config( $input['app_id'], true, false );

				if ( $cn->check_status( $app_data['status'] ) === 'active' && $cn->options['general']['app_id'] !== $input['app_id'] ) {
					// get_app_analytics requires fresh app data
					$this->analytics_app_data = [
						'id'	=> $input['app_id'],
						'key'	=> $input['app_key']
					];

					// update analytics data
					$cn->welcome_api->get_app_analytics( $input['app_id'], true, false );

					$this->analytics_app_data = [];
				}
			} else {
				if ( $is_network )
					update_site_option( 'cookie_notice_status', $cn->defaults['data'] );
				else
					update_option( 'cookie_notice_status', $cn->defaults['data'] );
			}

			// app blocking
			$input['app_blocking'] = isset( $input['app_blocking'] ) && ! $cn->threshold_exceeded();

			// conditional display
			$input['conditional_active'] = isset( $input['conditional_active'] );

			$input['conditional_display'] = isset( $input['conditional_display'] ) ? sanitize_key( $input['conditional_display'] ) : $cn->defaults['general']['conditional_display'];

			if ( ! in_array( $input['conditional_display'], array_keys( $this->conditional_display_types ), true ) )
				$input['conditional_display'] = $cn->defaults['general']['conditional_display'];

			if ( ! empty( $input['conditional_rules'] ) && is_array( $input['conditional_rules'] ) ) {
				$group_id = $rule_id = 1;
				$rules = [];

				foreach ( $input['conditional_rules'] as $group_number => $group ) {
					// skips template data or empty groups
					if ( (int) $group_number <= 0 || empty( $group ) )
						continue;

					foreach ( $group as $rule ) {
						$param = sanitize_key( $rule['param'] );
						$operator = sanitize_key( $rule['operator'] );
						$value = sanitize_key( $rule['value'] );

						if ( $this->check_rule( $param, $operator, $value ) ) {
							$rules[$group_id][$rule_id++] = [
								'param'		=> $param,
								'operator'	=> $operator,
								'value'		=> $value
							];
						}
					}

					$rule_id = 1;
					$group_id++;
				}

				$input['conditional_rules'] = $rules;
			} else
				$input['conditional_rules'] = [];

			// bot detection
			$input['bot_detection'] = isset( $input['bot_detection'] );

			// debug mode
			$input['debug_mode'] = isset( $input['debug_mode'] );

			// amp support
			$input['amp_support'] = isset( $input['amp_support'] ) && cn_is_plugin_active( 'amp' );

			// get active caching plugins
			$active_plugins = cn_get_active_caching_plugins();

			// caching compatibility
			$input['caching_compatibility'] = isset( $input['caching_compatibility'] ) && ! empty( $active_plugins );

			// position
			if ( isset( $input['position'] ) ) {
				$input['position'] = sanitize_key( $input['position'] );

				if ( ! array_key_exists( $input['position'], $this->positions ) )
					$input['position'] = $cn->defaults['general']['position'];
			} else
				$input['position'] = $cn->defaults['general']['position'];

			// text color
			if ( isset( $input['colors']['text'] ) ) {
				$input['colors']['text'] = sanitize_hex_color( $input['colors']['text'] );

				if ( empty( $input['colors']['text'] ) )
					$input['colors']['text'] = $cn->defaults['general']['colors']['text'];
			} else
				$input['colors']['text'] = $cn->defaults['general']['colors']['text'];

			// button color
			if ( isset( $input['colors']['button'] ) ) {
				$input['colors']['button'] = sanitize_hex_color( $input['colors']['button'] );

				if ( empty( $input['colors']['button'] ) )
					$input['colors']['button'] = $cn->defaults['general']['colors']['button'];
			} else
				$input['colors']['button'] = $cn->defaults['general']['colors']['button'];

			// bar color
			if ( isset( $input['colors']['bar'] ) ) {
				$input['colors']['bar'] = sanitize_hex_color( $input['colors']['bar'] );

				if ( empty( $input['colors']['bar'] ) )
					$input['colors']['bar'] = $cn->defaults['general']['colors']['bar'];
			} else
				$input['colors']['bar'] = $cn->defaults['general']['colors']['bar'];

			// bar opacity
			$input['colors']['bar_opacity'] = isset( $input['colors']['bar_opacity'] ) ? (int) $input['colors']['bar_opacity'] : $cn->defaults['general']['colors']['bar_opacity'];

			if ( $input['colors']['bar_opacity'] < 50 || $input['colors']['bar_opacity'] > 100 )
				$input['colors']['bar_opacity'] = $cn->defaults['general']['colors']['bar_opacity'];

			// message text
			if ( isset( $input['message_text'] ) ) {
				add_filter( 'safe_style_css', [ $this, 'allow_style_attributes' ] );

				$input['message_text'] = wp_kses_post( trim( $input['message_text'] ) );

				remove_filter( 'safe_style_css', [ $this, 'allow_style_attributes' ] );

				if ( $input['message_text'] === '' )
					$input['message_text'] = $cn->defaults['general']['message_text'];
			} else
				$input['message_text'] = $cn->defaults['general']['message_text'];

			// accept button text
			if ( isset( $input['accept_text'] ) ) {
				$input['accept_text'] = sanitize_text_field( $input['accept_text'] );

				if ( $input['accept_text'] === '' )
					$input['accept_text'] = $cn->defaults['general']['accept_text'];
			} else
				$input['accept_text'] = $cn->defaults['general']['accept_text'];

			// refuse button text
			if ( isset( $input['refuse_text'] ) ) {
				$input['refuse_text'] = sanitize_text_field( $input['refuse_text'] );

				if ( $input['refuse_text'] === '' )
					$input['refuse_text'] = $cn->defaults['general']['refuse_text'];
			} else
				$input['refuse_text'] = $cn->defaults['general']['refuse_text'];

			// revoke message text
			if ( isset( $input['revoke_message_text'] ) ) {
				add_filter( 'safe_style_css', [ $this, 'allow_style_attributes' ] );

				$input['revoke_message_text'] = wp_kses_post( trim( $input['revoke_message_text'] ) );

				remove_filter( 'safe_style_css', [ $this, 'allow_style_attributes' ] );

				if ( $input['revoke_message_text'] === '' )
					$input['revoke_message_text'] = $cn->defaults['general']['revoke_message_text'];
			} else
				$input['revoke_message_text'] = $cn->defaults['general']['revoke_message_text'];

			// revoke button text
			if ( isset( $input['revoke_text'] ) ) {
				$input['revoke_text'] = sanitize_text_field( $input['revoke_text'] );

				if ( $input['revoke_text'] === '' )
					$input['revoke_text'] = $cn->defaults['general']['revoke_text'];
			} else
				$input['revoke_text'] = $cn->defaults['general']['revoke_text'];

			// refuse consent
			$input['refuse_opt'] = isset( $input['refuse_opt'] );

			// revoke consent
			$input['revoke_cookies'] = isset( $input['revoke_cookies'] );

			// revoke consent type
			if ( isset( $input['revoke_cookies_opt'] ) ) {
				$input['revoke_cookies_opt'] = sanitize_key( $input['revoke_cookies_opt'] );

				if ( ! array_key_exists( $input['revoke_cookies_opt'], $this->revoke_opts ) )
					$input['revoke_cookies_opt'] = $cn->defaults['general']['revoke_cookies_opt'];
			} else
				$input['revoke_cookies_opt'] = $cn->defaults['general']['revoke_cookies_opt'];

			// body refuse code
			if ( isset( $input['refuse_code'] ) )
				$input['refuse_code'] = wp_kses( trim( $input['refuse_code'] ), $cn->get_allowed_html( 'body' ) );
			else
				$input['refuse_code'] = $cn->defaults['general']['refuse_code'];

			// head refuse code
			if ( isset( $input['refuse_code_head'] ) )
				$input['refuse_code_head'] = wp_kses( trim( $input['refuse_code_head'] ), $cn->get_allowed_html( 'head' ) );
			else
				$input['refuse_code_head'] = $cn->defaults['general']['refuse_code_head'];

			// css button class(es)
			if ( isset( $input['css_class'] ) ) {
				$input['css_class'] = trim( $input['css_class'] );

				if ( $input['css_class'] !== '' ) {
					// more than 1 class?
					if ( strpos( $input['css_class'], ' ' ) !== false ) {
						// get unique valid html classes
						$input['css_class'] = array_unique( array_filter( array_map( 'sanitize_html_class', explode( ' ', $input['css_class'] ) ) ) );

						if ( ! empty( $input['css_class'] ) )
							$input['css_class'] = implode( ' ', $input['css_class'] );
						else
							$input['css_class'] = $cn->defaults['general']['css_class'];
					// single class
					} else
						$input['css_class'] = sanitize_html_class( $input['css_class'] );
				}
			} else
				$input['css_class'] = $cn->defaults['general']['css_class'];

			// accepted expiry
			if ( isset( $input['time'] ) ) {
				$input['time'] = sanitize_key( $input['time'] );

				if ( ! array_key_exists( $input['time'], $this->times ) )
					$input['time'] = $cn->defaults['general']['time'];
			} else
				$input['time'] = $cn->defaults['general']['time'];

			// rejected expiry
			if ( isset( $input['time_rejected'] ) ) {
				$input['time_rejected'] = sanitize_key( $input['time_rejected'] );

				if ( ! array_key_exists( $input['time_rejected'], $this->times ) )
					$input['time_rejected'] = $cn->defaults['general']['time_rejected'];
			} else
				$input['time_rejected'] = $cn->defaults['general']['time_rejected'];

			// script placement
			if ( isset( $input['script_placement'] ) ) {
				$input['script_placement'] = sanitize_key( $input['script_placement'] );

				if ( ! array_key_exists( $input['script_placement'], $this->script_placements ) )
					$input['script_placement'] = $cn->defaults['general']['script_placement'];
			} else
				$input['script_placement'] = $cn->defaults['general']['script_placement'];

			// hide effect
			if ( isset( $input['hide_effect'] ) ) {
				$input['hide_effect'] = sanitize_key( $input['hide_effect'] );

				if ( ! array_key_exists( $input['hide_effect'], $this->effects ) )
					$input['hide_effect'] = $cn->defaults['general']['hide_effect'];
			} else
				$input['hide_effect'] = $cn->defaults['general']['hide_effect'];

			// reloading
			$input['redirection'] = isset( $input['redirection'] );

			// on scroll
			$input['on_scroll'] = isset( $input['on_scroll'] );

			// on scroll offset
			$input['on_scroll_offset'] = isset( $input['on_scroll_offset'] ) ? (int) $input['on_scroll_offset'] : $cn->defaults['general']['on_scroll_offset'];

			if ( $input['on_scroll_offset'] < 0 )
				$input['on_scroll_offset'] = 0;

			// on click
			$input['on_click'] = isset( $input['on_click'] );

			// deactivation
			$input['deactivation_delete'] = isset( $input['deactivation_delete'] );

			// privacy policy
			$input['see_more'] = isset( $input['see_more'] );

			// privacy policy link text
			if ( isset( $input['see_more_opt']['text'] ) ) {
				$input['see_more_opt']['text'] = sanitize_text_field( $input['see_more_opt']['text'] );

				if ( $input['see_more_opt']['text'] === '' )
					$input['see_more_opt']['text'] = $cn->defaults['general']['see_more_opt']['text'];
			} else
				$input['see_more_opt']['text'] = $cn->defaults['general']['see_more_opt']['text'];

			// privacy policy link type
			if ( isset( $input['see_more_opt']['link_type'] ) ) {
				$input['see_more_opt']['link_type'] = sanitize_key( $input['see_more_opt']['link_type'] );

				if ( ! array_key_exists( $input['see_more_opt']['link_type'], $this->links ) )
					$input['see_more_opt']['link_type'] = $cn->defaults['general']['see_more_opt']['link_type'];
			} else
				$input['see_more_opt']['link_type'] = $cn->defaults['general']['see_more_opt']['link_type'];

			if ( $input['see_more_opt']['link_type'] === 'custom' )
				$input['see_more_opt']['link'] = $input['see_more'] && isset( $input['see_more_opt']['link'] ) ? esc_url_raw( $input['see_more_opt']['link'] ) : '';
			elseif ( $input['see_more_opt']['link_type'] === 'page' ) {
				$input['see_more_opt']['id'] = $input['see_more'] && isset( $input['see_more_opt']['id'] ) ? (int) $input['see_more_opt']['id'] : 0;
				$input['see_more_opt']['sync'] = isset( $input['see_more_opt']['sync'] );

				if ( $input['see_more_opt']['sync'] )
					update_option( 'wp_page_for_privacy_policy', $input['see_more_opt']['id'] );
			}

			// privacy policy link target
			if ( isset( $input['link_target'] ) ) {
				$input['link_target'] = sanitize_key( $input['link_target'] );

				if ( ! array_key_exists( $input['link_target'], $this->link_targets ) )
					$input['link_target'] = $cn->defaults['general']['link_target'];
			} else
				$input['link_target'] = $cn->defaults['general']['link_target'];

			// policy policy link position
			if ( isset( $input['link_position'] ) ) {
				$input['link_position'] = sanitize_key( $input['link_position'] );

				if ( ! array_key_exists( $input['link_position'], $this->link_positions ) )
					$input['link_position'] = $cn->defaults['general']['link_position'];
			} else
				$input['link_position'] = $cn->defaults['general']['link_position'];

			// message link position?
			if ( $input['see_more'] && $input['link_position'] === 'message' && strpos( $input['message_text'], '[cookies_policy_link' ) === false )
				$input['message_text'] .= ' [cookies_policy_link]';

			$input['update_version'] = $cn->options['general']['update_version'];
			$input['update_notice'] = $cn->options['general']['update_notice'];

			$input['translate'] = false;

			// WPML >= 3.2
			if ( defined( 'ICL_SITEPRESS_VERSION' ) && version_compare( ICL_SITEPRESS_VERSION, '3.2', '>=' ) ) {
				do_action( 'wpml_register_single_string', 'Cookie Notice', 'Message in the notice', $input['message_text'] );
				do_action( 'wpml_register_single_string', 'Cookie Notice', 'Button text', $input['accept_text'] );
				do_action( 'wpml_register_single_string', 'Cookie Notice', 'Refuse button text', $input['refuse_text'] );
				do_action( 'wpml_register_single_string', 'Cookie Notice', 'Revoke message text', $input['revoke_message_text'] );
				do_action( 'wpml_register_single_string', 'Cookie Notice', 'Revoke button text', $input['revoke_text'] );
				do_action( 'wpml_register_single_string', 'Cookie Notice', 'Privacy policy text', $input['see_more_opt']['text'] );

				if ( $input['see_more_opt']['link_type'] === 'custom' )
					do_action( 'wpml_register_single_string', 'Cookie Notice', 'Custom link', $input['see_more_opt']['link'] );
			}

			add_settings_error( 'cn_cookie_notice_options', 'save_cookie_notice_options', esc_html__( 'Settings saved.', 'cookie-notice' ), 'updated' );
		} elseif ( isset( $_POST['reset_cookie_notice_options'] ) ) {
			$input = $cn->defaults['general'];

			add_settings_error( 'cn_cookie_notice_options', 'reset_cookie_notice_options', esc_html__( 'Settings restored to defaults.', 'cookie-notice' ), 'updated' );

			// network area?
			if ( $is_network ) {
				// set app data
				update_site_option( 'cookie_notice_status', $cn->defaults['data'] );
			} else {
				// set app data
				update_option( 'cookie_notice_status', $cn->defaults['data'] );
			}
		}

		do_action( 'cn_configuration_updated', 'settings' );

		return $input;
	}

	/**
	 * Validate network options.
	 *
	 * @return void
	 */
	public function validate_network_options() {
		if ( ! current_user_can( apply_filters( 'cn_manage_cookie_notice_cap', 'manage_options' ) ) )
			return;

		// get main instance
		$cn = Cookie_Notice();

		// global network page?
		if ( $cn->is_network_admin() && isset( $_POST['cn-network-settings'] ) ) {
			// network settings
			if ( ! empty( $_POST['cookie_notice_options'] ) && check_admin_referer( 'cookie_notice_options-options', '_wpnonce' ) !== false ) {
				if ( isset( $_POST['save_cookie_notice_options'] ) ) {
					// need to check it early for get_app_config and get_app_analytics
					$cn->network_options['global_override'] = isset( $_POST['cookie_notice_options']['global_override'] );

					// validate options
					$data = $this->validate_options( $_POST['cookie_notice_options'] );

					// check network settings
					$data['global_override'] = $cn->network_options['global_override'];
					$data['global_cookie'] = isset( $_POST['cookie_notice_options']['global_cookie'] );
					$data['update_notice_diss'] = $cn->options['general']['update_notice_diss'];

					if ( $data['global_override'] && ! $cn->options['general']['update_notice_diss'] )
						$data['update_notice'] = true;
					else
						$data['update_notice'] = false;

					// update database
					update_site_option( 'cookie_notice_options', $data );

					// update settings
					$cn->options['general'] = $cn->network_options = $cn->multi_array_merge( $cn->defaults['general'], get_site_option( 'cookie_notice_options', $cn->defaults['general'] ) );
				} elseif ( isset( $_POST['reset_cookie_notice_options'] ) ) {
					$cn->defaults['general']['update_notice'] = false;
					$cn->defaults['general']['update_notice_diss'] = false;

					// silent options validation
					$this->validate_options( $cn->defaults['general'] );

					// update database
					update_site_option( 'cookie_notice_options', $cn->defaults['general'] );

					// update settings
					$cn->options['general'] = $cn->network_options = $cn->defaults['general'];
				}
			}

			// update status of cookie compliance
			$cn->set_status_data();
		}
	}

	/**
	 * Load scripts and styles - admin.
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts( $page ) {
		// get main instance
		$cn = Cookie_Notice();

		if ( $page === 'toplevel_page_cookie-notice' ) {
			wp_enqueue_script( 'cookie-notice-admin', COOKIE_NOTICE_URL . '/js/admin' . ( ! ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '.min' : '' ) . '.js', [ 'jquery', 'wp-color-picker' ], $cn->defaults['version'] );

			// prepare script data
			$script_data = [
				'ajaxURL'				=> admin_url( 'admin-ajax.php' ),
				'nonce'					=> wp_create_nonce( 'cn-purge-cache' ),
				'nonceConditional'		=> wp_create_nonce( 'cn-get-group-values' ),
				'nonceConsentLogs'		=> wp_create_nonce( 'cn-get-consent-logs' ),
				'consentLogsTemplate'	=> $cn->consent_logs->get_single_row_template(),
				'consentLogsError'		=> $cn->consent_logs->get_error_template(),
				'network'				=> $cn->is_network_admin(),
				'resetToDefaults'		=> esc_html__( 'Are you sure you want to reset these settings to defaults?', 'cookie-notice' )
			];

			wp_add_inline_script( 'cookie-notice-admin', 'var cnArgs = ' . wp_json_encode( $script_data ) . ";\n", 'before' );

			wp_enqueue_style( 'wp-color-picker' );
		}

		wp_enqueue_style( 'cookie-notice-admin', COOKIE_NOTICE_URL . '/css/admin' . ( ! ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '.min' : '' ) . '.css', [], $cn->defaults['version'] );
	}

	/**
	 * Load admin style inline, for menu icon only.
	 *
	 * @return void
	 */
	public function admin_print_styles() {
		echo '
		<style>
			a.toplevel_page_cookie-notice .wp-menu-image {
				background-image: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+PCFET0NUWVBFIHN2ZyBQVUJMSUMgIi0vL1czQy8vRFREIFNWRyAxLjEvL0VOIiAiaHR0cDovL3d3dy53My5vcmcvR3JhcGhpY3MvU1ZHLzEuMS9EVEQvc3ZnMTEuZHRkIj48c3ZnIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIHZpZXdCb3g9IjAgMCAzMjEgMzIxIiB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHhtbG5zOnNlcmlmPSJodHRwOi8vd3d3LnNlcmlmLmNvbS8iIHN0eWxlPSJmaWxsLXJ1bGU6ZXZlbm9kZDtjbGlwLXJ1bGU6ZXZlbm9kZDtzdHJva2UtbGluZWpvaW46cm91bmQ7c3Ryb2tlLW1pdGVybGltaXQ6MjsiPjxwYXRoIGQ9Ik0zMTcuMjc4LDEzMC40NTFjLTAuODEyLC00LjMwMiAtNC4zMDEsLTcuNTYyIC04LjY0MiwtOC4wODFjLTQuMzU0LC0wLjUyMiAtOC41MDYsMS44MjkgLTEwLjMwNyw1LjgyMmMtMy4xNyw3LjAwMyAtMTAuMTMzLDExLjg3MyAtMTguMjA1LDExLjg2NGMtOC45NTUsMC4wMjIgLTE2LjUxNywtNi4wMjEgLTE5LjAzOCwtMTQuMzE1Yy0xLjUyMSwtNS4wNjMgLTYuNzI0LC04LjA2NCAtMTEuODY1LC02Ljg2M2MtMy4xNjMsMC43NDEgLTYuMTU0LDEuMTcyIC05LjEyNSwxLjE3MmMtMjIuMDM5LC0wLjA0MyAtMzkuOTc2LC0xNy45NzkgLTQwLjAxNSwtNDAuMDE5Yy0wLC0yLjk3IDAuNDMsLTUuOTYyIDEuMTY5LC05LjExM2MxLjIxMiwtNS4xNDEgLTEuNzk5LC0xMC4zNTMgLTYuODYsLTExLjg3M2MtOC4yOTUsLTIuNTEzIC0xNC4zMzcsLTEwLjA3NSAtMTQuMzE5LC0xOS4wMjljLTAuMDA5LC04LjA4MiA0Ljg2NCwtMTUuMDM2IDExLjg2NywtMTguMjA4YzMuOTkxLC0xLjc5OCA2LjM0MSwtNS45NjMgNS44MjIsLTEwLjMwNGMtMC41MjIsLTQuMzUxIC0zLjc4MywtNy44NDMgLTguMDg0LC04LjY1MmMtOS41NDMsLTEuNzkyIC0xOS40MjYsLTIuODUyIC0yOS42MTEsLTIuODUyYy04OC4yOTUsMC4wMjIgLTE2MC4wNDMsNzEuNzcgLTE2MC4wNjUsMTYwLjA2NWMwLjAyMiw4OC4yOTUgNzEuNzcsMTYwLjA0MyAxNjAuMDY1LDE2MC4wNjVjODguMjk1LC0wLjAyMiAxNjAuMDQzLC03MS43NyAxNjAuMDY1LC0xNjAuMDY1Yy0wLC0xMC4xODQgLTEuMDYzLC0yMC4wNjcgLTIuODUyLC0yOS42MTRabS01OC4yMjMsMTI4LjYwNGMtMjUuNDAxLDI1LjM4IC02MC4zNTUsNDEuMDY2IC05OC45OSw0MS4wNjZjLTM4LjYzNSwwIC03My41ODgsLTE1LjY4NiAtOTguOTg5LC00MS4wNjZjLTI1LjM4LC0yNS40MDEgLTQxLjA2NiwtNjAuMzU1IC00MS4wNjYsLTk4Ljk5Yy0wLC0zOC42MzUgMTUuNjg2LC03My41ODggNDEuMDY2LC05OC45ODljMjUuNDAxLC0yNS4zOCA2MC4zNTQsLTQxLjA2NiA5OC45ODksLTQxLjA2NmMxLjgwMSwwIDMuNTYsMC4xODkgNS4zNTIsMC4yNjhjLTMuMzQzLDUuODIzIC01LjM0MywxMi41MjcgLTUuMzUyLDE5LjczOGMwLjAxOCwxNC45MzUgOC4zMDQsMjcuNzQyIDIwLjM3OSwzNC41NzVjLTAuMTkyLDEuNzggLTAuMzczLDMuNTYgLTAuMzczLDUuNDRjMC4wMjIsMzMuMTI1IDI2LjkwMyw2MC4wMDcgNjAuMDI1LDYwLjAyNWMxLjg4LDAgMy42NjQsLTAuMTggNS40NDMsLTAuMzY5YzYuODMzLDEyLjA2NSAxOS42MjgsMjAuMzU2IDM0LjU3MiwyMC4zNzhjNy4yMTUsLTAuMDA5IDEzLjkxNiwtMi4wMTEgMTkuNzQxLC01LjM1MmMwLjA4LDEuNzggMC4yNjksMy41NTEgMC4yNjksNS4zNTJjLTAsMzguNjM1IC0xNS42ODYsNzMuNTg5IC00MS4wNjYsOTguOTlabS01OC45NzQsLTE4Ljk1OWMtMCwxMS4wNTIgLTguOTU4LDIwLjAxIC0yMC4wMSwyMC4wMWMtMTEuMDQ4LC0wIC0yMC4wMDUsLTguOTU4IC0yMC4wMDUsLTIwLjAxYy0wLC0xMS4wNDkgOC45NTcsLTIwLjAwNiAyMC4wMDUsLTIwLjAwNmMxMS4wNTIsLTAgMjAuMDEsOC45NTcgMjAuMDEsMjAuMDA2Wm0tODAuMDMxLC0xMC4wMDVjMCw1LjUyNiAtNC40NzksMTAuMDA1IC0xMC4wMDUsMTAuMDA1Yy01LjUyNiwtMCAtMTAuMDA1LC00LjQ3OSAtMTAuMDA1LC0xMC4wMDVjMCwtNS41MjMgNC40NzksLTEwLjAwMSAxMC4wMDUsLTEwLjAwMWM1LjUyNiwtMCAxMC4wMDUsNC40NzggMTAuMDA1LDEwLjAwMVptMTQwLjA1NSwtMjAuMDA2YzAsNS41MjYgLTQuNDc5LDEwLjAwNSAtMTAuMDA1LDEwLjAwNWMtNS41MjUsMCAtMTAuMDA1LC00LjQ3OSAtMTAuMDA1LC0xMC4wMDVjMCwtNS41MjYgNC40OCwtMTAuMDA1IDEwLjAwNSwtMTAuMDA1YzUuNTI2LDAgMTAuMDA1LDQuNDc5IDEwLjAwNSwxMC4wMDVabS0xNjAuMDY0LC01MC4wMmMtMCwxMS4wNDggLTguOTU3LDIwLjAwNiAtMjAuMDEsMjAuMDA2Yy0xMS4wNDgsMCAtMjAuMDA1LC04Ljk1OCAtMjAuMDA1LC0yMC4wMDZjLTAsLTExLjA1MiA4Ljk1NywtMjAuMDEgMjAuMDA1LC0yMC4wMWMxMS4wNTMsMCAyMC4wMSw4Ljk1OCAyMC4wMSwyMC4wMVptODAuMDMsMTAuMDA1YzAsNS41MjMgLTQuNDc4LDEwLjAwMSAtMTAuMDAxLDEwLjAwMWMtNS41MjYsMCAtMTAuMDA1LC00LjQ3OCAtMTAuMDA1LC0xMC4wMDFjMCwtNS41MjYgNC40NzksLTEwLjAwNSAxMC4wMDUsLTEwLjAwNWM1LjUyMywwIDEwLjAwMSw0LjQ3OSAxMC4wMDEsMTAuMDA1Wm0xMTUuNDkzLC02OS40MDZjMCw1LjUyNiAtNC40NzksMTAuMDA1IC0xMC4wMDUsMTAuMDA1Yy01LjUyNiwtMCAtMTAuMDA1LC00LjQ3OSAtMTAuMDA1LC0xMC4wMDVjMCwtNS41MjYgNC40NzksLTEwLjAwNSAxMC4wMDUsLTEwLjAwNWM1LjUyNiwtMCAxMC4wMDUsNC40NzkgMTAuMDA1LDEwLjAwNVptLTM1LjUyMywtMTkuODc0Yy0wLDExLjUwMyAtOS4zMjUsMjAuODI4IC0yMC44MjgsMjAuODI4Yy0xMS41MDQsLTAgLTIwLjgyOSwtOS4zMjUgLTIwLjgyOSwtMjAuODI4Yy0wLC0xMS41MDMgOS4zMjUsLTIwLjgyOCAyMC44MjksLTIwLjgyOGMxMS41MDMsLTAgMjAuODI4LDkuMzI1IDIwLjgyOCwyMC44MjhabS0xMTkuOTg1LC0wLjc1OWMtMCwxMS4wNTIgLTguOTU3LDIwLjAxIC0yMC4wMDYsMjAuMDFjLTExLjA1MiwtMCAtMjAuMDA5LC04Ljk1OCAtMjAuMDA5LC0yMC4wMWMtMCwtMTEuMDQ4IDguOTU3LC0yMC4wMDYgMjAuMDA5LC0yMC4wMDZjMTEuMDQ5LC0wIDIwLjAwNiw4Ljk1OCAyMC4wMDYsMjAuMDA2WiIgc3R5bGU9ImZpbGw6I2ZmZjtmaWxsLXJ1bGU6bm9uemVybzsiLz48L3N2Zz4=);
				background-position: center center;
				background-repeat: no-repeat;
				background-size: 18px auto;
			}
		</style>
		';
	}

	/**
	 * Register WPML (>= 3.2) strings if needed.
	 *
	 * @global object $wpdb
	 *
	 * @return void
	 */
	private function register_wpml_strings() {
		// get main instance
		$cn = Cookie_Notice();

		global $wpdb;

		// prepare strings
		$strings = [
			'Message in the notice'	=> $cn->options['general']['message_text'],
			'Button text'			=> $cn->options['general']['accept_text'],
			'Refuse button text'	=> $cn->options['general']['refuse_text'],
			'Revoke message text'	=> $cn->options['general']['revoke_message_text'],
			'Revoke button text'	=> $cn->options['general']['revoke_text'],
			'Privacy policy text'	=> $cn->options['general']['see_more_opt']['text'],
			'Custom link'			=> $cn->options['general']['see_more_opt']['link']
		];

		// get query results
		$results = $wpdb->get_col( $wpdb->prepare( "SELECT name FROM " . $wpdb->prefix . "icl_strings WHERE context = %s", 'Cookie Notice' ) );

		// check results
		foreach( $strings as $string => $value ) {
			// string does not exist?
			if ( ! in_array( $string, $results, true ) ) {
				// register string
				do_action( 'wpml_register_single_string', 'Cookie Notice', $string, $value );
			}
		}
	}

	/**
	 * Display errors and notices.
	 *
	 * @global string $pagenow
	 *
	 * @return void
	 */
	public function settings_errors() {
		global $pagenow;

		// force display notices in top menu settings page
		if ( $pagenow === 'options-general.php' )
			return;

		settings_errors( 'cn_cookie_notice_options' );
	}

	/**
	 * Save compliance config caching.
	 *
	 * @return void
	 */
	public function ajax_purge_cache() {
		// valid nonce?
		if ( ! check_ajax_referer( 'cn-purge-cache', 'nonce' ) )
			exit;

		// check capability
		if ( ! current_user_can( apply_filters( 'cn_manage_cookie_notice_cap', 'manage_options' ) ) )
			exit;

		// request for new config data
		Cookie_Notice()->welcome_api->get_app_config( '', true );

		// force new config on frontend
		if ( Cookie_Notice()->is_network_admin() )
			set_site_transient( 'cookie_notice_config_update', current_time( 'timestamp', true ), 600 );
		else
			set_transient( 'cookie_notice_config_update', current_time( 'timestamp', true ), 600 );

		exit;
	}

	/**
	 * Generate conditions.
	 *
	 * @param array $groups
	 * @return string
	 */
	public function conditional_display( $groups ) {
		$group_template = '
		<div%s>
			<table class="widefat">
				<tbody>
					%s
				</tbody>
			</table>
			<h4 class="or-rules">' . esc_html__( 'or', 'cookie-notice' ) . '</h4>
		</div>';

		$rule_template = '
		<tr data-rule-id="__RULE_ID__" %s>
			<td class="param">
				<select class="rule-type" name="cookie_notice_options[conditional_rules][__GROUP_ID__][__RULE_ID__][param]">
					%s
				</select>
			</td>
			<td class="operator">
				<select name="cookie_notice_options[conditional_rules][__GROUP_ID__][__RULE_ID__][operator]">
					%s
				</select>
			</td>
			<td class="value">
				<span class="spinner" style="display: none"></span>
				<select name="cookie_notice_options[conditional_rules][__GROUP_ID__][__RULE_ID__][value]">
					%s
				</select>
			</td>
			<td class="remove">
				<a href="#" class="dashicons dashicons-no-alt remove-rule"></a>
			</td>
		</tr>';

		$html = sprintf(
			$group_template,
			' class="rules-group" id="rules-group-template" style="display: none"',
			sprintf(
				$rule_template,
				' class="rule_template"',
				$this->prepare_parameters(),
				$this->prepare_operators(),
				$this->prepare_values( 'page_type' )
			)
		) . '
		<div id="cookie-notice-conditions">
			<table class="widefat">
				<tbody>
					<tr>
						<td>
							<div id="rules-groups">';

		if ( ! empty( $groups ) ) {
			foreach ( $groups as $group_id => $group ) {
				$html_rules = '';

				foreach ( $group as $rule_id => $rule ) {
					$html_rules .= sprintf(
						str_replace(
							[ '__GROUP_ID__', '__RULE_ID__' ],
							[ (int) $group_id, (int) $rule_id ],
							$rule_template
						),
						'',
						$this->prepare_parameters( $rule['param'] ),
						$this->prepare_operators( $rule['operator'] ),
						$this->prepare_values( $rule['param'], $rule['value'] )
					);
				}

				$html .= sprintf( str_replace( '__GROUP_ID__', $group_id, $group_template ), ' class="rules-group" id="rules-group-' . $group_id . '"', $html_rules );
			}
		}

		$html .= '			</div>
							<a class="add-rule-group button button-primary" href="#">' . esc_html__( '+ Add rule', 'cookie-notice' ) . '</a>
							<p class="description">' . esc_html__( 'Create a set of rules to define the exact conditions for displaying or hiding the banner.', 'cookie-notice' ) . '</p>
						</td>
					</tr>
				</tbody>
			</table>
		</div>';

		return $html;
	}

	/**
	 * Prepare condition parameters.
	 *
	 * @param string $selected
	 * @return string
	 */
	public function prepare_parameters( $selected = 'page_type' ) {
		$html = '';

		foreach ( $this->parameters as $id => $element ) {
			$html .= '<option value="' . esc_attr( $id ) . '" ' . selected( $id, $selected, false ) . '>' . esc_html( $element ) . '</option>';
		}

		return $html;
	}

	/**
	 * Prepare condition operators.
	 *
	 * @param string $selected
	 * @return string
	 */
	public function prepare_operators( $selected = 'equal' ) {
		$html = '';

		foreach ( $this->operators as $id => $operator ) {
			$html .= '<option value="' . esc_attr( $id ) . '" ' . selected( $id, $selected, false ) . '>' . esc_html( $operator ) . '</option>';
		}

		return $html;
	}

	/**
	 * Prepare condition values.
	 *
	 * @param string $type
	 * @param string $selected
	 * @return string
	 */
	public function prepare_values( $type = '', $selected = '' ) {
		$type = sanitize_key( $type );
		$selected = sanitize_key( $selected );
		$html = '';

		switch ( sanitize_key( $type ) ) {
			case 'page':
				$pages = $this->get_pages();

				if ( ! empty( $pages ) ) {
					foreach ( $pages as $page_id => $page_title ) {
						$html .= '<option value="' . esc_attr( $page_id ) . '" ' . selected( $page_id, $selected, false ) . '>' . esc_html( $page_title ) . '</option>';
					}
				}
				break;

			case 'page_type':
				$page_types = $this->get_page_types();

				if ( ! empty( $page_types ) ) {
					foreach ( $page_types as $page_type => $label ) {
						$html .= '<option value="' . esc_attr( $page_type ) . '" ' . selected( $page_type, $selected, false ) . '>' . esc_html( $label ) . '</option>';
					}
				}
				break;

			case 'post_type':
				$post_types = $this->get_post_types();

				if ( ! empty( $post_types ) ) {
					foreach ( $post_types as $post_type => $label ) {
						$html .= '<option value="' . esc_attr( $post_type ) . '" ' . selected( $post_type, $selected, false ) . '>' . esc_html( $label ) . '</option>';
					}
				}
				break;

			case 'user_type':
				$user_types = $this->get_user_types();

				if ( ! empty( $user_types ) ) {
					foreach ( $user_types as $user_type => $username ) {
						$html .= '<option value="' . esc_attr( $user_type ) . '" ' . selected( $user_type, $selected, false ) . '>' . esc_html( $username ).'</option>';
					}
				}
				break;

			case 'post_type_archive':
				$post_type_archives = $this->get_post_type_archives();

				if ( ! empty( $post_type_archives ) ) {
					foreach ( $post_type_archives as $post_type => $archive_name ) {
						$html .= '<option value="' . esc_attr( $post_type ) . '" ' . selected( $post_type, $selected, false ) . '>' . esc_html( $archive_name ) . '</option>';
					}
				} else
					$html .= '<option value="__none__">' . esc_html__( '-- no public archives --', 'cookie-notice' ) . '</option>';
		}

		return $html;
	}

	/**
	 * Check condition rule.
	 *
	 * @param string $type
	 * @param string $operator
	 * @param string $value
	 * @return bool
	 */
	public function check_rule( $type = '', $operator = '', $value = '' ) {
		if ( ! isset( $this->operators[$operator] ) )
			return false;

		switch( $type ) {
			case 'page':
				$pages = $this->get_pages();

				$valid_rule = ! empty( $pages[$value] );
				break;

			case 'page_type':
				$page_types = $this->get_page_types();

				$valid_rule = ! empty( $page_types[$value] );
				break;

			case 'post_type':
				$post_types = $this->get_post_types();

				$valid_rule = ! empty( $post_types[$value] );
				break;

			case 'user_type':
				$user_types = $this->get_user_types();

				$valid_rule = ! empty( $user_types[$value] );
				break;

			case 'post_type_archive':
				$post_type_archives = $this->get_post_type_archives();

				$valid_rule = ! empty( $post_type_archives[$value] );
				break;

			default:
				$valid_rule = false;
		}

		return $valid_rule;
	}

	/**
	 * Get page types.
	 *
	 * @return array
	 */
	public function get_pages() {
		$pages = [];

		// default arguments
		$args = [
			'post_type'			=> 'page',
			'post__not_in'		=> [],
			'nopaging'			=> true,
			'posts_per_page'	=> -1,
			'orderby'			=> 'title',
			'order'				=> 'asc',
			'suppress_filters'	=> false,
			'no_found_rows'		=> true,
			'cache_results'		=> false,
			'post_status'		=> [ 'publish', 'private', 'future' ]
		];

		// get static home pages
		$homepage = (int) get_option( 'page_for_posts', 0 );
		$posts_page = (int) get_option( 'page_on_front', 0 );

		// check homepage
		if ( $homepage > 0 )
			$args['post__not_in'][] = $homepage;

		// check posts page
		if ( $posts_page > 0 )
			$args['post__not_in'][] = $posts_page;

		$query = new WP_Query( $args );

		if ( ! empty( $query->posts ) ) {
			foreach ( $query->posts as $page ) {
				$page_id = (int) $page->ID;

				$pages[$page_id] = trim( $page->post_title ) === '' ? sprintf( __( 'Untitled Page %d', 'cookie-notice' ), $page_id ) : $page->post_title;
			}
		}

		return $pages;
	}

	/**
	 * Get page types.
	 *
	 * @return array
	 */
	public function get_page_types() {
		return [
			'front'	=> __( 'Front Page', 'cookie-notice' ),
			'home'	=> __( 'Home Page', 'cookie-notice' )
		];
	}

	/**
	 * Get user types.
	 *
	 * @return array
	 */
	public function get_user_types() {
		return [
			'logged_in'	=> __( 'Logged in', 'cookie-notice' ),
			'guest'		=> __( 'Guest', 'cookie-notice' )
		];
	}

	/**
	 * Get public post types.
	 *
	 * @return array
	 */
	public function get_post_types() {
		// get public post types
		$post_types = get_post_types(
			[
				'public' => true
			],
			'objects',
			'and'
		);

		$data = [];

		if ( ! empty( $post_types ) ) {
			foreach ( $post_types as $key => $post_type ) {
				$data[$key] = $post_type->labels->singular_name;
			}
		}

		asort( $data, SORT_STRING );

		return $data;
	}

	/**
	 * Get public post type archives.
	 *
	 * @return array
	 */
	public function get_post_type_archives() {
		// get public post types with archives
		$post_types = get_post_types(
			[
				'has_archive'	=> true,
				'public'		=> true
			],
			'objects',
			'and'
		);

		$archives = [];

		if ( ! empty( $post_types ) ) {
			foreach ( $post_types as $key => $post_type ) {
				$archives[$key] = $post_type->labels->name;
			}
		}

		// sort archives alphabetically
		asort( $archives, SORT_STRING );

		return $archives;
	}

	/**
	 * Get group rule values.
	 *
	 * @return void
	 */
	public function get_group_rule_values() {
		if ( isset( $_POST['action'], $_POST['cn_param'], $_POST['cn_nonce'] ) && wp_verify_nonce( $_POST['cn_nonce'], 'cn-get-group-values' ) !== false ) {
			echo wp_json_encode(
				[
					'select'	=> $this->prepare_values( sanitize_key( $_POST['cn_param'] ) )
				]
			);
		}

		exit;
	}

	/**
	 *
	 */
	public function get_analytics_app_data() {
		return $this->analytics_app_data;
	}

	/**
	 * Add new properties to style safe list.
	 *
	 * @param array $styles
	 * @return array
	 */
	public function allow_style_attributes( $styles ) {
		$styles[] = 'display';

		return $styles;
	}
}
