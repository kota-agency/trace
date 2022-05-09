<?php
/*
*	Plugin Name: Heroic Knowledge Base Analytics Pro
*	Plugin URI:  https://herothemes.com/heroic-knowledge base
*	Description: Enable analytics functionality for Heroic Knowledge Base
*	Author: HeroThemes
*	Version: 1.0.0
*	Author URI: https://www.herothemes.com/
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'ht_analytics_functions', '__return_true', 50 );