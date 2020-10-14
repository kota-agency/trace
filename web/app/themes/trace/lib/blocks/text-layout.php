<?php

/**
 * Block: Text Layout
 */

$classes = ['full-width', 'bg-primary', padding_classes()];

$split_level = get_field('split_level');
$lower_background_text = get_field('lower_background_text');
$image_to_right = get_field('image_to_right');
$heading = get_field('heading');
$background_text = get_field('background_text');
$intro_copy = get_field('intro_copy');
$image = wp_get_attachment_image(get_field('image'), 'full');

if($split_level) {
    $classes[] = 'text-layout--split-level';
}

if($lower_background_text) {
    $classes[] = 'text-layout--lower-background-text';
}

if($image_to_right) {
    $classes[] = 'text-layout--image-right';
}

if($intro_copy) {
    $classes[] = 'text-layout--intro-text';
}

?>

<section <?= block_id(); ?> class="text-layout <?= implode(' ', $classes); ?>">
    <div class="container" data-aos="fade">
        <?php if ($background_text) : ?>
            <h2 class="background-text background-text--large"><?= $background_text; ?></h2>
        <?php endif; ?>

        <?php if ($image) : ?>
            <div class="text-layout__image d-md-none" data-aos="fade">
                <?= $image; ?>
            </div>
        <?php endif; ?>
        <div class="text-layout__wrapper" >
            <?php if ($image_to_right) : ?>
                <div class="row">
                    <div class="col-12" data-aos="fade">
                        <?php if ($heading) : ?>
                            <h2><?= $heading; ?></h2>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6 col-lg-4" data-aos="fade">

                        <?php if ($intro_copy) : ?>
                            <div class="copy-xxl">
                                <?= $intro_copy; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6 col-lg-5" data-aos="fade">
                        <?php if (have_rows('columns')) : ?>
                            <div class="row">
                                <?php while (have_rows('columns')) : the_row(); ?>
                                    <?php $copy = get_sub_field('copy'); ?>
                                    <?php if ($copy) : ?>
                                        <div class="col-lg">
                                            <div>
                                                <?= $copy; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endwhile; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ($image) : ?>
                    <div class="text-layout__image d-none d-md-block">
                        <div data-aos="fade">
                        <?= $image; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else : ?>
                <?php if ($split_level) : ?>
                    <div class="row text-layout__split-row" data-aos="fade">
                        <div class="col-md-9">
                            <?php if ($heading) : ?>
                                <h2><?= $heading; ?></h2>
                            <?php endif; ?>
                            <?php if ($intro_copy) : ?>
                                <div class="copy-xxl text-layout__intro">
                                    <?= $intro_copy; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?php if ($image) : ?>
                                <div class="text-layout__image d-none d-md-block">
                                    <?= $image; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <?php if (have_rows('columns')) : ?>
                                <div class="row">
                                    <?php while (have_rows('columns')) : the_row(); ?>
                                        <?php $copy = get_sub_field('copy'); ?>
                                        <?php if ($copy) : ?>
                                            <div class="col-lg">
                                                <div>
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
                    <div class="row">
                        <div class="col-md-6" data-aos="fade">
                            <?php if ($heading) : ?>
                                <h2><?= $heading; ?></h2>
                            <?php endif; ?>
                            <?php if ($intro_copy) : ?>
                                <div>
                                    <?= $intro_copy; ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($image) : ?>
                                <div class="text-layout__image d-none d-md-block" data-aos="fade">
                                    <?= $image; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6" data-aos="fade">
                            <?php if (have_rows('columns')) : ?>
                                <div class="row">
                                    <?php while (have_rows('columns')) : the_row(); ?>
                                        <?php $copy = get_sub_field('copy'); ?>
                                        <?php if ($copy) : ?>
                                            <div class="col-lg">
                                                <div>
                                                    <?= $copy; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endwhile; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</section><!-- .services -->
