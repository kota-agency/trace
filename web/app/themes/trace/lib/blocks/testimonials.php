<?php

/**
 * Block: Testimonials
 */

$classes = ['full-width', padding_classes()];

?>

<section <?= block_id(); ?> class="testimonials <?= implode(' ', $classes); ?>" data-aos="zoom-in">
    <?php if (have_rows('items')) : ?>
        <div class="testimonials__items">
            <?php while (have_rows('items')) : the_row(); ?>
                <?php

                $text = get_sub_field('text');
                $author = get_sub_field('author');
                $company = get_sub_field('company');

                ?>
                <div class="testimonials__item">
                    <?php if ($text) : ?>
                        <h2><?= $text; ?></h2>
                    <?php endif; ?>
                    <div class="container">
                        <?php if ($author) : ?>
                            <strong><?= $author; ?><?= ($author && $company) ? ',' : ''; ?></strong>
                        <?php endif; ?>
                        <?php if ($company) : ?>
                            <span><?= $company; ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <div class="testimonials__arrows"></div>
    <?php endif; ?>
</section><!-- .testimonials -->
