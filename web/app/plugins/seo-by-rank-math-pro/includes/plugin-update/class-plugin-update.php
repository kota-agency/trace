<?php
/**
 * Plugin update class
 *
 * @since      1.0
 * @package    RankMathPro
 * @subpackage RankMathPro\Admin
 * @author     Rank Math <support@rankmath.com>
 */

namespace RankMathPro\Plugin_Update;

use DOMDocument;
use RankMath\Helper;
use RankMath\Traits\Hooker;
use RankMath\Admin\Admin_Helper;

defined( 'ABSPATH' ) || exit;

/**
 * Plugin_Update class
 */
class Plugin_Update {

	use Hooker;

	/**
	 * Plugin slug.
	 *
	 * @var string
	 */
	private $slug = 'seo-by-rank-math-pro';

	/**
	 * Rank Math API URL.
	 *
	 * @var string
	 */
	private $api_url = 'https://rankmath.com/wp-json/rankmath/v1';

	/**
	 * The Constructor
	 */
	public function __construct() {
		$this->action( 'admin_notices', 'admin_license_notice', 20 );
		$this->filter( 'plugin_action_links_' . plugin_basename( RANK_MATH_PRO_FILE ), 'plugin_action_links', 50 );
		$this->filter( 'current_screen', 'maybe_check_for_update' );
		$this->filter( 'site_transient_update_plugins', 'maybe_inject_update', 20, 1 );
		$this->filter( 'site_transient_update_plugins', 'maybe_disable_update', 90, 1 );
		$this->filter( 'plugins_api', 'filter_info', 10, 3 );
		$this->action( 'in_plugin_update_message-' . plugin_basename( RANK_MATH_PRO_FILE ), 'in_plugin_update_message', 10, 2 );
		$this->action( 'add_option_rank_math_connect_data', 'check_for_update' );
		$this->action( 'update_option_rank_math_connect_data', 'check_for_update' );
		$this->action( 'delete_option_rank_math_connect_data', 'check_for_update' );
	}

	/**
	 * Add connect/activation notice.
	 */
	public function admin_license_notice() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( $this->is_block_editor_page() ) {
			return;
		}

		if ( ! Helper::is_site_connected() ) {
			?>
			<div class="notice notice-success rank-math-notice">
				<p>
					<?php
					// translators: 1: opening HTML anchor tag, 2: closing HTML anchor tags.
					echo wp_kses_post( sprintf( __( 'Rank Math Pro is installed but not activated yet. %1$sActivate now%2$s. It only takes 20 seconds!', 'rank-math-pro' ), '<a href="' . esc_url( Admin_Helper::get_activate_url() ) . '">', '</a>' ) );
					?>
				</p>
			</div>
			<?php
			return;
		}
	}

	/**
	 * Check if we are on Block Editor page.
	 */
	private function is_block_editor_page() {
		$current_screen = get_current_screen();

		if ( method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
			return true;
		}

		if ( function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() ) {
			return true;
		}

		return false;
	}

	/**
	 * Add Connect/Activate action link
	 *
	 * @param array $links Action links.
	 */
	public function plugin_action_links( $links ) {
		if ( ! Helper::is_site_connected() ) {
			$links['activate_license'] = sprintf( '<a href="%s" class="rank-math-pro-activate-link" style="color:green">%s</a>', esc_url( Admin_Helper::get_activate_url( network_admin_url( 'plugins.php' ) ) ), __( 'Enable updates', 'rank-math-pro' ) );
		}

		return $links;
	}

	/**
	 * Manual check for updates.
	 *
	 * @return void
	 */
	public function maybe_check_for_update() {
		$force_check = ! empty( $_GET['force-check'] );
		if ( $force_check ) {
			$on_pages = [
				'rank-math_page_rank-math-status',
				'toplevel_page_rank-math',
				'plugins',
				'plugins-network',
				'update-core',
				'network-update-core',
			];

			$screen = get_current_screen();
			if ( in_array( $screen->id, $on_pages, true ) ) {
				$this->check_for_update();
			}
		}
	}

	/**
	 * Check for updates & inject to update_plugins transient.
	 *
	 * @return bool
	 */
	public function check_for_update() {
		$transient = get_site_transient( 'update_plugins' );
		$this->inject_update( $this->fetch_latest_version( true ), $transient );
	}

	/**
	 * Inject update fetched from the rankmath.com API or pushed to this site via the REST API.
	 *
	 * @param object $transient Origial transient.
	 * @return mixed
	 */
	public function maybe_inject_update( $transient ) {
		// Don't run more than once per page load.
		$this->remove_filter( 'site_transient_update_plugins', 'maybe_inject_update', 20 );

		$pushed = get_site_option( 'rank_math_pro_pushed_updates', false );
		if ( ! empty( $pushed ) ) {
			$transient = $this->inject_update( $pushed, $transient );
		}

		return $this->inject_update( $this->fetch_latest_version(), $transient );
	}

	/**
	 * Remove package download URL if needed.
	 *
	 * @param object $transient
	 * @return void
	 */
	public function maybe_disable_update( $transient ) {
		// Don't run more than once.
		$this->remove_filter( 'site_transient_update_plugins', 'maybe_disable_update', 90 );

		if ( isset( $transient->response['seo-by-rank-math/rank-math.php'] ) && isset( $transient->response['seo-by-rank-math-pro/rank-math-pro.php'] ) ) {
			unset( $transient->response['seo-by-rank-math-pro/rank-math-pro.php']->package );
			$transient->response['seo-by-rank-math-pro/rank-math-pro.php']->unavailability_reason = 'update_free';
		}

		return $transient;
	}

	/**
	 * Inject our update in the update_plugins transient.
	 *
	 * @param  mixed $update New update object or array, or false to clear current update.
	 * @return array
	 */
	public function inject_update( $update, $transient ) {
		$plugin = plugin_basename( RANK_MATH_PRO_FILE );

		if ( false !== $update ) {
			$obj = $this->get_default_update_data();
			$obj = (object) array_merge( (array) $obj, (array) $update );

			// If a newer version is already present in the update_plugins transient then don't inject.
			if ( $this->has_newer_version( $transient, $obj->new_version ) ) {
				return $transient;
			}

			// Inject if new data has URL and is a newer version than the one currently in use.
			if ( version_compare( $obj->new_version, RANK_MATH_PRO_VERSION, '>' ) ) {
				$transient->response[ $plugin ] = $obj;
				set_site_transient( 'update_plugins', $transient );
			}
		} elseif ( isset( $transient->response[ $plugin ] ) ) {
			unset( $transient->response[ $plugin ] );
		}

		return $transient;
	}

	/**
	 * Check if the transient already contains newer version of the plugin.
	 *
	 * @param object $transient   Site transient: 'update_plugins'.
	 * @param string $new_version New version we check against, e.g. '1.2'.
	 * @return boolean
	 */
	public function has_newer_version( $transient, $new_version ) {
		$plugin = plugin_basename( RANK_MATH_PRO_FILE );

		return isset( $transient->response[ $plugin ] )
			&& is_object( $transient->response[ $plugin ] )
			&& isset( $transient->response[ $plugin ]->new_version )
			&& version_compare( $transient->response[ $plugin ]->new_version, $new_version, '>' );
	}

	/**
	 * Filter plugin information.
	 *
	 * @param false|object|array $result The result object or array. Default false.
	 * @param string             $action The type of information being requested from the Plugin Installation API.
	 * @param object             $args   Plugin API arguments.
	 * @return false|object      false or Response object.
	 */
	public function filter_info( $result, $action, $args ) {
		if ( ! isset( $args->slug ) || ! ( $this->slug === $args->slug && 'plugin_information' === $action ) ) {
			return $result;
		}

		$information = $this->get_default_plugin_info();

		$fetched = $this->fetch_plugin_info( true, isset( $args->locale ) ? $args->locale : '' );
		if ( is_object( $fetched ) ) {
			$information = (object) array_merge( (array) $information, (array) $fetched );
		}

		return $information;
	}

	/**
	 * Get default plugin info.
	 *
	 * @return object
	 */
	private function get_default_plugin_info() {
		$description  = '<p><strong>' . __( 'Rank Math SEO PRO For WordPress', 'rank-math-pro' ) . '</strong><br />';
		$description .= '★★★★★</p>';
		$description .= '<p><strong>' . __( 'SEO is the most consistent source of traffic for any website', 'rank-math-pro' ) . '.</strong> ';
		// Translators: placeholders are the anchor tag opening and closing.
		$description .= sprintf( __( 'We created %1$sRank Math, a WordPress SEO plugin%2$s, to help every website owner get access to the SEO tools they need to improve their SEO and attract more traffic to their website.', 'rank-math-pro' ), '<a href="https://rankmath.com/wordpress/plugin/seo-suite/?utm_source=LP&amp;utm_campaign=WP" rel="nofollow ugc"><strong>', '</strong></a>' ) . '</p>';

		$plugin_info = [
			'external' => true,
			'name'     => 'Rank Math SEO PRO',
			'slug'     => $this->slug,
			'author'   => '<a href="https://rankmath.com/">Rank Math</a>',
			'homepage' => 'https://rankmath.com/',
			'banners'  => [
				'low'  => 'https://ps.w.org/seo-by-rank-math/assets/banner-772x250.png',
				'high' => 'https://ps.w.org/seo-by-rank-math/assets/banner-1544x500.png',
			],
			'sections' => [
				'description' => $description,
			],
		];

		return (object) $plugin_info;
	}

	private function get_default_update_data() {
		$plugin = plugin_basename( RANK_MATH_PRO_FILE );

		$update = [
			'slug'        => $this->slug,
			'plugin'      => $plugin,
			'url'         => 'https://rankmath.com/',
			'icons'       => [
				'svg' => 'https://ps.w.org/seo-by-rank-math/assets/icon.svg',
				'1x'  => 'https://ps.w.org/seo-by-rank-math/assets/icon-128x128.png',
				'2x'  => 'https://ps.w.org/seo-by-rank-math/assets/icon-256x256.png',
			],
			'new_version' => '',
			'package'     => '',
		];

		return (object) $update;
	}

	/**
	 * Checks the license manager to see if there is an update available for this product.
	 *
	 * @return object|bool If there is an update, returns the license information.
	 *                     Otherwise returns false.
	 */
	public function fetch_latest_version( $force_check = false ) {
		$stored = get_site_transient( 'rank_math_pro_updates' );
		if ( ! $force_check && ! empty( $stored ) ) {
			return $stored;
		}

		$params = [
			'site_url'     => is_multisite() ? network_site_url() : home_url(),
			'product_slug' => $this->slug,
		];

		$this->maybe_add_auth_params( $params );

		// Send the request.
		$response = wp_remote_post(
			$this->api_url . '/updateCheck/',
			[
				'timeout' => defined( 'DOING_CRON' ) && DOING_CRON ? 30 : 10,
				'body'    => $params,
			]
		);

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$response_body = wp_remote_retrieve_body( $response );
		$result        = json_decode( $response_body, true );
		if ( ! is_array( $result ) || ! isset( $result['new_version'] ) ) {
			return false;
		}

		set_site_transient( 'rank_math_pro_updates', $result, 3600 );
		return $result;
	}


	/**
	 * Checks the license manager to see if there is an update available for this product.
	 *
	 * @return object|bool If there is an update, returns the license information.
	 *                     Otherwise returns false.
	 */
	public function fetch_plugin_info( $force_check = false, $locale = '' ) {
		if ( ! $locale ) {
			$locale = get_locale();
		}

		$stored = get_site_transient( 'rank_math_pro_info_' . $locale );
		if ( ! $force_check && ! empty( $stored ) ) {
			return $stored;
		}

		$params = [
			'product_slug' => $this->slug,
			'locale'       => $locale,
			'site_url'     => is_multisite() ? network_site_url() : home_url(),
		];

		$this->maybe_add_auth_params( $params );

		// Send the request.
		$response = wp_remote_post(
			$this->api_url . '/pluginInfo/',
			[
				'timeout' => defined( 'DOING_CRON' ) && DOING_CRON ? 30 : 10,
				'body'    => $params,
			]
		);

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$response_body = wp_remote_retrieve_body( $response );

		// We do assoc=true and then cast to object to keep sub-items as array.
		$result = (object) json_decode( $response_body, true );

		if ( ! is_object( $result ) ) {
			return false;
		}

		set_site_transient( 'rank_math_pro_info_' . $locale, $result, 3600 );
		return $result;
	}

	/**
	 * Add username & api key if the site is connected.
	 *
	 * @param array $params Params passed by reference.
	 * @return void
	 */
	private function maybe_add_auth_params( &$params ) {
		$registered = Admin_Helper::get_registration_data();
		if ( $registered && isset( $registered['username'] ) && isset( $registered['api_key'] ) ) {
			$params['username'] = $registered['username'];
			$params['api_key']  = $registered['api_key'];
		}
	}

	/**
	 * Add additional text to notice if download is not available and account is connected.
	 *
	 * @param  array  $plugin_data An array of plugin metadata.
	 * @param  object $response    An array of metadata about the available plugin update.
	 */
	public function in_plugin_update_message( $plugin_data, $response ) {
		if ( current_user_can( 'update_plugins' ) && Helper::is_site_connected() && empty( $response->package ) ) {
			$unavailability_reasons = [
				'update_free'    => __( 'Please update the free version before updating Rank Math SEO PRO.', 'rank-math-pro' ),
				'not_subscribed' => sprintf(
					/* translators: 1: Plugin name, 2: Pricing Link's opening HTML anchor tag, 3: Pricing Link's closing HTML anchor tag. */
					__( 'It seems that you don\'t have an active subscription for %1$s. Please see %2$sdetails and pricing%3$s.', 'rank-math-pro' ),
					$plugin_data['Name'],
					'<a href="https://rankmath.com/pricing/">',
					'</a>'
				),
				'not_connected' => sprintf(
					/* translators: 1: Pricing Link's opening HTML anchor tag, 2: Plugin name, 3: Pricing Link's closing HTML anchor tag. */
					__( 'Please %1$sconnect %2$s%3$s for automatic updates.', 'rank-math-pro' ),
					$plugin_data['Name'],
					'<a href="#">',
					'</a>'
				),
			];

			if ( isset( $response->unavailability_reason ) && isset( $unavailability_reasons[ $response->unavailability_reason ] ) ) {
				echo ' ' . wp_kses_post( $unavailability_reasons[ $response->unavailability_reason ] );
				return;
			}
		}
	}
}
