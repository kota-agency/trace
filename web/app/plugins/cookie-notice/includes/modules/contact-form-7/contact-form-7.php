<?php
// exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Cookie Notice Modules Contact Form 7 class.
 *
 * Compatibility since: 5.1.0 (recaptcha v3 only)
 *
 * @class Cookie_Notice_Modules_ContactForm7
 */
class Cookie_Notice_Modules_ContactForm7 {

	private $service;

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->service = WPCF7_RECAPTCHA::get_instance();

		if ( $this->service->is_active() )
			add_action( 'wp_enqueue_scripts', [ $this, 'contact_form_7_recaptcha' ], 21 );
	}

	/**
	 * Replace original recaptcha script from Contact Form 7.
	 *
	 * @return void
	 */
	public function contact_form_7_recaptcha() {
		// deregister original script
		wp_deregister_script( 'wpcf7-recaptcha' );

		// register new script
		wp_register_script(
			'wpcf7-recaptcha',
			COOKIE_NOTICE_URL . '/includes/modules/contact-form-7/recaptcha.js',
			[
				'google-recaptcha',
				'wp-polyfill'
			],
			WPCF7_VERSION,
			true
		);

		wp_enqueue_script( 'wpcf7-recaptcha' );

		wp_localize_script(
			'wpcf7-recaptcha',
			'wpcf7_recaptcha',
			[
				'sitekey'	=> $this->service->get_sitekey(),
				'actions'	=> apply_filters(
					'wpcf7_recaptcha_actions',
					[
						'homepage'		=> 'homepage',
						'contactform'	=> 'contactform'
					]
				)
			]
		);
	}
}

new Cookie_Notice_Modules_ContactForm7();