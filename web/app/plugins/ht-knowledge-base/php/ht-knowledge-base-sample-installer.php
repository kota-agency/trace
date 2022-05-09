<?php
/**
* Knowledge Base Sample Installer
*/

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if (!class_exists('HT_Knowledge_Base_Sample_Installer')) {

	class HT_Knowledge_Base_Sample_Installer {

		//constructor
		function __construct(){
			//@todo - multisite compatibility?
			add_action( 'admin_init', array( $this, 'ht_kb_installer_actions' ) );
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );

			//independant action hook, so we can install content via ajax callback
			add_action( 'ht_kb_install_sample', array( $this, 'ht_kb_install_sample' ) );
		}

		/**
		* Main actions
		*/
		function ht_kb_installer_actions(){

			if($_GET && is_array($_GET) && array_key_exists( 'ht_kb_admin', $_GET )){
				if($_GET['ht_kb_admin'] == 'delete_kb_data'){
					//check security
					check_admin_referer( 'delete-ht-kb-data' );
					//remove all knowledge base data
					$this->remove_all_ht_kb_data();
					//set transient to display message that data removed
					set_transient('_removed_ht_kb_data', 'removed_ht_kb_data');
				}
				if($_GET['ht_kb_admin'] == 'install_sample'){
					//check security
					check_admin_referer( 'add-ht-kb-sample-data' );
					//add sample categories
					$this->add_sample_categories();
					//add sample tags
					$this->add_sample_tags();
					//add sample articles
					$this->add_sample_articles();
					//set transient to display message that sample was installed
					set_transient('_install_sample_ht_kb_data', 'install_sample_ht_kb_data');
				}
			}
		}

		/**
		 * Action hook installer
		 */
		function ht_kb_install_sample(){
			//security?
			//add sample categories
			$this->add_sample_categories();
			//add sample tags
			$this->add_sample_tags();
			//add sample articles
			$this->add_sample_articles();
			//set transient to display message that sample was installed
			set_transient('_install_sample_ht_kb_data', 'install_sample_ht_kb_data');
		}


		/**
		* Admin notices
		*/
		function admin_notices(){
			if('removed_ht_kb_data' == get_transient('_removed_ht_kb_data')){
				 delete_transient('_removed_ht_kb_data');
				 ?>
					<div class="updated">
						<p><?php _e( 'Knowldge Base Data Removed', 'ht-knowledge-base' ); ?></p>
					</div>
				<?php
			}
			if('install_sample_ht_kb_data' == get_transient('_install_sample_ht_kb_data')){
				 delete_transient('_install_sample_ht_kb_data');
				 ?>
					<div class="updated">
						<p><?php _e( 'Knowldge Base Sample Data Added', 'ht-knowledge-base' ); ?></p>
					</div>
				<?php
			}
		}

		/**
		* Add sample categories
		*/
		function add_sample_categories(){
			//getting started (3 articles)
			$name = __('Getting Started', 'ht-knowledge-base');
			$this->add_ht_kb_category($name);

			//account management (3 articles)
			$name = __('Account Management', 'ht-knowledge-base');
			$this->add_ht_kb_category($name);

			//copyright and legal (4 articles)
			$name = __('Copyright and Legal', 'ht-knowledge-base');
			$this->add_ht_kb_category($name);

			//knowledge base plugin (1 article)
			$name = __('Heroic Knowledge Base Plugin', 'ht-knowledge-base');
			$this->add_ht_kb_category($name);
		}

		/**
		* Insert a knowledge base tag
		* @param $name The name of the tag
		*/
		function add_ht_kb_category($name){
			$name = sanitize_text_field($name);
			wp_insert_term($name, 'ht_kb_category');
		}

		/**
		* Add sample tags
		*/
		function add_sample_tags(){
			//tips
			$name = __('tips', 'ht-knowledge-base');
			$this->add_ht_kb_tag($name);

			//installation
			$name = __('installation', 'ht-knowledge-base');
			$this->add_ht_kb_tag($name);

			//contact
			$name = __('contact', 'ht-knowledge-base');
			$this->add_ht_kb_tag($name);

			//support
			$name = __('support', 'ht-knowledge-base');
			$this->add_ht_kb_tag($name);

			//password
			$name = __('password', 'ht-knowledge-base');
			$this->add_ht_kb_tag($name);

			//avatar
			$name = __('avatar', 'ht-knowledge-base');
			$this->add_ht_kb_tag($name);

			//content
			$name = __('content', 'ht-knowledge-base');
			$this->add_ht_kb_tag($name);

			//location
			$name = __('location', 'ht-knowledge-base');
			$this->add_ht_kb_tag($name);

			//about
			$name = __('about', 'ht-knowledge-base');
			$this->add_ht_kb_tag($name);

			//kb
			$name = __('kb', 'ht-knowledge-base');
			$this->add_ht_kb_tag($name);
		}

		/**
		* Insert a knowledge base tag
		* @param $name The name of the tag
		*/
		function add_ht_kb_tag($name){
			$name = sanitize_text_field($name);
			wp_insert_term($name, 'ht_kb_tag');
		}


		/**
		* Add sample knowledge base articles
		*/
		function add_sample_articles(){
			//installation guide
			$this->add_sample_ht_kb_article('Installation Guide', $this->get_sample_article_content(), 'Getting Started', array('tips', 'installation') );

			//what you need to know
			$this->add_sample_ht_kb_article('What You Need to Know', $this->get_sample_article_content(), 'Getting Started', array('tips') );

			//how to contact support
			$this->add_sample_ht_kb_article('How to Contact Support', $this->get_sample_article_content(), 'Getting Started', array('contact', 'support') );

			//how secure is my password
			$this->add_sample_ht_kb_article('How Secure is my Password?', $this->get_sample_article_content(), 'Account Management', array('password') );

			//how do I change my password
			$this->add_sample_ht_kb_article('How do I Change my Password?', $this->get_sample_article_content(), 'Account Management', array('password', 'tips', 'installation') );

			//where can I upload my avatar
			$this->add_sample_ht_kb_article('Where can I Upload my Avatar?', $this->get_sample_article_content(), 'Account Management', array('tips', 'avatar') );

			//where are your offices located
			$this->add_sample_ht_kb_article('Where are Your Offices Located?', $this->get_sample_article_content(), 'Copyright and Legal', array('contact', 'location', 'about') );

			//our content policy
			$this->add_sample_ht_kb_article('Our Content Policy', $this->get_sample_article_content(), 'Copyright and Legal', array('tips', 'content') );

			//who are we
			$this->add_sample_ht_kb_article('Who are We?', $this->get_sample_article_content(), 'Copyright and Legal', array('about') );

			//another legal page
			$this->add_sample_ht_kb_article('Another Legal Page', $this->get_sample_article_content(), 'Copyright and Legal', array('tips', 'content') );

			//knowledge base wordpress plugin
			$this->add_sample_ht_kb_article('Knowledge Base WordPress Plugin', $this->get_sample_info_content(), 'Heroic Knowledge Base Plugin', array('tips', 'kb', 'tips') );
		}

		/**
		* Adds a sample knowledge base article
		* @param $title The title of the article
		* @param $content The content of the article
		* @param $category The category of the article
		* @param $tags An array of tags to assign to the article
		*/
		function add_sample_ht_kb_article($title = '', $content = '', $category = '', $tags = array() ){

			if( empty ( $content ) ){
				$content = $this->sample_content_generator();
			}

			$new_article = array(
				  'post_content'   => $content,
				  'post_name'      => $title,
				  'post_title'     => $title,
				  'post_status'    => 'publish',
				  'post_type'      => 'ht_kb'
				);

			$new_article_id = wp_insert_post($new_article);

			if( $new_article_id > 0 ){
				//ht_kb_categories
				$category_slug = sanitize_title($category);
				wp_set_object_terms( $new_article_id, $category_slug, 'ht_kb_category', true );

				//ht_kb_tags
				foreach ($tags as $key => $tag) {
				   $tag_slug = sanitize_title($tag);
					wp_set_object_terms( $new_article_id, $tag_slug, 'ht_kb_tag', true );
				}
			}
			
		}

		/**
		* Get the sample content
		* @return (String) Sample content
		*/
		function sample_content_generator(){
			$content = '';
			$content .= __('<h1>Sample Content</h1>', 'ht-knowledge-base');
			$content .= __('Sample content would go here', 'ht-knowledge-base');
			return $content;
		}

		/**
		* Remove all knowledge base data - articles, categories and tags
		*/
		function remove_all_ht_kb_data(){
			//remove articles
			$articles = get_posts( array( 'post_type' => 'ht_kb', 'posts_per_page' => -1) );
			foreach( $articles as $article ) {
				//delete post, bypass trash
				wp_delete_post( $article->ID, true);
			}
			//remove category terms
			$this->remove_terms_from_taxonomy('ht_kb_category');
			//remove tag terms
			$this->remove_terms_from_taxonomy('ht_kb_tag');
		}

		/**
		* Remove  the terms from a particular taxonomy
		*/
		function remove_terms_from_taxonomy($taxonomy){
			$args = array(
					'taxonomy' => $taxonomy,
					'hide_empty' => false,
					//bust cache
					'update_term_meta_cache' => false,
				);  
			$terms = get_terms( $args );
			$count = count($terms);
			if ( $count > 0 ){
				foreach ( $terms as $term ) {
					wp_delete_term( $term->term_id, $taxonomy );
				}
			}
		}

		/**
		* Get the sample content for an article
		* @return (String) Sample content
		*/
		function get_sample_article_content(){
			ob_start();
			@include(dirname(dirname(__FILE__)) . '/sample-articles/ht-kb-sample-article.php' );
			$sample = ob_get_contents();
			ob_end_clean();
			return $sample;

		}

		/**
		* Get the sample content for info
		* @return (String) Sample content
		*/
		function get_sample_info_content(){
			ob_start();
			@include(dirname(dirname(__FILE__)) . '/sample-articles/ht-kb-sample-info.php' );
			$sample = ob_get_contents();
			ob_end_clean();
			return $sample;
		}

	}

}

if (class_exists('HT_Knowledge_Base_Sample_Installer')) {
	new HT_Knowledge_Base_Sample_Installer();
}