<?php

/**
 * Component: Filter
 */

global $wp;

$data = get_query_var('data');

$page_obj = get_queried_object();

$_post_type = get_field('category_filter_post_type') ? get_field('category_filter_post_type') : 'post';

$_taxonomies = get_object_taxonomies($_post_type);

$tag_blacklist = ['post_tag', 'post_format', 'product_type', 'product_visibility', 'product_cat', 'product_tag', 'product_shipping_class'];


?>

<div class="filter remove-bullets">

    <?php if ($_taxonomies) : ?>
        <div class="filter__items list-unstyled">
            <h6 class="micro"><strong><?= __('FILTER BY CATEGORY', 'trace'); ?></strong></h6>
            <?php foreach ($_taxonomies as $_taxonomy) : ?>
                <?php

                if (in_array($_taxonomy, $tag_blacklist)) {
                    continue;
                }

                $_terms = get_terms($_taxonomy);

                ?>

                <?php if ($_terms) : ?>
                    <ul class="filter__terms list-unstyled">
                        <li>
                            <a href="<?= get_post_type_archive_link($_post_type); ?>" class="btn btn--small  <?= !$page_obj->term_id ? '' : 'btn--hollow btn--white'; ?>"><?= __("All", "trace"); ?></a>
                        </li>
                        <?php foreach ($_terms as $_term) : ?>
                            <li class="filter__term">
                                <a href="<?= get_term_link($_term->term_id, $_taxonomy); ?>" class="btn btn--small  <?= ($page_obj->term_id === $_term->term_id) ? '' : 'btn--hollow btn--white'; ?>"><?= $_term->name; ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            <?php endforeach; ?>

        </div>
    <?php endif; ?>

</div>
