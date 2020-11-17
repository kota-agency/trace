<?php // @codingStandardsIgnoreLine
/**
 * Rank Math SEO PRO Plugin.
 *
 * @package      RANK_MATH
 * @copyright    Copyright (C) 2018-2020, Rank Math - support@rankmath.com
 * @link         https://rankmath.com
 * @since        2.0.0
 *
 * @wordpress-plugin
 * Plugin Name:       Rank Math SEO PRO
 * Version:           2.0.2
 * Plugin URI:        https://rankmath.com/wordpress/plugin/seo-suite/
 * Description:       Super-charge your website’s SEO with the Rank Math PRO options like Site Analytics, SEO Performance, Custom Schema Templates, News/Video Sitemaps, etc.
 * Author:            Rank Math
 * Author URI:        https://s.rankmath.com/pro
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       rank-math-pro
 * Domain Path:       /languages
 */

use RankMath\Helper;
use MyThemeShop\Helpers\Conditional;

defined( 'ABSPATH' ) || exit;

/**
 * RankMath class.
 *
 * @class The class that holds the entire plugin.
 */
final class RankMathPro {

	/**
	 * Plugin version
	 *
	 * @var string
	 */
	public $version = '2.0.2';

	/**
	 * Holds various class instances
	 *
	 * @var array
	 */
	private $container = [];

	/**
	 * Holds messages.
	 *
	 * @var array
	 */
	private $messages = [];

	/**
	 * Slug for the free version of the plugin.
	 *
	 * @var string
	 */
	private $free_version_plugin_path = 'seo-by-rank-math/rank-math.php';

	/**
	 * The single instance of the class
	 *
	 * @var RankMath
	 */
	protected static $instance = null;

	/**
	 * Main RankMathPro instance.
	 *
	 * Ensure only one instance is loaded or can be loaded.
	 *
	 * @see rank_math_pro()
	 * @return RankMathPro
	 */
	public static function get() {
		if ( is_null( self::$instance ) && ! ( self::$instance instanceof RankMathPro ) ) {
			self::$instance = new RankMathPro();
		}
		return self::$instance;
	}

	/**
	 * Class constructor.
	 */
	private function __construct() {
		if ( ! $this->are_requirements_met() ) {
			return;
		}

		$this->define_constants();
		$this->includes();
		new \RankMathPro\Installer();

		add_action( 'rank_math/loaded', [ $this, 'setup' ] );
	}

	/**
	 * Instantiate the plugin.
	 */
	public function setup() {
		if ( ! $this->is_free_version_latest() ) {
			$this->messages[] = esc_html__( 'Please update Rank Math Free to the latest version first before activating the PRO version.', 'rank-math-pro' );
			add_action( 'admin_notices', [ $this, 'activation_error' ] );
			return false;
		}

		if ( $this->is_plan_expired() ) {
			$this->messages[] = sprintf(
				// translators: Links to pricing page and help page.
				wp_kses_post( __( 'Your account does not have any active subscription. <a href="%1$s" target="_blank">Please buy the Rank Math PRO version here</a> or <a href="%2$s">reconnect your account here</a>.', 'rank-math-pro' ) ),
				'https://rankmath.com/pricing/?utm_source=Plugin&utm_medium=Upgrade%20Notice&utm_campaign=WP',
				\RankMath\Helper::get_admin_url( '', 'view=help' )
			);

			add_action( 'admin_notices', [ $this, 'activation_error' ] );
			return false;
		}

		// Instantiate classes.
		$this->instantiate();

		// Initialize the action hooks.
		$this->init_actions();

		// Loaded action.
		do_action( 'rank_math_pro/loaded' );
	}

	/**
	 * Check that the WordPress and PHP setup meets the plugin requirements.
	 *
	 * @return bool
	 */
	private function are_requirements_met() {

		if ( $this->is_free_version_being_deactivated() ) {
			// Todo: this message is not displayed because of a redirect.
			$this->messages[] = esc_html__( 'Rank Math free version is required to run Rank Math Pro. Both plugins are now disabled.', 'rank-math-pro' );
		} else {
			if ( ! $this->is_free_version_installed() ) {
				if ( ! $this->install_free_version() ) {
					$this->messages[] = esc_html__( 'Rank Math free version is required to run Rank Math Pro, but it could not be installed automatically. Please install and activate the free version first.', 'rank-math-pro' );
				}
			}

			if ( ! $this->is_free_version_activated() ) {
				if ( ! $this->activate_free_version() ) {
					$this->messages[] = esc_html__( 'Rank Math free version is required to run Rank Math Pro, but it could not be activated automatically. Please install and activate the free version first.', 'rank-math-pro' );
				}
			}
		}

		if ( empty( $this->messages ) ) {
			return true;
		}

		// Auto-deactivate plugin.
		add_action( 'admin_init', [ $this, 'auto_deactivate' ] );
		add_action( 'admin_notices', [ $this, 'activation_error' ] );
		return false;
	}

	/**
	 * Auto-deactivate plugin if requirement not meet and display a notice.
	 */
	public function auto_deactivate() {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		// phpcs:disable
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
		// phpcs:enable
	}

	/**
	 * Plugin activation notice.
	 */
	public function activation_error() {
		?>
		<div class="rank-math-notice notice notice-error">
			<p>
				<?php echo join( '<br>', $this->messages ); // phpcs:ignore ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Define the plugin constants.
	 */
	private function define_constants() {
		define( 'RANK_MATH_PRO_VERSION', $this->version );
		define( 'RANK_MATH_PRO_FILE', __FILE__ );
		define( 'RANK_MATH_PRO_PATH', dirname( RANK_MATH_PRO_FILE ) . '/' );
		define( 'RANK_MATH_PRO_URL', plugins_url( '', RANK_MATH_PRO_FILE ) . '/' );
	}

	/**
	 * Include the required files.
	 */
	private function includes() {
		include dirname( __FILE__ ) . '/vendor/autoload.php';
	}

	/**
	 * Instantiate classes.
	 */
	private function instantiate() {
		new \RankMathPro\Modules();
	}

	/**
	 * Initialize WordPress action hooks.
	 */
	private function init_actions() {
		if ( is_admin() ) {
			add_action( 'rank_math/admin/loaded', [ $this, 'init_admin' ], 15 );
		}

		add_action( 'rest_api_init', [ $this, 'init_rest_api' ] );
		add_action( 'plugins_loaded', [ $this, 'init' ], 11 );
		new \RankMathPro\Common();
	}

	/**
	 * Initialize the admin.
	 */
	public function init_admin() {
		new \RankMathPro\Admin\Admin();
	}

	/**
	 * Load the REST API endpoints.
	 */
	public function init_rest_api() {
		$controllers = [
			new \RankMathPro\Schema\Rest(),
			new \RankMathPro\Analytics\Rest(),
			new \RankMathPro\Rest\Rest(),
		];

		foreach ( $controllers as $controller ) {
			$controller->register_routes();
		}
	}

	/**
	 * Initialize.
	 */
	public function init() {
		if ( Helper::is_module_active( 'image-seo' ) ) {
			new \RankMathPro\Image_Seo_Pro();
		}

		if ( Helper::is_module_active( 'bbpress' ) ) {
			new \RankMathPro\BBPress();
		}

		if ( Helper::is_module_active( 'local-seo' ) ) {
			new \RankMathPro\Local_Seo\Local_Seo();
		}

		if ( Helper::is_module_active( 'analytics' ) && ! $this->is_plan_expired() ) {
			new \RankMathPro\Analytics\Analytics();
		}

		if ( Conditional::is_woocommerce_active() && Helper::is_module_active( 'woocommerce' ) ) {
			new \RankMathPro\WooCommerce();
		}

		if ( Helper::is_module_active( '404-monitor' ) ) {
			new \RankMathPro\Monitor_Pro();
		}

		new \RankMathPro\Plugin_Update\Plugin_Update();
		new \RankMathPro\Thumbnail_Overlays();
	}

	/**
	 * Initialize plugin for localization.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
	 *
	 * Locales found in:
	 *     - WP_LANG_DIR/rank-math/rank-math-LOCALE.mo
	 *     - WP_LANG_DIR/plugins/rank-math-LOCALE.mo
	 */
	public function localization_setup() {
		$locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
		$locale = apply_filters( 'plugin_locale', $locale, 'rank-math-pro' ); // phpcs:ignore

		unload_textdomain( 'rank-math-pro' );
		if ( false === load_textdomain( 'rank-math-pro', WP_LANG_DIR . '/plugins/seo-by-rank-math-pro-' . $locale . '.mo' ) ) {
			load_textdomain( 'rank-math-pro', WP_LANG_DIR . '/seo-by-rank-math/seo-by-rank-math-pro-' . $locale . '.mo' );
		}
		load_plugin_textdomain( 'rank-math-pro', false, rank_math()->plugin_dir() . '/languages/' );
	}

	/**
	 * Check if Rank Math plugin is installed on the site.
	 *
	 * @return boolean Whether it's installed or not.
	 */
	public function is_free_version_installed() {
		// First check if active, because that is less costly.
		if ( $this->is_free_version_activated() ) {
			return true;
		}

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$installed_plugins = get_plugins();

		return array_key_exists( $this->free_version_plugin_path, $installed_plugins );
	}

	/**
	 * Install Rank Math free version from the wordpress.org repository.
	 *
	 * @return bool Whether install was successful.
	 */
	public function install_free_version() {
		include_once ABSPATH . 'wp-admin/includes/misc.php';
		include_once ABSPATH . 'wp-admin/includes/file.php';
		include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		$skin        = new Automatic_Upgrader_Skin();
		$upgrader    = new Plugin_Upgrader( $skin );
		$plugin_file = 'https://downloads.wordpress.org/plugin/seo-by-rank-math.latest-stable.zip';
		$result      = $upgrader->install( $plugin_file );

		return $result;
	}

	/**
	 * Check if Rank Math plugin is activated on the site.
	 *
	 * @return boolean Whether it's active or not.
	 */
	public function is_free_version_activated() {
		$active_plugins = get_option( 'active_plugins', [] );

		if ( in_array( $this->free_version_plugin_path, $active_plugins, true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Checks if WP is in the process of deactivating the free one.
	 *
	 * @return boolean Whether we are in the process of deactivating the plugin or not.
	 */
	public function is_free_version_being_deactivated() {
		// phpcs:disable
		if (
			! empty( $_GET['action'] ) &&
			isset( $_GET['plugin'] ) &&
			$_GET['plugin'] === $this->free_version_plugin_path
		) {
			return true;
		}
		// phpcs:enable

		return false;
	}

	/**
	 * Activate Rank Math free version.
	 *
	 * @return bool Whether activation was successful or not.
	 */
	public function activate_free_version() {
		return activate_plugin( $this->free_version_plugin_path );
	}

	/**
	 * Is free latest version.
	 *
	 * @return bool
	 */
	public function is_free_version_latest() {
		return defined( 'RANK_MATH_VERSION' ) && version_compare( RANK_MATH_VERSION, '1.0.52', '>=' );
	}

	/**
	 * is plan expired.
	 *
	 * @return bool
	 */
	public function is_plan_expired() {
		if ( ! method_exists( '\RankMath\Admin\Admin_Helper', 'is_plan_expired' ) ) {
			return true;
		}

		return \RankMath\Admin\Admin_Helper::is_plan_expired();
	}
}

/**
 * Returns the main instance of RankMathPro to prevent the need to use globals.
 *
 * @return RankMathPro
 */
function rank_math_pro() {
	return RankMathPro::get();
}

// Start it.
rank_math_pro();
