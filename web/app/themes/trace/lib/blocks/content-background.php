<?php

/**
 * Block: Content & Video
 */


$classses = ['full-width', 'theme-secondary', 'bg-secondary', 'tear-border', padding_classes()];

$heading = get_sub_field('heading');
$subheading = get_sub_field('subheading');
$logo = get_sub_field('logo');
$graphic = get_sub_field('graphic');
$graphic_position = get_sub_field('graphic_position');
$copy = get_sub_field('copy');

?>


<section <?= block_id(); ?> class="content-background <?= implode(' ', $classses); ?>">
    <div class="container <?= $graphic_position; ?>">
        <div class="row">
            <div class="col-lg-6">
                <div class="content-background__heading-wrapper" data-aos="fade">
                    <?php if ($heading) : ?>
                        <h2><?= $heading; ?></h2>
                    <?php endif; ?>
                     <?php if ($subheading) : ?>
                        <div class="content-background__subheading">
                            <?= $subheading; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($logo) : ?>
                        <div class="content-background__logo">
                            <img src="<?= $logo['url']; ?>" alt="<?= $logo['alt']; ?>">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-5" data-aos="fade">
                <?php if ($copy) : ?>
                    <div class="content-background__copy">
                        <?= $copy; ?>
                    </div>
                <?php endif; ?>
                <?php get_component('buttons'); ?>
            </div>
        </div>

        <?php if ($graphic) : ?>
            <div class="content-background__graphic">
                <div data-aos="fade">
                    <img src="<?= $graphic['url']; ?>" alt="<?= $graphic['alt']; ?>">
                </div>
            </div>
        <?php endif; ?>
    </div>
</section><!-- .content-background -->
