<?php
/**
 * Template for the main entry content
 *
 * @package hkb-templates/
 */

?>

<!-- .hkb-article__content -->
<div class="hkb-article__content">
	<?php the_content(); ?>
</div>
<!-- /.hkb-article__content -->

<?php wp_link_pages(
	array(
		'before' => '<div class="hkb-article__links">' . __( 'Pages:', 'ht-knowledge-base' ),
		'after'  => '</div>',
	)
); ?>
