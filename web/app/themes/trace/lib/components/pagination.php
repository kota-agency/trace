<?php

/**
 * Component: Pagination
 */

global $wp_query;

$_query = get_query_var('data');


$prev_label = __('Previous', 'imi');
$next_label = __('Next', 'imi');
$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
$max_page = $wp_query->max_num_pages;

if($_query) {
    if ($_query['data']->query['post_type'] === 'post') {
        $prev_label = __('Previous Articles', 'imi');
        $next_label = __('More Articles', 'imi');
    } elseif ($_query['data']->query['post_type'] === 'event') {
        $prev_label = __('Previous Events', 'imi');
        $next_label = __('More Events', 'imi');
    }
    $args = [
        'format' => '?_paged=%#%',
        'total' => $_query['data']->max_num_pages,
        'current' => $_query['paged'],
        'mid_size' => 1,
        'prev_text' => '<i class="fas fa-chevron-left"></i>' . $prev_label,
        'next_text' => $next_label . '<i class="fas fa-chevron-right"></i>',
        'add_fragment' => '#' . $_query['data']->query['post_type'] . 'Feed'
    ];
    $paged = $_query['paged'];
    $max_page = $_query['data']->max_num_pages;
} else {
    $args = [
        'mid_size' => 1,
        'prev_text' => '<i class="fas fa-chevron-left"></i>' . $prev_label,
        'next_text' => $next_label . '<i class="fas fa-chevron-right"></i>',
        'add_fragment' => '#searchFeed'
    ];
}
?>

<?php if(paginate_links($args)) : ?>
<div class="pagination remove-text-underline">
    <?php

    if ($paged == 1) {
        echo '<div class="pagination__prev-next"></div>';
    }

    echo paginate_links($args);

    if ($paged == $max_page) {
        echo '<div class="pagination__prev-next"></div>';
    }

    ?>
</div><!-- .pagination -->
<?php endif; ?>
