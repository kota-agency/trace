<?php
/**
* HKB Widgets
* TOC widget
*/

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class HT_KB_Table_Of_Contents extends WP_Widget {

    private $defaults;

    /**
    * Widget Constructor
    * Specifies the classname and description, instantiates the widget,
    * loads localization files, and includes necessary stylesheets and JS where necessary
    */
    public function __construct() {

        //set classname and description
        parent::__construct(
            'ht-kb-toc-widget',
            __( 'Knowledge Base Table of Contents', 'ht-knowledge-base' ),
            array(
              'classname'   =>  'hkb_widget_toc',
              'description' =>  __( 'A widget for displaying a Table of Contents on Knowledge Base ', 'ht-knowledge-base' )
            )
        );

        $default_widget_title = __('Contents', 'ht-knowledge-base');
        $default_scrollspy = 1;

        $this->defaults = array(
            'title' => $default_widget_title,
            'scrollspy' => $default_scrollspy,
          );

    } // end constructor

    //Widget API Functions

    /**
    * Outputs the content of the widget.
    * @param array args The array of form elements
    * @param array instance The current instance of the widget
    */
    public function widget( $args, $instance ) {
        global $ht_kb_toc_tools, $wp_query;

        if( ! is_singular() )
            return;

        if ( ! isset( $wp_query ) ) {
            return;
        }


        if( is_a($ht_kb_toc_tools, 'HT_KB_TOC_Tools') ){
            if( $ht_kb_toc_tools->ht_block_toc_detected && apply_filters('ht_kb_toc_disable_on_ht_block_toc', true ) ){
                //early exit if ht-block-toc detected
                return;    
            }                
        }

        //@todo - may need to detect Yoast SEO block and disable here if required?

        extract( $args, EXTR_SKIP );

        $instance = wp_parse_args( $instance, $this->defaults );

        //$post = get_post( $wp_query->post->ID );
        $post = get_post();

        if( is_preview() ){
            //get the post revisions
            $post_revisions = ( wp_get_post_revisions( $post ) );

            if ( !empty( $post_revisions ) ) {
                //get the latest revision - this should be the current preview
                $post = current( $post_revisions );
            }
        }

        $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );   

        $scrollspy = apply_filters( 'ht_kb_toc_scrollspy', empty( $instance['scollspy'] ) ? 1 : $instance['scollspy'] , $instance, $this->id_base );  

        
        if($scrollspy){
            //only the minified version is availalbe see js/hkb-toc-scrollspy.js to compile from source
            $hkb_toc_scrollspy_src = 'js/hkb-toc-scrollspy.min.js';
            wp_enqueue_script( 'hkb-toc-scrollspy', plugins_url( $hkb_toc_scrollspy_src, dirname(__FILE__) ), false, HT_KB_VERSION_NUMBER, true );   
            wp_localize_script( 'hkb-toc-scrollspy', 'hkbTOCSettings', array( 
                'htTOCWidgetScollspyViewOffset' => apply_filters('hkb_toc_widget_scrollspy_view_offset', 0),
             ) );
        }
        

        if(is_a($ht_kb_toc_tools, 'HT_KB_TOC_Tools')){

            //extract headings
            $headings = $ht_kb_toc_tools->ht_kb_toc_extract_headings( do_shortcode( $post->post_content ), true ); 

            //don't output widget if no headings are in content
            if(empty($headings))
                return;

            echo $before_widget;

            if ( $title )
                echo $before_title . $title . $after_title;


            ?>
            <nav id="navtoc" role="navigation">

                

            <?php
            //display items
            $ht_kb_toc_tools->ht_kb_display_items();
            ?>

            </nav>

            <?php
        }

        echo $after_widget;

    } // end widget

    /**
    * Processes the widget's options to be saved.
    * @param array new_instance The previous instance of values before the update.
    * @param array old_instance The new instance of values to be generated via the update.
    */
    public function update( $new_instance, $old_instance ) {

        $instance = $old_instance;

        //update widget's old values with the new, incoming values
        $instance['title'] = isset( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : $this->defaults['title'];
        $instance['scrollspy'] = isset( $new_instance['scrollspy'] ) && $new_instance['scrollspy'] ? 1 : 0;
        //$instance['category'] = $new_instance['category'];
        //$instance['asc_sort_order'] = $new_instance['asc_sort_order'] ? 1 : 0;

        return $instance;

    } // end widget

    /**
    * Generates the administration form for the widget.
    * @param array instance The array of keys and values for the widget.
    */
    public function form( $instance ) {

      $instance = wp_parse_args((array) $instance, $this->defaults);

      // Store the values of the widget in their own variable

      $title = strip_tags($instance['title']);
      $scrollspy = $instance['scrollspy'] ? 1 : 0;
      ?>
      <label for="<?php echo $this->get_field_id("title"); ?>">
            <?php _e( 'Title', 'ht-knowledge-base' ); ?>
            :
            <input type="text" class="widefat" id="<?php echo $this->get_field_id("title"); ?>" name="<?php echo $this->get_field_name("title"); ?>" type="text" value="<?php echo esc_attr($instance["title"]); ?>" />
        </label>
        <label for="<?php echo $this->get_field_id("scrollspy"); ?>">
            <input type="checkbox" class="widefat" id="<?php echo $this->get_field_id("scrollspy"); ?>" name="<?php echo $this->get_field_name("scrollspy"); ?>" type="checkbox" <?php checked( 1, $scrollspy ) ?> />
            <?php _e( 'Enable Scrollspy', 'ht-knowledge-base' ); ?>
        </label>
      </p>
    <?php } // end form



} // end class

//Action Hook
//deprecated PHP
//add_action( 'widgets_init', create_function( '', 'register_widget("HT_KB_Table_Of_Contents");' ) );

add_action( 'widgets_init', function(){
    register_widget( 'HT_KB_Table_Of_Contents' );
});


//TOC Tool Functions
if(!class_exists('HT_KB_TOC_Tools')){
    class HT_KB_TOC_Tools {

        private $anchors;
        private $items;
        private $current_level;
        private $toc_class;
        private $headings_extracted;
        private $done;

        private $find;
        private $replace;

        //widget will not display if wp-block-ht-block-toc
        public $ht_block_toc_detected;

        //constructor
        function __construct(){
            add_filter( 'the_content', array($this, 'ht_kb_toc_content_filter'), 100 ); 
        }

        /**
        * Content filter to extract headings and add IDs to the headings in the content
        */
        function ht_kb_toc_content_filter( $content ){

            //use this filter to disable this functionality if there are conflicts
            if( apply_filters('ht_kb_toc_content_filter_disabled', false ) ){
                //early exit if feature disabled
                return $content;    
            }

            //only apply to ht_kb, , it's possible to add post and page using the ht_kb_toc_content_filter_post_types below
            $ht_kb_toc_posts_types = array( 'ht_kb' );
            
            //apply ht_kb_toc_content_filter_post_types filter
            $ht_kb_toc_content_filter_post_types = apply_filters('ht_kb_toc_content_filter_post_types', $ht_kb_toc_posts_types);

            if( strpos( $content, 'wp-block-ht-block-toc' ) ){
                $this->ht_block_toc_detected = true;
                if( apply_filters('ht_kb_toc_disable_on_ht_block_toc', true ) ){
                    //early exit if ht-block-toc detected
                    return $content;    
                }                
            }
            
            if(is_singular($ht_kb_toc_content_filter_post_types)){
                //replace in content 
                $content = $this->mb_find_replace( $content );
            }       

            return $content;

        }

        /**
        * Extract headings using implied pass-by-reference for find and replace variables
        */
        function ht_kb_toc_extract_headings( $content, $widget=false ){

            //only do this once if already run before widget
            if( isset( $this->headings_extracted ) && $widget ){
                return $this->items;
            }
            
            //init variables
            $this->anchors = array();
            $this->find = array();
            $this->replace = array();

            $items = '';
            $this->current_level = 0;

            //header extract start level
            $h_start_level = apply_filters( 'ht_kb_toc_extract_headings_h_start_level', 1 );
            //warning - intval will be 0 if not castable integer
            $h_start_level_int = intval( $h_start_level );
            //header extract end level
            $h_end_level = apply_filters( 'ht_kb_toc_extract_headings_h_end_level', 6 );
            //warning - intval will be 0 if not castable integer
            $h_end_level_int = intval( $h_end_level ); 
            $header_extract_regex = apply_filters('ht_kb_toc_extract_headings_regex', '/(<h([' . $h_start_level_int . '-' . $h_end_level_int . ']{1})[^>]*)>.*<\/h\2>/msuU' );
            if ( preg_match_all($header_extract_regex, $content, $matches, PREG_SET_ORDER) ) {
                for ($i = 0; $i < count($matches); $i++) {
                    $complete_heading_tag = $matches[$i][0];
                    $opening_tag = $matches[$i][1];
                    $closing_tag = $matches[$i][2];
                    $opening_tag_id_stripped = preg_replace( '#\s(id)="[^"]+"#', '', $opening_tag );
                    // get anchor and add to find and replace arrays
                    $anchor = $this->ht_kb_toc_generate_anchor( $complete_heading_tag );
                    $this->find[] = $complete_heading_tag;
                    $this->replace[] = str_replace(
                                    array(
                                        $opening_tag,                // start h tag
                                        '</h' . $closing_tag . '>'   // end h tag
                                    ),
                                    array(
                                        $opening_tag_id_stripped . ' id="' . $anchor . '" ',
                                        '</h' . $closing_tag . '>'
                                    ),
                                    $complete_heading_tag
                                );

                    if ( false ) {
                        //flat list - currently unused
                        $items .= '<li><a href="#' . $anchor . '">';
                        //$items .= count($replace) ;
                        $items .= strip_tags($complete_heading_tag) . '</a></li>';
                    } else {
                        $items .= $this->ht_kb_build_hierachy( $matches[$i], $anchor );
                    }
                }
            }
            //set items
            $this->items = $items;

            //set headings extracted
            $this->headings_extracted = true;

            //return items
            return $items;
        }

        /**
        * Display the items in the list
        */
        public function ht_kb_display_items(){
            echo '<ol class="nav">';
            echo balanceTags( $this->items, true );
            echo '</ol><!-- /ht-kb-toc-widget -->';
        }

        /**
        * Heirarchy TOC builder
        */
        public function ht_kb_build_hierachy($match, $anchor, $list_style='ol'){
            $new_level = $match[2];
            if(0==$this->current_level){
                //init
                $this->current_level = $new_level;
                $this->toc_class = apply_filters( 'ht_kb_toc_first_element_toc_class', 'active' );
            }
            $items = '';
            if($this->current_level==$new_level){
                $items .= '<!-- adding li -->';
                //add li
                $items .= '<li class="'. $this->toc_class .'"><a href="#' . $anchor . '">';
                $items .= strip_tags($match[0]) . '</a>';
            } elseif ($this->current_level>$new_level) {
                $items .= '<!-- removing level -->';
                //remove levels
                while($this->current_level>$new_level){
                    $items .= '</' . $list_style . '>';
                    $this->current_level = $this->current_level - 1;
                }                
                $items .= '<li><a href="#' . $anchor . '">';
                $items .= strip_tags($match[0]) . '</a>';
            } elseif($new_level>$this->current_level){
                $items .= '<!-- adding level -->';
                $items .= '<' . $list_style . '>';
                $items .= '<li><a href="#' . $anchor . '">';
                $items .= strip_tags($match[0]) . '</a>';
            }
            $this->current_level = $new_level;
            $this->toc_class = '';
            return $items;
        }

        /**
        * Anchor generator
        */
        private function ht_kb_toc_generate_anchor( $h_content = '' ){
            $anchor = '';
            if(empty($h_content)){
                //don't do anything if tag content empty
            } else {
                //generate anchor using santize text field 
                //$anchor = sanitize_text_field($h_content);

                //use the sanitize title function for wider character set support
                //may be able to remove remaining santizations
                $anchor = sanitize_title($h_content);

                //convert accents
                $anchor = remove_accents( $anchor );
                
                // replace newlines with spaces (eg when headings are split over multiple lines)
                $anchor = str_replace( array("\r", "\n", "\n\r", "\r\n"), ' ', $anchor );
                
                //remove &amp;
                $anchor = str_replace( '&amp;', '', $anchor );
                
                //remove non alphanumeric chars
                $anchor = preg_replace( '/[^a-zA-Z0-9 \-_]*/', '', $anchor );
                
                // convert spaces to underscores
                $anchor = str_replace(
                    array('  ', ' '),
                    '_',
                    $anchor
                );
                
                //remove trailing - and _
                $anchor = rtrim( $anchor, '-_' );
                
                //lowercase
                $anchor = strtolower($anchor);

                if(empty($anchor)){
                    //append fragment
                    $anchor .= 'toc_anchor_';
                    $h_content .= 'toc_anchor_';
                }
                
                //hyphenate where necessary
                $anchor = str_replace('_', '-', $anchor);
                $anchor = str_replace('--', '-', $anchor); 
                
                //check not already in array of anchors
                if(array_key_exists($anchor, $this->anchors)){
                    //increase anchor
                    $this->anchors[$anchor]++;
                    //append index to anchor tag
                    $anchor = $anchor . '-' . $this->anchors[$anchor];
                }else{
                    //add new anchor to list of anchors
                    $this->anchors[$anchor] = 1;
                }
                
            }
            return $anchor;
        }

        /**
        * Multibyte safe find and replace
        */
        private function mb_find_replace(  &$string = '' ){

            //only process this filter once
            if( !in_the_loop() || isset( $this->done ) ){
                return $string;
            }

            //extract headings
            $this->ht_kb_toc_extract_headings( $string, false );

            if ( is_array($this->find) && is_array($this->replace) && $string ) {
                // check if multibyte strings are supported
                if ( function_exists( 'mb_strpos' ) ) {
                    for ($i = 0; $i < count($this->find); $i++) {
                        $string = 
                            mb_substr( $string, 0, mb_strpos($string, $this->find[$i]) ) . 
                            $this->replace[$i] . 
                            mb_substr( $string, mb_strpos($string, $this->find[$i]) + mb_strlen($this->find[$i]) )  
                        ;
                    }
                } else {
                    for ($i = 0; $i < count($this->find); $i++) {
                        $string = substr_replace(
                            $string,
                            $this->replace[$i],
                            strpos($string, $this->find[$i]),
                            strlen($this->find[$i])
                        );
                    }
                }
            }  

            //set done state 
            $this->done = true;

            //return content          
            return $string;
        } 

    }
}

if(class_exists('HT_KB_TOC_Tools')){
    //run the tool
    global $ht_kb_toc_tools;

    $ht_kb_toc_tools = new HT_KB_TOC_Tools();
}