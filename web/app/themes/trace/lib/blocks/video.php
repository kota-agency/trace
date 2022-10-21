<?php

/**
 * Block: Video
 */

$classes = [ padding_classes()];

$embed = get_sub_field('vimeo_embed');
$placeholder = wp_get_attachment_image_url(get_sub_field('placeholder'), 'full');

?>

<?php if ($embed && $placeholder) : ?>
    <section <?= block_id(); ?> class="video <?= implode('', $classes); ?>"  data-aos="trigger" data-aos-delay="1000" data-aos-offset="500">
        <div class="container">
            <div class="video__wrapper">
                <?= $embed; ?>
                <div class="video__image bg-cover" style="background-image: url(<?= $placeholder; ?>);">
                    <span class="play"></span>
                </div>
            </div>
        </div>
        <?php get_component('buttons'); ?>
    </section><!-- .video -->
<?php endif; ?>


