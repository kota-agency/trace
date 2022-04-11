<?php

/**
 * Block: Hero
 */

$classes = ['full-width', 'theme-primary', 'bg-primary', padding_classes()];

$title = get_field('title');
$background_text = get_field('background_text');
$video = get_field('hero_video', false, false);
$image = wp_get_attachment_image(get_field('image'), 'full');

?>

<section <?= block_id(); ?> class="hero <?= implode(' ', $classes); ?>">
    <div class="container">
        <?php if ($background_text) : ?>
            <h2 class="background-text"><?= $background_text; ?></h2>
        <?php endif; ?>
        <div class="row">
            <div class="col-md-8">

                <div class="hero__content" data-aos="fade-up">
                    <?php if ($title) : ?>
                        <h1 class="text-uppercase"><?= str_replace(['<p>', '</p>'], '', $title); ?></h1>
                    <?php endif; ?>

                </div>
            </div>
        </div>
        <?php if ($image) : ?>

            <div class="hero__image">
                <div data-aos="fade-left" data-aos-delay="500">
                    <?= $image; ?>
                    <?php if($video): ?>
                        <a href="<?= $video ?>" data-fancybox>
                            <span class="play"></span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

        <?php endif; ?>
    </div>
</section><!-- .hero -->
