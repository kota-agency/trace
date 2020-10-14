<?php

/**
 * Component: Dropdown
 */

$_post = get_query_var('data');

?>

<div class="dropdown">
    <div class="dropdown__trigger">
        <h3><strong><?php echo get_the_title($_post->ID); ?></strong></h3>
    </div>
    <div class="dropdown__content">
        <div class="dropdown__copy copy-large">
            <?php echo apply_filters('the_content', $_post->post_content); ?>
        </div>
    </div>
</div>
