<?php

/**
 * Block: Form
 */

$classes = ['full-width', padding_classes()];

$heading = get_sub_field('heading');
$form_id = get_sub_field('form_id');

?>

<section <?= block_id(); ?> class="form <?= implode(' ', $classes); ?>" data-aos="fade">
    <div class="container">
        <?php if ($heading) : ?>
            <h2 class="heading-width text-center"><?= $heading; ?></h2>
        <?php endif; ?>
        <?php if ($form_id) : ?>
            <div class="form__wrapper">
                <?php gravity_form($form_id, false, false, false, null, true); ?>
            </div>
        <?php endif; ?>
    </div>
</section><!-- .form -->
