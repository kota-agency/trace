<?php

get_header();

//$_post_type = get_queried_object();
//
//if(is_tax() || is_category() || is_tag()) {
//	$tax_obj = get_taxonomy($_post_type->taxonomy);
//	$_post_type->name = $tax_obj->object_type[0];
//}
//
//$args = [
//	'post_type' => 'page',
//	'meta_query' => [
//		[
//			'key' => 'page_archive',
//			'value' => $_post_type->name
//		]
//	]
//];
//
//$archive_query = new WP_Query($args);
//
//?>
<!---->
<?php //if ($archive_query->have_posts()) : ?>
<!--	--><?php //while ($archive_query->have_posts()) : $archive_query->the_post(); ?>
<!--		--><?php //$_id = $archive_query->post->ID; ?>
<!--		--><?php //the_content($_id); ?>
<!--	--><?php //endwhile; ?>
<!--	--><?php //wp_reset_postdata(); ?>
<?php //endif; ?>

<?php
  get_template_part('lib/blocks/page-header');
?>

<a href="<?php echo home_url('/feed'); ?>" class="link-rss-feed">
<div class="wrap-icon">
  <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
    viewBox="0 0 95 95" style="enable-background:new 0 0 95 95;" xml:space="preserve">
  <circle cx="17" cy="78" r="17"/>
  <path d="M0,15.6c43.8,0,79.3,35.6,79.4,79.4H95C95,42.5,52.5,0,0,0l0,0V15.6z"/>
  <path d="M0,42.8c28.8,0,52.2,23.4,52.2,52.2h15.6c0-37.5-30.4-67.9-67.9-67.9c0,0,0,0,0,0l0,0V42.8z"/>
  </svg>
</div>
  <span class="link">RSS feed</span>
</a>

<?php
  get_template_part('lib/blocks/post-loop');
?>

<?php get_footer();
