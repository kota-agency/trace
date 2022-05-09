<?php
/**
* HKB Widgets
* Articles widget
*/

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

//@todo if comments for KB has been disabled do we show the option to show comments?

class HT_KB_Articles_Widget extends WP_Widget {

		private $defaults;

		/**
		* Widget Constructor
		* Specifies the classname and description, instantiates the widget,
		* loads localization files, and includes necessary stylesheets and JS where necessary
		*/
		public function __construct() {

				//update classname and description
				parent::__construct(
						'ht-kb-articles-widget',
						__( 'Knowledge Base Articles', 'ht-knowledge-base' ),
						array(
							'classname'	=>	'hkb_widget_articles',
							'description'	=>	__( 'A widget for displaying Knowledge Base articles', 'ht-knowledge-base' )
						)
				);

				$default_widget_title = __('Knowledge Base Articles', 'ht-knowledge-base');

				$this->defaults = array(
						'title' => $default_widget_title,
						'num' => '5',
						'sort_by' => '',
						'asc_sort_order' => '',
						'comment_num' => '',
						'rating' => '',
						'category' => 'all',
						'thumb' => 1,
						'contextual' => 0,
					);

		} // end constructor

		//Widget API Functions

		/**
		* Outputs the content of the widget.
		*
		* @param array args The array of form elements
		* @param array instance The current instance of the widget
		*/
		public function widget( $args, $instance ) {
				global $post, $ht_kb_article_widget_render, $ht_kb_article_widget_category;

				//global $ht_kb_article_widget_render for detecting if in this widget
				$ht_kb_article_widget_render = true;

				extract( $args, EXTR_SKIP );

				//temp store for current post id
				$current_post_id = ( $post && $post->ID ) ? $post->ID : 0;

				$instance = wp_parse_args( $instance, $this->defaults );

				//$title = $instance['title'];
				$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

				$meta_key = '';

				$valid_sort_orders = array('date', 'title', 'comment_count', 'rand', 'modified', 'popular', 'helpful', 'custom');
				if ( in_array($instance['sort_by'], $valid_sort_orders) ) {
						$sort_by = $instance['sort_by'];
						$sort_order = (bool) $instance['asc_sort_order'] ? 'ASC' : 'DESC';
				} else {
						// by default, display latest first
						$sort_by = 'date';
						$sort_order = 'DESC';
				}

				if($instance['sort_by']=='popular'){
						$sort_by = 'meta_value_num date';
						$meta_key = HT_KB_POST_VIEW_COUNT_KEY;
				}

				if($instance['sort_by']=='helpful'){
						$sort_by = 'meta_value_num date';
						$meta_key = HT_USEFULNESS_KEY;
				}

				if($instance['sort_by']=='custom'){
					 $sort_by = 'meta_value_num date';
					 $meta_key = '_ht_article_order_'.$instance['category'];
				}        

				// Setup time/date
				// TODO: is this still needed?
				$post_date = the_date( 'Y-m-d','','', false );
				$month_ago = date( "Y-m-d", mktime(0,0,0,date("m")-1, date("d"), date("Y")) );
				if ( $post_date > $month_ago ) {
				$post_date = sprintf( __( '%1$s ago', 'ht-knowledge-base', 'ht-knowledge-base' ), human_time_diff( get_the_time('U'), current_time('timestamp') ) );
				} else {
					$post_date = get_the_date();
				}


				$args = array(
						'post_type' => 'ht_kb',
						'orderby' => $sort_by,
						'order' => $sort_order,
						'meta_key' => $meta_key,
						'posts_per_page' => $instance['num'],
						'ignore_sticky_posts' => 1
				);

				$category = $instance['category'];

				//override category if contextual selected, for single ht_kb articles
				if( $instance['contextual'] && is_singular( 'ht_kb' ) ){
					$get_object_term_args = array('orderby' => 'name', 'order' => 'ASC', 'fields' => 'ids');
					$object_terms = wp_get_object_terms( $post->ID, 'ht_kb_category', $get_object_term_args );
					$category = $object_terms;
					//exclude self by default, unless ht_kb_articles_widget_exclude_self filter returns false
					if( apply_filters('ht_kb_articles_widget_exclude_self', true ) ){
						$args['post__not_in'] = array( $post->ID );
					}
				}

				//override category if contextual selected, for ht_kb categories
				if( $instance['contextual'] && is_tax( 'ht_kb_category' ) ){
					$object_terms = [];
					$object_terms[] = get_queried_object_id();
					$category = $object_terms;
				}

				if($category=='' || $category=='all'){
						//do nothing - no tax query required
				} else {
						//set global $ht_kb_article_widget_category
						$ht_kb_article_widget_category = $category;
						//tax query
						$args['tax_query'] = array(
						array(
						'taxonomy' => 'ht_kb_category',
						'field' => 'term_id',
						'terms' => $category,
						'operator' => 'IN'
						));
				}  

				echo $before_widget;

				if ( $title )
						echo $before_title . $title . $after_title;

				$wp_query = new WP_Query($args);
				if($wp_query->have_posts()) : ?>

				<ul>

					<?php 
						while($wp_query->have_posts()) : 
							$wp_query->the_post(); 
							//check if currently viewing post is the same as the same item in the article list loop
							$is_current_class = ( $current_post_id == get_the_ID() ) ? 'ht-kb-articles-widget__current-post' : '';
							//apply filters
							$is_current_class = apply_filters( 'ht_kb_articles_widget_is_current_class', $is_current_class, $post );
					?>
						<li> 
							<a class="hkb-widget__entry-title <?php echo $is_current_class; ?>" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</li>
					<?php endwhile; ?>

				</ul>

				<?php endif;

				$ht_kb_article_widget_render = false;

				wp_reset_postdata();
				echo $after_widget;

		} // end widget

		/**
		* Processes the widget's options to be saved.
		* @param array new_instance The previous instance of values before the update.
		* @param array old_instance The new instance of values to be generated via the update.
		*/
		public function update( $new_instance, $old_instance ) {

				$instance = $old_instance;

				//update widget's old values with the new, incoming values
				$instance['title'] = isset( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : $this->defaults['title'];
				$instance['category'] = isset( $new_instance['category'] ) ? $new_instance['category'] : $this->defaults['category'];
				$instance['num'] = isset( $new_instance['num'] ) ? $new_instance['num'] : $this->defaults['num']; 
				$instance['sort_by'] = isset( $new_instance['sort_by'] ) ? $new_instance['sort_by'] : $this->defaults['sort_by']; 
				$instance['asc_sort_order'] = isset( $new_instance['asc_sort_order'] ) && $new_instance['asc_sort_order'] ? 1 : 0;
				//$instance['comment_num'] = isset( $new_instance['comment_num'] ) ? 1 : 0;
				//$instance['rating'] = isset( $new_instance['rating'] ) ? 1 : 0;
				$instance['contextual'] = isset( $new_instance['contextual'] ) && $new_instance['contextual'] ? 1 : 0;
				//thumb is unused

				return $instance;

		} // end widget

		/**
		* Generates the administration form for the widget.
		* @param array instance The array of keys and values for the widget.
		*/
		public function form( $instance ) {

			$instance = wp_parse_args((array) $instance, $this->defaults);

			$args = array(
					'taxonomy' => 'ht_kb_category',
					//cache buster
					'update_term_meta_cache' => false,
			);  
			//@since 3.0 updated get_terms call 
			$categories = get_terms( $args );

			// Store the values of the widget in their own variable

			$title = strip_tags($instance['title']);
			$num = $instance['num'];
			$sort_by = $instance['sort_by'];
			$asc_sort_order = $instance['asc_sort_order'];
			//$comment_num = $instance['comment_num'];
			//$rating = $instance['rating'];
			$category = $instance['category'];
			$contextual = $instance['contextual'];
			?>
			<label for="<?php echo $this->get_field_id("title"); ?>">
				<?php _e( 'Title', 'ht-knowledge-base' ); ?>
				:
				<input type="text" class="widefat" id="<?php echo $this->get_field_id("title"); ?>" name="<?php echo $this->get_field_name("title"); ?>" type="text" value="<?php echo esc_attr($instance["title"]); ?>" />
			</label>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id("category"); ?>">
					<?php _e( 'Category', 'ht-knowledge-base' ); ?>
					:
					<select id="<?php echo $this->get_field_id("category"); ?>" name="<?php echo $this->get_field_name("category"); ?>" class="ht-kb-widget-admin-dropdown">
						 <option value="all"<?php selected( $instance["category"], "all" ); ?>><?php _e('All', 'ht-knowledge-base'); ?></option>
						<?php foreach ($categories as $category): ?> 
							<option value="<?php echo $category->term_id; ?>"<?php selected( $instance["category"], $category->term_id ); ?>><?php echo $category->name; ?></option>
						<?php endforeach; ?>
					</select>
				</label>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id("num"); ?>">
					<?php _e( 'Number of posts to show', 'ht-knowledge-base' ); ?>
					:
					<input style="text-align: center;" id="<?php echo $this->get_field_id("num"); ?>" name="<?php echo $this->get_field_name("num"); ?>" type="text" value="<?php echo absint($instance["num"]); ?>" size='3' />
				</label>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id("sort_by"); ?>">
					<?php _e( 'Sort by', 'ht-knowledge-base' ); ?>
					:
					<select id="<?php echo $this->get_field_id("sort_by"); ?>" name="<?php echo $this->get_field_name("sort_by"); ?>" class="ht-kb-widget-admin-dropdown">
						<option value="date"<?php selected( $instance["sort_by"], "date" ); ?>><?php _e( 'Date', 'ht-knowledge-base' ); ?></option>
						<option value="title"<?php selected( $instance["sort_by"], "title" ); ?>><?php _e( 'Title', 'ht-knowledge-base' ); ?></option>
						<option value="comment_count"<?php selected( $instance["sort_by"], "comment_count" ); ?>><?php _e( 'Number of comments', 'ht-knowledge-base' ); ?></option>
						<option value="rand"<?php selected( $instance["sort_by"], "rand" ); ?>><?php _e( 'Random', 'ht-knowledge-base' ); ?></option>
						<option value="modified"<?php selected( $instance["sort_by"], "modified" ); ?>><?php _e( 'Modified', 'ht-knowledge-base' ); ?></option>
						<option value="popular"<?php selected( $instance["sort_by"], "popular" ); ?>><?php _e( 'Popular', 'ht-knowledge-base' ); ?></option>
						<option value="helpful"<?php selected( $instance["sort_by"], "helpful" ); ?>><?php _e( 'Helpful', 'ht-knowledge-base' ); ?></option>
						<option value="custom"<?php selected( $instance["sort_by"], "custom" ); ?>><?php _e( 'Custom', 'ht-knowledge-base' ); ?></option>
					</select>
				</label>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('contextual'); ?>">
					<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('contextual'); ?>" name="<?php echo $this->get_field_name('contextual'); ?>"<?php checked( (bool) $instance["contextual"], true ); ?> />
					<?php _e( 'Only display articles in the same category', 'ht-knowledge-base' ); ?>
				</label>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id("asc_sort_order"); ?>">
					<input type="checkbox" class="checkbox"
			id="<?php echo $this->get_field_id("asc_sort_order"); ?>"
			name="<?php echo $this->get_field_name("asc_sort_order"); ?>"
			<?php checked( (bool) $instance["asc_sort_order"], true ); ?> />
					<?php _e( 'Reverse sort order (ascending)', 'ht-knowledge-base' ); ?>
				</label>
			</p>
			<?php if ( function_exists('the_post_thumbnail') && current_theme_supports('post-thumbnails') ) : ?>
			<p style="display:none;"><!-- THIS FEATURE IS NOT USED IN THIS VERSION -->
				<label for="<?php echo $this->get_field_id('thumb'); ?>">
					<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('thumb'); ?>" name="<?php echo $this->get_field_name('thumb'); ?>"<?php checked( (bool) $instance["thumb"], true ); ?> />
					<?php _e( 'Show post thumbnail', 'ht-knowledge-base' ); ?>
				</label>
			</p>
			<?php endif; ?>
		<?php } // end form

} // end class

//Action Hook
//deprecated PHP
//add_action( 'widgets_init', create_function( '', 'register_widget("HT_KB_Articles_Widget");' ) );

add_action( 'widgets_init', function(){
	register_widget( 'HT_KB_Articles_Widget' );
});