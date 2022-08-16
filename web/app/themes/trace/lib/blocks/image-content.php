<?php

/**
 * Block: Image & Content
 */

$classes = ['full-width', padding_classes()];

$wide_heading = get_sub_field('wide_heading');
$heading = get_sub_field('heading');
$subheading = get_sub_field('subheading');
$image = wp_get_attachment_image(get_sub_field('image'), 'full');
$mobile_image = wp_get_attachment_image(get_sub_field('image_mobile'), 'full');
$copy = get_sub_field('copy');
$pre_copy = get_sub_field('pre_copy');
$reverse = get_sub_field('reverse_layout');

$add_video = get_sub_field('add_video');

$video_url = get_sub_field('video_url');
$placeholder = wp_get_attachment_image(get_sub_field('video_placeholder'), 'full');

$dark_background = get_sub_field('dark_background');

if ($wide_heading) {
    $classes[] = 'image-content--wide-heading';
}

if ($dark_background) {
    $classes[] = 'image-content--dark-background bg-secondary tear-border';
}

if($reverse) {
    $classes[] = 'reverse';
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
                <div class="<?= $wide_heading ? 'col-md-9' : 'col-md-6'; ?> <?php if($reverse): ?> offset-md-6 <?php endif; ?>  <?php if(!$dark_background): ?> position-static <?php endif; ?>">
                    <?php if ($heading) : ?>
                        <h2 data-aos="fade"><?= $heading; ?></h2>
                    <?php endif; ?>
                    <?php if ($image) : ?>
                        <div class="image-content__image <?php if(!$dark_background): ?> image-content__image--desktop <?php endif; ?> d-none d-md-block <?php if($reverse): ?> image-content__image-right <?php endif; ?>">
                            <div data-aos="fade-right">
                                <?= $image; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
            <div class="row">
                <div class="<?php if(!$reverse): ?> offset-md-6 col-md-6 <?php else: ?> col-md-5 <?php endif; ?>">
                    <div class="image-content__content" data-aos="fade-up">
                        <?php if($add_video): ?>
                            <?php if ($video_url) : ?>
                                <a href="<?= $video_url; ?>" data-fancybox>
                            <?php endif; ?>
                            <div class="image-content__video position-relative round-image">
                                <?= $placeholder; ?>

                                <?php if ($video_url) : ?>
                                    <span class="play"></span>
                                <?php endif; ?>
                            </div>
                            <?php if ($video_url) : ?>
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if($subheading) : ?>
                            <h3 class="image-content__subheading">
                                <?= $subheading ?>
                            </h3>
                        <?php endif; ?>
                        <?php if($pre_copy): ?>
                            <div class="image-content__precopy">
                                <?= $pre_copy; ?>
                            </div>
                        <?php endif; ?>
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
