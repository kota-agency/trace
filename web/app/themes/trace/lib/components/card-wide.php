<?php

/**
 * Component: Card Wide
 */

$_post = get_query_var('data');

$card_thumbnail_id = get_field('p_card_image', $_post->ID);
$thumbnail = get_the_post_thumbnail_url($_post->ID, 'card');
$heading = get_the_title($_post->ID);
$excerpt = apply_filters('the_content', excerpt(30, $_post->ID));

if ($card_thumbnail_id) {
    $thumbnail = wp_get_attachment_image_url($card_thumbnail_id, 'card');
}

?>

<article class="card-wide">
    <a href="<?= get_the_permalink($_post->ID); ?>" class="card-wide__link">
        <?php if ($thumbnail) : ?>
            <div class="card-wide__image bg-cover" style="background-image: url(<?= $thumbnail; ?>);"></div>
        <?php endif; ?>
        <div class="card-wide__content">
            <?php if ($heading) : ?>
                <h4 class="card-wide__heading"><?= $heading; ?></h4>
            <?php endif; ?>
            <?php if ($excerpt) : ?>
                <div class="copy-s">
                    <?= $excerpt; ?>
                </div>
            <?php endif; ?>
        </div>
    </a>
</article><!-- .card-wide -->
