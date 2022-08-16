<?php

/**
 * Block: Signposts
 */

$classes = ['full-width', padding_classes()];

$border_top = get_sub_field('top_border');
$heading = get_sub_field('heading');
$_posts = get_sub_field('posts');

if ($border_top) {
    $classes[] = 'signposts--top-border';
}

if (is_single() && !$_posts) {

    if (!$heading) {
        $heading = __('Related', 'trace');
    }

    $_terms = get_the_terms(get_the_ID(), 'category');

    $args = [
        'post_type' => 'post',
        'post_status' => 'publish',
        'numberposts' => 2,
        'post__not_in' => [get_the_ID()],
        'orderby' => 'rand',
        'order' => 'ASC'
    ];

    if ($_terms) {
        $term_ids = wp_list_pluck($_terms, 'term_id');

        $args['tax_query'] = [
            [
                'taxonomy' => 'category',
                'terms' => $term_ids
            ]
        ];
    }

    $_posts = get_posts($args);
}

?>

<section <?= block_id(); ?> class="signposts <?= implode(' ', $classes); ?>" data-aos="fade">
    <div class="container">
        <?php if ($border_top) : ?>
            <hr>
        <?php endif; ?>
        <?php if ($heading) : ?>
            <h3 class="signposts__heading"><?= $heading; ?></h3>
        <?php endif; ?>
        <?php if ($_posts) : ?>
            <div class="row">
                <?php foreach ($_posts as $_post) : ?>
                    <div class="col-md-6 signposts__col">
                        <?php get_component('card', $_post); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    
    <?php if (is_singular('post')) : ?>
        <div class="signposts__btn">
            <div class="container">
                <?php

                $button = ['url' => get_permalink(get_option('page_for_posts')), 'title' => __("See all news", 'trace')];
                get_component('button', $button);

                ?>
            </div>
        </div>
    <?php endif; ?>

</section><!-- .signposts -->