<?php

/**
 * Block: Page Header
 */

$classes = ['full-width', padding_classes()];

$label = get_sub_field('label');
$title = get_sub_field('title');
$image = wp_get_attachment_image(get_sub_field('image'), 'full');
$copy = get_sub_field('copy');
$form_id = get_sub_field('form_id');

if(is_home() || is_archive()) {
    $label = __('News', 'trace');
    $title = __('Our Latest News & Insights', 'trace');

    $classes[] = 'block-space--no-bottom';
}

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
                <div class="col-lg-6">
                    <?php if (have_rows('side_quote')) : ?>
                        <?php while (have_rows('side_quote')) : the_row(); ?>
                            <?php

                            $quote = get_sub_field('quote');
                            $desc = get_sub_field('description');

                            ?>
                            <?php if ($quote && $desc) : ?>
                                <div class="half-width-content__side-quote">
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
