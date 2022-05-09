<?php
/**
 * Search extensions (beta)
 * Allows article tags and article categories to be searched
 */

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if (!class_exists('HT_Knowledge_Base_Search_Extensions')) {

    class HT_Knowledge_Base_Search_Extensions {

        private $is_rest_ht_kb_search_request;
        private $rest_ht_kb_search_request_term;

        //Constructor
        function __construct(){  
            global $ht_knowledge_base_settings;  

            $this->is_rest_ht_kb_search_request = false;
            $this->rest_ht_kb_search_request_term = '';

            //search filter flag for the rest api
            add_filter('rest_ht_kb_query', array( $this, 'hkb_rest_flag' ), 10, 2 );          
            
            //WPML compat fix: the post_where hook needs to be after WPML post_where hook (priority must be > 10)
            
            add_filter( 'posts_where', array( $this, 'hkb_search_where' ), 15, 2 );
            add_filter( 'posts_join',  array( $this, 'hkb_search_join' ) );
            add_filter( 'posts_groupby',  array( $this, 'hkb_search_groupby' ) );  
            add_filter( 'posts_orderby',  array( $this, 'hkb_search_orderby' ) );       

    
        }

        function hkb_rest_flag($args, $request){

            $s = ( array_key_exists('s', $args) ) ? $args['s']  : '';
            //2.23.0 - extend to include the 'search' argument
            $s = ( array_key_exists('search', $args) ) ? $args['search']  : $s;

            if(!empty($s)){
                $this->is_rest_ht_kb_search_request = true;  
                $this->rest_ht_kb_search_request_term = esc_attr($s);
            }
            return $args;           

        }

        function hkb_search_where($where, $query){
            global $wpdb;
            if ( ( $this->is_rest_ht_kb_search_request || ( is_search() && ht_kb_is_ht_kb_search() ) ) && apply_filters('hkb_search_terms', true) ){
                if($this->is_rest_ht_kb_search_request){
                    $search_term = $this->rest_ht_kb_search_request_term;
                } else {
                    $search_term = get_search_query();    
                }                
                //var_dump($search_term);
                //remove this filter
                remove_filter( 'posts_where', array( $this, 'hkb_search_where' ), 15 );
                //supplementary where clause
                $supp_where = " 1=1 ";
                //reapply filters
                $supp_where = apply_filters_ref_array( 'posts_where', array( $supp_where, &$query ) );
                $where .= " OR (terms_table_ht.name LIKE '%" . $search_term . "%' AND {$wpdb->posts}.post_status = 'publish' AND " . $supp_where . " ) ";
            }
            
            return $where;
        }

        function hkb_search_join($join){
            global $wpdb;

            if ( ( $this->is_rest_ht_kb_search_request || ( is_search() && ht_kb_is_ht_kb_search() ) ) && apply_filters('hkb_search_terms', true) ){

                //build terms to search clause
                $terms_to_search = " 1!=1 ";
                if(apply_filters('hkb_search_ht_kb_tag', true)){
                    $terms_to_search .= " OR tax_term_table_ht.taxonomy = 'ht_kb_tag' ";
                }
                if(apply_filters('hkb_search_ht_kb_category', true)){
                    $terms_to_search .= " OR tax_term_table_ht.taxonomy = 'ht_kb_category' ";
                }

                $join .= "  LEFT JOIN {$wpdb->term_relationships} term_relationships_table_ht 
                        ON {$wpdb->posts}.ID = term_relationships_table_ht.object_id 
                        LEFT JOIN {$wpdb->term_taxonomy} tax_term_table_ht 
                        ON tax_term_table_ht.term_taxonomy_id=term_relationships_table_ht.term_taxonomy_id  
                        LEFT JOIN {$wpdb->terms} terms_table_ht ON terms_table_ht.term_id = tax_term_table_ht.term_id 
                        AND ( " . $terms_to_search . " ) ";
            }
            
            return $join;
        }

        function hkb_search_groupby($groupby){
            global $wpdb;

            //filter can be removed using hkb_search_terms filter
            if(!apply_filters('hkb_search_terms', true)){
                return $groupby;
            }

            //group by post ID
            $groupby_id = "{$wpdb->posts}.ID";

            //if not a ht_kb_is_ht_kb_search(), do nothing
            if( !$this->is_rest_ht_kb_search_request && !ht_kb_is_ht_kb_search() ){
                return $groupby;
            }

            //if not a search or already groupedby post ID
            if( ( !$this->is_rest_ht_kb_search_request && !is_search() ) || ( !$this->is_rest_ht_kb_search_request && strpos($groupby, $groupby_id) !== false ) ){
                return $groupby;
            } 

            //groupby empty
            if(!strlen(trim($groupby))){
                return $groupby_id;
            } 

            //else append additional groupby clause
            return $groupby. ", " .$groupby_id;
        }

        function hkb_search_orderby($orderby){
            global $wpdb;
            if(apply_filters('hkb_search_without_date', true)){
                if( $this->is_rest_ht_kb_search_request ){
                    $orderby = " {$wpdb->posts}.post_title LIKE '%" . $this->rest_ht_kb_search_request_term . "%' DESC ";
                } elseif (is_search() && ht_kb_is_ht_kb_search()) {
                    $search_term = get_search_query();   
                    $orderby = " {$wpdb->posts}.post_title LIKE '%" . $search_term . "%' DESC ";
                }
            }
            if( $this->is_rest_ht_kb_search_request ){
                $orderby = " {$wpdb->posts}.post_title LIKE '%" . $this->rest_ht_kb_search_request_term . "%' DESC, " . $orderby;
            }
            return $orderby;
        }

     

    }//end class

}

//run the module 
if (class_exists('HT_Knowledge_Base_Search_Extensions')) {
    new HT_Knowledge_Base_Search_Extensions();
}