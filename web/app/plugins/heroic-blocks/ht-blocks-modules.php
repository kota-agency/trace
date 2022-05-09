<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( !class_exists( 'HT_Blocks_Modules' ) ){
	class HT_Blocks_Modules {

		static function init(){
			// Hook: Frontend assets.
			add_action( 'enqueue_block_assets', array( __CLASS__, 'ht_blocks_block_assets' ) );
			// Hook: Editor assets.
			add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'ht_blocks_editor_assets' ) );
		}

		/**
		 * Enqueue Gutenberg block assets for both frontend + backend.
		 *
		 * @uses {wp-editor} for WP editor styles.
		 * @since 1.0.0
		 */
		static function ht_blocks_block_assets() { // phpcs:ignore
			// Styles.
			wp_enqueue_style(
				'ht-blocks-modules-styles', // Handle.
				plugins_url( '/dist/css/ht-blocks-modules-styles.css',  __FILE__  ), // Block frontend + backend style CSS.
				array( 'wp-editor' ), // Dependency to include the CSS after it.
				filemtime( __DIR__  . '/dist/css/ht-blocks-modules-styles.css' ) // Version: File modification time.
			);

			// any frontend scripts to be added here
			wp_enqueue_script(
				'ht-blocks-frontend', // Handle.
				plugins_url( '/dist/js/ht-blocks-frontend.js',  __FILE__  ), // Block.build.js: We register the block here. Built with Webpack.
				array( 'jquery' ), //dependencies
				filemtime( __DIR__  . '/dist/js/ht-blocks-frontend.js' ), // Version: File modification time.
				true // Enqueue the script in the footer.
			);
		}

		/**
		 * Enqueue Gutenberg block assets for backend editor.
		 *
		 * @uses {wp-blocks} for block type registration & related functions.
		 * @uses {wp-element} for WP Element abstraction — structure of blocks.
		 * @uses {wp-i18n} to internationalize the block's text.
		 * @uses {wp-editor} for WP editor styles.
		 * @since 1.0.0
		 */
		static function ht_blocks_editor_assets() { // phpcs:ignore
			$screen = get_current_screen();

			$ht_block_editor_script_dependencies = array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' );

			if($screen && is_a($screen, 'WP_Screen') && 'widgets' == $screen->base ){
				//don't allow on the widget editor screen due to wp-editor script conflict
				//https://core.trac.wordpress.org/ticket/53569
				return;
			}

			// Scripts.
			wp_enqueue_script(
				'ht-blocks-modules', // Handle.
				plugins_url( '/dist/js/ht-blocks-modules.js',  __FILE__  ), // Block.build.js: We register the block here. Built with Webpack.
				$ht_block_editor_script_dependencies, // Dependencies, defined above.
				filemtime( __DIR__  . '/dist/js/ht-blocks-modules.js' ), // Version: File modification time.
				true // Enqueue the script in the footer.
			);

			wp_localize_script(
				'ht-blocks-modules',
				'customVars',
				array(
					'mediaPlaceholder' => plugins_url( 'img/media-placeholder-500.png', __FILE__ )
				)
			);

			// Editor Only Styles.
			/*
			wp_enqueue_style(
				'ht-blocks-editor-style', // Handle.
				plugins_url( '/dist/css/block-editor-styles.css',  __FILE__  ), // Block editor style CSS.
				array( 'wp-editor' ), // Dependency to include the CSS after it.
				filemtime( __DIR__  . '/dist/css/block-editor-styles.css' ) // Version: File modification time.
			);
			*/
		}

	}
}

//run the plugin
if( class_exists( 'HT_Blocks_Modules' ) ){
	HT_Blocks_Modules::init();
}