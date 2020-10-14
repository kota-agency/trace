<?php

/**
 * Block: Quote
 */

$classes = ['full-width', padding_classes()];

$quote = get_sub_field('quote');
$description = str_replace(['<p>', '</p>'], '', get_sub_field('description'));

?>

<section class="half-width-content quote <?= implode(' ', $classes); ?>">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 last-margin">
                <?php if($quote) : ?>
                    <blockquote>
                        <?= $quote; ?>
                        <?php if($description) : ?>
                            <p class="copy-s"><?= $description; ?></p>
                        <?php endif; ?>
                    </blockquote>
                <?php endif; ?>

            </div>
        </div>
    </div>
</section><!-- .quote -->
