<?php

/**
 * Block: Family
 */

$classes = ['full-width', padding_classes()];

$heading = get_field('heading');
$background_text = get_field('background_text');
$logos = get_field('logos');

?>

<section <?= block_id(); ?> class="family <?= implode(' ', $classes); ?>">
    <div class="container">
        <div class="family__wrapper" data-aos="fade-up">
            <?php if ($heading) : ?>
                <h2 class="no-margin"><?= $heading; ?></h2>
            <?php endif; ?>
            <?php if ($background_text) : ?>
                <h2 class="background-text background-text--large"><?= $background_text; ?></h2>
            <?php endif; ?>

            <?php if ($logos) : ?>
                <div class="family__logos">
                    <?php foreach ($logos as $logo_id) : ?>
                        <?php $logo = wp_get_attachment_image($logo_id, 'logo'); ?>
                        <?php if ($logo) : ?>
                            <div class="family__logo">
                                <?= $logo; ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section><!-- .family -->
