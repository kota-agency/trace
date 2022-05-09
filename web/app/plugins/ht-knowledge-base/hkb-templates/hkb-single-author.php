<?php
/**
 * Template for author single
 *
 * @package hkb-templates/
 */

?>

<?php if ( function_exists( 'get_the_author_meta' ) && hkb_show_author_display( 'single' ) ) : ?>
	<div class="hkb-article-author">
		<?php if ( ! is_author() ) : ?>
			<h3 class="hkb-article-author__title">
			<?php _e( 'About The Author', 'ht-knowledge-base' ); ?>
			</h3>
		<?php endif; ?>
		<div class="hkb-article-author__avatar">
			<?php if ( function_exists( 'get_avatar' ) ) : ?> 
				<?php echo get_avatar( get_the_author_meta( 'email' ), '70' ); ?>
			<?php endif; ?>
		</div>
		<strong class="hkb-article-author__name">
			<?php echo get_the_author(); ?>
		</strong>
		<div class="hkb-article-author__bio">
			<?php if ( get_the_author_meta( 'description' ) ) : ?>
				<?php the_author_meta( 'description' ); ?>
			<?php else : ?>
				<?php printf( __( '%s has not written a bio yet', 'ht-knowledge-base' ), get_the_author() ); ?>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>
