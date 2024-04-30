<?php
// exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Cookie Notice Modules AMP class.
 *
 * Compatibility since: 2.0.0
 *
 * @class Cookie_Notice_Modules_AMP
 */
class Cookie_Notice_Modules_AMP {

	private $nonce = '';

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->nonce = wp_create_nonce( 'cookie-compliance-amp-consent' );

		add_action( 'init', [ $this, 'handle_iframe' ] );
		add_action( 'wp_head', [ $this, 'load_amp_consent' ] );
	}

	/**
	 * Load AMP consent module.
	 *
	 * @return void
	 */
	public function load_amp_consent() {
		// is banner allowed to display?
		if ( ! Cookie_Notice()->frontend->maybe_display_banner( [ 'skip_amp' => true ] ) )
			return;

		if ( function_exists( 'amp_is_request' ) && amp_is_request() ) {
			// load styles
			echo '
			<style amp-custom>
				#cnConsentContainer {
					background: none;
					border: none;
					box-shadow: none;
					border-radius: 0;
					height: 100vh;
					width: 100%;
				}
				#cnConsentContainer amp-iframe {
					height: 100vh;
					width: 100%;
				}
				#cnConsentWidget {
					height: 100vh;
					width: 100%;
				}
			</style>';

			// load scripts
			echo '
			<script async custom-element="amp-script" src="https://cdn.ampproject.org/v0/amp-script-0.1.js"></script>
			<script async custom-element="amp-consent" src="https://cdn.ampproject.org/v0/amp-consent-0.1.js"></script>
			<script async custom-element="amp-iframe" src="https://cdn.ampproject.org/v0/amp-iframe-0.1.js"></script>';

			// get iframe url
			$url = apply_filters( 'cn_cookie_compliance_amp_iframe_url', $this->add_subdomain_to_url( get_site_url(), 'www' ) );

			// load consent iframe
			echo '
			<amp-consent id="cnConsentContainer" layout="nodisplay">
				<script type="application/json">
					{
						"consentInstanceId": "cnConsent",
						"consentRequired": true,
						"purposeConsentRequired": [ "basic_operations", "content_personalization", "site_optimization", "ad_personalization" ],
						"promptUI": "cnConsentWidget"
					}
				</script>
				<div id="cnConsentWidget">
					<amp-iframe layout="fill" sandbox="allow-scripts allow-same-origin" src="' . esc_url( $url . '/?cn-amp-consent-iframe=' . $this->nonce ) . '">
						<div placeholder></div>
					</amp-iframe>
				</div>
			</amp-consent>';
		}
	}

	/**
	 * Add subdomain to url.
	 *
	 * @param string $url
	 * @param string $subdomain
	 * @return string
	 */
	public function add_subdomain_to_url( $url, $subdomain ) {
		// parse url
		$parts = parse_url( $url );

		// subdomain does not exist?
		if ( substr( $parts['host'], 0, strlen( $subdomain ) + 1 ) !== $subdomain . '.' ) {
			// find host
			$pos = strpos( $url, $parts['host'] );

			// update url and add subdomain
			$url = substr_replace( $url, $subdomain . '.' . $parts['host'], $pos, strlen( $parts['host'] ) );
		}

		return $url;
	}

	/**
	 * Generate consent iframe.
	 *
	 * @return void
	 */
	public function handle_iframe() {
		if ( isset( $_GET['cn-amp-consent-iframe'] ) && $_GET['cn-amp-consent-iframe'] === $this->nonce ) {
			wp_ob_end_flush_all();

			// display iframe
			echo $this->generate_iframe_html();
			exit;
		}
	}

	/**
	 * Generate consent iframe.
	 *
	 * @return string
	 */
	public function generate_iframe_html() {
		// get main instance
		$cn = Cookie_Notice();

		// get options
		$options = $cn->frontend->get_cc_options();

		// get output
		$cc_output = $cn->frontend->get_cc_output( $options );

		// get allowed html for cookie compliance html output
		$allowed_html = array_merge(
			wp_kses_allowed_html( 'post' ),
			[
				'script'	=> [
					'type'	=> true,
					'src'	=> true
				]
			]
		);

		$html = '
		<!DOCTYPE html>
		<html ' . get_language_attributes( 'html' ) . '>
			<head>
				<meta charset="' . esc_attr( get_bloginfo( 'charset', 'display' ) ) . '">
				<meta name="viewport" content="width=device-width, initial-scale=1.0">
				<meta name="robots" content="noindex">
				<title>' . esc_html__( 'Cookie Compliance AMP Consent', 'cookie-notice' ) . '</title>
				' . wp_kses( $cc_output, $allowed_html ) . '
				<script type="text/javascript" src="' . esc_url( COOKIE_NOTICE_URL . '/includes/modules/amp/iframe.js' ) . '"></script>
			</head>
			<body></body>
		</html>';

		return $html;
	}
}

new Cookie_Notice_Modules_AMP();