<?php
/**
* Knowledge Base API
*/

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'HT_Knowledge_Base_API' ) ){
    class HT_Knowledge_Base_API {

        //constructor
        function __construct() {
            //allow orderby 
            add_filter( 'rest_ht_kb_collection_params', array( $this, 'ht_kb_add_rest_orderby_params' ), 10, 1 );
            //orderyby                     
            add_filter( 'rest_ht_kb_query', array( $this, 'ht_kb_rest_filter_query' ), 10, 2 );
            //kb category
            add_action( 'rest_api_init', array( $this, 'ht_kb_category_rest_api_add_meta' ) );
        }

        /** 
         * allow the orderby article_views parameter
         */        
        function ht_kb_add_rest_orderby_params( $params ) {
            $params['orderby']['enum'][] = 'article_views';
            return $params;
        }

        function ht_kb_rest_filter_query($query_vars, $request) {
            
            $orderby = $request->get_param('orderby');
            if (isset($orderby) && 'article_views' == $orderby ) {
                $query_vars['meta_key'] = HT_KB_POST_VIEW_COUNT_KEY;
                $query_vars['orderby'] = 'meta_value_num';
            }
            return $query_vars;
        }

        /**
         * extend the wp-api to add display content field
         */
        function ht_kb_category_rest_api_add_meta() {

            $ht_kb_category_meta_fields = array (
                                    'term_order',
                                    'default_preview',
                                    'meta_image',
                                    'meta_image',
                                    'attachment_thumb',
                                    'thumbnail_url',
                                    'meta_svg',
                                    'meta_svg_color',
                                    'custom_link',
                                    'meta_color',
                                    'restrict_access',
                                );

            $ht_kb_category_meta_fields = apply_filters( 'ht_kb_category_meta_fields', $ht_kb_category_meta_fields );

            foreach ($ht_kb_category_meta_fields as $key => $field) {
                register_rest_field(
                    'ht_kb_category',
                    $field,
                    array(
                        'get_callback'    => array( $this, 'ht_kb_category_get_meta' ),
                        'update_callback' => null,
                        'schema'          => null,
                    )
                );
            }


            //category thumbnail
            register_rest_field(
                    'ht_kb_category',
                    'category_thumbnail',
                    array(
                        'get_callback'    => array( $this, 'ht_kb_category_get_icon_thumb' ),
                        'update_callback' => null,
                        'schema'          => null,
                    )
                );

            
        }

        function ht_kb_category_get_meta( $term, $field_name, $request ){
            $meta_value =  get_term_meta( $term['id'], $field_name, true );
            return apply_filters( 'ht_kb_category_get_meta_' . $field_name, $meta_value ); 
        }

        function ht_kb_category_get_icon_thumb( $term, $field_name, $request ){
            ob_start();
            hkb_category_thumb_img($term['id'] );
            $thumb = ob_get_clean();
            return $thumb;
        }

        

    }
}

//run the module
if( class_exists( 'HT_Knowledge_Base_API' ) ){
    new HT_Knowledge_Base_API();
}