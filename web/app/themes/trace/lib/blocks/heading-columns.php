<?php

/**
 * Block: Heading * Columns
 */

$classes = ['full-width', 'tear-border', 'theme-secondary', 'bg-secondary'];

$heading = get_sub_field('heading');
$graphic = get_sub_field('graphic');
$items = get_sub_field('items');

?>

<section <?= block_id(); ?> class="heading-columns block <?= implode(' ', $classes); ?>">
    <div class="container">
        <div class="heading-columns__heading-wrapper" data-aos="fade-up">
            <?php if ($heading) : ?>
                <h1 class="heading-columns__heading">
                    <?= $heading; ?>        
                </h1>
            <?php endif; ?>
        </div>

        <?php if (have_rows('items')) : ?>
            <div class="row heading-columns__columns" data-aos="fade-up">
                <?php while(have_rows('items')): the_row(); ?>
                    <div class="col-md-6 heading-columns__column">
                        <?php if(get_sub_field('title')): ?>
                            <div class="heading-columns__title">
                                <?= get_sub_field('title') ?>
                            </div>
                        <?php endif; ?>
                        <div class="heading-columns__copy">
                            <?= get_sub_field('copy') ?>
                        </div>

                        <?php get_component('buttons'); ?>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

        <div class="heading-columns__graphic">
            <div data-aos="fade">
                <img src="<?= $graphic['url']; ?>" alt="<?= $graphic['alt']; ?>">
            </div>
        </div>
    </div>
</section><!-- .heading-columns -->
