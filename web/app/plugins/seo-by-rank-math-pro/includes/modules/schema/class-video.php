<?php
/**
 * The Video Schema.
 *
 * @since      1.0
 * @package    RankMath
 * @subpackage RankMath\Schema
 * @author     Rank Math <support@rankmath.com>
 */

namespace RankMathPro\Schema;

use RankMath\KB;
use RankMath\Helper;
use RankMath\Traits\Hooker;
use RankMath\Schema\DB;
use MyThemeShop\Helpers\Str;

defined( 'ABSPATH' ) || exit;

/**
 * Video class.
 */
class Video {

	use Hooker;

	/**
	 * The Constructor.
	 */
	public function __construct() {
		$this->action( 'save_post', 'save_post', 10, 2 );
		$this->filter( 'rank_math/settings/title', 'add_title_settings' );
		$this->filter( 'rank_math/settings/general', 'add_general_settings' );
		$this->action( 'rank_math/opengraph/facebook', 'add_video_tags', 99 );

		new Media_RSS();
	}

	/**
	 * Add post Settings in titles optional panel.
	 *
	 * @param array $tabs Array of option panel tabs.
	 *
	 * @return array
	 */
	public function add_general_settings( $tabs ) {
		$tabs['others']['file'] = dirname( __FILE__ ) . '/views/others.php';
		return $tabs;
	}

	/**
	 * Add post Settings in titles optional panel.
	 *
	 * @param array $tabs Array of option panel tabs.
	 *
	 * @return array
	 */
	public function add_title_settings( $tabs ) {
		$icons = Helper::choices_post_type_icons();
		$links = [
			'post'       => '<a href="' . KB::get( 'post-settings' ) . '" target="_blank">' . esc_html__( 'Learn more', 'rank-math-pro' ) . '</a>.',
			'page'       => '<a href="' . KB::get( 'page-settings' ) . '" target="_blank">' . esc_html__( 'Learn more', 'rank-math-pro' ) . '</a>.',
			'product'    => '<a href="' . KB::get( 'product-settings' ) . '" target="_blank">' . esc_html__( 'Learn more', 'rank-math-pro' ) . '</a>.',
			'attachment' => '<a href="' . KB::get( 'media-settings' ) . '" target="_blank">' . esc_html__( 'Learn more', 'rank-math-pro' ) . '</a>.',
		];

		$names = [
			'post'       => 'single %s',
			'page'       => 'single %s',
			'product'    => 'product pages',
			'attachment' => 'media %s',
		];

		$tabs['p_types'] = [
			'title' => esc_html__( 'Post Types:', 'rank-math-pro' ),
			'type'  => 'seprator',
		];

		foreach ( Helper::get_accessible_post_types() as $post_type ) {
			$obj      = get_post_type_object( $post_type );
			$link     = isset( $links[ $obj->name ] ) ? $links[ $obj->name ] : '';
			$obj_name = isset( $names[ $obj->name ] ) ? sprintf( $names[ $obj->name ], $obj->name ) : $obj->name;

			$tabs[ 'post-type-' . $obj->name ] = [
				'title'     => 'attachment' === $post_type ? esc_html__( 'Attachments', 'rank-math-pro' ) : $obj->label,
				'icon'      => isset( $icons[ $obj->name ] ) ? $icons[ $obj->name ] : $icons['default'],
				/* translators: 1. post type name 2. link */
				'desc'      => sprintf( esc_html__( 'Change Global SEO, Schema, and other settings for %1$s. %2$s', 'rank-math-pro' ), $obj_name, $link ),
				'post_type' => $obj->name,
				'file'      => dirname( __FILE__ ) . '/views/post-types.php',
				'classes'   => 'attachment' === $post_type ? 'rank-math-advanced-option' : '',
			];
		}

		return $tabs;
	}

	/**
	 * Output the video tags.
	 *
	 * @link https://yandex.com/support/video/partners/open-graph.html#player
	 *
	 * @param OpenGraph $opengraph The current opengraph network object.
	 */
	public function add_video_tags( $opengraph ) {
		if ( ! is_singular() ) {
			return;
		}

		global $post;
		$video_data = get_post_meta( $post->ID, 'rank_math_schema_VideoObject', true );
		if ( empty( $video_data ) ) {
			return;
		}

		$tags = [
			'og:video'           => ! empty( $video_data['contentUrl'] ) ? $video_data['contentUrl'] : $video_data['embedUrl'],
			'og:video:duration'  => ! empty( $video_data['duration'] ) ? Helper::duration_to_seconds( $video_data['duration'] ) : '',
			'ya:ovs:adult'       => ! empty( $video_data['isFamilyFriendly'] ) ? false : true,
			'ya:ovs:upload_date' => ! empty( $video_data['uploadDate'] ) ? Helper::replace_vars( $video_data['uploadDate'], $post ) : '',
			'ya:ovs:allow_embed' => ! empty( $video_data['embedUrl'] ) ? 'true' : 'false',
		];

		foreach ( $tags as $tag => $value ) {
			$opengraph->tag( $tag, $value );
		}
	}

	/**
	 * Automatically add Video Schema when post is updated.
	 *
	 * @param  int    $post_id Post id.
	 * @param  object $post    Post object.
	 */
	public function save_post( $post_id, $post ) {
		if (
			wp_is_post_revision( $post->ID ) ||
			in_array( $post->post_status, [ 'auto-draft', 'trash' ], true ) ||
			! Helper::get_settings( "titles.pt_{$post->post_type}_autodetect_video" )
		) {
			return $post_id;
		}

		$content = trim( $post->post_content . ' ' . $this->get_custom_fields_data( $post_id ) );
		if ( empty( trim( $content ) ) ) {
			return $post_id;
		}

		$video_schema_exists = get_post_meta( $post_id, 'rank_math_schema_VideoObject', true );
		if ( ! empty( $video_schema_exists ) ) {
			return $post_id;
		}

		$content             = apply_filters( 'the_content', $content );
		$allowed_media_types = apply_filters( 'media_embedded_in_content_allowed_types', [ 'video', 'embed', 'iframe' ] );
		$tags                = implode( '|', $allowed_media_types );
		$video_src           = [];

		preg_match_all( '#<(?P<tag>' . $tags . ')[^<]*?(?:>[\s\S]*?<\/(?P=tag)>|\s*\/>)#', $content, $matches );
		if ( empty( $matches ) || empty( $matches[0] ) ) {
			return $post_id;
		}

		foreach ( $matches[0] as $links ) {
			$video_src = $this->get_video_src( $links, $post_id );
			if ( ! empty( $video_src ) ) {
				break;
			}
		}

		if ( empty( $video_src ) ) {
			return $post_id;
		}

		$schemas   = empty( DB::get_schemas( $post_id ) ) ? $this->get_default_schema_data( $post->post_type ) : [];
		$schemas[] = [
			'@type'            => 'VideoObject',
			'metadata'         => [
				'title'                   => 'Video',
				'type'                    => 'template',
				'shortcode'               => uniqid( 's-' ),
				'isPrimary'               => empty( $schemas ),
				'reviewLocationShortcode' => '[rank_math_rich_snippet]',
				'category'                => '%categories%',
				'tags'                    => '%tags%',
			],
			'name'             => '%seo_title%',
			'description'      => '%seo_description%',
			'uploadDate'       => '%date(Y-m-dTH:i:sP)%',
			'thumbnailUrl'     => ! empty( $video_src['thumbnail'] ) ? $video_src['thumbnail'] : '%post_thumbnail%',
			'embedUrl'         => $video_src['embed'] ? $video_src['src'] : '',
			'contentUrl'       => ! $video_src['embed'] ? $video_src['src'] : '',
			'duration'         => '',
			'width'            => $video_src['width'],
			'height'           => $video_src['height'],
			'isFamilyFriendly' => true,
		];

		foreach ( array_filter( $schemas ) as $schema ) {
			update_post_meta( $post_id, "rank_math_schema_{$schema['@type']}", $schema );
		}
	}

	/**
	 * Get default schema data.
	 *
	 * @param string $post_type Post type.
	 */
	private function get_default_schema_data( $post_type ) {
		$default_type = ucfirst( Helper::get_default_schema_type( $post_type ) );
		if ( ! in_array( $default_type, [ 'Article', 'NewsArticle', 'BlogPosting' ], true ) ) {
			return [];
		}

		return [
			[
				'@type'         => $default_type,
				'metadata'      => [
					'title'     => 'Article',
					'type'      => 'template',
					'isPrimary' => true,
				],
				'headline'      => Helper::get_settings( "titles.pt_{$post_type}_default_snippet_name" ),
				'description'   => Helper::get_settings( "titles.pt_{$post_type}_default_snippet_desc" ),
				'datePublished' => '%date(Y-m-dTH:i:sP)%',
				'dateModified'  => '%modified(Y-m-dTH:i:sP)%',
				'image'         => [
					'@type' => 'ImageObject',
					'url'   => '%post_thumbnail%',
				],
				'author'        => [
					'@type' => 'Person',
					'name'  => '%name%',
				],
			]
		];
	}

	/**
	 * Get Custom fields data.
	 *
	 * @param int $post_id Post id.
	 */
	private function get_custom_fields_data( $post_id ) {
		$custom_fields = Str::to_arr_no_empty( Helper::get_settings( 'sitemap.video_sitemap_custom_fields' ) );
		if ( empty( $custom_fields ) ) {
			return;
		}

		$content = '';
		foreach ( $custom_fields as $custom_field ) {
			$content = $content . ' ' . get_post_meta( $post_id, $custom_field, true );
		}

		return trim( $content );
	}

	/**
	 * Get Video source from the content.
	 *
	 * @param array $links   Video Links.
	 * @param int   $post_id Current Post ID.
	 *
	 * @return array
	 */
	private function get_video_src( $links, $post_id ) {
		preg_match_all( '@src="([^"]+)"@', $links, $matches );
		if ( empty( $matches ) || empty( $matches[1] ) ) {
			return false;
		}

		return $this->validate_video( $matches[1][0], $post_id );
	}

	/**
	 * Validate Video source.
	 *
	 * @param string $src     Video Source.
	 * @param int    $post_id Current Post ID.
	 *
	 * @return array
	 *
	 * Credits to Leedo @ https://noembed.com/
	 */
	private function validate_video( $src, $post_id ) {
		if (
			preg_match( '#^https?://(?:www\.)?(?:youtube\.com/|youtu\.be/)#', $src ) ||
			preg_match( '#^https?://(.+\.)?vimeo\.com/.*#', $src )
		) {
			$data = \json_decode( file_get_contents( "http://noembed.com/embed?url={$src}" ), true );
			return [
				'src'       => $src,
				'embed'     => true,
				'width'     => ! empty( $data['width'] ) ? $data['width'] : '',
				'height'    => ! empty( $data['height'] ) ? $data['height'] : '',
				'thumbnail' => ! empty( $data['thumbnail_url'] ) ? $this->get_video_thumbnail_url( $data['thumbnail_url'], $post_id ) : '',
			];
		}

		$src           = preg_replace( '/\?.*/', '', $src ); // Remove query string from URL before chcking for the supported type.
		$default_types = wp_get_video_extensions();
		$type          = wp_check_filetype( $src, wp_get_mime_types() );

		if ( in_array( strtolower( $type['ext'] ), $default_types, true ) ) {
			$data          = [];
			$attachment_id = attachment_url_to_postid( $src );
			if ( $attachment_id ) {
				$video_details = wp_get_attachment_metadata( $attachment_id );
				$data          = [
					'width'  => ! empty( $video_details['width'] ) ? $video_details['width'] : '',
					'height' => ! empty( $video_details['height'] ) ? $video_details['height'] : '',
				];
			}
			return array_merge(
				[
					'src'   => $src,
					'embed' => false,
				],
				$data
			);
		}

		return [];
	}

	/**
	 * Validate Video source.
	 *
	 * @param string $url            Thumbnail URL.
	 * @param int    $parent_post_id Current Post ID.
	 *
	 * @return array
	 *
	 * Credits to m1r0 @ https://gist.github.com/m1r0/f22d5237ee93bcccb0d9
	 */
	private function get_video_thumbnail_url( $url, $parent_post_id = 0 ) {
		if ( ! class_exists( 'WP_Http' ) ) {
			include_once( ABSPATH . WPINC . '/class-http.php' );
		}

		$http     = new \WP_Http();
		$response = $http->request( $url );
		if ( 200 !== $response['response']['code'] ) {
			return false;
		}

		$upload = wp_upload_bits( basename( $url ), null, $response['body'] );
		if ( ! empty( $upload['error'] ) ) {
			return false;
		}

		$file_path        = $upload['file'];
		$file_name        = basename( $file_path );
		$file_type        = wp_check_filetype( $file_name, null );
		$attachment_title = sanitize_file_name( pathinfo( $file_name, PATHINFO_FILENAME ) );
		$wp_upload_dir    = wp_upload_dir();

		$post_info = [
			'guid'           => $wp_upload_dir['url'] . '/' . $file_name,
			'post_mime_type' => $file_type['type'],
			'post_title'     => $attachment_title,
			'post_content'   => '',
			'post_status'    => 'inherit',
		];

		$attach_id = wp_insert_attachment( $post_info, $file_path, $parent_post_id );

		// Include image.php.
		require_once( ABSPATH . 'wp-admin/includes/image.php' );

		// Define attachment metadata.
		$attach_data = wp_generate_attachment_metadata( $attach_id, $file_path );

		// Assign metadata to attachment.
		wp_update_attachment_metadata( $attach_id, $attach_data );

		return wp_get_attachment_url( $attach_id );
	}
}
