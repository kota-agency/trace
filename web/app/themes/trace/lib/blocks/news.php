<?php

/**
 * Block: News
 */

$classes = ['full-width', 'btn-space', 'text-center', padding_classes()];

$heading = get_sub_field('heading');
$_posts = get_sub_field('posts');

if (!$_posts) {
    $_posts = get_posts([
        'post_type' => 'post',
        'post_status' => 'publish',
        'numberposts' => 3
    ]);
}

?>

<section <?= block_id(); ?> class="news <?= implode(' ', $classes); ?>">
    <div class="container">
        <?php if ($heading) : ?>
            <h2 class="news__heading text-center heading-width" data-aos="fade-up"><?= $heading; ?></h2>
        <?php endif; ?>
        <?php if ($_posts) : ?>
            <div class="news__items">
                <div class="row">
                    <?php foreach ($_posts as $_post) : ?>
                        <div class="col-12 news__col" data-aos="fade-up">
                            <?php get_component('card-wide', $_post); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        <div data-aos="fade">
            <?php get_component('buttons'); ?>
        </div>
    </div>
</section><!-- .news -->
