<?php
/**
* Self contained restrict access functionality
*/

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'HT_Knowledge_Base_Restrict_Access' ) ){
	class HT_Knowledge_Base_Restrict_Access {

		//constructor
		function __construct() {

			//define access levels
			add_filter( 'hkb_restrict_access_levels', array( $this,  'hkb_restrict_access_levels' ), 10, 2 );

			//posts where filter
			add_filter( 'posts_where',  array( $this,  'hkb_restrict_posts' ), 10 );

			//get terms filter
			add_filter( 'hkb_master_tax_terms',  array( $this,  'hkb_restrict_terms' ), 10 ); 

			//filter search
			add_filter( 'pre_get_posts', array( $this, 'hkb_restrict_search'), 99 );

			//posts where taxonomy filter
			add_filter( 'posts_where',  array( $this,  'hkb_restrict_taxonomy' ), 10 ); 

			//restrict hkb post content (by term)
			add_filter( 'the_excerpt', array( $this,  'hkb_restrict_the_excerpt' ), 499 ); 
			add_filter( 'the_content', array( $this,  'hkb_restrict_the_content' ), 500 ); 

			//restrict hkb post title (by term)
			add_filter( 'the_title', array( $this,  'hkb_restrict_the_title' ), 500 ); 

			//admin bar helper
			add_action( 'admin_bar_menu', array( $this, 'hkb_add_admin_toolbar_helper' ), 10000 ); 

		}

		/**
		* Restrict levels filter
		* @param (Array) $levels Existing access levels array
		* @param (String) $location The location where (for later use eg archive vs category)
		* @return $levels The various access levels (array as key=>label pairs)
		*/
		function hkb_restrict_access_levels($levels=null, $location='archive'){
			//init levls array
			if(!is_array($levels)){
					$levels = array();
			}
			//add public
			$levels['public'] = __('Public', 'ht-knowledge-base');
			//add logged in
			$levels['loggedin'] = __('Logged In', 'ht-knowledge-base');

			//private not yet supported in beta
			//$levels['private'] = __('Private', 'ht-knowledge-base');

			return $levels;
		}


		/**
		* Simple posts restrict 
		* @return $where The filtered where clause
		*/
		function hkb_restrict_posts($where){
			//if the current user does not have the read_private_post cap, remove any ht_kb items from post query
			$hkb_posts_access_restrict_level = function_exists('hkb_restrict_access') ? hkb_restrict_access() : '';
			//apply logic
			switch ($hkb_posts_access_restrict_level) {
				case 'private':
					if( !current_user_can('read_private_posts') ){
							//remove all post ht_kb post types 
							$where .= " AND post_type != 'ht_kb' ";
					}
					break;
				case 'loggedin':
					if( !is_user_logged_in() ){
							//remove all post ht_kb post types 
							$where .= " AND post_type != 'ht_kb' ";
					}
					break;
				
				default:
					break;
			}

			return $where;
		}

		/**
		* Terms restrict 
		* This will remove terms resolved with the get_terms call, based on access restriction
		* Behaviour - In an archive view, term will not show if not public. In term archive, no posts will be displayed 
		* @return $terms The filtered terms
		*/
		function hkb_restrict_terms($terms){
			//don't use in admin?
			if(is_admin()){
				return $terms;
			}

			if(is_array( $terms )){
				foreach ( $terms as $term => $term_object ) {
					//check current terms is an article category
					if(  ( isset($term_object->taxonomy) && 'ht_kb_category' == $term_object->taxonomy )  ){
						
						//FIRST restrict term based on the global hkb restrict access setting
						$hkb_posts_access_restrict_level = function_exists('hkb_restrict_access') ? hkb_restrict_access() : '';                      

						 switch ($hkb_posts_access_restrict_level) {
							case 'private':
								if( !current_user_can('read_private_posts') ){
									//unset term
									unset($terms[$term]);
								}
								break;
							case 'loggedin':
								if( !is_user_logged_in() ){
									//unset term
									unset($terms[$term]);
								}
								break;									
							default:
									break;
						}

						

						//NEXT restrict term based on the term meta
						$category_restrict_access = hkb_get_category_restrict_access_level($term_object);
						//apply logic
						switch ($category_restrict_access) {
								case 'private':
									if( !current_user_can('read_private_posts') ){
										//unset term
										unset($terms[$term]);
									}
									break;
								case 'loggedin':
									if( !is_user_logged_in() ){
										//unset term
										unset($terms[$term]);
									}
									break;                            
								default:
									break;
						}
					}
				}
			}

			return $terms;
		}


		/**
		* Simple taxonomy is_tax filter for query where clause
		* @return $where The filtered where clause
		*/
		function hkb_restrict_taxonomy($where){
				//@since 2.13.0 - refactored to support ANSI_QUOTES defaults
				if( is_tax('ht_kb_category') ) {
						$term_id = get_queried_object_id();
						$hkb_category_access_restrict_level = hkb_get_category_restrict_access_level($term_id);
						switch ($hkb_category_access_restrict_level) {
							case 'private':
								if( !current_user_can('read_private_posts') ){
									//remove all post ht_kb post types 
									$where .= " AND post_type != 'ht_kb' ";
								}
								break;
							case 'loggedin':
								if( !is_user_logged_in() ){
									//remove all post ht_kb post types 
									$where .= " AND post_type != 'ht_kb' ";
								}
								break;
							
							default:
								break;
						}
				}

				return $where;
		}


		/**
		* Search restrict
		* Pending - restrict status is inferred from global hkb_restrict_access() and hkb_get_category_restrict_access_level($term_id)
		* this is expensive, needs review
		*/
		function hkb_restrict_search( $query ){
				global $wp_query;

				//if the wp_query is empty, early exit
				if( empty( $wp_query ) ){
						return $query;
				}

				//remove own filter
				remove_filter( 'pre_get_posts', array( $this, 'hkb_restrict_search'), 99 );

				//get the post type from the query
				$post_type = get_query_var( 'post_type', [] );

				//hard cast
				$post_type = (array)$post_type;

				if( ht_kb_is_ht_kb_search() && in_array( 'ht_kb', $post_type ) ){
							 
								$restricted_article_id_array = [];

								$args = array(
											'taxonomy' => 'ht_kb_category',
											'hide_empty' => false,
											//cache use OK
											//'update_term_meta_cache' => false,
								);  
								//@since 3.0 updated get_terms call 
								$categories = get_terms( $args );

								foreach ($categories as $key => $term) {
										$term_id=$term->term_id;
										$hkb_category_access_restrict_level = hkb_get_category_restrict_access_level($term_id);
										switch ($hkb_category_access_restrict_level) {
											case 'private':
												if( !current_user_can('read_private_posts') ){
													//get posts in this term and add them to the restricted_article_id_array
													$restricted_articles = get_posts(array(
														'post_type' => 'ht_kb',
														'numberposts' => -1,
														'tax_query' => array(
															array(
																'taxonomy' => 'ht_kb_category',
																'field' => 'term_id', 
																'terms' => $term_id, /// Where term_id of Term 1 is "1".
																'include_children' => false
															)
														)
													));
													//loop restricted articles and add the ID
													foreach ($restricted_articles as $key => $article) {
														$restricted_article_id_array[] = $article->ID;
													}
												}
													break;
											case 'loggedin':
												if( !is_user_logged_in() ){
													//get posts in this term and add them to the restricted_article_id_array
													$restricted_articles = get_posts(array(
														'post_type' => 'ht_kb',
														'numberposts' => -1,
														'tax_query' => array(
															array(
																'taxonomy' => 'ht_kb_category',
																'field' => 'term_id', 
																'terms' => $term_id, /// Where term_id of Term 1 is "1".
																'include_children' => false
															)
														)
													));
													//loop restricted articles and add the ID
													foreach ($restricted_articles as $key => $article) {
														$restricted_article_id_array[] = $article->ID;
													}

												}
												break;
											
											default:
												break;
										}
								}

							 //kb search - remove posts belonging to restricted categories                   
								$query->set( 'post__not_in', $restricted_article_id_array );
				}  

				//readd filter
				add_filter( 'pre_get_posts', array( $this, 'hkb_restrict_search'), 99 );      

				return $query;
		}


		/**
		* Simple restrict filter on the_content (the_excerpt) for when article is in restricted category 
		* @return $content The filtered content
		*/
		function hkb_restrict_the_excerpt($content){
				global $post;

				if(isset($post->post_type) || 'ht_kb' != $post_type){
						return $content;
				}

				return apply_filters('hkb_restrict_the_excerpt', $this->hkb_restrict_the_content);
		}

		/**
		* Simple restrict filter on the_content for when article is in restricted category
		* @return $content The filtered content
		*/
		function hkb_restrict_the_content($content){
				global $post;

				if( !isset($post->post_type) || 'ht_kb' != $post->post_type){
						return $content;
				}

				//return content if we're in the ht_kb archive and not in a widget
				if( is_post_type_archive( 'ht_kb' ) && ( is_main_query() && in_the_loop() ) ){
						return $content;
				}

				//don't use if search result
				if( ht_kb_is_ht_kb_search() ){
						return $content;
				}


				if( apply_filters('stop_hkb_restrict_the_content', false) ){
						return $content;
				}

				//get terms for the post
				$terms = wp_get_post_terms( $post->ID, 'ht_kb_category' );

				foreach ($terms as $index => $term) {
					// put the term ID into a variable
					$t_id = $term->term_id;
					
					//restrict_access
					$restrict_access = get_term_meta( $t_id, 'restrict_access', true ); 

					//get category restriction
					$category_restrict_access = ( $restrict_access ) ? $restrict_access : '';

					switch ($category_restrict_access) {
						case 'private':
							if( !current_user_can('read_private_posts') ){
									//state restricted
									$content = __('This content is not available', 'ht-knowledge-base');
							}
							break;
						case 'loggedin':
							if( !is_user_logged_in() ){
									//state restricted
									$content = __('You must log in to view this article', 'ht-knowledge-base');
							}
							break;								
						default:
								break;
					}

				}

				return apply_filters('hkb_restrict_the_content', $content);
		}

		/**
		* Simple restrict filter on the_title for when article is in restricted category
		* @return $title The filtered title
		*/
		function hkb_restrict_the_title($title){
				global $post;

				if( !isset($post->post_type) || 'ht_kb' != $post->post_type){
						return $title;
				}

				if( $post->post_title != $title ){
						return $title;
				}

				//return title if we're in the ht_kb archive and not in a widget
				if( is_post_type_archive( 'ht_kb' ) && ( is_main_query() && in_the_loop() ) ){
						return $title;
				}

				//don't use if search result
				if( ht_kb_is_ht_kb_search() ){
						return $title;
				}


				if( apply_filters('stop_hkb_restrict_the_title', false) ){
						return $title;
				}

				//get terms for the post
				$terms = wp_get_post_terms( $post->ID, 'ht_kb_category' );

				foreach ($terms as $index => $term) {
					// put the term ID into a variable
					$t_id = $term->term_id;

					//restrict_access
					$restrict_access = get_term_meta( $t_id, 'restrict_access', true ); 

					//get category restriction
					$category_restrict_access = ( $restrict_access ) ? $restrict_access : '';

					switch ($category_restrict_access) {
						case 'private':
							if( !current_user_can('read_private_posts') ){
									//state restricted
									$title = __('Private article', 'ht-knowledge-base');
							}
							break;
						case 'loggedin':
							if( !is_user_logged_in() ){
									//state restricted
									$title = __('Private article', 'ht-knowledge-base');
							}
							break;								
						default:
								break;
					}

				}

				return apply_filters('hkb_restrict_the_title', $title);
		}

		/**
		* Admin toolbar helper
		*/
		function hkb_add_admin_toolbar_helper($admin_bar){
				global $post;

				//exit if not a single article
				if(!is_singular( 'ht_kb' )){
						return;
				}

				$dashicon_class = 'dashicons-info-outline';
				$display_text = '';
				$display_title = __('Article is not restricted, all users can view this article', 'ht-knowledge-base');
				$edit_link = admin_url( sprintf('post.php?post=%s&action=edit', $post->ID ) ); 


				//apply stop filters
				if( !apply_filters( 'ht_kb_display_visibility_helper_on_front_end', true ) ){
						return;
				}

				//get terms for the post
				$terms = wp_get_post_terms( $post->ID, 'ht_kb_category' );

				foreach ($terms as $index => $term) {
						// put the term ID into a variable
						$t_id = $term->term_id;
						
						//restrict_access
						$restrict_access = get_term_meta( $t_id, 'restrict_access', true ); 

						//get category restriction
						$category_restrict_access = ( $restrict_access ) ? $restrict_access : '';

						switch ($category_restrict_access) {
							case 'private':
								if( !current_user_can('read_private_posts') ){
										//state restricted
										$display_title = sprintf( __('Only users that can read private posts can view an article in this category (%s)', 'ht-knowledge-base'), $term->name );
										$dashicon_class = 'dashicons-hidden';
										$edit_link = admin_url( sprintf('term.php?taxonomy=ht_kb_category&tag_ID=%s&post_type=ht_kb', $t_id ) ); 
								}
								break;
							case 'loggedin':
								if( is_user_logged_in() ){
										//state restricted
										$display_title = sprintf( __('The user must log in to view an article in this category (%s)', 'ht-knowledge-base'), $term->name );
										$dashicon_class = 'dashicons-lock';
										$edit_link = admin_url( sprintf('term.php?taxonomy=ht_kb_category&tag_ID=%s&post_type=ht_kb', $t_id ) ); 
								}
								break;										
							default:
									break;
						}

				}

				if( post_password_required( $post->ID ) ){
						$display_title = __('A password is required to view this article', 'ht-knowledge-base');
						$dashicon_class = 'dashicons-admin-network';
				}

				if( 'private' === $post->post_status ){
						$display_title = __('This is a private article, only you and editors can see it', 'ht-knowledge-base');
						$dashicon_class = 'dashicons-welcome-view-site';
				}

				$global_kb_restrict = hkb_restrict_access();
				if( 'loggedin' == $global_kb_restrict ){
						$display_title = __('The entire knowledge base is hidden to non logged-in users', 'ht-knowledge-base');
						$dashicon_class = 'dashicons-admin-site';
						$edit_link = admin_url( 'edit.php?post_type=ht_kb&page=ht_knowledge_base_settings_page' ); 
				}

				$admin_bar->add_menu( array(
								'id'    => 'kb-visibility-helper',
								'title' => '<div class="ht-kb-menu-icon ht-kb-visibility ' . $dashicon_class . '" style="
																		font-family: dashicons;
																		float: left;
																		width: 19px !important;
																		height: 30px !important;
																		background-repeat: no-repeat;
																		background-position: 0 6px;
																		background-size: 20px;
																		opacity: 0.6;
														"></div><span class="ab-label">' . esc_attr( $display_text ) . '</span>',
								'href'  => $edit_link,
								'meta'  => array(
												'title' => esc_attr( $display_title ),  
												'class' => 'ht-kb-visibility-admin-bar-helper' ,         
										),
								)
						);

		}


	} //end class

} //end class exists

//run the module
if( class_exists( 'HT_Knowledge_Base_Restrict_Access' ) ){
		new HT_Knowledge_Base_Restrict_Access();
}