<?php
/**
 * The Local_Seo Module
 *
 * @since      1.0.0
 * @package    RankMath
 * @subpackage RankMathPro
 * @author     Rank Math <support@rankmath.com>
 */

namespace RankMathPro\Local_Seo;

use RankMath\Helper;
use RankMath\Traits\Hooker;

defined( 'ABSPATH' ) || exit;

/**
 * Local_Seo class.
 */
class Local_Seo {

	use Hooker;

	/**
	 * Post Type.
	 *
	 * @var string
	 */
	private $post_type = 'rank_math_locations';

	/**
	 * The Constructor.
	 */
	public function __construct() {
		$this->action( 'init', 'init' );
		$this->includes();
	}

	/**
	 * Intialize.
	 */
	public function init() {
		if ( ! Helper::get_settings( 'titles.use_multiple_locations', false ) ) {
			return;
		}

		$this->post_singular_name = Helper::get_settings( 'titles.locations_post_type_label', 'Locations' );

		$this->register_location_post_type();
		$this->register_location_taxonomy();
	}

	/**
	 * Include required files.
	 */
	private function includes() {
		if ( is_admin() ) {
			new Admin();
			return;
		}

		new Frontend();
		new Location_Shortcode();
	}

	/**
	 * Register Locations post type.
	 */
	private function register_location_post_type() {
		$plural_label   = Helper::get_settings( 'titles.locations_post_type_plural_label', 'RM Locations' );
		$post_type_slug = Helper::get_settings( 'titles.locations_post_type_base', 'locations' );
		$labels         = [
			'name'          => $this->post_singular_name,
			'singular_name' => $this->post_singular_name,
			'menu_name'     => $plural_label,
			/* translators: Post Type Plural Name */
			'all_items'     => sprintf( esc_html__( 'All %s', 'rank-math-pro' ), $plural_label ),
			/* translators: Post Type Singular Name */
			'add_new_item'  => sprintf( esc_html__( 'Add New %s', 'rank-math-pro' ), $this->post_singular_name ),
			/* translators: Post Type Singular Name */
			'new_item'      => sprintf( esc_html__( 'New %s', 'rank-math-pro' ), $this->post_singular_name ),
			/* translators: Post Type Singular Name */
			'edit_item'     => sprintf( esc_html__( 'Edit %s', 'rank-math-pro' ), $this->post_singular_name ),
			/* translators: Post Type Singular Name */
			'update_item'   => sprintf( esc_html__( 'Update %s', 'rank-math-pro' ), $this->post_singular_name ),
			/* translators: Post Type Singular Name */
			'view_item'     => sprintf( esc_html__( 'View %s', 'rank-math-pro' ), $this->post_singular_name ),
			/* translators: Post Type Plural Name */
			'view_items'    => sprintf( esc_html__( 'View %s', 'rank-math-pro' ), $plural_label ),
			/* translators: Post Type Singular Name */
			'search_items'  => sprintf( esc_html__( 'Search %s', 'rank-math-pro' ), $this->post_singular_name ),
		];

		$args = [
			'label'           => $this->post_singular_name,
			'labels'          => $labels,
			'public'          => true,
			'publicly_queryable' => true,
			'show_ui'         => true,
			'capability_type' => 'post',
			'hierarchical'    => false,
			'rewrite'         => [
				'slug'       => $post_type_slug,
				'with_front' => $this->filter( 'locations/front', true ),
			],
			'has_archive'     => $post_type_slug,
			'menu_icon'       => 'dashicons-location',
			'query_var'       => true,
			'show_in_rest'    => true,
			'rest_base'       => 'rank-math-locations',
			'supports'        => [ 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'revisions', 'custom-fields', 'page-attributes', 'publicize' ],
		];

		register_post_type( $this->post_type, $args );
	}

	/**
	 * Register Locations Category taxonomy.
	 */
	private function register_location_taxonomy() {
		$category_slug = esc_html( Helper::get_settings( 'titles.locations_category_base', 'locations-category' ) );
		$labels        = [
			/* translators: Post Type Singular Name */
			'name'              => sprintf( esc_html__( '%s categories', 'rank-math-pro' ), $this->post_singular_name ),
			/* translators: Post Type Singular Name */
			'singular_name'     => sprintf( esc_html__( '%s category', 'rank-math-pro' ), $this->post_singular_name ),
			/* translators: Post Type Singular Name */
			'all_items'         => sprintf( esc_html__( 'All %s categories', 'rank-math-pro' ), $this->post_singular_name ),
			/* translators: Post Type Singular Name */
			'edit_item'         => sprintf( esc_html__( 'Edit %s category', 'rank-math-pro' ), $this->post_singular_name ),
			/* translators: Post Type Singular Name */
			'update_item'       => sprintf( esc_html__( 'Update %s category', 'rank-math-pro' ), $this->post_singular_name ),
			/* translators: Post Type Singular Name */
			'add_new_item'      => sprintf( esc_html__( 'Add New %s category', 'rank-math-pro' ), $this->post_singular_name ),
			/* translators: Post Type Singular Name */
			'new_item_name'     => sprintf( esc_html__( 'New %s category', 'rank-math-pro' ), $this->post_singular_name ),
			/* translators: Post Type Singular Name */
			'menu_name'         => sprintf( esc_html__( '%s categories', 'rank-math-pro' ), $this->post_singular_name ),
			'search_items'      => esc_html__( 'Search categories', 'rank-math-pro' ),
			'parent_item'       => esc_html__( 'Parent Category', 'rank-math-pro' ),
			'parent_item_colon' => esc_html__( 'Parent Category:', 'rank-math-pro' ),
		];

		$args = [
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'show_in_rest'      => true,
			'rewrite'           => [ 'slug' => $category_slug ],
		];

		register_taxonomy( 'rank_math_location_category', [ $this->post_type ], $args );
	}
}
