<?php

$data = get_query_var('data');

$_query = !empty($data['data']) ? $data['data'] : '';

$max_pages = $_query->max_num_pages;

?>
<?php if (get_next_posts_link('', $max_pages)) : ?>
    <div class="pagination pagination-infinite centre">
        <div class="pagination-infinite__infinite-scroll">
            <span class="btn pagination-infinite__button">
                <?php _e('Load More', 'floor'); ?>
            </span>
            <div class="page-load-status"><span class="loader infinite-scroll-request"></span></div>
        </div>
        <div class="next-posts-link d-none"><?php next_posts_link('Load more', $max_pages); ?></div>
    </div>
<?php endif; ?>
