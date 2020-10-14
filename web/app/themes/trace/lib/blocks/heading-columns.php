<?php

/**
 * Block: Heading * Columns
 */

$classes = ['full-width', 'tear-border', 'theme-secondary', 'bg-secondary'];

$heading = get_field('heading');
$background_text = get_field('background_text');
$intro_text = get_field('intro_copy');
$vertical_heading = get_field('vertical_heading');
$cover_graphic = get_field('cover_bottom_graphic');
$graphics = get_field('graphics');
$offset_graphics = get_field('offset_graphics');

if ($vertical_heading) {
    $classes[] = 'heading-columns--vertical-heading';
}

if ($cover_graphic) {
    $classes[] = 'heading-columns--cover-graphic';
}

if ($offset_graphics) {
    $classes[] = 'heading-columns--offset-graphics';
}


?>

<section <?= block_id(); ?> class="heading-columns <?= implode(' ', $classes); ?>">
    <div class="container">
        <?php if ($vertical_heading) : ?>
            <?php if ($background_text) : ?>
                <h2 class="background-text background-text--large"><?= $background_text; ?></h2>
            <?php endif; ?>
            <div class="row">
                <div class="col-md-3 heading-columns__vert-heading" data-aos="fade-up">
                    <?php if ($heading) : ?>
                        <h2 class="heading-columns__heading"><span class="vert-heading"><?= $heading; ?></span></h2>
                    <?php endif; ?>
                </div>
                <div class="col col-lg-6">
                    <?php if ($intro_text) : ?>
                        <div class="heading-columns__intro-text" data-aos="fade-up">
                            <?= $intro_text; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (have_rows('columns')) : ?>
                        <div class="row heading-columns__columns" data-aos="fade-up">
                            <?php while (have_rows('columns')) : the_row(); ?>
                                <?php $copy = get_sub_field('copy'); ?>
                                <?php if ($copy) : ?>
                                    <div class="col-md-6">
                                        <div class="heading-columns__copy">
                                            <?= $copy; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endwhile; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php else : ?>
            <div class="heading-columns__heading-wrapper" data-aos="fade-up">
                <?php if ($heading) : ?>
                    <h1 class="heading-columns__heading"><?= $heading; ?></h1>
                <?php endif; ?>
                <?php if ($background_text) : ?>
                    <h2 class="background-text background-text--large"><?= $background_text; ?></h2>
                <?php endif; ?>
                <?php if ($intro_text) : ?>
                    <div class="heading-columns__intro-text">
                        <?= $intro_text; ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (have_rows('columns')) : ?>
                <div class="row heading-columns__columns" data-aos="fade-up">
                    <?php while (have_rows('columns')) : the_row(); ?>
                        <?php $copy = get_sub_field('copy'); ?>
                        <?php if ($copy) : ?>
                            <div class="col-md-6">
                                <div class="heading-columns__copy">
                                    <?= $copy; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        <?php if ($graphics) : ?>

            <div class="heading-columns__graphics">
                <?php foreach ($graphics as $graphic) : ?>
                    <div class="heading-columns__graphic">
                        <div data-aos="fade">
                            <img src="<?= $graphic['url']; ?>" alt="<?= $graphic['alt']; ?>">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section><!-- .heading-columns -->
