<?php
/**
 * Template for displaying heroic knowledgebase category archive content
 *
 * @package hkb-templates/
 */

?>

<?php $hkb_current_term_id = get_queried_object()->term_id; ?>

<div class="hkb-categoryheader">

	<?php if ( hkb_has_category_custom_icon( $hkb_current_term_id ) ) : ?>
		<div class="hkb-categoryheader__icon" data-hkb-cat-icon="<?php echo hkb_has_category_custom_icon( $hkb_current_term_id ); ?>">
			<?php hkb_category_thumb_img( $hkb_current_term_id ); ?>
		</div>
	<?php endif; ?>

	<div class="hkb-categoryheader__content">
		<h1 class="hkb-categoryheader__title"><?php hkb_term_name(); ?></h1>
		<?php if ( get_queried_object()->description != '' ) : ?>
			<div class="hkb-categoryheader__description">
				<?php echo esc_html( get_queried_object()->description ); ?>
			</div>
		<?php endif; ?>
	</div>

</div>

<?php hkb_get_template_part( 'hkb-subcategories', 'taxonomy' ); ?>

<?php if ( have_posts() ) : ?>

	<div class="hkb-article-grid">

	<?php
	while ( have_posts() ) :
		the_post();
		?>

		<?php hkb_get_template_part( 'hkb-content-article', 'taxonomy' ); ?>

	<?php endwhile; ?>

	</div>

	<?php hkb_posts_nav_link(); ?>

<?php else : ?>

	<?php $subcategories = hkb_get_subcategories( hkb_get_term_id() ); ?>
	<?php if ( ! $subcategories ) : ?>
		<p><?php esc_html_e( 'No articles in this category.', 'ht-knowledge-base' ); ?></p>
	<?php endif; ?>

<?php endif; ?>
