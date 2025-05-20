<?php

/**
 * Block: Content & Video
 */


$classses = ['full-width', 'theme-secondary', 'bg-secondary', 'tear-border', padding_classes()];

$heading = get_sub_field('heading');
$logo = get_sub_field('logo');
$video_url = get_sub_field('video_url');
$video_image = wp_get_attachment_image_url(get_sub_field('video_image'), 'content_image');
$graphic = get_sub_field('graphic');
$copy = get_sub_field('copy');

?>

<section <?= block_id(); ?> class="content-video <?= implode(' ', $classses); ?>">
    <div class="container">
        <div class="row">
            <div class="col-lg-7">
                <div class="content-video__heading-wrapper" data-aos="fade">
                    <?php if ($heading) : ?>
                        <h2><?= $heading; ?></h2>
                    <?php endif; ?>
                    <?php if ($logo) : ?>
                        <div class="content-video__logo">
                            <img src="<?= $logo['url']; ?>" alt="<?= $logo['alt']; ?>">
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($video_image && $video_url) : ?>
                    <div class="content-video__video d-none d-lg-block" data-aos="fade">
                        <a href="<?= $video_url ?>" data-fancybox aria-label="Play video">
                            <div class="content-video__image bg-cover"
                                style="background-image: url(<?= $video_image; ?>);">
                                <span class="play"></span>
                            </div>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-lg-4" data-aos="fade">
                <?php if ($copy) : ?>
                    <div class="content-video__copy">
                        <?= $copy; ?>
                    </div>
                <?php endif; ?>
                <?php if ($video_image && $video_url) : ?>
                    <div class="content-video__video d-lg-none">
                        <a href="<?= $video_url ?>" data-fancybox aria-label="Play video">
                            <div class="content-video__image bg-cover"
                                style="background-image: url(<?= $video_image; ?>);">
                                <span class="play"></span>
                            </div>
                        </a>
                    </div>
                <?php endif; ?>
                <?php get_component('buttons'); ?>
            </div>
        </div>

        <?php if ($graphic) : ?>
            <div class="content-video__graphic">
                <div data-aos="fade">
                    <img src="<?= $graphic['url']; ?>" alt="<?= $graphic['alt']; ?>">
                </div>
            </div>
        <?php endif; ?>
    </div>
</section><!-- .content-video -->