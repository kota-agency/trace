<?php

/**
 * Block: Half Width Content
 */

$classes = ['full-width', padding_classes()];

$side_image = wp_get_attachment_image(get_sub_field('side_image'), 'full');
$offset = get_sub_field('offset');
$copy = get_sub_field('copy');
$quote_image = wp_get_attachment_image(get_sub_field('quote_image'), 'full');
$quote_image_position = get_sub_field('quote_image_position');
$layout = get_sub_field('layout');

?>

<?php if ($copy) : ?>
    <section <?= block_id(); ?> class="half-width-content half-width-content--<?= $layout ?> <?= implode(' ', $classes); ?>" data-aos="fade">
        <div class="container">
            <div class="row <?php if($layout == 'quote-left') : ?> flex-lg-row-reverse <?php endif; ?>">
                <div class="col-lg-6 <?php if ($offset) : ?>offset-lg-1<?php endif; ?> order-2 order-lg-1 last-margin">
                    <?= $copy; ?>
                </div>
                <div class="col-lg-5 order-1 order-lg-2 
                <?php if($side_image): ?>
                    position-relative 
                <?php else: ?>
                    position-static
                <?php endif; ?>">
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
                                <div class="half-width-content__side-quote <?php if ($quote_image) : ?> has-image <?php endif; ?>">

                                    <?php if ($quote_image) : ?>
                                        <div class="half-width-content__quote-image half-width-content__quote-image--<?= $quote_image_position ?>">
                                            <div data-aos="fade-left">
                                                <?= $quote_image; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
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
