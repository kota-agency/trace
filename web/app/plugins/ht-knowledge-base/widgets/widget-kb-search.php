<?php
/**
* HKB Widgets
* KB Search widget
*/

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class HT_KB_Search_Widget extends WP_Widget {

    private $defaults;
    private $in_use;

    /**
    * Widget Constructor
    * Specifies the classname and description, instantiates the widget,
    * loads localization files, and includes necessary stylesheets and JS where necessary
    */
    public function __construct() {

        //set classname and description
        parent::__construct(
            'ht-kb-search-widget',
            __( 'Knowledge Base Search', 'ht-knowledge-base' ),
            array(
                'classname'   =>  'hkb_widget_search',
                'description' =>  __( 'A widget for displaying Knowledge Base Search', 'ht-knowledge-base' )
            )
        );

        $default_widget_title = __('Knowledge Base Search', 'ht-knowledge-base');

        //default values for variables
        $this->defaults = array(
            'title' => $default_widget_title,
        );


    } // end constructor

    //Widget API Functions

    /**
    * Outputs the content of the widget.
    * @param array args The array of form elements
    * @param array instance The current instance of the widget
    */
    public function widget( $args, $instance ) {

          extract( $args, EXTR_SKIP );

          $instance = wp_parse_args( $instance, $this->defaults );

          echo $before_widget;

          $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );


          if ( $title )
            echo $before_title . $title . $after_title;
        ?>
          

          <form class="hkb_widget_search__form" method="get" action="<?php echo home_url( '/' ); ?>">
            <label class="hkb-screen-reader-text" for="s"><?php _e( 'Search For', 'ht-knowledge-base' ); ?></label>
            <input class="hkb_widget_search__field" type="text" value="<?php echo get_search_query(); ?>" placeholder="<?php echo hkb_get_knowledgebase_searchbox_placeholder_text(); ?>" name="s" autocomplete="off">
            <input type="hidden" name="ht-kb-search" value="1" />
          </form>

        <?php echo $after_widget;

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

        return $instance;

    } // end update

    /**
    * Generates the administration form for the widget.
    * @param array instance The array of keys and values for the widget.
    */
    public function form( $instance ) {
        
        $instance = wp_parse_args((array) $instance, $this->defaults);

        $title = strip_tags($instance['title']);

        ?>
        <label for="<?php echo $this->get_field_id("title"); ?>">
            <?php _e( 'Title', 'ht-knowledge-base' ); ?>
                    :
            <input type="text" class="widefat" id="<?php echo $this->get_field_id("title"); ?>" name="<?php echo $this->get_field_name("title"); ?>" type="text" value="<?php echo esc_attr($instance["title"]); ?>" />
        </label>
        </p>
        <?php
    }//end function

} // end class

//Action Hook
//deprecated PHP
//add_action( 'widgets_init', create_function( '', 'register_widget("HT_KB_Search_Widget");' ) );

add_action( 'widgets_init', function(){
    register_widget( 'HT_KB_Search_Widget' );
});