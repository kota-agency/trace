<?php

/**
 * Block: Full Width Image
 */

$image = wp_get_attachment_image(get_sub_field('image'), 'full');

?>

<?php if ($image) : ?>
    <section class="full-width-image full-width <?= padding_classes(); ?>">
        <div class="container">
            <div class="round-image">
            <?= $image; ?>
            </div>
        </div>
    </section><!-- .full-width-image -->
<?php endif; ?>
