<?php
/**
 * The CSV Import class.
 *
 * @since      1.0
 * @package    RankMathPro
 * @subpackage RankMathPro\Admin
 * @author     Rank Math <support@rankmath.com>
 */

namespace RankMathPro\Admin\CSV_Import_Export_Redirections;

defined( 'ABSPATH' ) || exit;

/**
 * CSV Importer class.
 *
 * @codeCoverageIgnore
 */
class Importer {

	/**
	 * Term slug => ID cache.
	 *
	 * @var array
	 */
	private static $term_ids = [];

	/**
	 * Settings array. Default values.
	 *
	 * @var array
	 */
	private $settings = [];

	/**
	 * Lines in the CSV that could not be imported for any reason.
	 *
	 * @var array
	 */
	private $failed_rows = [];

	/**
	 * Lines in the CSV that could be imported successfully.
	 *
	 * @var array
	 */
	private $imported_rows = [];

	/**
	 * Error messages.
	 *
	 * @var array
	 */
	private $errors = [];

	/**
	 * Start import from file.
	 *
	 * @param string $file     Path to temporary CSV file.
	 * @param string $settings Import settings.
	 * @return void
	 */
	public function start( $file, $settings = [] ) {
		update_option( 'rank_math_csv_import_redirections', $file );
		update_option( 'rank_math_csv_import_redirections_settings', $settings );
		delete_option( 'rank_math_csv_import_redirections_status' );

		$this->settings        = apply_filters( 'rank_math/admin/csv_import_redirections_settings', $settings );

		$lines = $this->count_lines( $file );
		update_option( 'rank_math_csv_import_redirections_total', $lines );
		Import_Background_Process::get()->start( $lines );
	}

	/**
	 * Count all lines in CSV file.
	 *
	 * @param mixed $file Path to CSV.
	 * @return int
	 */
	public function count_lines( $file ) {
		$file = new \SplFileObject( $file );
		while ( $file->valid() ) {
			$file->fgets();
		}

		$count = $file->key();

		// Check if last line is empty.
		$file->seek( $count );
		$contents = $file->current();
		if ( empty( trim( $contents ) ) ) {
			$count--;
		}

		// Unlock file.
		$file = null;

		return $count;
	}

	/**
	 * Get specified line from CSV.
	 *
	 * @param string $file Path to file.
	 * @param int    $line Line number.
	 * @return string
	 */
	public function get_line( $file, $line ) {
		if ( empty( $this->spl ) ) {
			$this->spl = new \SplFileObject( $file );
		}

		if ( ! $this->spl->eof() ) {
			$this->spl->seek( $line );
			$contents = $this->spl->current();
		}

		return $contents;
	}

	/**
	 * Parse and return column headers (first line in CSV).
	 *
	 * @param string $file Path to file.
	 * @return array
	 */
	public function get_column_headers( $file ) {
		if ( ! empty( $this->column_headers ) ) {
			return $this->column_headers;
		}

		if ( empty( $this->spl ) ) {
			$this->spl = new \SplFileObject( $file );
		}

		if ( ! $this->spl->eof() ) {
			$this->spl->seek( 0 );
			$contents = $this->spl->current();
		}

		if ( empty( $contents ) ) {
			return [];
		}

		$this->column_headers = array_map( 'trim', explode( ',', $contents ) );
		return $this->column_headers;
	}

	/**
	 * Import specified line.
	 *
	 * @param int $line_number Selected line number.
	 * @return void
	 */
	public function import_line( $line_number ) {
		// Skip headers.
		if ( $line_number == 0 ) {
			return;
		}

		$file = get_option( 'rank_math_csv_import_redirections' );
		if ( ! $file ) {
			$this->add_error( esc_html__( 'Missing import file.', 'rank-math-pro' ), 'missing_file' );
			CSV_Import_Export_Redirections::cancel_import( true );
			return;
		}

		$headers  = $this->get_column_headers( $file );
		if ( empty( $headers ) ) {
			$this->add_error( esc_html__( 'Missing CSV headers.', 'rank-math-pro' ), 'missing_headers' );
			return;
		}

		$required_columns = [ 'source', 'destination' ];
		if ( count( array_intersect( $headers, $required_columns ) ) !== count( $required_columns ) ) {
			$this->add_error( esc_html__( 'Missing one ore more required columns.', 'rank-math-pro' ), 'missing_required_columns' );
			return;
		}

		$raw_data = $this->get_line( $file, $line_number );
		if ( empty( $raw_data ) ) {
			$total_lines = (int) get_option( 'rank_math_csv_import_redirections_total' );

			// Last line can be empty, that is not an error.
			if ( $line_number !== $total_lines ) {
				$this->add_error( esc_html__( 'Empty column data.', 'rank-math-pro' ), 'missing_data' );
				$this->row_failed( $line_number );
			}

			return;
		}

		$decoded = str_getcsv( $raw_data );
		if ( count( $headers ) !== count( $decoded ) ) {
			$this->add_error( esc_html__( 'Columns number mismatch.', 'rank-math-pro' ), 'columns_number_mismatch' );
			$this->row_failed( $line_number );
			return;
		}

		$data = array_combine( $headers, $decoded );
		$import_row = new Import_Row( $data, $this->settings );
		if ( ! $import_row->success ) {
			$this->add_error( $import_row->get_error(), 'row_import_error' );
			$this->row_failed( $line_number );
			return;
		}
		$this->row_imported( $line_number );
	}

	/**
	 * Get term ID from slug.
	 *
	 * @param string $term_slug Term slug.
	 * @return int
	 */
	public static function get_term_id( $term_slug ) {
		if ( ! empty( self::$term_ids[ $term_slug ] ) ) {
			return self::$term_ids[ $term_slug ];
		}

		global $wpdb;
		$where = $wpdb->prepare( 'slug = %s', $term_slug );
		self::$term_ids[ $term_slug ] = $wpdb->get_var( "SELECT term_id FROM {$wpdb->terms} WHERE $where" ); // phpcs:ignore

		return self::$term_ids[ $term_slug ];
	}

	/**
	 * After each batch is finished.
	 *
	 * @param array $items Processed items.
	 */
	public function batch_done( $items ) {
		unset( $this->spl );

		$status = (array) get_option( 'rank_math_csv_import_redirections_status', [] );
		if ( ! isset( $status['errors'] ) || ! is_array( $status['errors'] ) ) {
			$status['errors'] = [];
		}
		if ( ! isset( $status['failed_rows'] ) || ! is_array( $status['failed_rows'] ) ) {
			$status['failed_rows'] = [];
		}
		if ( ! isset( $status['imported_rows'] ) || ! is_array( $status['imported_rows'] ) ) {
			$status['imported_rows'] = [];
		}

		$status['imported_rows'] = array_merge( $status['imported_rows'], $this->get_imported_rows() );

		$errors = $this->get_errors();
		if ( $errors ) {
			$status['errors']      = array_merge( $status['errors'], $errors );
			$status['failed_rows'] = array_merge( $status['failed_rows'], $this->get_failed_rows() );
		}

		update_option( 'rank_math_csv_import_redirections_status', $status );
	}

	/**
	 * Set row import status.
	 *
	 * @param string $status New status.
	 * @return void
	 */
	private function row_failed( $row ) {
		$this->failed_rows[] = $row + 1;
	}

	/**
	 * Set row import status.
	 *
	 * @param string $status New status.
	 * @return void
	 */
	private function row_imported( $row ) {
		$this->imported_rows[] = $row + 1;
	}

	/**
	 * Get failed rows array.
	 *
	 * @return array
	 */
	private function get_failed_rows() {
		return $this->failed_rows;
	}

	/**
	 * Get failed rows array.
	 *
	 * @return array
	 */
	private function get_imported_rows() {
		return $this->imported_rows;
	}

	/**
	 * Get all import errors.
	 *
	 * @return mixed Array of errors or false if there is no error.
	 */
	public function get_errors() {
		return empty( $this->errors ) ? false : $this->errors;
	}

	/**
	 * Add import error.
	 *
	 * @param string $error New error.
	 * @return void
	 */
	public function add_error( $error_message, $error_id = null ) {
		if ( is_null( $error_id ) ) {
			$this->errors[] = $error_message;
			return;
		}
		$this->errors[ $error_id ] = $error_message;
	}


}
