<?php
/**
 * WooCommerce module.
 *
 * @since      1.0
 * @package    RankMathPro
 * @author     Rank Math <support@rankmath.com>
 */

namespace RankMathPro;

use RankMath\Helper;
use RankMath\Traits\Hooker;
use MyThemeShop\Helpers\Conditional;

defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce class.
 *
 * @codeCoverageIgnore
 */
class WooCommerce {

	use Hooker;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->action( 'cmb2_admin_init', 'cmb_init', 99 );
		$this->filter( 'rank_math/frontend/robots', 'robots' );
	}

	/**
	 * Hook CMB2 init process.
	 */
	public function cmb_init() {
		$this->action( 'cmb2_init_hookup_rank-math-options-general_options', 'add_options', 110 );
	}

	/**
	 * Add options to WooCommerce module.
	 *
	 * @param object $cmb CMB object.
	 */
	public function add_options( $cmb ) {
		$field_ids       = wp_list_pluck( $cmb->prop( 'fields' ), 'id' );
		$fields_position = array_search( 'product_brand', array_keys( $field_ids ), true ) + 1;

		include_once dirname( __FILE__ ) . '/options.php';
	}

	/**
	 * Change robots for WooCommerce pages according to settings
	 *
	 * @param array $robots Array of robots to sanitize.
	 *
	 * @return array Modified robots.
	 */
	public function robots( $robots ) {
		$is_hidden = Conditional::is_woocommerce_active() && is_product() && \wc_get_product()->get_catalog_visibility() === 'hidden';

		if ( Helper::get_settings( 'general.noindex_hidden_products' ) && $is_hidden ) {
			return [
				'noindex'  => 'noindex',
				'nofollow' => 'nofollow',
			];
		}

		return $robots;
	}
}
