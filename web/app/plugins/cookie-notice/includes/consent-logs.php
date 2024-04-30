<?php
// exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Cookie_Notice_Consent_Logs class.
 *
 * @class Cookie_Notice_Consent_Logs
 */
class Cookie_Notice_Consent_Logs {

	/**
	 * Class constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		// actions
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ], 11 );
		add_action( 'wp_ajax_cn_get_consent_logs_by_date', [ $this, 'get_consent_logs_by_date' ] );
	}

	/**
	 * Load default scripts and styles.
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts() {
		// get main instance
		$cn = Cookie_Notice();

		// get cookie compliance status
		$status = $cn->get_status();

		// pagination script
		wp_enqueue_script( 'cookie-notice-admin-pagination', COOKIE_NOTICE_URL . '/assets/pagination/pagination.min.js', [ 'jquery' ], $cn->defaults['version'] );
		wp_enqueue_style( 'cookie-notice-admin-pagination', COOKIE_NOTICE_URL . '/assets/pagination/pagination.css', [], $cn->defaults['version'] );
	}

	/**
	 * Get consent by date via AJAX request.
	 *
	 * @return void
	 */
	public function get_consent_logs_by_date() {
		// valid nonce?
		if ( ! check_ajax_referer( 'cn-get-consent-logs', 'nonce' ) )
			wp_send_json_error();

		// check data
		if ( ! isset( $_POST['action'], $_POST['date'], $_POST['nonce'] ) )
			wp_send_json_error();

		// check capability
		if ( ! current_user_can( apply_filters( 'cn_manage_cookie_notice_cap', 'manage_options' ) ) )
			wp_send_json_error();

		// sanitize date
		$date = preg_replace( '[^\d-]', '', $_POST['date'] );

		// get datetime
		$dt = DateTime::createFromFormat( 'Y-m-d', $date );

		// valid date?
		if ( $dt && $dt->format( 'Y-m-d' ) === $date ) {
			$data = Cookie_Notice()->welcome_api->get_consent_logs_by_date( $date );

			if ( is_array( $data ) )
				wp_send_json_success( $this->get_consent_logs_table( $data ) );
			else
				wp_send_json_error( $data );
		}

		wp_send_json_error();
	}

	/**
	 * Get single row template.
	 *
	 * @return string
	 */
	public function get_consent_logs_table( $data ) {
		$html = '
		<table class="wp-list-table widefat fixed striped table-view-list toplevel_page_cookie-notice">
			<thead>
				<tr>
					<th id="cn_consent_id">' . esc_html__( 'Consent ID', 'cookie-notice' ) . '</th>
					<th id="cn_consent_id">' . esc_html__( 'Consent Level', 'cookie-notice' ) . '</th>
					<th id="cn_consent_id">' . esc_html__( 'Categories', 'cookie-notice' ) . '</th>
					<th id="cn_consent_id">' . esc_html__( 'Duration', 'cookie-notice' ) . '</th>
					<th id="cn_consent_id">' . esc_html__( 'Time', 'cookie-notice' ) . '</th>
				</tr>
			</thead>
			<tbody>';

		// no data?
		if ( empty( $data ) ) {
			$html .= '
				<tr>
					<td colspan="5">' . esc_html__( 'No consent logs found.', 'cookie-notice' ) . '</td>
				</tr>';
		} else {
			// set current timezone
			$current_timezone = new DateTimeZone( Cookie_Notice()->dashboard->timezone_string() );

			foreach ( $data as $no => $consent_log ) {
				$categories = [];

				if ( $consent_log->ev_essential )
					$categories[] = esc_html__( 'Basic Operations', 'cookie-notice' );

				if ( $consent_log->ev_functional )
					$categories[] = esc_html__( 'Content Personalization', 'cookie-notice' );

				if ( $consent_log->ev_analytics )
					$categories[] = esc_html__( 'Site Optimization', 'cookie-notice' );

				if ( $consent_log->ev_marketing )
					$categories[] = esc_html__( 'Ad Personalization', 'cookie-notice' );

				// get current date
				$timestamp = new DateTime( $consent_log->timestamp );
				$timestamp->setTimezone( $current_timezone );

				// get deuration in days
				$duration = (int) $consent_log->ev_eventdetails_expiry;

				if ( $duration === 30 )
					$duration = __( '1 month', 'cookie-notice' );
				elseif ( $duration === 90 )
					$duration = __( '3 months', 'cookie-notice' );
				elseif ( $duration === 182 )
					$duration = __( '6 months', 'cookie-notice' );
				elseif ( $duration === 365 )
					$duration = __( '1 year', 'cookie-notice' );
				elseif ( $duration === 730 )
					$duration = __( '2 years', 'cookie-notice' );

				$html .= '
				<tr' . ( $no > 9 ? ' style="display: none"' : '' ) . '>
					<td>' . esc_html( $consent_log->ev_eventdetails_consentid ) . '</td>
					<td>' . sprintf( esc_html__( 'Level %d', 'cookie-notice' ), $consent_log->ev_consentlevel ) . '</td>
					<td>' . implode( ', ', $categories ) . '</td>
					<td>' . esc_html( $duration ) . '</td>
					<td>' . esc_html( $timestamp->format( get_option( 'time_format' ) ) ) . '</td>
				</tr>';
			}
		}

		$html .= '
			</tbody>
		</table>';

		return $html;
	}

	/**
	 * Get single row template.
	 *
	 * @return string
	 */
	public function get_single_row_template() {
		return '
		<tr id="" class="cn-consent-log-details">
			<th></th>
			<td colspan="5">
				<div class="cn-consent-logs-data loading">
					<span class="spinner is-active"></span>
				</div>
			</td>
		</tr>';
	}

	/**
	 * Get error template.
	 *
	 * @return string
	 */
	public function get_error_template() {
		return '<p class="description">' . esc_html__( 'We were unable to download consent logs due to an error. Please try again later.', 'cookie-notice' ) . '</p>';
	}
}
