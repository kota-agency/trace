<?php

get_header();


//$args = [
//	'post_type' => 'page',
//	'meta_query' => [
//		[
//			'key' => 'page_archive',
//			'value' => 'post'
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
get_template_part('lib/blocks/post-loop');

?>

<?php get_footer();
