<?php

/**
 * Block: Image & Content
 */

$classes = ['full-width', padding_classes()];

$wide_heading = get_sub_field('wide_heading');
$heading = get_sub_field('heading');
$image = wp_get_attachment_image(get_sub_field('image'), 'full');
$mobile_image = wp_get_attachment_image(get_sub_field('image_mobile'), 'full');
$copy = get_sub_field('copy');

if ($wide_heading) {
    $classes[] = 'image-content--wide-heading';
}

?>

<section <?= block_id() ?> class="image-content <?= implode(' ', $classes); ?>">
    <div class="image-content__inner">
        <div class="container">
            <?php if ($mobile_image) : ?>
                <div class="image-content__image d-md-none">
                    <div data-aos="fade-up">
                        <?= $mobile_image; ?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="row">
                <div class="<?= $wide_heading ? 'col-md-9' : 'col-md-6'; ?> position-static">
                    <?php if ($heading) : ?>
                        <h2 data-aos="fade"><?= $heading; ?></h2>
                    <?php endif; ?>
                    <?php if ($image) : ?>
                        <div class="image-content__image image-content__image--desktop d-none d-md-block">
                            <div data-aos="fade-right">
                                <?= $image; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
            <div class="row">
                <div class="offset-md-7 col-md-5">
                    <div class="image-content__content" data-aos="fade-up">
                        <?php if ($copy) : ?>
                            <div class="image-content__copy">
                                <?= $copy; ?>
                            </div>
                        <?php endif; ?>
                        <?php get_component('icon-list'); ?>
                        <?php get_component('buttons'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section><!-- .image-content -->
