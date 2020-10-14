<?php

/**
 * Component: Modal
 */


$title = get_query_var('data') ? get_query_var('data') : get_mixed_field('heading');


?>
<?php if (have_rows('modal')) : ?>
    <?php while (have_rows('modal')) : the_row(); ?>
        <?php

        $id = get_sub_field('id');
        $subtitle = get_sub_field('subtitle');

        ?>
        <div <?= $id ? 'id="' . $id . '"' : ''; ?> class="modal theme-secondary">
            <div class="modal__inner">
                <div class="modal__close"></div>
                <?php if ($title) : ?>
                    <h3 class="modal__heading"><?= $title; ?></h3>
                <?php endif; ?>
                <?php if ($subtitle) : ?>
                    <h5 class="text-tertiary modal__subheading text-uppercase"><?= $subtitle; ?></h5>
                <?php endif; ?>
                <?php if (have_rows('columns')) : ?>
                    <div class="row">
                        <?php while (have_rows('columns')) : the_row(); ?>
                            <?php $copy = get_sub_field('copy'); ?>
                            <?php if ($copy) : ?>
                                <div class="col-md-6">
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
    <?php endwhile; ?>
<?php endif; ?>
