<?php

/**
 * Block: Page Links
 */

$classes = ['full-width', padding_classes()];
$item_count = count(get_field('items'));

if ($item_count <= 2) {
	$classes[] = 'page-links--remove-border';
}

?>
<?php if (have_rows('items')) : ?>
	<section <?= block_id(); ?> class="page-links <?= implode(' ', $classes); ?>" data-aos="fade">
		<div class="container">

			<div class="row page-links__row no-gutters" >
				<?php while (have_rows('items')) : the_row(); ?>

					<div class="col-lg-6 page-links__col">
						<?php get_component('page-link'); ?>


					</div>
				<?php endwhile; ?>
			</div>

		</div>
	</section><!-- .page-links -->

	<div class="page-links__modals">
		<?php while (have_rows('items')) : the_row(); ?>
			<?php get_component('modal'); ?>
		<?php endwhile; ?>
	</div>
<?php endif; ?>
