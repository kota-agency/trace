<?php
/**
* Exits module
* Exit widget
*/

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class HT_KB_Exit_Widget extends WP_Widget {

    private $defaults;
    private $add_script;

    /*--------------------------------------------------*/
    /* Constructor
    /*--------------------------------------------------*/

    /**
    * Specifies the classname and description, instantiates the widget,
    * loads localization files, and includes necessary stylesheets and JavaScript.
    */
    public function __construct() {

        $this->add_script = false;

        //use this filter in your child theme to use non js link
        //add_filter('hkb_exits_seo_optimize', '__return_false');

        add_action( 'wp_enqueue_scripts', array( $this, 'wp_register_scripts'));
        add_action( 'wp_footer', array( $this, 'wp_print_scripts'));

        //update classname and description
        parent::__construct(
            'ht-kb-exit-widget',
            __( 'Knowledge Base Exit Point', 'ht-knowledge-base' ),
            array(
              'classname'   =>  'hkb_widget_exit',
              'description' =>  __( 'A widget for displaying an exit for the knowledge base (such as support ticket system)', 'ht-knowledge-base' )
            )
        );

        $this->defaults = array(
            'title' => __('Need Support?', 'ht-knowledge-base'),
            'text' => __('Can&#8217;t find the answer you&#8217;re looking for? Don&#8217;t worry we&#8217;re here to help!', 'ht-knowledge-base'),
            'btn' => __('Contact Support', 'ht-knowledge-base'),
            'url' => ''
          );

    } // end constructor

    /*--------------------------------------------------*/
    /* Widget API Functions
    /*--------------------------------------------------*/

    /**
    * Outputs the content of the widget.
    *
    * @param array args The array of form elements
    * @param array instance The current instance of the widget
    */
    public function widget( $args, $instance ) {
        global $ht_kb_exit_tools, $wp_query;

        //load scripts
        $this->add_script = true;

        extract( $args, EXTR_SKIP );

        $instance = wp_parse_args( $instance, $this->defaults );

        $default_url = ht_kb_exit_url_option();
        $new_window = ht_kb_exit_new_window_option() ? 'target="_blank"' : '';

        $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
        $text = isset( $instance['text'] ) ? esc_attr( $instance['text'] ) : '';
        $btn = isset( $instance['btn'] ) ? esc_attr( $instance['btn'] ) : '';
        $url = isset( $instance['url'] ) ? esc_attr( $instance['url'] ) : '';

        //check url not empty
        $url = empty($url) ? $default_url : $url;

        echo $before_widget;


        if ( $title )
            echo $before_title . $title . $after_title;

        //filter hkb_exits_nofollow_tag
        $hkb_exits_nofollow_tag = apply_filters('hkb_exits_nofollow_tag', 'rel="nofollow"');

        

        $exit_widget_href = apply_filters(HKB_EXITS_URL_FILTER_TAG, $url, 'widget');
        $exit_widget_href_data = '';

        //filter hkb_exits_seo_optimize
        $hkb_exits_seo_optimize = apply_filters('hkb_exits_seo_optimize', true);

        //js version (swap href for # link and use js to load the link)
        if($hkb_exits_seo_optimize){
            $exit_widget_href_data = $exit_widget_href;
            $exit_widget_href = '#';
        }

        $exit_widget = '<div class="hkb_widget_exit__content">' . $text . '</div>';

        $exit_widget .= '<a class="hkb_widget_exit__btn" data-ht-kb-exit-href="' . $exit_widget_href_data . '" href="' . $exit_widget_href  . '" ' . $new_window . ' ' . $hkb_exits_nofollow_tag . '>' . $btn . '</a>';

        //output widget
        echo $exit_widget;
        
        echo $after_widget;

    } // end widget

    /**
    * Outputs the content of the widget.
    *
    * @param array args The array of form elements
    * @param array instance The current instance of the widget
    */
    public function widget_old( $args, $instance ) {
        global $ht_kb_exit_tools, $wp_query;

        extract( $args, EXTR_SKIP );

        $instance = wp_parse_args( $instance, $this->defaults );

        $default_url = ht_kb_exit_url_option();
        $new_window = ht_kb_exit_new_window_option() ? 'target="_blank"' : '';

        $title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        $text = isset( $instance['text'] ) ? esc_attr( $instance['text'] ) : '';
        $btn = isset( $instance['btn'] ) ? esc_attr( $instance['btn'] ) : '';
        $url = isset( $instance['url'] ) ? esc_attr( $instance['url'] ) : '';

        //check url not empty
        $url = empty($url) ? $default_url : $url;

        echo $before_widget;


        if ( $title )
            echo $before_title . $title . $after_title;

        //filter hkb_exits_nofollow_tag
        $hkb_exits_nofollow_tag = apply_filters('hkb_exits_nofollow_tag', 'rel="nofollow"');

        $exit_widget = '<div class="hkb_widget_exit__content">' . $text . '</div>';
        $exit_widget .= '<a class="hkb_widget_exit__btn" href="' . apply_filters(HKB_EXITS_URL_FILTER_TAG, $url, 'widget') . '" ' . $new_window . ' ' . $hkb_exits_nofollow_tag . '>' . $btn . '</a>';

        //output widget
        echo $exit_widget;
        
        echo $after_widget;

    } // end widget

    /**
    * Processes the widget's options to be saved.
    *
    * @param array new_instance The previous instance of values before the update.
    * @param array old_instance The new instance of values to be generated via the update.
    */
    public function update( $new_instance, $old_instance ) {

        $instance = $old_instance;

        //update widget's old values with the new, incoming values
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['text'] = strip_tags( $new_instance['text'] );
        $instance['btn'] = strip_tags( $new_instance['btn'] );
        $instance['url'] = strip_tags( $new_instance['url'] );


        return $instance;

    } // end widget

    /**
    * Generates the administration form for the widget.
    *
    * @param array instance The array of keys and values for the widget.
    */
    public function form( $instance ) {

      $instance = wp_parse_args((array) $instance, $this->defaults);

      // Store the values of the widget in their own variable

      $title = strip_tags($instance['title']);
      $text = strip_tags($instance['text']);
      $btn = strip_tags($instance['btn']);
      $url = strip_tags($instance['url']);
      ?>
      <label for="<?php echo $this->get_field_id('title'); ?>">
        <?php _e( 'Title', 'ht-knowledge-base' ); ?>
        :
        <input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance["title"]); ?>" />
      </label>
      <label for="<?php echo $this->get_field_id('text'); ?>">
        <?php _e( 'Text', 'ht-knowledge-base' ); ?>
        :
        <input type="text" class="widefat" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>" type="text" value="<?php echo esc_attr($instance["text"]); ?>" />
      </label>
      <label for="<?php echo $this->get_field_id('btn'); ?>">
        <?php _e( 'Button Text', 'ht-knowledge-base' ); ?>
        :
        <input type="text" class="widefat" id="<?php echo $this->get_field_id('btn'); ?>" name="<?php echo $this->get_field_name('btn'); ?>" type="text" value="<?php echo esc_attr($instance["btn"]); ?>" />
      </label>
      <label for="<?php echo $this->get_field_id('url'); ?>">
        <?php _e( 'Link URL (leave blank for default Knowledge Base setting url)', 'ht-knowledge-base' ); ?>
        :
        <input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('url') ); ?>" name="<?php echo $this->get_field_name('url'); ?>" type="text" value="<?php echo esc_attr($instance["url"]); ?>" />
      </label>
      </p>
    <?php } // end form


    /**
    * Register scripts
    */
    function wp_register_scripts(){
        if(!apply_filters('hkb_exits_seo_optimize', true))
            return;

        if(SCRIPT_DEBUG){
            $ht_exits_frontend_js_src = 'exits/js/ht-exits-frontend.js';
            wp_register_script( 'ht-exits-frontend', plugins_url( $ht_exits_frontend_js_src, HT_KB_MAIN_PLUGIN_FILE  ), array('jquery') , HT_KB_VERSION_NUMBER, true );
        } else {
            //load the combined ht-kb-frontend.min.js script
            wp_register_script('ht-kb-frontend-scripts', plugins_url( 'dist/ht-kb-frontend.min.js' , HT_KB_MAIN_PLUGIN_FILE ), array( 'jquery' ), HT_KB_VERSION_NUMBER, true);
        }
    }

    /**
    * Selectively print scripts (in footer)
    */
    function wp_print_scripts(){
        global $ht_kb_frontend_scripts_loaded;
        
        if(!apply_filters('hkb_exits_seo_optimize', true))
            return;

        if ( ! $this->add_script )
            return;

        if(SCRIPT_DEBUG){
            wp_print_scripts( 'ht-exits-frontend' );
        } else {
            if(!$ht_kb_frontend_scripts_loaded){
                wp_print_scripts('ht-kb-frontend-scripts');
                $ht_kb_frontend_scripts_loaded = true;
            }
        } 
    }





} // end class

//call widget
//deprecated PHP
//add_action( 'widgets_init', create_function( '', 'register_widget("HT_KB_Exit_Widget");' ) );

add_action( 'widgets_init', function(){
    register_widget( 'HT_KB_Exit_Widget' );
});