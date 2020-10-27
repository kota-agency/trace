<?php
//Template Name: Mojo
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

<section <?= block_id(); ?> class="page-header mojo-hero-mobile-position <?= implode(' ', $classes); ?>">
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
 * Block: Text Layout
 */

$classes2 = ['full-width', 'bg-primary', padding_classes()];

$split_level2 = get_field('text_layout_split_level');
$lower_background_text2 = get_field('text_layout_lower_background_text');
$image_to_right2 = get_field('text_layout_image_to_right');
$heading2 = get_field('text_layout_heading');
$background_text2 = get_field('text_layout_background_text');
$intro_copy2 = get_field('text_layout_intro_copy');
$image2 = wp_get_attachment_image(get_field('text_layout_image'), 'full');

if($split_level2) {
	$classes[] = 'text-layout--split-level';
}

if($lower_background_text2) {
	$classes[] = 'text-layout--lower-background-text';
}

if($image_to_right2) {
	$classes[] = 'text-layout--image-right';
}

if($intro_copy2) {
	$classes[] = 'text-layout--intro-text';
}

?>

<section class="text-layout <?= implode(' ', $classes2); ?>" style="padding-top: 0;">
	<div class="container" data-aos="fade">
		<?php if ($background_text2) : ?>
			<h2 class="background-text background-text--large"><?= $background_text2; ?></h2>
		<?php endif; ?>

		<?php if ($image2) : ?>
			<div class="text-layout__image d-md-none" data-aos="fade">
				<?= $image2; ?>
			</div>
		<?php endif; ?>
		<div class="text-layout__wrapper" >
			<?php if ($image_to_right2) : ?>
				<div class="row">
					<div class="col-12" data-aos="fade">
						<?php if ($heading2) : ?>
							<h2><?= $heading2; ?></h2>
						<?php endif; ?>
					</div>
					<div class="col-md-6 col-lg-4" data-aos="fade">

						<?php if ($intro_copy2) : ?>
							<div class="copy-xxl">
								<?= $intro_copy2; ?>
							</div>
						<?php endif; ?>
					</div>
					<div class="col-md-6 col-lg-5" data-aos="fade">
						<?php if (have_rows('text_layout_columns')) : ?>
							<div class="row">
								<?php while (have_rows('text_layout_columns')) : the_row(); ?>
									<?php $copy = get_sub_field('copy'); ?>
									<?php if ($copy) : ?>
										<div class="col-lg">
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
				<?php if ($image2) : ?>
					<div class="text-layout__image d-none d-md-block">
						<div data-aos="fade">
						<?= $image2; ?>
						</div>
					</div>
				<?php endif; ?>
			<?php else : ?>
				<?php if ($split_level2) : ?>
					<div class="row text-layout__split-row" data-aos="fade">
						<div class="col-md-9">
							<?php if ($heading2) : ?>
								<h2><?= $heading2; ?></h2>
							<?php endif; ?>
							<?php if ($intro_copy2) : ?>
								<div class="copy-xxl text-layout__intro">
									<?= $intro_copy2; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<?php if ($image2) : ?>
								<div class="text-layout__image d-none d-md-block">
									<?= $image2; ?>
								</div>
							<?php endif; ?>
						</div>
						<div class="col-md-6">
							<?php if (have_rows('text_layout_columns')) : ?>
								<div class="row">
									<?php while (have_rows('text_layout_columns')) : the_row(); ?>
										<?php $copy = get_sub_field('copy'); ?>
										<?php if ($copy) : ?>
											<div class="col-lg">
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
				<?php else : ?>
					<div class="row">
						<div class="col-md-6" data-aos="fade">
							<?php if ($heading2) : ?>
								<h2><?= $heading2; ?></h2>
							<?php endif; ?>
							<?php if ($intro_copy2) : ?>
								<div>
									<?= $intro_copy2; ?>
								</div>
							<?php endif; ?>
							<?php if ($image2) : ?>
								<div class="text-layout__image d-none d-md-block" data-aos="fade">
									<?= $image2; ?>
								</div>
							<?php endif; ?>
						</div>
						<div class="col-md-6" data-aos="fade">
							<?php if (have_rows('text_layout_columns')) : ?>
								<div class="row">
									<?php while (have_rows('text_layout_columns')) : the_row(); ?>
										<?php $copy = get_sub_field('copy'); ?>
										<?php if ($copy) : ?>
											<div class="col-lg">
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
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>
</section><!-- .services -->


<?php

/**
 * Block: Text Layout
 */

$classes3 = ['full-width', 'bg-primary', padding_classes()];

$split_level3 = get_field('text_layout_b_split_level');
$lower_background_text3 = get_field('text_layout_b_lower_background_text');
$image_to_right3 = get_field('text_layout_b_image_to_right');
$heading3 = get_field('text_layout_b_heading');
$background_text3 = get_field('text_layout_b_background_text');
$intro_copy3 = get_field('text_layout_b_intro_copy');
$image3 = wp_get_attachment_image(get_field('text_layout_b_image'), 'full');

if($split_level3) {
	$classes[] = 'text-layout--split-level';
}

if($lower_background_text3) {
	$classes[] = 'text-layout--lower-background-text';
}

if($image_to_right3) {
	$classes[] = 'text-layout--image-right';
}

if($intro_copy3) {
	$classes[] = 'text-layout--intro-text';
}

?>

<section class="text-layout <?= implode(' ', $classes3); ?>" style="padding-top: 0;">
	<div class="container" data-aos="fade">
		<?php if ($background_text3) : ?>
			<h2 class="background-text background-text--large"><?= $background_text3; ?></h2>
		<?php endif; ?>

		<?php if ($image3) : ?>
			<div class="text-layout__image d-md-none" data-aos="fade">
				<?= $image3; ?>
			</div>
		<?php endif; ?>
		<div class="text-layout__wrapper" >
			<?php if ($image_to_right3) : ?>
				<div class="row">
					<div class="col-12" data-aos="fade">
						<?php if ($heading3) : ?>
							<h2><?= $heading3; ?></h2>
						<?php endif; ?>
					</div>
					<div class="col-md-6 col-lg-4" data-aos="fade">

						<?php if ($intro_copy3) : ?>
							<div class="copy-xxl">
								<?= $intro_copy3; ?>
							</div>
						<?php endif; ?>
					</div>
					<div class="col-md-6 col-lg-5" data-aos="fade">
						<?php if (have_rows('text_layout_b_columns')) : ?>
							<div class="row">
								<?php while (have_rows('text_layout_b_columns')) : the_row(); ?>
									<?php $copy = get_sub_field('copy'); ?>
									<?php if ($copy) : ?>
										<div class="col-lg">
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
				<?php if ($image3) : ?>
					<div class="text-layout__image d-none d-md-block">
						<div data-aos="fade">
						<?= $image3; ?>
						</div>
					</div>
				<?php endif; ?>
			<?php else : ?>
				<?php if ($split_level3) : ?>
					<div class="row text-layout__split-row" data-aos="fade">
						<div class="col-md-9">
							<?php if ($heading3) : ?>
								<h2><?= $heading3; ?></h2>
							<?php endif; ?>
							<?php if ($intro_copy3) : ?>
								<div class="copy-xxl text-layout__intro">
									<?= $intro_copy3; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<?php if ($image3) : ?>
								<div class="text-layout__image d-none d-md-block">
									<?= $image3; ?>
								</div>
							<?php endif; ?>
						</div>
						<div class="col-md-6">
							<?php if (have_rows('text_layout_b_columns')) : ?>
								<div class="row">
									<?php while (have_rows('text_layout_b_columns')) : the_row(); ?>
										<?php $copy = get_sub_field('copy'); ?>
										<?php if ($copy) : ?>
											<div class="col-lg">
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
				<?php else : ?>
					<div class="row">
						<div class="col-md-6" data-aos="fade">
							<?php if ($heading3) : ?>
								<h2><?= $heading3; ?></h2>
							<?php endif; ?>
							<?php if ($intro_copy3) : ?>
								<div>
									<?= $intro_copy3; ?>
								</div>
							<?php endif; ?>
							<?php if ($image3) : ?>
								<div class="text-layout__image d-none d-md-block" data-aos="fade">
									<?= $image3; ?>
								</div>
							<?php endif; ?>
						</div>
						<div class="col-md-6" data-aos="fade">
							<?php if (have_rows('text_layout_b_columns')) : ?>
								<div class="row">
									<?php while (have_rows('text_layout_b_columns')) : the_row(); ?>
										<?php $copy = get_sub_field('copy'); ?>
										<?php if ($copy) : ?>
											<div class="col-lg">
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
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>
</section><!-- .services -->

<?php

/**
 * Block: Content Modals
 */

$classes4 = ['full-width', 'tear-border', 'theme-secondary', 'bg-secondary'];

$heading4 = get_field('content_modals_heading');
$background_text4 = get_field('content_modals_background_text');
$graphics4 = get_field('content_modals_graphics');

?>

<section class="content-modals <?= implode(' ', $classes4); ?>">
	<div class="container">
		<?php if ($background_text4) : ?>
			<h2 class="background-text background-text--large"><?= $background_text4; ?></h2>
		<?php endif; ?>
		<?php if ($heading4) : ?>
			<div class="row">
				<div class="col-md-6" data-aos="fade-up">
					<h2><?= $heading4; ?></h2>
				</div>
			</div>
		<?php endif; ?>
		<?php if (have_rows('content_modals_items')) : $i = 0;
			$j = 0; ?>
			<div class="content-modals__items row" data-aos="fade-up">
				<div class="content-modals__column col-md-6">
					<?php while (have_rows('content_modals_items')) : the_row(); ?>
						<?php if ($i % 2 === 0) : ?>
							<?php

							$item_heading = get_sub_field('heading');
							$snippet = get_sub_field('snippet');

							?>
							<div class="content-modals__item" data-item="<?= $i; ?>">
								<?php if ($item_heading) : ?>
									<span class="d-block font-weight-demi"><?= $item_heading; ?></span>
								<?php endif; ?>
								<?php if ($snippet) : ?>
									<div class="last-margin">
										<?= $snippet; ?>
									</div>
								<?php endif; ?>
								<div class="btn-wrap">
									<span class="link">
										<?= __('Discover more'); ?>
										<span class="arrow-link">
										<svg xmlns="http://www.w3.org/2000/svg" width="11.247" height="10.296" viewBox="0 0 11.247 10.296">
											<g id="Btn_SecondaryWithArrow" transform="translate(-106.001 3.158)">
												<g id="Group_250" data-name="Group 250" transform="translate(106.001 -2.451)">
													<line id="Line_23" data-name="Line 23" x2="9.515" transform="translate(0 4.441)" fill="none" stroke="#ffcb39" stroke-width="2"/>
													<path id="Path_158" data-name="Path 158" d="M682.556,3376.6l4.441,4.441-4.441,4.441" transform="translate(-677.164 -3376.596)" fill="none" stroke="#ffcb39" stroke-width="2"/>
												</g>
											</g>
										</svg>
										</span>
									</span>
								</div>

							</div>
						<?php endif; ?>
						<?php $i++; endwhile; ?>
				</div>
				<div class="content-modals__column col-md-6">
					<?php while (have_rows('content_modals_items')) : the_row(); ?>
						<?php if ($j % 2 !== 0) : ?>
							<?php

							$heading = get_sub_field('heading');
							$snippet = get_sub_field('snippet');

							?>
							<div class="content-modals__item" data-item="<?= $j; ?>">
								<?php if ($heading) : ?>
									<span class="d-block font-weight-demi"><?= $heading; ?></span>
								<?php endif; ?>
								<?php if ($snippet) : ?>
									<div class="last-margin">
										<?= $snippet; ?>
									</div>
								<?php endif; ?>
								<div class="btn-wrap">
									<span class="link">
										<?= __('Discover more'); ?>
										<span class="arrow-link">
											<svg xmlns="http://www.w3.org/2000/svg" width="11.247" height="10.296" viewBox="0 0 11.247 10.296">
												<g id="Btn_SecondaryWithArrow" transform="translate(-106.001 3.158)">
													<g id="Group_250" data-name="Group 250" transform="translate(106.001 -2.451)">
														<line id="Line_23" data-name="Line 23" x2="9.515" transform="translate(0 4.441)" fill="none" stroke="#ffcb39" stroke-width="2"/>
														<path id="Path_158" data-name="Path 158" d="M682.556,3376.6l4.441,4.441-4.441,4.441" transform="translate(-677.164 -3376.596)" fill="none" stroke="#ffcb39" stroke-width="2"/>
													</g>
												</g>
											</svg>
										</span>
									</span>
								</div>
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
							</div>
						<?php endif; ?>
						<?php $j++; endwhile; ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ($graphics4) : ?>
			<div class="content-modals__graphics">
				<?php foreach ($graphics4 as $graphic) : ?>
					<div class="content-modals__graphic">
						<div data-aos="fade">
							<img src="<?= $graphic['url']; ?>" alt="<?= $graphic['alt']; ?>">
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section><!-- .content-modals -->
<?php if (have_rows('content_modals_items')) : ?>
	<div class="content-modals__modals">
		<?php while (have_rows('content_modals_items')) : the_row(); ?>
			<?php $modal_heading = get_sub_field('heading'); ?>
			<?php get_component('modal', $modal_heading); ?>
		<?php endwhile; ?>
	</div>
<?php endif; ?>

<?php
/** Get in touch component, data comming from site settings tab  **/
    get_component('get-in-touch');
?>


<?php get_footer();?>
