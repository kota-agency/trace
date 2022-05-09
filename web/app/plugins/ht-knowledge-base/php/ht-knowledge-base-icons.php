<?php
/**
* Icons Functionality
*/

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


if (!class_exists('HT_Knowledge_Base_Icons')) {

	class HT_Knowledge_Base_Icons {

		//Constructor
		function __construct() {

			//print media templates
			add_action( 'admin_footer', array ($this, 'ht_kb_icons_print_media_templates' ) );

		 }

		/**
		* Display icons
		*/
		function ht_kb_icons_list_all_icons(){
			$directories = array();
			$file_names = array();
			$directories[dirname(HT_KB_MAIN_PLUGIN_FILE) . '/hkb-icons/'] = plugins_url( '/hkb-icons/' , HT_KB_MAIN_PLUGIN_FILE );
			//hkb_icon_directories filter accepts an array with key->value as path_base->url_base
			$directories = apply_filters('hkb_icons_directories', $directories);
			foreach ($directories as $path_base => $url_base) {
				foreach (new DirectoryIterator($path_base) as $file) {
					if ($file->isFile()) {
						$file_name = $file->getFilename();
						//only add unique file names
						if(!in_array($file_name, $file_names)){
							$file_uri = $url_base . $file_name;
							echo "<li class='ht-kb-icon'>";
							include_once( $path_base . $file_name );
							//echo "<img class='hkb-icon' src='$file_uri' alt='$file_name' height='100px' width='100px' />";
							echo "</li>";
							//add to file_names register
							$file_names[] = $file_name;
						}
					}
				}
			}
		}

		/**
		* Print the templates
		*/
		function ht_kb_icons_print_media_templates(){
			$screen = get_current_screen();
            if(  $screen->id != 'edit-ht_kb_category' ) {
            	//early exit
                return;           
            } 
			?>
				<script type="text/html" id="tmpl-hkb-icons">
					<div class="ht-kb-icon-picker">
			
						<h2><?php _e('Select an Icon', 'ht-knowledge-base'); ?></h2>

						<form action="#">
							<input type="text" name="category" class="icon-color" value="" />
						</form>

						<ul class="ht-kb-icon-list"> 
							<?php $this->ht_kb_icons_list_all_icons(); ?>
						</ul>								

					</div>
				</script>

			<?php
		}

	} //end class
}//end class exists

//run the module
if(class_exists('HT_Knowledge_Base_Icons')){
	new HT_Knowledge_Base_Icons();
}