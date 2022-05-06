<?php

/**
 * Component: Card Wide
 */

$_post = get_query_var('data');

$heading = get_the_title($_post->ID);
$excerpt = get_the_excerpt($_post->ID);
$thumbnail = get_the_post_thumbnail_url($_post->ID, 'large');

if ($card_thumbnail_id) {
    $thumbnail = wp_get_attachment_image_url($card_thumbnail_id, 'large');
}

?>

<article class="card">
    <a href="<?= get_the_permalink($_post->ID); ?>">
        <?php if ($thumbnail) : ?>
            <div class="card__image"><div class="card__image-el bg-cover" style="background-image: url(<?= $thumbnail; ?>);"></div></div>
        <?php endif; ?>
        <div class="card__content">
            <?php if ($heading) : ?>
                <h4 class="card__heading"><?= $heading; ?></h4>
            <?php endif; ?>
            <?php if ($excerpt) : ?>
                <p class="card__excerpt"><?= $excerpt; ?></p>
            <?php endif; ?>
        </div>
    </a>
</article><!-- .card-wide -->
