<?php
/**
 * The Analytics AJAX
 *
 * @since      1.4.0
 * @package    RankMathPro
 * @subpackage RankMathPro\modules
 * @author     Rank Math <support@rankmath.com>
 */

namespace RankMathPro\Analytics;

use RankMath\Helper;
use RankMath\Google\Api;
use RankMath\Google\Console as Google_Analytics;
use RankMath\Google\Authentication;
use MyThemeShop\Helpers\Str;
use MyThemeShop\Helpers\Param;

defined( 'ABSPATH' ) || exit;

/**
 * AJAX class.
 */
class AJAX {

	use \RankMath\Traits\Ajax;

	/**
	 * The Constructor
	 */
	public function __construct() {
		$this->ajax( 'save_adsense_account', 'save_adsense_account' );
	}

	/**
	 * Save adsense profile.
	 */
	public function save_adsense_account() {
		check_ajax_referer( 'rank-math-ajax-nonce', 'security' );

		$values               = get_option( 'rank_math_google_analytic_options', [] );
		$values['adsense_id'] = Param::post( 'accountID' );
		update_option( 'rank_math_google_analytic_options', $values );

		$this->success();
	}
}
