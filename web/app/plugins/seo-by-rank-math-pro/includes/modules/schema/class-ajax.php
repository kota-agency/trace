<?php
/**
 * The Schema AJAX
 *
 * @since      1.0.0
 * @package    RankMath
 * @subpackage RankMathPro
 * @author     MyThemeShop <admin@mythemeshop.com>
 */

namespace RankMathPro\Schema;

use RankMath\Helper;
use MyThemeShop\Helpers\Param;

defined( 'ABSPATH' ) || exit;

/**
 * Ajax class.
 */
class Ajax {

	use \RankMath\Traits\Hooker;
	use \RankMath\Traits\Ajax;

	/**
	 * The Constructor.
	 */
	public function __construct() {
		$this->ajax( 'fetch_from_url', 'fetch_from_url' );
		$this->ajax( 'get_conditions_data', 'get_conditions_data' );
	}

	/**
	 * Fetch from url.
	 */
	public function fetch_from_url() {
		check_ajax_referer( 'rank-math-ajax-nonce', 'security' );

		$url = Param::post( 'url', false );
		if ( ! $url ) {
			$this->error( esc_html__( 'No url found.', 'rank-math-pro' ) );
		}

		$parser = new Parser();
		$output = $parser->from_url( $url );

		if ( is_wp_error( $output ) ) {
			$this->error( $output->get_error_message() );
		}

		$this->success( [ 'json' => $output ] );
	}

	/**
	 * Get posts/terms/author data.
	 */
	public function get_conditions_data() {
		check_ajax_referer( 'rank-math-ajax-nonce', 'security' );

		$method = 'singular' === Param::get( 'category', false ) ? 'get_singular' : 'get_terms';
		$data   = $this->{$method}( Param::get( 'userInput', false ), Param::get( 'type', false ), Param::get( 'value', false ) );

		$this->success( [ 'data' => $data ] );
	}

	/**
	 * Get posts by searched string & post type.
	 *
	 * @param string $search Searched String.
	 * @param string $type   Post Type.
	 * @param int    $value  Post ID.
	 */
	private function get_singular( $search, $type, $value ) {
		if ( 'null' === $search && $value ) {
			$data = [
				'value' => $value,
				'title' => get_the_title( $value ),
			];

			return $data;
		}

		$posts = get_posts(
			[
				'post_type'   => $type,
				's'           => $search,
				'numberposts' => -1,
			]
		);

		if ( empty( $posts ) ) {
			return [];
		}

		$data = [];
		foreach ( $posts as $post ) {
			$data[] = [
				'value' => $post->ID,
				'title' => $post->post_title,
			];
		}

		return $data;
	}

	/**
	 * Get terms by searched string and taxonomy.
	 *
	 * @param string $search Searched String.
	 * @param string $type   Taxonomy Name.
	 * @param int    $value  Term ID.
	 */
	private function get_terms( $search, $type, $value ) {
		$data = [];
		if ( 'author' === $type ) {
			return $this->get_authors( $search, $value );
		}

		if ( 'null' === $search && $value ) {
			$term = get_term_by( 'id', absint( $value ), $type );
			return [
				'value' => $value,
				'title' => ! empty( $term ) ? $term->name : '',
			];
		}

		$terms = get_terms(
			[
				'taxonomy' => $type,
				'search'   => $search,
			]
		);

		if ( ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$data[] = [
					'value' => $term->term_id,
					'title' => $term->name,
				];
			}
		}

		return $data;
	}

	/**
	 * Get terms by searched string and taxonomy.
	 *
	 * @param string $search Searched String.
	 * @param int    $value  Term ID.
	 */
	private function get_authors( $search, $value ) {
		if ( 'null' === $search && $value ) {
			$author = get_user_by( 'id', $value );

			return [
				'value' => $value,
				'title' => ! empty( $author ) ? $author->display_name : '',
			];
		}

		$data    = [];
		$authors = get_users(
			[
				'search'         => '*' . $search . '*',
				'search_columns' => [ 'display_name', 'user_nicename', 'user_login', 'user_email' ],
			]
		);

		if ( ! empty( $authors ) ) {
			foreach ( $authors as $author ) {
				$data[] = [
					'value' => $author->ID,
					'title' => $author->data->display_name,
				];
			}
		}

		return $data;
	}
}
