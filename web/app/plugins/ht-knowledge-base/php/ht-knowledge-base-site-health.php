<?php
/**
* Site Health (Debug)
*/

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


if (!class_exists('HT_Knowledge_Base_Site_Health')) {

	class HT_Knowledge_Base_Site_Health {

		//Constructor
		function __construct() {
			add_filter( 'debug_information', array( $this, 'ht_kb_add_debug_info' ) );
		}

		/**
		 * Output debug info
		 */
		function ht_kb_add_debug_info( $debug_info ){
			global $is_apache;
			
			$connection = $this->get_test_herothemes_communication();
			$connection_status = is_array($connection) && array_key_exists('label', $connection) ? $connection['label'] : __('Could not run connection test', 'ht-knowledge-base');
			$debug_info['ht-kb'] = array(
				'label'    => __( 'Heroic Knowledge Base', 'ht-knowledge-base' ),
				'fields'   => array(
					'version' => array(
						'label'    => __( 'Version', 'ht-knowledge-base' ),
						'value'   => HT_KB_VERSION_NUMBER,
						'private' => false, //this can be copied
					),
					'build' => array(
						'label'    => __( 'Build', 'ht-knowledge-base' ),
						'value'   => HT_KB_BUILD_NUMBER,
						'private' => false, //this can be copied
					),
					'license_status' => array(
						'label'    => __( 'Support & Updates License', 'ht-knowledge-base' ),
						'value'   =>  get_option( 'ht_kb_license_status', 'empty' ),
						'private' => false, //this can be copied
					),					
					'connection' => array(
						'label'    => __( 'HeroThemes.com connection', 'ht-knowledge-base' ),
						'value'   => $connection_status,
						'private' => false, //this can be copied
					),
					/* this is already included in the server section
					'curl' => array(
						'label'    => __( 'cUrl support', 'ht-knowledge-base' ),
						'value'   =>  function_exists( 'curl_version' ) ? curl_version()['version'] : 'not supported',
						'private' => false, //this can be copied
					),
					*/
					'mb_support' => array(
						'label'    => __( 'Multibyte Support', 'ht-knowledge-base' ),
						'value'   => function_exists( 'mb_strpos' ) ? 'enabled' : 'disabled',
						'private' => false, //this can be copied
					),	
					'apache_server' => array(
						'label'    => __( 'Apache Server', 'ht-knowledge-base' ),
						'value'   => ( $is_apache ) ? 'detected' : 'not detected',
						'private' => false, //this can be copied
					),	
					'apache_mod_security' => array(
						'label'    => __( 'Apache Mod Security', 'ht-knowledge-base' ),
						'value'   => ( function_exists( 'apache_mod_loaded' ) && apache_mod_loaded('mod_security') ) ? 'loaded' : 'not loaded',
						'private' => false, //this can be copied
					),
					'ht_kb_cpt_slug' => array(
						'label'    => __( 'KB CPT Slug', 'ht-knowledge-base' ),
						'value'   => ht_kb_get_cpt_slug(),
						'private' => false, //this can be copied
					),
					'ht_kb_cat_slug' => array(
						'label'    => __( 'KB Category Slug', 'ht-knowledge-base' ),
						'value'   => ht_kb_get_cat_slug(),
						'private' => false, //this can be copied
					),
					'ht_kb_tag_slug' => array(
						'label'    => __( 'KB Tag Slug', 'ht-knowledge-base' ),
						'value'   => ht_kb_get_tag_slug(),
						'private' => false, //this can be copied
					),
					'ht_kb_default_page_id' => array(
						'label'    => __( 'KB Default Home Page ID', 'ht-knowledge-base' ),
						'value'   => hkb_kb_default_page_id(),
						'private' => false, //this can be copied
					),
					'ht_kb_article_count' => array(
						'label'    => __( 'KB Article Count', 'ht-knowledge-base' ),
						'value'   => $this->ht_get_article_count(),
						'private' => false, //this can be copied
					),
					'ht_kb_category_count' => array(
						'label'    => __( 'KB Category Count', 'ht-knowledge-base' ),
						'value'   => $this->ht_get_category_count(),
						'private' => false, //this can be copied
					),
					'ht_kb_category_structure' => array(
						'label'    => __( 'KB Category Structure', 'ht-knowledge-base' ),
						'value'   => $this->ht_get_category_structure(),
						'private' => true, //this can not be copied
					),

				)
			);		
		 
			return $debug_info;
		}

	/**
	 * Test if the site can communicate with HeroThemes Store.
	 * @return array The test results.
	 */
	public function get_test_herothemes_communication() {
		//early exit if test disabled
		if( apply_filters('ht_kb_disable_store_connection_test', false ) ){
			return false;
		}

		$result = array(
			'label'       => __( 'Can communicate with HeroThemes.com store', 'ht-knowledge-base' ),
			'status'      => '',
			'description' => sprintf(
				'<p>%s</p>',
				__( 'Communicating with the HeroThemes is used to check for new versions and updates.', 'ht-knowledge-base' )
			),
			'actions'     => '',
			'test'        => 'herothemes_communication',
		);

		$herothemes_store = wp_remote_get(
			HT_STORE_URL,
			array(
				'timeout' => 10,
			)
		);

		if ( ! is_wp_error( $herothemes_store ) ) {
			$result['status'] = 'good';
			$result['label'] = __( 'HeroThemes.com is reachable', 'ht-knowledge-base' );
			$result['description'] = sprintf(
				'<p>%s</p>',
				__( 'Sucessfully connected to HeroThemes.com', 'ht-knowledge-base' )
			);
		} else {
			$result['status'] = 'critical';

			$result['label'] = __( 'Could not reach HeroThemes.com', 'ht-knowledge-base' );

			$result['description'] .= sprintf(
				'<p>%s</p>',
				sprintf(
					'<span class="error"><span class="screen-reader-text">%s</span></span> %s',
					__( 'Error', 'ht-knowledge-base' ),
					sprintf(
						/* translators: 1: The IP address WordPress.org resolves to. 2: The error returned by the lookup. */
						__( 'Your site is unable to reach HeroThemes.com at %1$s, and returned the error: %2$s', 'ht-knowledge-base' ),
						gethostbyname( HT_STORE_URL ),
						$herothemes_store->get_error_message()
					)
				)
			);
		}

		return $result;
	}

	/**
	* Get article count
	*/
	public function ht_get_article_count(){
		$articles = get_posts('post_type=ht_kb&posts_per_page=-1');
		$article_count = count($articles);
		return $article_count;
	}

	/**
	* Get category count
	*/
	public function ht_get_category_count(){
		$terms = get_terms( array(
			'taxonomy' => 'ht_kb_category',
			'hide_empty' => false,
		) );
		$term_count = count($terms);
		return $term_count;
	}

	/**
	* Get cateogry structure
	*/
	public function ht_get_category_structure(){
		$structure = '';
		$terms = get_terms( array(
			'taxonomy' => 'ht_kb_category',
			'hide_empty' => false,
		) );
		foreach ($terms as $key => $term) {
			$structure .= sprintf('%s (%s),', $term->name, $term->count);
		}
		return $structure;
	}


	} //end class
}//end class exists

//run the module
if(class_exists('HT_Knowledge_Base_Site_Health')){
	new HT_Knowledge_Base_Site_Health();
}