<?php

/**
 * Block: Contact Details
 */

$classes = ['full-width', padding_classes()];
$address = get_field('address', 'options');
$address_link = get_field('address_link', 'options');

?>


<section <?= block_id(); ?> class="contact-details <?= implode(' ', $classes); ?>">
    <div class="contact-details__inner">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-lg-5 contact-details__col">
                    <div class="contact-details__content">
                        <h2><?= __('Call Us:', 'trace'); ?></h2>
                        <?php if (have_rows('numbers', 'options')) : ?>
                            <ul class="list-unstyled">
                                <?php while (have_rows('numbers', 'options')) : the_row(); ?>
                                    <?php

                                    $label = get_sub_field('label');
                                    $number = get_sub_field('number');

                                    ?>
                                    <li>
                                        <?php if ($label) : ?>
                                            <span class="d-block text-uppercase font-weight-black"><?= $label; ?></span>
                                        <?php endif; ?>
                                        <?php if ($number) : ?>
                                            <a href="<?= $number['url']; ?>" <?= $number['target'] ? 'target="_blank"' : ''; ?>><?= $number['title']; ?></a>
                                        <?php endif; ?>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6 col-lg-7 contact-details__col">
                    <div class="contact-details__content">
                        <h2><?= __('Visit Us:', 'trace'); ?></h2>
                        <?php if ($address) : ?>
                            <div class="copy-l">
                                <?= $address; ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($address_link) : ?>
                            <?php get_component('button', $address_link); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section><!-- .contact-details -->
