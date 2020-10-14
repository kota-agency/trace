<?php

/**
 * Block: Sectors
 */

$classes = ['full-width', padding_classes()];

$footnote = get_field('footnote');

?>

<section <?= block_id(); ?> class="sectors <?= implode(' ', $classes); ?>" data-aos="fade-up">
    <div class="container">
        <?php if (have_rows('items')) : ?>
            <div class="row justify-content-center">
                <?php while (have_rows('items')) : the_row(); ?>
                    <div class="col-md-6 col-lg-4 sectors__col">
                        <?php get_component('sector-tab'); ?>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
        <?php if($footnote) : ?>
            <div class="text-center sectors__footnote" data-aos="fade-up">
                <?= $footnote; ?>
            </div>
        <?php endif; ?>
    </div>
</section><!-- .sectors -->
