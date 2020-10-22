<?php
//Template Name: Sectors
get_header();?>

<style>
	.split-content{
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

<section <?= block_id(); ?> class="page-header <?= implode(' ', $classes); ?>">
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
 * Block: Split Content
 */

$classes2 = ['full-width', padding_classes()];

?>
<?php if (have_rows('split_content_rows')) : $i = 0; ?>
	<section class="split-content <?= implode(' ', $classes2); ?>">

		<?php while (have_rows('split_content_rows')) : the_row(); ?>
			<?php

      $id = get_sub_field('id');
			$heading = get_sub_field('heading');
			$copy = get_sub_field('copy');
			$image = wp_get_attachment_image(get_sub_field('image'), 'full');
			$link = get_sub_field('button');

			?>
			<div <?= $id ? 'id="' . $id . '"' : ''; ?> class="split-content__item <?= $i % 2 === 0 ? 'tear-border bg-secondary theme-secondary' : ''; ?>" data-aos="fade">
				<div class="container">
					<div class="row align-items-center">
						<div class="col-lg-6 align-self-stretch">
							<?php if ($image) : ?>
								<div class="split-content__image">
									<?= $image; ?>
								</div>
							<?php endif; ?>
						</div>
						<div class="col-lg-6">
							<div class="split-content__content">
								<?php if ($heading) : ?>
									<h3><?= $heading; ?></h3>
								<?php endif; ?>
								<?php if ($copy) : ?>
									<div>
										<?= $copy; ?>
									</div>
								<?php endif; ?>
								<div class="btn-wrap">
									<a class="btn" 
										href="<?= $link['url'] ? $link['url'] : "#" ?>" 
										<?= $link['target'] ? 'target="_blank"' : ''; ?>
									>
									<?= $link['title'] ? $link['title'] : __('Find out more', 'trace'); ?>
								</a>
								</div>
							</div>

						</div>
					</div>
				</div>
			</div>
			<?php $i++; endwhile; ?>

	</section><!-- .split-content -->
	<div class="split-content__modals">

		<?php while (have_rows('split_content_rows')) : the_row(); ?>
			<?php get_component('modal'); ?>
		<?php endwhile; ?>
	</div>
<?php endif; ?>


<?php get_footer();?>
