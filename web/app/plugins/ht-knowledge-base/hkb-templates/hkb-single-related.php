<?php
/**
 * Template for displaying related articles
 *
 * @package hkb-templates/
 */

?>

<?php $related_articles = hkb_get_related_articles(); ?>

<?php if ( ! empty( $related_articles ) && $related_articles->have_posts() ) : ?>
<!-- .hkb-article__related -->     
	<div class="hkb-article-related">
		<h3 class="hkb-article-related__title"><?php _e( 'Related Articles', 'ht-knowledge-base' ); ?></h3>
		<ul class="hkb-article-list">
		<?php
		while ( $related_articles->have_posts() ) {
				$related_articles->the_post();
			?>
								
			<li>
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</li>

		<?php } ?>
		</ul>
	</div>
<!-- /.hkb-article__related -->
<?php endif; ?>

<?php
	// important - reset the post
	hkb_after_releated_post_reset();
?>
