<?php
/**
* Template helper functions
*/

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if(!function_exists('hkb_category_thumb_img')){
	/**
	* Print the category thumb img
	* @param (Object) $category The category (not required)
	*/
	function hkb_category_thumb_img($category=null){  
		$category_thumb_att_id  =  hkb_get_category_thumb_att_id($category);
		if( !empty( $category_thumb_att_id ) && $category_thumb_att_id!=0 ){
			$category_thumb_obj = wp_get_attachment_image_src( $category_thumb_att_id, 'hkb-thumb');                                
			$category_thumb_src = $category_thumb_obj[0];
			$alt = hkb_get_term_name( $category );

			echo '<img src="' . $category_thumb_src . '" class="hkb-category__icon" alt="' . $alt . '" />';
		}

		$category_thumb_svg  =  hkb_get_category_thumb_svg( $category );
		if( !empty( $category_thumb_svg ) && $category_thumb_svg!='' ){           
			//svg
			//@todo - review the theme control the size, positioning etc?
			echo stripslashes( $category_thumb_svg );
		}
	}
}

if(!function_exists('hkb_category_class')){
	/**
	* Print the category class
	* @param (Object) $category The category (not required)
	*/
	function hkb_category_class($category=null){
		$ht_kb_category_class = "hkb-category-hasicon";

		$category_thumb_att_id  =  hkb_get_category_thumb_att_id($category);
		if( !empty( $category_thumb_att_id ) && $category_thumb_att_id!=0 ){
			$ht_kb_category_class = "hkb-category-hasthumb";
		}

		echo $ht_kb_category_class;
	}
}

if(!function_exists('hkb_has_category_custom_icon')){
	/**
	* Print the category custom icon true/false (extended for SVG)
	* @param (Object) $category The category (not required)
	*/
	function hkb_has_category_custom_icon($category=null){
		$data_ht_category_custom_icon = false;

		//category thumb attachment
		$category_thumb_att_id  =  hkb_get_category_thumb_att_id($category);
		if( !empty( $category_thumb_att_id ) && $category_thumb_att_id!=0 ){
			$data_ht_category_custom_icon = true;
		}

		//category thumb svg
		$category_thumb_svg = hkb_get_category_thumb_svg($category);
		if( !empty( $category_thumb_svg ) && $category_thumb_svg!='' ){
			$data_ht_category_custom_icon = true;
		}

		return $data_ht_category_custom_icon;
	}
}


//if(!function_exists('hkb_has_category_custom_icon')){
//    /**
//    * Print the category custom icon true/false
//    * @param (Object) $category The category (not required)
//    */
//    function hkb_has_category_custom_icon($category=null){
//        $data_ht_category_custom_icon = false;
//
//        $category_thumb_att_id  =  hkb_get_category_thumb_att_id($category);
//        if( !empty( $category_thumb_att_id ) && $category_thumb_att_id!=0 ){
//            $data_ht_category_custom_icon = true;
//        }
//
//        return $data_ht_category_custom_icon;
//    }
//}

if(!function_exists('hkb_term_name')){
	/**
	* Print the term name
	* @param (Object) $category The category (not required)
	*/
	function hkb_term_name($category=null){
			echo hkb_get_term_name($category);      
	}
}

if(!function_exists('hkb_get_term_name')){
	/**
	* Return the term name
	* @param (Object) $category The category (not required)
	* @return (String) Term name or empty string
	*/
	function hkb_get_term_name($category=null){
		$term = hkb_get_term($category);
		if($term && isset($term->name)){
			return $term->name;
		} else {
			return '';
		}    
	}
}

if(!function_exists('hkb_get_term_desc')){
	/**
	* Return the term description
	* @param (Object) $category The category (not required)
	*/
	function hkb_get_term_desc($category=null){
		$hkb_term_desc = '';
		$term = hkb_get_term($category);
		if($term && isset($term->description)){
			$hkb_term_desc = $term->description;
		}
		return $hkb_term_desc;
	}
}
if(!function_exists('hkb_term_desc')){
	/**
	* Print the term description
	* @param (Object) $category The category (not required)
	*/
	function hkb_term_desc($category=null){
		echo hkb_get_term_desc($category);
	}
}

if(!function_exists('hkb_get_term_count')){
	/**
	* Return the term count
	* @param (Object) $category The category (not required)
	*/
	function hkb_get_term_count($category=null){
		$term = hkb_get_term( $category );
		$count = 0;
		$taxonomy = 'ht_kb_category';
		$args = array( 'child_of' => $term->term_id );

		$count = $term->count;
		$legacy_args = array(
					'taxonomy' => $taxonomy,
					'hide_empty' => false,
					//cache ok
					//'update_term_meta_cache' => false,
				);  
		//@since 3.0 updated get_terms call 
		$tax_terms = get_terms( array_merge( $legacy_args, $args ) );

		foreach ( $tax_terms as $tax_term ) {
			$count += $tax_term->count;
		}
		return $count;

	}
}

function wp_get_postcount($id) {
	//@todo - implement or remove this function
}

if(!function_exists('hkb_term_count')){
	/**
	* Print the term count
	* @param (Object) $category The category (not required)
	*/
	function hkb_term_count($category=null){
		echo hkb_get_term_count( $category );
	}
}

if(!function_exists('hkb_get_related_articles')){
	/**
	* Get related articles
	* @return (WP_Query) $related_articles The related articles query  
	*/
	function hkb_get_related_articles(){
		global $post, $orig_post;
		$related_articles = array();
		
		//check show related option
		if(!hkb_show_related_articles()){
			return $related_articles;
		}

		$orig_post = $post;
		$categories = get_the_terms($post->ID, 'ht_kb_category');
		$ht_kb_related_articles_category_ids = array();


		if ($categories) {  
			$category_ids = array();
			foreach($categories as $individual_category) 
				$category_ids[] = $individual_category->term_id;

			//apply filters
			$ht_kb_related_articles_category_ids = apply_filters( 'ht_kb_related_articles_category_ids', $category_ids );
			$ht_kb_related_articles_count = apply_filters( 'ht_kb_related_articles_count', 6 );

			$args=array(
				'post_type' => 'ht_kb',
				'tax_query' => array(
					array(
						'taxonomy' => 'ht_kb_category',
						'field' => 'term_id',
						'terms' => $ht_kb_related_articles_category_ids
					)
				),
				'post__not_in' => array($post->ID),
				'posts_per_page'=> $ht_kb_related_articles_count, // Number of related posts that will be shown.
				'ignore_sticky_posts'=>1
			);

			$related_articles = new wp_query( $args );

		}

		//apply ht_kb_related_articles filter
		$related_articles = apply_filters( 'ht_kb_related_articles', $related_articles, $orig_post, $ht_kb_related_articles_category_ids );        
			
		return $related_articles; 
	}
}

/**
* This function is designed to improve on hkb_get_related_articles by returning 
* an ordered list of articles respecting the archive_sortby and archive_sortorder settings
* It does not take into account the position of the current article in the listing
* so it not yet fully implemented
*/
if(!function_exists('hkb_get_related_articles_ordered')){
	/**
	* Get related articles (ordered version)
	* @return (WP_Query) $related_articles_ordered The related articles query  
	*/
	function hkb_get_related_articles_ordered(){
		global $post, $orig_post;
		$related_articles_ordered = array();
		
		//check show related option
		if(!hkb_show_related_articles()){
			return $related_articles_ordered;
		}

		$orig_post = $post;
		$categories = get_the_terms($post->ID, 'ht_kb_category');
		$ht_kb_related_articles_ordered_category_ids = array();


		if ($categories) {  
			$category_ids = array();
			foreach($categories as $individual_category) 
				$category_ids[] = $individual_category->term_id;

			//apply filters
			$ht_kb_related_articles_ordered_category_ids = apply_filters( 'ht_kb_related_articles_ordered_category_ids', $category_ids );
			$ht_kb_related_articles_ordered_count = apply_filters( 'ht_kb_related_articles_ordered_count', 6 );

			$args=array(
				'post_type' => 'ht_kb',
				'tax_query' => array(
					array(
						'taxonomy' => 'ht_kb_category',
						'field' => 'term_id',
						'terms' => $ht_kb_related_articles_ordered_category_ids
					)
				),
				'post__not_in' => array($post->ID),
				'posts_per_page'=> $ht_kb_related_articles_ordered_count, // Number of related posts that will be shown.
				'ignore_sticky_posts'=>1
			);

			$sort_meta_key = '';

			/* Ordering functionality */
			//get the user set sort by and sort order
			$user_sort_by = hkb_archive_sortby();
			$user_sort_order = hkb_archive_sortorder();

			$valid_sort_orders = array('date', 'title', 'comment_count', 'rand', 'modified', 'popular', 'helpful', 'custom');
			if ( in_array($user_sort_by, $valid_sort_orders) ) {
			  $sort_by = $user_sort_by;
			  $sort_order = ($user_sort_order=='asc') ? 'ASC' : 'DESC';
			} else {
			  // by default, display latest first
			  $sort_by = 'date';
			  $sort_order = 'DESC';
			}

			if($user_sort_by=='popular'){
			  $sort_by = 'meta_value_num';
			  $sort_meta_key = HT_KB_POST_VIEW_COUNT_KEY;
			}

			if($user_sort_by=='helpful'){
			  $sort_by = 'meta_value_num';
			  $sort_meta_key = HT_USEFULNESS_KEY;
			} 

			if($user_sort_by=='custom' && ( is_tax('ht_kb_category') ) ){
				$sort_by = 'meta_value_num date';
				$term = get_queried_object();
				if(property_exists($term, 'term_id')){
					$sort_meta_key = '_ht_article_order_'.$term->term_id;
				}
				
			}        

		   
			//set query 
		   $args['orderby'] =  $sort_by;
		   $args['order'] =  $sort_order;
		   $args['meta_key'] =  $sort_meta_key;

		   $related_articles_ordered = new wp_query( $args );

		}

		//apply ht_kb_related_articles_ordered filter
		$related_articles_ordered = apply_filters( 'ht_kb_related_articles_ordered', $related_articles_ordered, $orig_post, $ht_kb_related_articles_ordered_category_ids );        
			
		return $related_articles_ordered; 
	}
}

if(!function_exists('hkb_after_releated_post_reset')){
	/**
	* Reset afer related articles
	*/
	function hkb_after_releated_post_reset(){
		global $post, $orig_post;
		$post = $orig_post;
		wp_reset_postdata(); 
	}
}

if(!function_exists('hkb_post_format_class')){
	/**
	* Print post format class
	* @param (Int) $post_id The post id
	*/
	function hkb_post_format_class($post_id=null){
		$post_id = isset($post_id) ? $post_id : get_the_ID();
		//set post format class  
		if ( get_post_format( $post_id )=='video') { 
		  $ht_kb_format_class = 'format-video';
		} else {
		  $ht_kb_format_class = 'format-standard';
		} 

		echo $ht_kb_format_class;
	}
}

if(!function_exists('hkb_post_type_class')){
	/**
	* Print post type class
	* @param (Int) $post_id The post id
	*/
	function hkb_post_type_class($post_id=null){
		$post_id = isset($post_id) ? $post_id : get_the_ID();
		//post type 
		$post_type = get_post_type( $post_id );
		$ht_kb_type_class = 'hkb-post-type-' . $post_type;

		echo $ht_kb_type_class;
	}
}

if(!function_exists('hkb_term_link')){
	/**
	* Print term link
	* @param (Object) $term The term
	* @deprecated - use get_term_link($tax_term, 'ht_kb_category') instead
	*/
	function hkb_term_link($term){
		global $wp_query; 
		$term_link = get_term_link( $term );
		$link = is_wp_error( $term_link ) ? '#' : esc_url( $term_link );
		echo $link;
	}
}

if(!function_exists('hkb_get_category_custom_link')){
	/**
	* Get the category custom link
	* @param (Object) $category The category  (not required)
	* @return (String) The category custom link
	*/
	function hkb_get_category_custom_link($category=null){
		$term = hkb_get_term($category);
		//custom_link
		$custom_link = get_term_meta( $term->term_id, 'custom_link' , true ); 
		$category_custom_link = ''; 

		if(!empty($custom_link)){
			$category_custom_link = $custom_link;
		}

		return $category_custom_link;
	}
}

if(!function_exists('hkb_get_category_thumb_att_id')){
	/**
	* Get the category thumb attachment id
	* @param (Object) $category The category (not required)
	* @return (Int) Thumb attachment id
	*/
	function hkb_get_category_thumb_att_id($category=null){
		$term = hkb_get_term($category);
		//meta_image
		$meta_image = get_term_meta( $term->term_id, 'meta_image' , true ); 
		$category_thumb_att_id = 0;

		if(!empty($meta_image)){
			$category_thumb_att_id = $meta_image;
		}

		return $category_thumb_att_id;

	}
}

if(!function_exists('hkb_get_category_thumb_svg')){
	/**
	* Get the category thumb svg
	* @param (Object) $category The category (not required)
	* @return (String) The inline SVG
	*/
	function hkb_get_category_thumb_svg($category=null){
		$term = hkb_get_term($category);
		//meta_svg
		$meta_svg = get_term_meta( $term->term_id, 'meta_svg', true );
		$category_thumb_svg = '';

		if(!empty($meta_svg)){
			$category_thumb_svg = $meta_svg;
		}

		return $category_thumb_svg;

	}
}

if(!function_exists('hkb_get_category_color')){
	/**
	* Get the category colour
	* @param (Object) $category The category  (not required)
	* @return (String) The category colour
	*/
	function hkb_get_category_color($category=null){
		$term = hkb_get_term($category);
		//meta_color
		$meta_color = get_term_meta( $term->term_id, 'meta_color', true );
		$category_color = '#222'; 

		if(!empty($meta_color)){
			$category_color = $meta_color;
		}

		return $category_color;
	}
}

if(!function_exists('hkb_get_category_restrict_access_level')){
	/**
	* Get the restrict access level
	* @param (Object) $category The category  (not required)
	* @return (String) The category restrict access level
	*/
	function hkb_get_category_restrict_access_level($category=null){
		$term = hkb_get_term($category);
		//restrict_access
		$restrict_access = get_term_meta( $term->term_id, 'restrict_access', true );
		$restrict_access_level = 'public'; 

		if(!empty($restrict_access)){
			$restrict_access_level = $restrict_access;
		}

		return $restrict_access_level;
	}
}

if(!function_exists('hkb_get_restrict_access_level_label_from_key')){
	/**
	* Get the restrict access level from an access level
	* @param (String) $key Access level key
	* @return (String) The acceess level label
	*/
	function hkb_get_restrict_access_level_label_from_key($key=null){
		$label = '';
		$valid_restrict_access_levels = apply_filters( 'hkb_restrict_access_levels', array() );
		if(array_key_exists($key, $valid_restrict_access_levels)){
			$label = $valid_restrict_access_levels[$key];
		}
		return $label;
	}
}


if(!function_exists('ht_apply_schema')){
	/**
	* Applies schema
	* @itemprop is global attribute is used to add properties to an item. Every HTML element can have an itemprop attribute specified
	* @itemscope is a boolean global attribute that defines the scope of associated metadata.
	* @itemtype specifies the URL of the vocabulary that will be used to define itemprops (item properties) in the data structure
	* @pluggable
	* @param (
	*/
	function ht_apply_schema($itemprop='name', $itemscope=true, $itemtype='BreadcrumbList'){

			$itemprop_ouput = !empty($itemprop) ? sprintf('itemprop="%s"', $itemprop) : '';
			$itemprop_ouput = apply_filters( 'ht_apply_schema_itemprop', $itemprop_ouput, $itemprop, $itemscope, $itemtype );

			$itemscope_ouput = !empty($itemscope) ? sprintf('itemscope', $itemscope) : '';
			$itemscope_ouput = apply_filters( 'ht_apply_schema_itemscope', $itemscope_ouput, $itemprop, $itemscope, $itemtype );

			$itemtype_schema = apply_filters( 'ht_apply_schema_itemtype_schema', 'https://schema.org/');
			$itemtype_item = !empty($itemtype) ? $itemtype : '';
			$itemtype_item = apply_filters( 'ht_apply_schema_item', $itemtype_item, $itemprop, $itemscope, $itemtype );

			$itemtype_ouput = !empty($itemtype) ?  apply_filters( 'ht_apply_schema_itemtype', sprintf('itemtype="%s"', $itemtype_schema . $itemtype_item ) ) : '';    
			

			$schema_string = $itemprop_ouput . ' ' . $itemscope_ouput . ' ' . $itemtype_ouput;
			return apply_filters('ht_apply_schema', $schema_string );
	}//end function
}//end function exists

if(!function_exists('ht_echo_schema')){
	/**
	* Applies schema
	* @pluggable
	* @param $itemprop, $itemscope, $itemtype
	*/
	function ht_echo_schema($itemprop='name', $itemscope=true, $itemtype='BreadcrumbList'){

			echo ht_apply_schema($itemprop, $itemscope, $itemtype);
	}//end function
}//end function exists

/**
* Pagination function
*/
if ( ! function_exists( 'hkb_posts_nav_link' ) ) :
function hkb_posts_nav_link() {
	global $wp_query;
	$max_num_pages = $wp_query->max_num_pages;

	if ( $max_num_pages > 1 ) : ?>
		<div class="hkb-pagination">

			<?php if ( get_previous_posts_link() ) : ?>

				<span class="hkb-pagination__prev"><?php  previous_posts_link( __( 'Prev', 'ht-knowledge-base' ) ); ?></span>

			<?php endif; ?>

			<?php if ( get_next_posts_link() ) : ?>

				<span class="hkb-pagination__next"><?php next_posts_link( __( 'Next', 'ht-knowledge-base' ) ); ?></span>

			<?php endif; ?>
			
		</div>
	<?php endif;
}
endif;

/**
* WPML print ICL_LANGUAGE_CODE
*/
if(!function_exists('ht_print_icl_language_code')){
	/**
	* Prints ICL_LANGUAGE_CODE from WPML if defined
	* @pluggable
	*/
	function ht_print_icl_language_code(){
		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
			echo( ICL_LANGUAGE_CODE );	
			return;
		}
		echo false;
		return;
	}//end function
}//end function exists