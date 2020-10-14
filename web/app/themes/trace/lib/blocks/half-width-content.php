<?php

/**
 * Block: Half Width Content
 */

$classes = ['full-width', padding_classes()];

$side_image = wp_get_attachment_image(get_sub_field('side_image'), 'full');
$offset = get_sub_field('offset');
$copy = get_sub_field('copy');

?>

<?php if ($copy) : ?>
    <section <?= block_id(); ?> class="half-width-content <?= implode(' ', $classes); ?>" data-aos="fade">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 <?php if ($offset) : ?>offset-lg-1<?php endif; ?> order-2 order-lg-1 last-margin">
                    <?= $copy; ?>
                </div>
                <div class="col-lg-5 order-1 order-lg-2 position-static">
                    <?php if ($side_image) : ?>
                        <div class="half-width-content__image">
                            <div data-aos="fade-left">
                                <?= $side_image; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (have_rows('side_quote')) : ?>
                        <?php while (have_rows('side_quote')) : the_row(); ?>
                            <?php

                            $quote = get_sub_field('quote');
                            $desc = get_sub_field('description');

                            ?>
                            <?php if ($quote && $desc) : ?>
                                <div class="half-width-content__side-quote">
                                    <?php if ($quote) : ?>
                                        <blockquote>
                                            <?= $quote; ?>
                                            <?php if ($desc) : ?>
                                                <p class="copy-s no-margin"><?= $desc; ?></p>
                                            <?php endif; ?>
                                        </blockquote>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>


        </div>
    </section><!-- .half-width-content -->
<?php endif; ?>
