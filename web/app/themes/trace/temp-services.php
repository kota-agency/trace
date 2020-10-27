<?php
//Template Name: Services
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

<section <?= block_id(); ?> class="page-header services-hero-mobile-position <?= implode(' ', $classes); ?>">
	<div class="container" data-aos="fade">
		<div class="row">
			<div class="col-12 col-lg-9">
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
$image_mob2 = wp_get_attachment_image(get_field('text_layout_mobile_image'), 'full');
$offset_image_mobile2 = get_field('text_layout_offset_image_on_mobile');

if($offset_image_mobile2) {
    $classes2[] = 'text-layout--offset-image';
}

if($split_level2) {
	$classes2[] = 'text-layout--split-level';
}

if($lower_background_text2) {
	$classes2[] = 'text-layout--lower-background-text';
}

if($image_to_right2) {
	$classes2[] = 'text-layout--image-right';
}

if($intro_copy2) {
	$classes2[] = 'text-layout--intro-text';
}

?>

<section class="text-layout section-two-image-position <?= implode(' ', $classes2); ?>">
	<div class="container" data-aos="fade">
		<?php if ($background_text2) : ?>
			<h2 class="background-text background-text--large"><?= $background_text2; ?></h2>
		<?php endif; ?>

		<?php if ($image_mob2) : ?>
			<div class="text-layout__image d-md-none" data-aos="fade">
				<?= $image_mob2; ?>
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
	$classes3[] = 'heading-columns--vertical-heading';
}

if ($cover_graphic3) {
	$classes3[] = 'heading-columns--cover-graphic';
}

if ($offset_graphics3) {
	$classes3[] = 'heading-columns--offset-graphics';
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
					<h2 class="heading-columns__heading"><?= $heading3; ?></h2>
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
 * Block: Text Layout
 */

$classes4 = ['full-width', 'bg-primary', padding_classes()];

$split_level4 = get_field('text_layout_b_split_level');
$lower_background_text4 = get_field('text_layout_b_lower_background_text');
$image_to_right4 = get_field('text_layout_b_image_to_right');
$heading4 = get_field('text_layout_b_heading');
$background_text4 = get_field('text_layout_b_background_text');
$intro_copy4 = get_field('text_layout_b_intro_copy');
$image4 = wp_get_attachment_image(get_field('text_layout_b_image'), 'full');
$image_mob4 = wp_get_attachment_image(get_field('text_layout_b_mobile_image'), 'full');
$offset_image_mobile4 = get_field('text_layout_b_offset_image_on_mobile');

if($offset_image_mobile4) {
    $classes4[] = 'text-layout--offset-image';
}

if($split_level4) {
	$classes4[] = 'text-layout--split-level';
}

if($lower_background_text4) {
	$classes4[] = 'text-layout--lower-background-text';
}

if($image_to_right4) {
	$classes4[] = 'text-layout--image-right';
}

if($intro_copy4) {
	$classes4[] = 'text-layout--intro-text';
}

?>

<section class="text-layout <?= implode(' ', $classes4); ?>" style="padding-bottom: 0;">
	<div class="container" data-aos="fade">
		<?php if ($background_text4) : ?>
			<h2 class="background-text background-text--large"><?= $background_text4; ?></h2>
		<?php endif; ?>

		<?php if ($image_mob4) : ?>
			<div class="text-layout__image d-md-none" data-aos="fade">
				<?= $image_mob4; ?>
			</div>
		<?php endif; ?>
		<div class="text-layout__wrapper" >
			<?php if ($image_to_right4) : ?>
				<div class="row">
					<div class="col-12" data-aos="fade">
						<?php if ($heading4) : ?>
							<h2><?= $heading4; ?></h2>
						<?php endif; ?>
					</div>
					<div class="col-md-6 col-lg-4" data-aos="fade">

						<?php if ($intro_copy4) : ?>
							<div class="copy-xxl">
								<?= $intro_copy4; ?>
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
				<?php if ($image4) : ?>
					<div class="text-layout__image d-none d-md-block">
						<div data-aos="fade">
						<?= $image4; ?>
						</div>
					</div>
				<?php endif; ?>
			<?php else : ?>
				<?php if ($split_level4) : ?>
					<div class="row text-layout__split-row" data-aos="fade">
						<div class="col-md-9">
							<?php if ($heading4) : ?>
								<h2><?= $heading4; ?></h2>
							<?php endif; ?>
							<?php if ($intro_copy4) : ?>
								<div class="copy-xxl text-layout__intro">
									<?= $intro_copy4; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<?php if ($image4) : ?>
								<div class="text-layout__image d-none d-md-block">
									<?= $image4; ?>
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
							<?php if ($heading4) : ?>
								<h2><?= $heading4; ?></h2>
							<?php endif; ?>
							<?php if ($intro_copy4) : ?>
								<div>
									<?= $intro_copy4; ?>
								</div>
							<?php endif; ?>
							<?php if ($image4) : ?>
								<div class="text-layout__image d-none d-md-block" data-aos="fade">
									<?= $image4; ?>
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
 * Block: Text Layout
 */

$classes5 = ['full-width', 'bg-primary', padding_classes()];

$split_level5 = get_field('text_layout_c_split_level');
$lower_background_text5 = get_field('text_layout_c_lower_background_text');
$image_to_right5 = get_field('text_layout_c_image_to_right');
$heading5 = get_field('text_layout_c_heading');
$background_text5 = get_field('text_layout_c_background_text');
$intro_copy5 = get_field('text_layout_c_intro_copy');
$image5 = wp_get_attachment_image(get_field('text_layout_c_image'), 'full');
$image_mob5 = wp_get_attachment_image(get_field('text_layout_c_mobile_image'), 'full');
$offset_image_mobile5 = get_field('text_layout_c_offset_image_on_mobile');

if($offset_image_mobile5) {
    $classes5[] = 'text-layout--offset-image';
}

if($split_level5) {
	$classes5[] = 'text-layout--split-level';
}

if($lower_background_text5) {
	$classes5[] = 'text-layout--lower-background-text';
}

if($image_to_right5) {
	$classes5[] = 'text-layout--image-right';
}

if($intro_copy5) {
	$classes5[] = 'text-layout--intro-text';
}

?>

<section class="text-layout section-five-image-postion <?= implode(' ', $classes5); ?>" style="padding-bottom: 0;">
	<div class="container" data-aos="fade">
		<?php if ($background_text5) : ?>
			<h2 class="background-text background-text--large"><?= $background_text5; ?></h2>
		<?php endif; ?>

		<?php if ($image_mob5) : ?>
			<div class="text-layout__image d-md-none" data-aos="fade">
				<?= $image_mob5; ?>
			</div>
		<?php endif; ?>
		<div class="text-layout__wrapper" >
			<?php if ($image_to_right5) : ?>
				<div class="row">
					<div class="col-12" data-aos="fade">
						<?php if ($heading5) : ?>
							<h2><?= $heading5; ?></h2>
						<?php endif; ?>
					</div>
					<div class="col-md-6 col-lg-4" data-aos="fade">

						<?php if ($intro_copy5) : ?>
							<div class="copy-xxl">
								<?= $intro_copy5; ?>
							</div>
						<?php endif; ?>
					</div>
					<div class="col-md-6 col-lg-5" data-aos="fade">
						<?php if (have_rows('text_layout_c_columns')) : ?>
							<div class="row">
								<?php while (have_rows('text_layout_c_columns')) : the_row(); ?>
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
				<?php if ($image5) : ?>
					<div class="text-layout__image d-none d-md-block">
						<div data-aos="fade">
						<?= $image5; ?>
						</div>
					</div>
				<?php endif; ?>
			<?php else : ?>
				<?php if ($split_level5) : ?>
					<div class="row text-layout__split-row" data-aos="fade">
						<div class="col-md-9">
							<?php if ($heading5) : ?>
								<h2><?= $heading5; ?></h2>
							<?php endif; ?>
							<?php if ($intro_copy5) : ?>
								<div class="copy-xxl text-layout__intro">
									<?= $intro_copy5; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<?php if ($image5) : ?>
								<div class="text-layout__image d-none d-md-block">
									<?= $image5; ?>
								</div>
							<?php endif; ?>
						</div>
						<div class="col-md-6">
							<?php if (have_rows('text_layout_c_columns')) : ?>
								<div class="row">
									<?php while (have_rows('text_layout_c_columns')) : the_row(); ?>
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
							<?php if ($heading5) : ?>
								<h2><?= $heading5; ?></h2>
							<?php endif; ?>
							<?php if ($intro_copy5) : ?>
								<div>
									<?= $intro_copy5; ?>
								</div>
							<?php endif; ?>
							<?php if ($image5) : ?>
								<div class="text-layout__image d-none d-md-block" data-aos="fade">
									<?= $image5; ?>
								</div>
							<?php endif; ?>
						</div>
						<div class="col-md-6" data-aos="fade">
							<?php if (have_rows('text_layout_c_columns')) : ?>
								<div class="row">
									<?php while (have_rows('text_layout_c_columns')) : the_row(); ?>
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

$classes6 = ['full-width', 'bg-primary', padding_classes()];

$split_level6 = get_field('text_layout_d_split_level');
$lower_background_text6 = get_field('text_layout_d_lower_background_text');
$image_to_right6 = get_field('text_layout_d_image_to_right');
$heading6 = get_field('text_layout_d_heading');
$background_text6 = get_field('text_layout_d_background_text');
$intro_copy6 = get_field('text_layout_d_intro_copy');
$image6 = wp_get_attachment_image(get_field('text_layout_d_image'), 'full');
$image_mob6 = wp_get_attachment_image(get_field('text_layout_d_mobile_image'), 'full');
$offset_image_mobile6 = get_field('text_layout_d_offset_image_on_mobile');

if($offset_image_mobile6) {
    $classes6[] = 'text-layout--offset-image';
}

if($split_level6) {
	$classes6[] = 'text-layout--split-level';
}

if($lower_background_text6) {
	$classes6[] = 'text-layout--lower-background-text';
}

if($image_to_right6) {
	$classes6[] = 'text-layout--image-right';
}

if($intro_copy6) {
	$classes6[] = 'text-layout--intro-text';
}

?>

<section class="text-layout <?= implode(' ', $classes6); ?>">
	<div class="container" data-aos="fade">
		<?php if ($background_text6) : ?>
			<h2 class="background-text background-text--large"><?= $background_text6; ?></h2>
		<?php endif; ?>

		<?php if ($image_mob6) : ?>
			<div class="text-layout__image d-md-none" data-aos="fade">
				<?= $image_mob6; ?>
			</div>
		<?php endif; ?>
		<div class="text-layout__wrapper" >
			<?php if ($image_to_right6) : ?>
				<div class="row">
					<div class="col-12" data-aos="fade">
						<?php if ($heading6) : ?>
							<h2><?= $heading6; ?></h2>
						<?php endif; ?>
					</div>
					<div class="col-md-6 col-lg-4" data-aos="fade">

						<?php if ($intro_copy6) : ?>
							<div class="copy-xxl">
								<?= $intro_copy6; ?>
							</div>
						<?php endif; ?>
					</div>
					<div class="col-md-6 col-lg-5" data-aos="fade">
						<?php if (have_rows('text_layout_d_columns')) : ?>
							<div class="row">
								<?php while (have_rows('text_layout_d_columns')) : the_row(); ?>
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
				<?php if ($image6) : ?>
					<div class="text-layout__image d-none d-md-block">
						<div data-aos="fade">
						<?= $image6; ?>
						</div>
					</div>
				<?php endif; ?>
			<?php else : ?>
				<?php if ($split_level6) : ?>
					<div class="row text-layout__split-row" data-aos="fade">
						<div class="col-md-9">
							<?php if ($heading6) : ?>
								<h2><?= $heading6; ?></h2>
							<?php endif; ?>
							<?php if ($intro_copy6) : ?>
								<div class="copy-xxl text-layout__intro">
									<?= $intro_copy6; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<?php if ($image6) : ?>
								<div class="text-layout__image d-none d-md-block">
									<?= $image6; ?>
								</div>
							<?php endif; ?>
						</div>
						<div class="col-md-6">
							<?php if (have_rows('text_layout_d_columns')) : ?>
								<div class="row">
									<?php while (have_rows('text_layout_d_columns')) : the_row(); ?>
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
							<?php if ($heading6) : ?>
								<h2><?= $heading6; ?></h2>
							<?php endif; ?>
							<?php if ($intro_copy6) : ?>
								<div>
									<?= $intro_copy6; ?>
								</div>
							<?php endif; ?>
							<?php if ($image6) : ?>
								<div class="text-layout__image d-none d-md-block" data-aos="fade">
									<?= $image6; ?>
								</div>
							<?php endif; ?>
						</div>
						<div class="col-md-6" data-aos="fade">
							<?php if (have_rows('text_layout_d_columns')) : ?>
								<div class="row">
									<?php while (have_rows('text_layout_d_columns')) : the_row(); ?>
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
 * Block: Heading * Columns
 */

$classes7 = ['full-width', 'tear-border', 'theme-secondary', 'bg-secondary'];

$heading7 = get_field('heading_&_columns_b_heading');
$background_text7 = get_field('heading_&_columns_b_background_text');
$intro_text7 = get_field('heading_&_columns_b_intro_copy');
$vertical_heading7 = get_field('heading_&_columns_b_vertical_heading');
$cover_graphic7 = get_field('heading_&_columns_b_cover_bottom_graphic');
$graphics7 = get_field('heading_&_columns_b_graphics');
$offset_graphics7 = get_field('heading_&_columns_b_offset_graphics');

if ($vertical_heading7) {
	$classes7[] = 'heading-columns--vertical-heading';
}

if ($cover_graphic7) {
	$classes7[] = 'heading-columns--cover-graphic';
}

if ($offset_graphics7) {
	$classes7[] = 'heading-columns--offset-graphics';
}


?>

<section class="heading-columns specific-graphic-position <?= implode(' ', $classes7); ?>">
	<div class="container">
		<?php if ($vertical_heading7) : ?>
			<?php if ($background_text7) : ?>
				<h2 class="background-text background-text--large"><?= $background_text7; ?></h2>
			<?php endif; ?>
			<div class="row">
				<div class="col-md-3 heading-columns__vert-heading" data-aos="fade-up">
					<?php if ($heading7) : ?>
						<h2 class="heading-columns__heading"><span class="vert-heading"><?= $heading7; ?></span></h2>
					<?php endif; ?>
				</div>
				<div class="col col-lg-6">
					<?php if ($intro_text7) : ?>
						<div class="heading-columns__intro-text copy-xxl" data-aos="fade-up">
							<?= $intro_text7; ?>
						</div>
					<?php endif; ?>
					<?php if (have_rows('heading_&_columns_b_columns')) : ?>
						<div class="row heading-columns__columns" data-aos="fade-up">
							<?php while (have_rows('heading_&_columns_b_columns')) : the_row(); ?>
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
				<?php if ($heading7) : ?>
					<h2 class="heading-columns__heading"><?= $heading7; ?></h2>
				<?php endif; ?>
				<?php if ($background_text7) : ?>
					<h2 class="background-text background-text--large"><?= $background_text7; ?></h2>
				<?php endif; ?>
				<?php if ($intro_text7) : ?>
					<div class="heading-columns__intro-text">
						<?= $intro_text7; ?>
					</div>
				<?php endif; ?>
			</div>

			<?php if (have_rows('heading_&_columns_b_columns')) : ?>
				<div class="row heading-columns__columns" data-aos="fade-up">
					<?php while (have_rows('heading_&_columns_b_columns')) : the_row(); ?>
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
		<?php if ($graphics7) : ?>

			<div class="heading-columns__graphics">
				<?php foreach ($graphics7 as $graphic) : ?>
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
 * Block: Text Layout
 */

$classes8 = ['full-width', 'bg-primary', padding_classes()];

$split_level8 = get_field('text_layout_e_split_level');
$lower_background_text8 = get_field('text_layout_e_lower_background_text');
$image_to_right8 = get_field('text_layout_e_image_to_right');
$heading8 = get_field('text_layout_e_heading');
$background_text8 = get_field('text_layout_e_background_text');
$intro_copy8 = get_field('text_layout_e_intro_copy');
$image8 = wp_get_attachment_image(get_field('text_layout_e_image'), 'full');
$image_mob8 = wp_get_attachment_image(get_field('text_layout_e_mobile_image'), 'full');
$offset_image_mobile8 = get_field('text_layout_e_offset_image_on_mobile');

//if($offset_image_mobile8) {
//    $classes8[] = 'text-layout--offset-image';
//}



if($split_level8) {
	$classes8[] = 'text-layout--split-level';
}

if($lower_background_text8) {
	$classes8[] = 'text-layout--lower-background-text';
}

if($image_to_right8) {
	$classes8[] = 'text-layout--image-right';
}

if($intro_copy8) {
	$classes8[] = 'text-layout--intro-text';
}

?>

<section class="text-layout <?= implode(' ', $classes8); ?>">
	<div class="container" data-aos="fade">
		<?php if ($background_text8) : ?>
			<h2 class="background-text background-text--large"><?= $background_text8; ?></h2>
		<?php endif; ?>

		<?php if ($image_mob8) : ?>
			<div class="text-layout__image d-md-none" data-aos="fade">
				<?= $image_mob8; ?>
			</div>
		<?php endif; ?>
		<div class="text-layout__wrapper" >
			<?php if ($image_to_right8) : ?>
				<div class="row">
					<div class="col-12" data-aos="fade">
						<?php if ($heading8) : ?>
							<h2><?= $heading8; ?></h2>
						<?php endif; ?>
					</div>
					<div class="col-md-6 col-lg-4" data-aos="fade">

						<?php if ($intro_copy8) : ?>
							<div class="copy-xxl">
								<?= $intro_copy8; ?>
							</div>
						<?php endif; ?>
					</div>
					<div class="col-md-6 col-lg-5" data-aos="fade">
						<?php if (have_rows('text_layout_e_columns')) : ?>
							<div class="row">
								<?php while (have_rows('text_layout_e_columns')) : the_row(); ?>
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
				<?php if ($image8) : ?>
					<div class="text-layout__image d-none d-md-block">
						<div data-aos="fade">
						<?= $image8; ?>
						</div>
					</div>
				<?php endif; ?>
			<?php else : ?>
				<?php if ($split_level8) : ?>
					<div class="row text-layout__split-row" data-aos="fade">
						<div class="col-md-9">
							<?php if ($heading8) : ?>
								<h2><?= $heading8; ?></h2>
							<?php endif; ?>
							<?php if ($intro_copy8) : ?>
								<div class="copy-xxl text-layout__intro">
									<?= $intro_copy8; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<?php if ($image8) : ?>
								<div class="text-layout__image d-none d-md-block">
									<?= $image8; ?>
								</div>
							<?php endif; ?>
						</div>
						<div class="col-md-6">
							<?php if (have_rows('text_layout_e_columns')) : ?>
								<div class="row">
									<?php while (have_rows('text_layout_e_columns')) : the_row(); ?>
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
							<?php if ($heading8) : ?>
								<h2><?= $heading8; ?></h2>
							<?php endif; ?>
							<?php if ($intro_copy8) : ?>
								<div>
									<?= $intro_copy8; ?>
								</div>
							<?php endif; ?>
							<?php if ($image8) : ?>
								<div class="text-layout__image d-none d-md-block" data-aos="fade">
									<?= $image8; ?>
								</div>
							<?php endif; ?>
						</div>
						<div class="col-md-6" data-aos="fade">
							<?php if (have_rows('text_layout_e_columns')) : ?>
								<div class="row">
									<?php while (have_rows('text_layout_e_columns')) : the_row(); ?>
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
/** Get in touch component, data comming from site settings tab  **/
    get_component('get-in-touch');
?>


<?php get_footer();?>
