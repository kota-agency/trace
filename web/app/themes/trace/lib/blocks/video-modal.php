<?php

/**
 * Block: Video Modal
 */

$classes = ['full-width', padding_classes()];

$video_url = get_sub_field('video_url');
$image = wp_get_attachment_image(get_sub_field('placeholder_image'), 'full');

?>

<section <?= block_id(); ?> class="video-modal <?= implode(' ', $classes); ?>" data-aos="fade">
    <div class="container">
        <?php if ($image) : ?>
            <?php if ($video_url) : ?>
                <a href="<?= $video_url; ?>" data-fancybox>
            <?php endif; ?>
            <div class="video-modal__image position-relative round-image">
                <?= $image; ?>

                <?php if ($video_url) : ?>
                    <span class="play"></span>
                <?php endif; ?>
            </div>
            <?php if ($video_url) : ?>
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section><!-- .video-modal -->
