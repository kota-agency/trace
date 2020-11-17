<?php
/**
 * The images settings.
 *
 * @package    RankMath
 * @subpackage RankMath\Settings
 */

use RankMath\Helper;

$cmb->add_field(
	[
		'id'      => 'noindex_hidden_products',
		'type'    => 'toggle',
		'name'    => esc_html__( 'Noindex Hidden Products', 'rank-math-pro' ),
		'desc'    => wp_kses_post( __( 'Set Product Pages to noindex when WooCommerce Catalog visibility is set to hidden.', 'rank-math-pro' ) ),
		'default' => 'on',
	],
	++$fields_position
);
