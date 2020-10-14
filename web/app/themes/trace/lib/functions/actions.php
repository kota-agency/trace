<?php

/*
Remove info from headers
- remove the stuff we don't need
 */
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'index_rel_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'start_post_rel_link', 10, 0);
remove_action('wp_head', 'parent_post_rel_link', 10, 0);
remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');


/*
Admin Menus
- hide unused menu items
 */
add_action('admin_menu', function() {
    remove_menu_page('edit-comments.php');
});


/**
 * Remove the content from post types
 */
//add_action('admin_init', function() {
//
//});


/**
 * Google Maps ACF key
 */
add_action('acf/init', function() {

    $google_maps_key = get_field('google_maps_api_key', 'options');

    if($google_maps_key) {
        acf_update_setting('google_api_key', get_field('google_maps_api_key', 'options'));
    }
});


/**
 * Add the wp-editor back into WordPress after it was removed in 4.2.2.
 *
 * @see https://wordpress.org/support/topic/you-are-currently-editing-the-page-that-shows-your-latest-posts?replies=3#post-7130021
 * @param $post
 * @return void
 */
function fix_no_editor_on_posts_page($post)
{

    if ($post->ID != get_option('page_for_posts')) {
        return;
    }

    remove_action('edit_form_after_title', '_wp_posts_page_notice');
    add_post_type_support('page', 'editor');

}

// This is applied in a namespaced file - so amend this if you're not namespacing
add_action('edit_form_after_title', __NAMESPACE__ . '\\fix_no_editor_on_posts_page', 0);
