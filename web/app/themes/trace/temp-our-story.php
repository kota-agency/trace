<?php
//Template Name: Our Story
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

<section <?= block_id(); ?> class="page-header our-story-hero-mobile-position <?= implode(' ', $classes); ?>">
	<div class="container">
		<div class="row">
			<div class="col-12" data-aos="fade">
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
				<div class="col-lg-6 page-header__copy" data-aos="fade">
					<?= $copy; ?>
				</div>
                <div class="col-lg-6 position-static">
                    <?php if (have_rows('side_quote')) : ?>
                        <?php while (have_rows('side_quote')) : the_row(); ?>
                            <?php

                            $quote = get_sub_field('quote');
                            $desc = get_sub_field('description');

                            ?>
                            <?php if ($quote && $desc) : ?>
                                <div class="page-header__side-quote">
                                    <?php if ($quote) : ?>
                                        <blockquote>
                                            <?= $quote; ?>
                                            <?php if ($desc) : ?>
                                                <p class="copy-s no-margin"><?= $desc; ?></p>
                                            <?php endif; ?>
                                        </blockquote>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
			</div>
		<?php endif; ?>
		<?php if ($form_id) : ?>
			<div class="page-header__form" data-aos="fade">
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
 * Block: Timeline
 */

$classes2 = ['timeline_full-width', padding_classes()];
$heading2 = get_field('timeline_heading');

?>

<section class="timeline <?= implode(' ', $classes2); ?>" data-aos="fade" style="padding-top: 0; padding-bottom: 0;">
	<div class="timeline__inner">
		<div class="container">
			<?php if ($heading2) : ?>
				<h2 class="timeline__heading"><?= $heading2; ?></h2>
			<?php endif; ?>
			<div class="row">
				<div class="col-lg-8">
					<?php if (have_rows('timeline_milestones')) : $i = 0; ?>
						<div class="timeline__milestones">
							<?php while (have_rows('timeline_milestones')) : the_row(); ?>
								<?php

								$year = get_sub_field('year');
								$image = wp_get_attachment_image(get_sub_field('image'), 'card');
								$copy = get_sub_field('copy');

								?>
								<div class="milestone <?= $i === 0 ? 'active' : '' ?> <?= $image ? 'with-image' : '' ?>">
									<div class="milestone__inner">
										<?php if ($year) : ?>
											<div class="milestone__year"><span><?= $year; ?></span></div>
										<?php endif; ?>

										<div class="milestone__image">
											<?php if ($image) : ?>
												<span><?= $image; ?></span>
											<?php endif; ?>
										</div>

										<?php if ($copy) : ?>
											<div class="milestone__copy last-margin">
												<?= $copy; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
							<?php $i++; endwhile; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section><!-- .timeline -->

<?php

/**
 * Block: CTA
 */

$classes3 = ['full-width', 'text-center', padding_classes()];

$larger_text3 = get_field('cta_larger_text');
$logo3 = get_field('cta_logo');
$background_text3 = get_field('cta_background_text');
$heading3 = get_field('cta_heading');
$copy3 = get_field('cta_copy');
$graphics3 = get_field('cta_graphics');

if ($larger_text3) {
	$classes3[] = 'cta--larger';
}

?>

<section <?= block_id() ?> class="cta <?= implode(' ', $classes3); ?>">
	<div class="container" data-aos="fade-up">
		<?php if ($logo3) : ?>
			<div class="cta__logo">
				<img src="<?= $logo3['url']; ?>" alt="<?= $logo3['alt']; ?>">
			</div>
		<?php endif; ?>
		<div class="cta__heading-wrapper">
			<?php if ($heading3) : ?>
				<?php if ($larger_text3) : ?>
					<h2><?= $heading3; ?></h2>
				<?php else : ?>
					<h2><?= $heading3; ?></h2>
				<?php endif; ?>
			<?php endif; ?>
			<?php if ($background_text3) : ?>
				<h2 class="background-text background-text--large"><?= $background_text3; ?></h2>
			<?php endif; ?>
			<?php if ($graphics3) : ?>
				<div class="cta__graphics">
					<?php foreach ($graphics3 as $graphic) : ?>
						<div class="cta__graphic">
							<img src="<?= $graphic['url']; ?>" alt="<?= $graphic['alt']; ?>">
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php if ($copy3) : ?>
			<div class="cta__copy <?= $larger_text3 ? 'copy--xxl' : ''; ?>">
				<?= $copy3; ?>
			</div>
		<?php endif; ?>
		<?php get_component('buttons'); ?>

	</div>
</section><!-- .cta -->

<?php
/** Get in touch component, data comming from site settings tab  **/
    get_component('get-in-touch');
?>


<?php get_footer();?>
