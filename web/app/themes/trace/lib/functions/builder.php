<?php

add_filter('block_categories', function ($categories, $post) {
    return array_merge(
        [
            [
                'slug' => 'page-blocks',
                'title' => __('Page Blocks', 'theme-blocks'),
            ],

        ],
        $categories
    );
}, 10, 2);

add_action('acf/init', function () {

    if (function_exists('acf_register_block')) {

//        acf_register_block([
//            'name' => 'hero',
//            'title' => __('Hero'),
//            'description' => __('Title, background image'),
//            'render_callback' => 'theme_block_render_callback',
//            'category' => 'page-blocks',
//            'keywords' => ['Hero'],
//            'mode' => 'edit'
//        ]);

//        acf_register_block([
//            'name' => 'image_content',
//            'title' => __('Image & Content'),
//            'description' => __('Image & Content'),
//            'render_callback' => 'theme_block_render_callback',
//            'category' => 'page-blocks',
//            'keywords' => ['Copy'],
//            'mode' => 'edit'
//        ]);

//        acf_register_block([
//            'name' => 'family',
//            'title' => __('Family'),
//            'description' => __('Family'),
//            'render_callback' => 'theme_block_render_callback',
//            'category' => 'page-blocks',
//            'keywords' => ['Copy'],
//            'mode' => 'edit'
//        ]);

//        acf_register_block([
//            'name' => 'sectors',
//            'title' => __('Sectors'),
//            'description' => __('Sectors'),
//            'render_callback' => 'theme_block_render_callback',
//            'category' => 'page-blocks',
//            'keywords' => ['Copy'],
//            'mode' => 'edit'
//        ]);

//        acf_register_block([
//            'name' => 'video',
//            'title' => __('Video'),
//            'description' => __('Video'),
//            'render_callback' => 'theme_block_render_callback',
//            'category' => 'page-blocks',
//            'keywords' => ['Copy'],
//            'mode' => 'edit'
//        ]);

//        acf_register_block([
//            'name' => 'testimonials',
//            'title' => __('Testimonials'),
//            'description' => __('Testimonials'),
//            'render_callback' => 'theme_block_render_callback',
//            'category' => 'page-blocks',
//            'keywords' => ['Copy'],
//            'mode' => 'edit'
//        ]);

//        acf_register_block([
//            'name' => 'content_video',
//            'title' => __('Content & Video'),
//            'description' => __('Content & Video'),
//            'render_callback' => 'theme_block_render_callback',
//            'category' => 'page-blocks',
//            'keywords' => ['Copy'],
//            'mode' => 'edit'
//        ]);

//        acf_register_block([
//            'name' => 'cta',
//            'title' => __('CTA'),
//            'description' => __('CTA'),
//            'render_callback' => 'theme_block_render_callback',
//            'category' => 'page-blocks',
//            'keywords' => ['Copy'],
//            'mode' => 'edit'
//        ]);

//        acf_register_block([
//            'name' => 'news',
//            'title' => __('News'),
//            'description' => __('News'),
//            'render_callback' => 'theme_block_render_callback',
//            'category' => 'page-blocks',
//            'keywords' => ['Posts'],
//            'mode' => 'edit'
//        ]);

//        acf_register_block([
//            'name' => 'form',
//            'title' => __('Form'),
//            'description' => __('Form'),
//            'render_callback' => 'theme_block_render_callback',
//            'category' => 'page-blocks',
//            'keywords' => ['Form'],
//            'mode' => 'edit'
//        ]);

        acf_register_block([
            'name' => 'page_header',
            'title' => __('Page Header'),
            'description' => __('Page Header'),
            'render_callback' => 'theme_block_render_callback',
            'category' => 'page-blocks',
            'keywords' => ['Copy'],
            'mode' => 'edit'
        ]);

//        acf_register_block([
//            'name' => 'page_links',
//            'title' => __('Page Links'),
//            'description' => __('Page Links'),
//            'render_callback' => 'theme_block_render_callback',
//            'category' => 'page-blocks',
//            'keywords' => ['Copy'],
//            'mode' => 'edit'
//        ]);

//        acf_register_block([
//            'name' => 'heading_columns',
//            'title' => __('Heading & Columns'),
//            'description' => __('Heading & Columns'),
//            'render_callback' => 'theme_block_render_callback',
//            'category' => 'page-blocks',
//            'keywords' => ['Copy'],
//            'mode' => 'edit'
//        ]);

//        acf_register_block([
//            'name' => 'split_content',
//            'title' => __('Split Content'),
//            'description' => __('Split Content'),
//            'render_callback' => 'theme_block_render_callback',
//            'category' => 'page-blocks',
//            'keywords' => ['Copy'],
//            'mode' => 'edit'
//        ]);


        acf_register_block([
            'name' => 'half_width_content',
            'title' => __('Half Width Content'),
            'description' => __('Half Width Content'),
            'render_callback' => 'theme_block_render_callback',
            'category' => 'page-blocks',
            'keywords' => ['Copy'],
            'mode' => 'edit'
        ]);

        acf_register_block([
            'name' => 'video_modal',
            'title' => __('Video Modal'),
            'description' => __('Video Modal'),
            'render_callback' => 'theme_block_render_callback',
            'category' => 'page-blocks',
            'keywords' => ['Video'],
            'mode' => 'edit'
        ]);

//        acf_register_block([
//            'name' => 'contact_details',
//            'title' => __('Contact Details'),
//            'description' => __('Contact Details'),
//            'render_callback' => 'theme_block_render_callback',
//            'category' => 'page-blocks',
//            'keywords' => ['Copy'],
//            'mode' => 'edit'
//        ]);

        acf_register_block([
            'name' => 'signposts',
            'title' => __('Signposts'),
            'description' => __('Signposts'),
            'render_callback' => 'theme_block_render_callback',
            'category' => 'page-blocks',
            'keywords' => ['Copy'],
            'mode' => 'edit'
        ]);

//        acf_register_block([
//            'name' => 'timeline',
//            'title' => __('Timeline'),
//            'description' => __('Timeline'),
//            'render_callback' => 'theme_block_render_callback',
//            'category' => 'page-blocks',
//            'keywords' => ['Copy'],
//            'mode' => 'edit'
//        ]);

        acf_register_block([
            'name' => 'post_loop',
            'title' => __('Post Loop'),
            'description' => __('Post Loop'),
            'render_callback' => 'theme_block_render_callback',
            'category' => 'page-blocks',
            'keywords' => ['Posts'],
            'mode' => 'edit'
        ]);

//        acf_register_block([
//            'name' => 'text_layout',
//            'title' => __('Text Layout'),
//            'description' => __('Text Layout'),
//            'render_callback' => 'theme_block_render_callback',
//            'category' => 'page-blocks',
//            'keywords' => ['Copy'],
//            'mode' => 'edit'
//        ]);

//        acf_register_block([
//            'name' => 'content_modals',
//            'title' => __('Content Modals'),
//            'description' => __('Content Modals'),
//            'render_callback' => 'theme_block_render_callback',
//            'category' => 'page-blocks',
//            'keywords' => ['Copy'],
//            'mode' => 'edit'
//        ]);
    }

});


function theme_block_render_callback($block)
{

    // convert name ("acf/testimonial") into path friendly slug ("testimonial")
    $slug = str_replace('acf/', '', $block['name']);

    // include a template part from within the "template-parts/block" folder
    if (file_exists(get_theme_file_path("/lib/blocks/{$slug}.php"))) {
        include(get_theme_file_path("/lib/blocks/{$slug}.php"));
    }
}
