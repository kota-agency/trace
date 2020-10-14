<?php
//Template Name: Product
get_header();?>

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
 * Block: Page Links
 */

$classes2 = ['full-width', padding_classes()];
$item_count2 = count(get_field('page_links_items'));

if ($item_count2 <= 2) {
	$classes2[] = 'page-links--remove-border';
}

?>
<?php if (have_rows('page_links_items')) : ?>
	<section <?= block_id(); ?> class="page-links <?= implode(' ', $classes2); ?>" data-aos="fade" style="padding-top: 0;">
		<div class="container">

			<div class="row page-links__row no-gutters" >
				<?php while (have_rows('page_links_items')) : the_row(); ?>

					<div class="col-lg-6 page-links__col">
						<?php

						/**
						 * Component: Page Link
						 */

						$heading = get_sub_field('heading');
						$link = get_sub_field('link');
						$copy = get_sub_field('copy');

						?>


						<?php if ($link) : ?>
							<div class="page-link">
								<a href="<?= $link['url']; ?>" <?= $link['target'] ? 'target="_blank"' : ''; ?>>
									<?php if ($heading) : ?>
										<h3><?= $heading; ?></h3>
									<?php endif; ?>
									<?php if ($copy) : ?>
										<div class="page-link__copy">
											<?= $copy; ?>
										</div>
									<?php endif; ?>
									<div class="btn-wrap">
										<span class="arrow"><i class="fas fa-arrow-right"></i></span>
									</div>
									<div class="page-link__overlay theme-secondary">
										<?php if ($heading) : ?>
											<h3 class="larger"><?= $heading; ?></h3>
										<?php endif; ?>
										<?php if ($copy) : ?>
											<div class="copy-l page-link__copy">
												<?= $copy; ?>
											</div>
										<?php endif; ?>
										<div class="btn-wrap">
											<span class="arrow-to-btn"><span class="arrow-to-btn__arrow"><i
															class="fas fa-arrow-right"></i></span><span
														class="arrow-to-btn__text"><?= $link['title'] ? $link['title'] : __('Find out more', 'trace'); ?></span></span>
										</div>
									</div>
								</a>
							</div>
						<?php endif; ?>
					</div>
				<?php endwhile; ?>
			</div>

		</div>
	</section><!-- .page-links -->

	<div class="page-links__modals">
		<?php while (have_rows('page_links_items')) : the_row(); ?>
			<?php

			/**
			 * Component: Modal
			 */


			$title = get_query_var('data') ? get_query_var('data') : get_mixed_field('heading');


			?>
			<?php if (have_rows('modal')) : ?>
				<?php while (have_rows('modal')) : the_row(); ?>
					<?php

					$subtitle = get_sub_field('subtitle');

					?>
					<div class="modal theme-secondary">
						<div class="modal__inner">
							<div class="modal__close"></div>
							<?php if ($title) : ?>
								<h3 class="modal__heading"><?= $title; ?></h3>
							<?php endif; ?>
							<?php if ($subtitle) : ?>
								<h5 class="text-tertiary modal__subheading text-uppercase"><?= $subtitle; ?></h5>
							<?php endif; ?>
							<?php if (have_rows('columns')) : ?>
								<div class="row">
									<?php while (have_rows('columns')) : the_row(); ?>
										<?php $copy = get_sub_field('copy'); ?>
										<?php if ($copy) : ?>
											<div class="col-md-6">
												<div>
													<?= $copy; ?>
												</div>
											</div>
										<?php endif; ?>
									<?php endwhile; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endwhile; ?>
			<?php endif; ?>

		<?php endwhile; ?>
	</div>
<?php endif; ?>

<?php

/**
 * Block: Heading * Columns
 */

$classes3 = ['full-width', 'tear-border', 'theme-secondary', 'bg-secondary'];

$heading3 = get_field('heading_&_columns_heading');
$background_text3 = get_field('heading_&_columns_background_text');
$intro_text3 = get_field('heading_&_columns_intro_copy');
$vertical_heading3 = get_field('heading_&_columns_vertical_heading');
$cover_graphic3 = get_field('heading_&_columns_cover_bottom_graphic');
$graphics3 = get_field('heading_&_columns_graphics');
$offset_graphics3 = get_field('heading_&_columns_offset_graphics');

if ($vertical_heading3) {
	$classes[] = 'heading-columns--vertical-heading';
}

if ($cover_graphic3) {
	$classes[] = 'heading-columns--cover-graphic';
}

if ($offset_graphics3) {
	$classes[] = 'heading-columns--offset-graphics';
}


?>

<section class="heading-columns <?= implode(' ', $classes3); ?>">
	<div class="container">
		<?php if ($vertical_heading3) : ?>
			<?php if ($background_text3) : ?>
				<h2 class="background-text background-text--large"><?= $background_text3; ?></h2>
			<?php endif; ?>
			<div class="row">
				<div class="col-md-3 heading-columns__vert-heading" data-aos="fade-up">
					<?php if ($heading3) : ?>
						<h2 class="heading-columns__heading"><span class="vert-heading"><?= $heading3; ?></span></h2>
					<?php endif; ?>
				</div>
				<div class="col col-lg-6">
					<?php if ($intro_text3) : ?>
						<div class="heading-columns__intro-text" data-aos="fade-up">
							<?= $intro_text3; ?>
						</div>
					<?php endif; ?>
					<?php if (have_rows('heading_&_columns_columns')) : ?>
						<div class="row heading-columns__columns" data-aos="fade-up">
							<?php while (have_rows('heading_&_columns_columns')) : the_row(); ?>
								<?php $copy = get_sub_field('copy'); ?>
								<?php if ($copy) : ?>
									<div class="col-md-6">
										<div class="heading-columns__copy">
											<?= $copy; ?>
										</div>
									</div>
								<?php endif; ?>
							<?php endwhile; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		<?php else : ?>
			<div class="heading-columns__heading-wrapper" data-aos="fade-up">
				<?php if ($heading3) : ?>
					<h1 class="heading-columns__heading"><?= $heading3; ?></h1>
				<?php endif; ?>
				<?php if ($background_text3) : ?>
					<h2 class="background-text background-text--large"><?= $background_text3; ?></h2>
				<?php endif; ?>
				<?php if ($intro_text3) : ?>
					<div class="heading-columns__intro-text">
						<?= $intro_text3; ?>
					</div>
				<?php endif; ?>
			</div>

			<?php if (have_rows('heading_&_columns_columns')) : ?>
				<div class="row heading-columns__columns" data-aos="fade-up">
					<?php while (have_rows('heading_&_columns_columns')) : the_row(); ?>
						<?php $copy = get_sub_field('copy'); ?>
						<?php if ($copy) : ?>
							<div class="col-md-6">
								<div class="heading-columns__copy">
									<?= $copy; ?>
								</div>
							</div>
						<?php endif; ?>
					<?php endwhile; ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>
		<?php if ($graphics3) : ?>

			<div class="heading-columns__graphics">
				<?php foreach ($graphics3 as $graphic) : ?>
					<div class="heading-columns__graphic">
						<div data-aos="fade">
							<img src="<?= $graphic['url']; ?>" alt="<?= $graphic['alt']; ?>">
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

	</div>
</section><!-- .heading-columns -->

<?php

/**
 * Block: Page Links
 */

$classes4 = ['full-width', padding_classes()];
$item_count4 = count(get_field('page_links2_items'));

if ($item_count4 <= 2) {
	$classes4[] = 'page-links--remove-border';
}

?>


<?php if (have_rows('page_links2_items')) : ?>
	<section <?= block_id(); ?> class="page-links <?= implode(' ', $classes4); ?>" data-aos="fade"  style="padding-bottom: 0;">
		<div class="container">

			<div class="row page-links__row no-gutters" >
				<?php while (have_rows('page_links2_items')) : the_row(); ?>

					<div class="col-lg-6 page-links__col">
						<?php

						/**
						 * Component: Page Link
						 */

						$heading = get_sub_field('heading');
						$link = get_sub_field('link');
						$copy = get_sub_field('copy');

						?>


						<?php if ($link) : ?>
							<div class="page-link">
								<a href="<?= $link['url']; ?>" <?= $link['target'] ? 'target="_blank"' : ''; ?>>
									<?php if ($heading) : ?>
										<h3><?= $heading; ?></h3>
									<?php endif; ?>
									<?php if ($copy) : ?>
										<div class="page-link__copy">
											<?= $copy; ?>
										</div>
									<?php endif; ?>
									<div class="btn-wrap">
										<span class="arrow"><i class="fas fa-arrow-right"></i></span>
									</div>
									<div class="page-link__overlay theme-secondary">
										<?php if ($heading) : ?>
											<h3 class="larger"><?= $heading; ?></h3>
										<?php endif; ?>
										<?php if ($copy) : ?>
											<div class="copy-l page-link__copy">
												<?= $copy; ?>
											</div>
										<?php endif; ?>
										<div class="btn-wrap">
											<span class="arrow-to-btn"><span class="arrow-to-btn__arrow"><i
															class="fas fa-arrow-right"></i></span><span
														class="arrow-to-btn__text"><?= $link['title'] ? $link['title'] : __('Find out more', 'trace'); ?></span></span>
										</div>
									</div>
								</a>
							</div>
						<?php endif; ?>
					</div>
				<?php endwhile; ?>
			</div>

		</div>
	</section><!-- .page-links -->

	<div class="page-links__modals">
		<?php while (have_rows('page_links2_items')) : the_row(); ?>
			<?php

			/**
			 * Component: Modal
			 */


			$title = get_query_var('data') ? get_query_var('data') : get_mixed_field('heading');


			?>
			<?php if (have_rows('modal')) : ?>
				<?php while (have_rows('modal')) : the_row(); ?>
					<?php

					$subtitle = get_sub_field('subtitle');

					?>
					<div class="modal theme-secondary">
						<div class="modal__inner">
							<div class="modal__close"></div>
							<?php if ($title) : ?>
								<h3 class="modal__heading"><?= $title; ?></h3>
							<?php endif; ?>
							<?php if ($subtitle) : ?>
								<h5 class="text-tertiary modal__subheading text-uppercase"><?= $subtitle; ?></h5>
							<?php endif; ?>
							<?php if (have_rows('columns')) : ?>
								<div class="row">
									<?php while (have_rows('columns')) : the_row(); ?>
										<?php $copy = get_sub_field('copy'); ?>
										<?php if ($copy) : ?>
											<div class="col-md-6">
												<div>
													<?= $copy; ?>
												</div>
											</div>
										<?php endif; ?>
									<?php endwhile; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endwhile; ?>
			<?php endif; ?>

		<?php endwhile; ?>
	</div>
<?php endif; ?>


<?php get_footer();?>
