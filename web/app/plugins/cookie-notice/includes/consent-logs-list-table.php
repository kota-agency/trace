<?php
// exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Cookie_Notice_Consent_Logs_List_Table class.
 *
 * @class Cookie_Notice_Consent_Logs_List_Table
 */
class Cookie_Notice_Consent_Logs_List_Table extends WP_List_Table {

	/**
	 * Display content.
	 *
	 * @return void
	 */
	public function views() {
		// get main instance
		$cn = Cookie_Notice();

		$message = __( 'The table below shows the consent records from your website accumulated from the last thirty days. You can view individual records by expanding a single row of data.', 'cookie-notice' );

		// disable if basic plan and data older than 7 days
		if ( $cn->get_subscription() === 'basic' )
			$message .= '<br/><span class="cn-asterix">*</span> ' . __( 'Note: domains using Cookie Compliance limited, Basic plan allow you to view consent records from the last 7 days and store data only for 30 days.', 'cookie-notice' );

		echo '<p class="description">' . wp_kses_post( $message ) . '</p>';
	}

	/**
	 * Prepare the items for the table to process.
	 *
	 * @return void
	 */
	public function prepare_items() {
		// get main instance
		$cn = Cookie_Notice();

		// get consent logs
		if ( is_multisite() && $cn->is_network_admin() && $cn->is_plugin_network_active() && $cn->network_options['global_override'] )
			$analytics = get_site_option( 'cookie_notice_app_analytics', [] );
		else
			$analytics = get_option( 'cookie_notice_app_analytics', [] );

		// get date format
		$format = get_option( 'date_format' );

		// get 30 days of default data
		$logs = $this->fill_missing_dates( $format );

		// any data?
		if ( ! empty( $analytics['consentActivities'] ) && is_array( $analytics['consentActivities'] ) ) {
			foreach ( $analytics['consentActivities'] as $index => $entry ) {
				// get date in digits only
				$digits = (int) str_replace( '-', '', substr( $entry->eventdt, 0, 10 ) );

				// current data?
				if ( array_key_exists( $digits, $logs ) ) {
					$logs[$digits]['level_' . (int) $entry->consentlevel] = (int) $entry->totalrecd;
					$logs[$digits]['total'] += (int) $entry->totalrecd;
				}
			}

			krsort( $logs, SORT_NUMERIC );
		}

		$this->_column_headers = [ $this->get_columns(), [], $this->get_sortable_columns(), 'date' ];

		usort( $logs, [ $this, 'usort_reorder' ] );

		$this->items = $logs;
	}

	/**
	 * Fill missing dates.
	 *
	 * @param string $format
	 * @return array
	 */
	private function fill_missing_dates( $format ) {
		$empty_logs = [];

		// get current date
		$d = new DateTime();

		// go back 30 days
		$d->modify( '-30 days' );

		// update dates for last 30 days
		for ( $i = 1; $i <= 31; $i++ ) {
			$date = $d->format( 'Y-m-d' );
			$digits = (int) str_replace( '-', '', $date );

			$empty_logs[$digits] = [
				'slug'		=> $digits,
				'date'		=> date_i18n( $format, strtotime( $date ) ),
				'date_iso'	=> $date,
				'level_1'	=> 0,
				'level_2'	=> 0,
				'level_3'	=> 0,
				'total'		=> 0
			];

			$d->modify( '+1 days' );
		}

		return $empty_logs;
	}

	/**
	 * Sort consent logs.
	 *
	 * @param int $first
	 * @param int $second
	 * @return array
	 */
	public function usort_reorder( $first, $second ) {
		// get orderby
		$orderby = ( ! empty( $_GET['orderby'] ) ) ? sanitize_key( $_GET['orderby'] ) : 'date';

		// skip invalid orderby
		if ( ! array_key_exists( $orderby, $this->get_sortable_columns() ) )
			return 0;

		// get order
		$order = ( ! empty( $_GET['order'] ) ) ? sanitize_key( $_GET['order'] ) : 'desc';

		// use numeric value for dates
		if ( $orderby === 'date' )
			$orderby = 'slug';

		// determine sort order
		if ( $first[$orderby] === $second[$orderby] )
			$result = 0;
		else
			$result = ( $first[$orderby] < $second[$orderby] ) ? -1 : 1;

		return ( $order === 'asc' ) ? $result : -$result;
	}

	/**
	 * Override the parent columns method. Defines the columns to use in your listing table.
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = [
			'cb'		=> '',
			'date'		=> __( 'Date', 'cookie-notice' ),
			'level_1'	=> __( 'Level 1', 'cookie-notice' ),
			'level_2'	=> __( 'Level 2', 'cookie-notice' ),
			'level_3'	=> __( 'Level 3', 'cookie-notice' ),
			'total'		=> __( 'Total', 'cookie-notice' )
		];

		return $columns;
	}

	/**
	 * Define the sortable columns
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		return [
			'date'		=> [ 'date', false ],
			'level_1'	=> [ 'level_1', true ],
			'level_2'	=> [ 'level_2', true ],
			'level_3'	=> [ 'level_3', true ],
			'total'		=> [ 'total', true ]
		];
	}

	/**
	 * Display single row.
	 *
	 * @param array $item
	 * @return void
	 */
	public function single_row( $item ) {
		$disabled = false;

		// disable if basic plan and data older than 7 days
		if ( Cookie_Notice()->get_subscription() === 'basic' ) {
			$last_date = strtotime( '-7 day' );
			$event_date = strtotime( $item[ 'date_iso' ] );

			if ( $event_date < $last_date )
				$disabled = true;
		}

		echo '
		<tr id="cn_consent_log_' . esc_attr( $item['slug'] ) . '" class="cn-consent-log' . ( $disabled ? ' disabled' : '' ) . '" data-date="' . esc_attr( $item['date_iso'] ) . '">';

		$this->single_row_columns( $item );

		echo '
		</tr>';
	}

	/**
	 * Define what data to show on each column of the table.
	 *
	 * @param array $item
	 * @param string $column_name
	 * @return string
	 */
	public function column_default( $item, $column_name ) {
		return esc_html( $item[$column_name] );
	}

	/**
	 * Define what data to show on cb column of the table.
	 *
	 * @param array $item
	 * @return string
	 */
	function column_cb( $item ) {
		$disabled = false;

		// disable if no data
		if ( $item['total'] === 0 )
			$disabled = true;

		return '
		<label for="cn-consent-log-' . esc_attr( $item['slug'] ) . '" class="cn-consent-log-item' . ( $disabled ? ' disabled' : '' ) . '">
			<input id="cn-consent-log-' . esc_attr( $item['slug'] ) . '" type="checkbox">
			<span class="cn-consent-log-head"></span>
		</label>';
	}

	/**
	 * Display bulk actions.
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		return [];
	}

	/**
	 * Display empty result.
	 *
	 * @return void
	 */
	public function no_items() {
		echo __( 'No consent logs found.', 'cookie-notice' );
	}
}
