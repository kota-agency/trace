<?php

/**
 * Block: Timeline
 */

$classes = ['full-width', padding_classes()];
$heading = get_field('heading');

?>

<section <?= block_id(); ?> class="timeline <?= implode(' ', $classes); ?>" data-aos="fade">
    <div class="timeline__inner">
        <div class="container">
            <?php if ($heading) : ?>
                <h1 class="timeline__heading"><?= $heading; ?></h1>
            <?php endif; ?>
            <div class="row">
                <div class="col-lg-8">
                    <?php if (have_rows('milestones')) : $i = 0; ?>
                        <div class="timeline__milestones">
                            <?php while (have_rows('milestones')) : the_row(); ?>
                                <?php

                                $year = get_sub_field('year');
                                $image = wp_get_attachment_image(get_sub_field('image'), 'milestone');
                                $copy = get_sub_field('copy');

                                ?>
                                <div class="milestone <?= $i === 0 ? 'active' : '' ?>">
                                    <div class="milestone__inner">
                                        <?php if ($year) : ?>
                                            <div class="milestone__year"><span><?= $year; ?></span></div>
                                        <?php endif; ?>

                                        <div class="milestone__image">
                                            <?php if ($image) : ?>
                                                <span><?= $image; ?></span>
                                            <?php endif; ?>
                                        </div>

                                        <?php if ($copy) : ?>
                                            <div class="milestone__copy last-margin">
                                                <?= $copy; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php $i++; endwhile; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section><!-- .timeline -->
