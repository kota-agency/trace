<?php
/**
 * The Analytics Module
 *
 * @since      1.4.0
 * @package    RankMathPro
 * @subpackage RankMathPro\modules
 * @author     Rank Math <support@rankmath.com>
 */

namespace RankMathPro\Analytics;

use RankMath\Analytics\Data_Fetcher;
use RankMathPro\Google\Analytics as Google_Analytics;

defined( 'ABSPATH' ) || exit;

use Exception;

/**
 * Installer class.
 */
class Installer {

	/**
	 * Install routine.
	 */
	public function install() {
		$done = \boolval( get_option( 'rank_math_analytics_pro_installed' ) );
		if ( $done || ! Google_Analytics::get_view_id() ) {
			return;
		}

		$this->create_tables();
		self::start_analytics_fetch();

		update_option( 'rank_math_analytics_pro_installed', true );
	}

	public static function start_analytics_fetch() {
		if ( ! Google_Analytics::get_view_id() ) {
			return;
		}

		Data_Fetcher::get()->start_process( 90, 'get_analytics_only' );
	}

	/**
	 * Create tables
	 */
	private function create_tables() {
		global $wpdb;

		$collate      = $wpdb->get_charset_collate();
		$prefix       = $wpdb->prefix . 'rank_math_analytics_';
		$table_schema = [

			"CREATE TABLE IF NOT EXISTS {$prefix}ga (
				id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				page VARCHAR(500) NOT NULL,
				created TIMESTAMP NOT NULL,
				pageviews MEDIUMINT(6) NOT NULL,
				visitors MEDIUMINT(6) NOT NULL,
				PRIMARY KEY (id),
				INDEX analytics_object_analytics (page(500))
			) $collate;",

			"CREATE TABLE IF NOT EXISTS {$prefix}adsense (
				id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				created TIMESTAMP NOT NULL,
				earnings DOUBLE NOT NULL DEFAULT 0,
				PRIMARY KEY (id)
			) $collate;",

			// Link Storage.
			"CREATE TABLE IF NOT EXISTS {$prefix}keyword_manager (
				id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				keyword VARCHAR(1000) NOT NULL,
				collection VARCHAR(200) NULL,
				is_active TINYINT(1) NOT NULL DEFAULT 1,
				PRIMARY KEY (id)
			) $collate;",

			// Link meta.
			// "CREATE TABLE IF NOT EXISTS {$prefix}object_links (
			// 	id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			// 	object_id BIGINT(20) UNSIGNED NOT NULL,
			// 	link VARCHAR(255) NOT NULL,
			// 	type VARCHAR(255) NOT NULL,
			// 	rel VARCHAR(255) NOT NULL,
			// 	status TINYINT NOT NULL,
			// 	PRIMARY KEY (id)
			// ) $collate;",
		];

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		foreach ( $table_schema as $table ) {
			try {
				dbDelta( $table );
			} catch ( Exception $e ) {
				// Will log.
			}
		}
	}
}
