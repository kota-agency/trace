<?php

define('THEME_DIRECTORY', get_template_directory());


/*
Thumbnails
- this theme supports thumbnails
 */
add_theme_support('post-thumbnails');
add_image_size('full', 3000, 3000, true);
add_image_size('logo', 200, 100, false);
add_image_size('content_image', 600, 350, true);
add_image_size('card', 400, 400, true);
// add_image_size('milestone', 100, 100, true);

/*
Scripts & Styles
- include the scripts and stylesheets
 */
add_action('wp_enqueue_scripts', function () {
	if (wp_script_is('jquery', 'registered')) {
		wp_deregister_script('jquery');

	}


	//wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js', array(), '2.2.4', false);
	wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js', array(), '3.3.1', false);
	//wp_enqueue_script('vendor', get_template_directory_uri() . '/dist/scripts/vendor.min.js', array(), '1.0.0', true);
	wp_enqueue_script('custom', get_template_directory_uri() . '/dist/scripts/script.min.js', array(), '1.0.12', true);
	//wp_enqueue_script('icons', get_template_directory_uri() . '/dist/scripts/icons.min.js', array(), '1.0.0', true);
	//wp_script_add_data( 'icons', 'data-search-pseudo-elements', true );

	wp_enqueue_style('style', get_template_directory_uri() . '/dist/styles/style.min.css', false, '1.0.20', 'all');
	wp_localize_script('custom', 'theme_params', array(
		'ajaxurl' => admin_url('admin-ajax.php'), // WordPress AJAX
		'stylesheet_dir' => get_stylesheet_directory_uri(),
	));
});

add_action('admin_enqueue_scripts', function () {
	wp_enqueue_style('admin-styles', get_stylesheet_directory_uri() . '/style-admin.css');
});


/*
Menus
- enable WordPress Menus
 */
if (function_exists('register_nav_menus')) {
	register_nav_menus(array('header' => __('Main Nav'), 'footer' => __('Footer Nav')));
}

/*
 * Add Excerpts to pages
 */
add_post_type_support('page', 'excerpt');

/**
 * Yoast breadcrumbs
 */
add_theme_support('yoast-seo-breadcrumbs');


/*
AFC Options
- register the ACF theme options
 */
if (function_exists('acf_add_options_page')) {

	acf_add_options_page(array(
		'page_title' => 'Theme Settings',
		'menu_title' => 'Theme Settings',
		'menu_slug' => 'theme-settings',
		'capability' => 'edit_posts',
		'redirect' => false
	));

}

add_theme_support('editor-styles');
add_editor_style('style-editor.css');


add_filter('mce_buttons_2', function ($buttons) {
	array_unshift($buttons, 'styleselect');

	return $buttons;
});

add_filter('tiny_mce_before_init', function ($styles) {

	$formats = [
		[
			'title' => 'Button Primary',
			'selector' => 'a',
			'classes' => 'btn',
			'wrapper' => false,
		],
		[
			'title' => 'Button Secondary',
			'selector' => 'a',
			'classes' => 'btn btn--secondary',
			'wrapper' => false,
		],
		[
			'title' => 'Styled Link',
			'selector' => 'a',
			'classes' => 'link',
			'wrapper' => false,
		],
		[
			'title' => 'Highlight Text',
			'inline' => 'span',
			'classes' => 'text-tertiary',
			'wrapper' => false,
		],
		[
			'title' => 'Demi Weight Text',
			'inline' => 'span',
			'classes' => 'font-weight-demi',
			'wrapper' => false,
		],
		[
			'title' => 'Heavy Weight Text',
			'inline' => 'span',
			'classes' => 'font-weight-black',
			'wrapper' => false,
		],
		[
			'title' => 'Small Copy',
			'selector' => 'p',
			'classes' => 'copy-s',
			'wrapper' => false,
		],
		[
			'title' => 'Large Copy',
			'selector' => 'p',
			'classes' => 'copy-l',
			'wrapper' => false,
		],
		[
			'title' => 'Extra Large Copy',
			'selector' => 'p',
			'classes' => 'copy-xl',
			'wrapper' => false,
		],
		[
			'title' => 'Extra Extra Large Copy',
			'selector' => 'p',
			'classes' => 'copy-xxl',
			'wrapper' => false,
		],
	];

	$styles['style_formats'] = json_encode($formats);

	return $styles;
});

function wdm_add_mce_button()
{

	if (!is_admin()) {
		return;
	}

	// check user permissions
	if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
		return;
	}
	// check if WYSIWYG is enabled
	if ('true' == get_user_option('rich_editing')) {
		add_filter('mce_external_plugins', 'wdm_add_tinymce_plugin');
		add_filter('mce_buttons', 'wdm_register_mce_button');
	}
}

add_action('admin_head', 'wdm_add_mce_button');

// register new button in the editor
function wdm_register_mce_button($buttons)
{
	array_push($buttons, 'wdm_mce_button');
	return $buttons;
}


// declare a script for the new button
// the script will insert the shortcode on the click event
function wdm_add_tinymce_plugin($plugin_array)
{
	$plugin_array['wdm_mce_button'] = get_stylesheet_directory_uri() . '/assets/scripts/admin/mce-buttons.js';
	return $plugin_array;
}

// Disable Gutenberg by template
function ea_disable_editor( $id = false ) {

	$excluded_templates = array(
		'temp-home.php',
		'temp-product.php',
		'temp-sectors.php',
		'temp-cookie.php',
		'temp-contact.php',
		'temp-mojo.php',
		'temp-services.php',
		'temp-our-story.php',
	);

	$excluded_ids = array(
		// get_option( 'page_on_front' )
	);

	if( empty( $id ) )
		return false;

	$id = intval( $id );
	$template = get_page_template_slug( $id );

	return in_array( $id, $excluded_ids ) || in_array( $template, $excluded_templates );
}


/**
 * Disable Gutenberg by template
 *
 */
function ea_disable_gutenberg( $can_edit, $post_type ) {

	if( ! ( is_admin() && !empty( $_GET['post'] ) ) )
		return $can_edit;

//	if( ea_disable_editor( $_GET['post'] ) )
		$can_edit = false;

	return $can_edit;

}
add_filter( 'gutenberg_can_edit_post_type', 'ea_disable_gutenberg', 10, 2 );
add_filter( 'use_block_editor_for_post_type', 'ea_disable_gutenberg', 10, 2 );

/**
 * Disable Classic Editor by template
 *
 */
function ea_disable_classic_editor() {

//	$screen = get_current_screen();
//	if( 'page' !== $screen->id || ! isset( $_GET['post']) )
//		return;

//	if( ea_disable_editor( $_GET['post'] ) ) {
		remove_post_type_support( 'page', 'editor' );
		remove_post_type_support( 'post', 'editor' );
//	}

}
add_action( 'admin_head', 'ea_disable_classic_editor' );



/**
 * Disable any Gutenberg blocks that we don't want to be displayed.
 */
add_filter('allowed_block_types', function ($allowed) {
    $registered = WP_Block_type_Registry::get_instance()->get_all_registered();
    $allowed = array_filter($registered, function ($key) {
        return strpos($key, 'acf/') !== false;
    }, ARRAY_FILTER_USE_KEY);
    return array_keys($allowed);
});

add_shortcode('gated_download', function ($atts, $content = null) { 
    $a = shortcode_atts( array(
        'id' => '',
        'style' => ''
    ), $atts );
     
    $gf = '[gravityform id=' . $a["id"] . ' ajax=true]';
    $hidden_form = '<div style="display:none;opacity:0;">' . do_shortcode($gf) . '</div>';
    add_action('wp_footer', function() use ($hidden_form){
    	echo $hidden_form;
    });
    if($a['style'] == 'btn') {
    	return '<a class="gated-file btn" data-gravity-form="'.$a['id'].'">' . $content . '</a>' ;
    }
    return '<a class="gated-file" data-gravity-form="'.$a['id'].'">' . $content . '</a>' ;
} ); 

add_action('wp_ajax_get_gravity_form', function() {
    $gravity_id = $_GET['gravity_form_id'];
    echo gravity_form($gravity_id, true, true, false, null, true);
    die;
});
add_action('wp_ajax_nopriv_get_gravity_form', function() {
    $gravity_id = $_GET['gravity_form_id'];
    echo gravity_form($gravity_id, true, true, false, null, true);
    die;
});