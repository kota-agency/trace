<?php
//Template Name: Cookie
get_header();?>

<style>
	.page-header.page-header.page-header{
		padding-bottom: 0;
	}
</style>

<?php

/**
 * Block: Page Header
 */

$classes = ['full-width', padding_classes()];

$label = get_field('label');
$title = get_field('title');
$image = wp_get_attachment_image(get_field('image'), 'full');
$copy = get_field('copy');
$form_id = get_field('form_id');

?>

<section class="page-header <?= implode(' ', $classes); ?>">
	<div class="container" data-aos="fade">
		<div class="row">
			<div class="col-12">
				<?php if ($label) : ?>
					<h6 class="label"><?= $label; ?></h6>
				<?php endif; ?>
				<?php if ($title) : ?>
					<h1><?= $title; ?></h1>
				<?php endif; ?>
			</div>
		</div>
		<?php if ($copy) : ?>
			<div class="row">
				<div class="col-lg-6 page-header__copy">
					<?= $copy; ?>
				</div>
			</div>
		<?php endif; ?>
		<?php if ($form_id) : ?>
			<div class="page-header__form">
				<?php gravity_form($form_id, false, false, false, null, true); ?>
			</div>
		<?php endif; ?>
	</div>
	<?php if ($image) : ?>
		<div class="page-header__image">
			<div data-aos="fade-down-left">
				<?= $image; ?>
			</div>
		</div>
	<?php endif; ?>
</section><!-- .page-header -->

<?php

/**
 * Block: Half Width Content
 */

$classes2 = ['full-width', padding_classes()];

$side_image2 = wp_get_attachment_image(get_field('half_width_content_side_image'), 'full');
$offset2 = get_field('half_width_content_offset');
$copy2 = get_field('half_width_content_copy');

?>

<?php if ($copy2) : ?>
	<section class="half-width-content <?= implode(' ', $classes2); ?>" data-aos="fade">
		<div class="container">
			<div class="row">
				<div class="col-lg-6 <?php if ($offset2) : ?>offset-lg-1<?php endif; ?> order-2 order-lg-1 last-margin">
					<?= $copy2; ?>
				</div>
				<div class="col-lg-5 order-1 order-lg-2">
					<?php if ($side_image2) : ?>
						<div class="half-width-content__image">
							<div data-aos="fade-left">
							<?= $side_image2; ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section><!-- .half-width-content -->
<?php endif; ?>


<?php get_footer();?>
