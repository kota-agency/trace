<?php
/**
 * Analytics module.
 *
 * @since      1.0
 * @package    RankMathPro
 * @subpackage RankMathPro
 * @author     Rank Math <support@rankmath.com>
 */

namespace RankMathPro\Analytics;

use Exception;
use RankMath\Helper;
use RankMath\Traits\Hooker;
use RankMath\Admin\Admin_Helper;
use MyThemeShop\Helpers\Str;
use MyThemeShop\Helpers\Param;

// Analytics.
use RankMath\Google\Api;
use RankMath\Analytics\Stats;
use RankMathPro\Google\Adsense;
use RankMathPro\Google\Analytics as Google_Analytics;

defined( 'ABSPATH' ) || exit;

/**
 * Analytics class.
 */
class Analytics {

	use Hooker;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->action( 'rank_math/admin/enqueue_scripts', 'enqueue_analytics' );
		$this->action( 'rank_math/analytics/options/console', 'add_country_dropdown3' );
		$this->action( 'rank_math/analytics/options/analytics', 'add_country_dropdown2' );
		$this->action( 'update_option_rank_math_analytics_last_updated', 'send_summary' );
		$this->action( 'rank_math/analytics/get_analytics_data', 'save_analytics' );
		$this->action( 'rank_math/analytics/get_analytics_only', 'save_analytics' );
		$this->action( 'rank_math/analytics/get_analytics_data', 'save_adsense' );
		$this->action( 'rank_math/analytics/options/analytics_saved', 'run_analytic_installer' );
		$this->filter( 'rank_math/analytics/schedule_gap', 'schedule_gap' );
		$this->filter( 'rank_math/analytics/fetch_gap', 'fetch_gap' );
		$this->filter( 'rank_math/analytics/max_days_allowed', 'data_retention_period' );
		$this->filter( 'rank_math/analytics/options/cahce_control/description', 'change_description' );
		$this->filter( 'rank_math/analytics/check_all_services', 'check_all_services' );
		$this->filter( 'rank_math/analytics/user_preference', 'change_user_preference' );
		$this->filter( 'rank_math/admin/settings/analytics', 'add_new_settings' );

		// Cache.
		$this->action( 'rank_math/analytics/purge_cache', 'purge_cache' );
		$this->action( 'rank_math/analytics/delete_by_days', 'delete_by_days' );
		$this->action( 'rank_math/analytics/delete_data_log', 'delete_data_log' );

		add_filter( 'rank_math/analytics/classic/pro_notice', '__return_empty_string' );
		$this->action( 'cmb2_save_options-page_fields_rank-math-options-general_options', 'sync_global_settings', 25, 2 );

		if ( Helper::has_cap( 'analytics' ) ) {
			$this->action( 'rank_math/admin_bar/items', 'admin_bar_items', 11 );
		}

		Posts::get();
		Keywords::get();
		new Pageviews();
		new Summary();
		new GTag();
		new AJAX();
	}

	/**
	 * Change user perference.
	 *
	 * @param  array $preference Array of preference.
	 * @return array
	 */
	public function change_user_preference( $preference ) {
		Helper::add_json( 'isAdsenseConnected', ! empty( Adsense::get_adsense_id() ) );
		Helper::add_json( 'isLinkModuleActive', Helper::is_module_active( 'link-counter' ) );

		$preference['topKeywords']['ctr']    = false;
		$preference['topKeywords']['ctr']    = false;
		$preference['performance']['clicks'] = false;

		return $preference;
	}

	/**
	 * Purge cache.
	 *
	 * @param object $table Table insance.
	 */
	public function purge_cache( $table ) {
		$table->whereLike( 'option_name', 'losing_posts' )->delete();
		$table->whereLike( 'option_name', 'winning_posts' )->delete();
		$table->whereLike( 'option_name', 'losing_keywords' )->delete();
		$table->whereLike( 'option_name', 'winning_keywords' )->delete();
		$table->whereLike( 'option_name', 'tracked_keywords_summary' )->delete();
	}

	/**
	 * Purge cache.
	 *
	 * @param  int $days Decide whether to delete all or delete 90 days data.
	 */
	public function delete_by_days( $days ) {
		if ( -1 === $days ) {
			DB::traffic()->truncate();
			DB::adsense()->truncate();
		} else {
			$start = date_i18n( 'Y-m-d H:i:s', strtotime( '-1 days' ) );
			$end   = date_i18n( 'Y-m-d H:i:s', strtotime( '-' . $days . ' days' ) );

			DB::traffic()->whereBetween( 'created', [ $end, $start ] )->delete();
			DB::adsense()->whereBetween( 'created', [ $end, $start ] )->delete();
		}
	}

	/**
	 * Delete record for comparison.
	 *
	 * @param string $start Start date.
	 */
	public function delete_data_log( $start ) {
		DB::traffic()->where( 'created', '<', $start )->delete();
		DB::adsense()->where( 'created', '<', $start )->delete();
	}

	/**
	 * Run analytic installer.
	 */
	public function run_analytic_installer() {
		( new Installer() )->install();
		Installer::start_analytics_fetch();
	}

	/**
	 * Data rentention days.
	 *
	 * @return int
	 */
	public function data_retention_period() {
		return 'pro' === Admin_Helper::get_user_plan() ? 180 : 1000;
	}

	/**
	 * Data retrival job gap in seconds.
	 *
	 * @return int
	 */
	public function schedule_gap() {
		return 10;
	}

	/**
	 * Data retrival fetch gap in days.
	 *
	 * @return int
	 */
	public function fetch_gap() {
		return 3;
	}

	/**
	 * Fetch adsense account.
	 *
	 * @param  array $result Result array.
	 * @return array
	 */
	public function check_all_services( $result ) {
		$result['adsenseAccounts'] = Adsense::get_adsense_accounts();

		return $result;
	}

	/**
	 * Add admin bar item.
	 *
	 * @param Admin_Bar_Menu $menu Menu class instance.
	 */
	public function admin_bar_items( $menu ) {
		if ( is_single() ) {
			$menu->add_sub_menu(
				'post_analytics',
				[
					'title'    => esc_html__( 'Post Analytics', 'rank-math-pro' ),
					'href'     => Helper::get_admin_url( 'analytics#/single/' . get_the_ID() ),
					'meta'     => [ 'title' => esc_html__( 'Analytics Report', 'rank-math-pro' ) ],
					'priority' => 20,
				]
			);
		}
	}

	/**
	 * Enqueue scripts for the metabox.
	 */
	public function enqueue_analytics() {
		$screen = get_current_screen();
		if ( 'rank-math_page_rank-math-analytics' !== $screen->id ) {
			return;
		}

		$url = RANK_MATH_PRO_URL . 'includes/modules/analytics/assets/';
		wp_enqueue_style(
			'rank-math-pro-analytics',
			$url . 'css/stats.css',
			null,
			rank_math()->version
		);

		wp_enqueue_script(
			'rank-math-pro-analytics',
			$url . 'js/stats.js',
			[
				'wp-components',
				'wp-element',
				'wp-i18n',
				'wp-date',
				'wp-html-entities',
				'wp-api-fetch',
			],
			rank_math()->version,
			true
		);
	}

	/**
	 * Add country dropdown.
	 */
	public function add_country_dropdown3() {
		$profile = wp_parse_args(
			get_option( 'rank_math_google_analytic_profile' ),
			[
				'profile' => '',
				'country' => 'all',
			]
		);
		?>
		<div class="cmb-row-col">
			<label for="site-console-country"><?php esc_html_e( 'Country', 'rank-math-pro' ); ?></label>
			<select class="cmb2_select site-console-country notrack" name="site-console-country" id="site-console-country" disabled="disabled">
				<?php foreach ( Helper::choices_countries_3() as $code => $label ) : ?>
					<option value="<?php echo $code; ?>"<?php selected( $profile['country'], $code ); ?>><?php echo $label; ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php
	}

	/**
	 * Add country dropdown.
	 */
	public function add_country_dropdown2() {
		$analytics = wp_parse_args(
			get_option( 'rank_math_google_analytic_options' ),
			[
				'adsense_id'       => '',
				'account_id'       => '',
				'property_id'      => '',
				'view_id'          => '',
				'country'          => 'all',
				'install_code'     => false,
				'anonymize_ip'     => false,
				'exclude_loggedin' => false,
			]
		);
		?>
		<div class="cmb-row-col country-option">
			<label for="site-analytics-country"><?php esc_html_e( 'Country', 'rank-math-pro' ); ?></label>
			<select class="cmb2_select site-analytics-country notrack" name="site-analytics-country" id="site-analytics-country" disabled="disabled">
				<?php foreach ( Helper::choices_countries() as $code => $label ) : ?>
					<option value="<?php echo $code; ?>"<?php selected( $analytics['country'], $code ); ?>><?php echo $label; ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php
	}

	/**
	 * Send analytics summary to RankMath.com.
	 */
	public function send_summary() {
		if ( ! Helper::get_settings( 'general.sync_global_setting' ) ) {
			return;
		}

		$registered = Admin_Helper::get_registration_data();
		if ( $registered && isset( $registered['username'] ) && isset( $registered['api_key'] ) ) {
			Stats::get()->set_date_range( '-30 days' );
			$stats = Stats::get()->get_analytics_summary();
			\RankMathPro\Admin\Api::get()->send_summary(
				[
					'username'    => $registered['username'],
					'api_key'     => $registered['api_key'],
					'site_url'    => esc_url( site_url() ),
					'impressions' => array_values( $stats['impressions'] ),
					'clicks'      => array_values( $stats['clicks'] ),
					'keywords'    => array_values( $stats['keywords']->keywords ),
					'pageviews'   => array_values( $stats['pageviews'] ),
					'adsense'     => array_values( $stats['adsense'] ),
				]
			);
		}
	}

	/**
	 * Get analytics and save it into database.
	 *
	 * @param string $date Date to fetch data for.
	 */
	public function save_analytics( $date ) {
		$rows = Google_Analytics::get_analytics( $date, $date );
		if ( empty( $rows ) ) {
			return;
		}

		try {
			DB::add_analytics_bulk( $date, $rows );
		} catch ( Exception $e ) {} // phpcs:ignore
	}

	/**
	 * Get adsense and save it into database.
	 *
	 * @param string $date Date to fetch data for.
	 */
	public function save_adsense( $date ) {
		$rows = Adsense::get_adsense( $date, $date );
		if ( empty( $rows ) ) {
			return;
		}

		try {
			DB::add_adsense( $date, $rows );
		} catch ( Exception $e ) {} // phpcs:ignore
	}

	/**
	 * Change option description.
	 */
	public function change_description() {
		return __( 'Enter the number of days to keep Analytics data in your database. The maximum allowed days are 180. Though, 2x data will be stored in the DB for calculating the difference properly.', 'rank-math-pro' );
	}

	/**
	 * Add new settings.
	 *
	 * @param object $cmb CMB2 instance.
	 */
	public function add_new_settings( $cmb ) {
		$cmb->add_field(
			[
				'id'      => 'sync_global_setting',
				'type'    => 'toggle',
				'name'    => esc_html__( 'Monitor SEO Performance', 'rank-math-pro' ),
				/* translators: Link to kb article */
				'desc'    => sprintf(
					wp_kses_post( __( 'This option allows you to monitor the SEO performance of all of your sites in one centralized dashboard on RankMath.com, so you can check up on sites at a glance. <a href="%1$s" target="_blank">Learn more</a>.', 'rank-math-pro' ) ),
					'https://rankmath.com/kb/analytics/'
				),
				'default' => 'off',
			]
		);
	}


		/**
		 * Check if certain fields got updated.
		 *
		 * @param int   $object_id The ID of the current object.
		 * @param array $updated   Array of field ids that were updated.
		 *                         Will only include field ids that had values change.
		 */
		public function sync_global_settings( $object_id, $updated ) {
			if ( in_array( 'sync_global_setting', $updated, true ) ) {
				\RankMathPro\Admin\Api::get()->sync_setting(
					cmb2_get_option( $object_id, 'sync_global_setting' )
				);
			}
		}
}
