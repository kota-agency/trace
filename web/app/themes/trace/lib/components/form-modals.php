<?php

/*
 * Component: Form Modals
 */

?>

<?php if (have_rows('form_modals', 'options')) : ?>
    <?php while (have_rows('form_modals', 'options')) : the_row(); ?>
        <?php

        $heading = get_sub_field('heading');
        $copy = get_sub_field('copy');
        $form_link_id = get_sub_field('form_link_id');
        $form_id = get_sub_field('form_id');

        ?>
        <div id="<?= $form_link_id; ?>" class="modal theme-secondary">
            <div class="modal__inner">
                <div class="modal__close"></div>
                <div class="modal__content">
                    <?php if ($heading) : ?>
                        <h3 class="modal__heading"><?= $heading; ?></h3>
                    <?php endif; ?>
                    <?php if ($copy) : ?>
                    <div><?= $copy; ?></div>
                </div>
                <?php endif; ?>
                <?php if ($form_id) : ?>
                    <?php gravity_form($form_id, false, false, false, null, true, $form_id * 50); ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endwhile; ?>
<?php endif; ?>
