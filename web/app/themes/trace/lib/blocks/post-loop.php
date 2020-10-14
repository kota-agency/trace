<?php

/**
 * Block: Post Loop
 */

global $wp_query;

$classes = ['full-width', padding_classes()];

if(is_home() || is_archive()) {
    $classes[] = 'block-space--no-top';
}

?>

<section id="postFeed" class="post-loop <?= implode(' ', $classes); ?>" data-aos="fade">
    <div class="container">
        <?php
        get_component('filter');
        ?>
        <?php if (have_posts()) : $i = 0; ?>
            <div class="post-loop__feed">
                <div class="row inf-grid">
                    <?php while (have_posts()) : the_post(); ?>
                        <?php

                        $_post = get_post();

                        ?>
                        <div class="col-md-6 inf-post post-loop__col">
                            <?php get_component('card', $_post); ?>
                        </div>
                        <?php $i++; ?>
                    <?php endwhile; ?>
                </div>
                <?php get_component('pagination-infinite', ['data' => $wp_query, 'paged' => $paged]); ?>
            </div>
            <?php wp_reset_query(); ?>
        <?php else : ?>
            <?php get_component('no-results'); ?>
        <?php endif; ?>
    </div>
</section><!-- .post-loop -->
