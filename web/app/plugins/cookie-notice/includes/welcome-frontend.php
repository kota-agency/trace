<?php
// exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Cookie_Notice_Welcome_Frontend class.
 *
 * @class Cookie_Notice_Welcome_Frontend
 */
class Cookie_Notice_Welcome_Frontend {

	private $preview_mode = false;

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'after_setup_theme', [ $this, 'preview_init' ], 1 );
	}

	/**
	 * Initialize preview mode.
	 *
	 * @return void
	 */
	public function preview_init() {
		// check preview mode
		$this->preview_mode = isset( $_GET['cn_preview_mode'] ) ? (int) $_GET['cn_preview_mode'] : false;

		if ( $this->preview_mode !== false ) {
			// filters
			add_filter( 'show_admin_bar', '__return_false' );
			add_filter( 'cn_cookie_notice_output', '__return_false', 1000 );

			// actions
			add_action( 'wp_enqueue_scripts', [ $this, 'wp_dequeue_scripts' ] );

			// only in live preview
			if ( $this->preview_mode === 1 ) {
				add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ] );
				add_action( 'wp_head', [ $this, 'wp_head_scripts' ], 0 );
			}
		}
	}

	/**
	 * Load scripts and styles.
	 *
	 * @return void
	 */
	public function wp_enqueue_scripts() {
		// get main instance
		$cn = Cookie_Notice();

		// show only in live preview
		if ( $this->preview_mode === 1 ) {
			wp_enqueue_script( 'cookie-notice-welcome-frontend', COOKIE_NOTICE_URL . '/js/front-welcome.js', [ 'jquery', 'underscore' ], $cn->defaults['version'] );

			// prepare script data
			$script_data = [
				'previewMode'	=> $this->preview_mode,
				'allowedURLs'	=> $this->get_allowed_urls(),
				'levelNames'	=> $cn->settings->level_names,
				'textStrings'	=> $cn->settings->text_strings
			];

			wp_add_inline_script( 'cookie-notice-welcome-frontend', 'var cnFrontWelcome = ' . wp_json_encode( $script_data ) . ";\n", 'before' );
		}
	}

	/**
	 * Unload scripts and styles.
	 *
	 * @return void
	 */
	public function wp_dequeue_scripts() {
		// deregister native cookie notice script
		wp_dequeue_script( 'cookie-notice-front' );
	}

	/**
	 * Load cookie compliance script.
	 *
	 * @return void
	 */
	public function wp_head_scripts() {
		$options = [
			'currentLanguage'	=> 'en',
			'previewMode'		=> true,
			'debugMode'			=> true,
			'config'			=> [
				'privacyPaper'		=> true,
				'privacyContact'	=> true
			]
		];

		echo '
		<!-- Cookie Compliance -->
		<script type="text/javascript">var huOptions = ' . wp_json_encode( $options ) . ';</script>
		<script type="text/javascript" src="' . esc_url( Cookie_Notice()->get_url( 'widget' ) ) . '"></script>
		<style>.hu-preview-mode #hu::after {content: "";position: fixed;width: 100%;height: 100%;display: block;top: 0;left: 0}</style>';
	}

	/**
	 * Get URLs allowed to be previewed.
	 *
	 * @return array
	 */
	public function get_allowed_urls() {
		$allowed_urls = [ home_url( '/' ) ];

		if ( is_ssl() && ! $this->is_cross_domain() )
			$allowed_urls[] = home_url( '/', 'https' );

		return $allowed_urls;
	}

	/**
	 * Determines whether the admin and the frontend are on different domains.
	 *
	 * @return bool
	 */
	public function is_cross_domain() {
		$admin_origin = wp_parse_url( admin_url() );
		$home_origin = wp_parse_url( home_url() );

		return ( strtolower( $admin_origin['host'] ) !== strtolower( $home_origin['host'] ) );
	}
}
