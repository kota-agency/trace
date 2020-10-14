<?php

/**
 * Component: Post Header
 */

$cats = get_the_category();
$cat_slugs = wp_list_pluck($cats, 'slug');

$label = get_the_date('jS F Y');

if (in_array('case-studies', $cat_slugs)) {
    $label = __('Case Study', 'trace');
}

$title = get_the_title();
$image = get_the_post_thumbnail(get_the_ID(), 'full');

?>

<section class="post-heading full-width">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <?php if ($label) : ?>
                    <h6 class="label"><?= $label; ?></h6>
                <?php endif; ?>
                <?php if ($title) : ?>
                    <h1><?= $title; ?></h1>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($image) : ?>
            <div class="post-heading__image round-image">
                <?= $image; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
