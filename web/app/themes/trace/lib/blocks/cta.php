<?php

/**
 * Block: CTA
 */

$classes = ['full-width', 'text-center', padding_classes()];

$larger_text = get_sub_field('larger_text');
$logo = get_sub_field('logo');
$background_text = get_sub_field('background_text');
$heading = get_sub_field('heading');
$copy = get_sub_field('copy');
$graphics = get_sub_field('graphics');

if ($larger_text) {
    $classes[] = 'cta--larger';
}

?>

<section <?= block_id() ?> class="cta <?= implode(' ', $classes); ?>">
    <div class="container" data-aos="fade-up">
        <?php if ($logo) : ?>
            <div class="cta__logo">
                <img src="<?= $logo['url']; ?>" alt="<?= $logo['alt']; ?>">
            </div>
        <?php endif; ?>
        <div class="cta__heading-wrapper">
            <?php if ($heading) : ?>
                <?php if ($larger_text) : ?>
                    <h2><?= $heading; ?></h2>
                <?php else : ?>
                    <h2><?= $heading; ?></h2>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($background_text) : ?>
                <h2 class="background-text background-text--large"><?= $background_text; ?></h2>
            <?php endif; ?>
            <?php if ($graphics) : ?>
                <div class="cta__graphics">
                    <?php foreach ($graphics as $graphic) : ?>
                        <div class="cta__graphic">
                            <img src="<?= $graphic['url']; ?>" alt="<?= $graphic['alt']; ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php if ($copy) : ?>
            <div class="cta__copy <?= $larger_text ? 'copy--xxl' : ''; ?>">
                <?= $copy; ?>
            </div>
        <?php endif; ?>
        <?php get_component('buttons'); ?>

    </div>
</section><!-- .cta -->
