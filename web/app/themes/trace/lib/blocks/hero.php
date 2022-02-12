<?php

/**
 * Block: Hero
 */

$classes = ['full-width', 'theme-primary', 'bg-primary', padding_classes()];

$title = get_field('title');
$background_text = get_field('background_text');
$video = get_field('video');

if($video['vimeo_embed'])
    preg_match('/src="([^"]+)"/', $video['vimeo_embed'], $match);

if($video['youtube_embed'])
    preg_match('/src="([^"]+)"/', $video['youtube_embed'], $match);
$video_url = $match[1];

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
                    <?php if($video_url): ?>
                        <a href="<?= $video_url ?>" data-fancybox>
                            <span class="play"></span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

        <?php endif; ?>
    </div>
</section><!-- .hero -->
