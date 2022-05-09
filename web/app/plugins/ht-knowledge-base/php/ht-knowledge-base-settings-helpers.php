<?php
/**
* Settings helper functions
*/

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if(!function_exists('hkb_show_knowledgebase_search')){
    /**
    * Get the Knowledge Base search display option
    * Filterable - hkb_show_knowledgebase_search
    * @deprecated - As of 3.0 the knowledge base search is now always displayed, override with searchbox template 
    * @param (String) $location The location of where to display (currently unused)
    * @return (Bool) The option
    */
    function hkb_show_knowledgebase_search($location=null){
        global $ht_knowledge_base_settings;
        $hkb_show_knowledgebase_search = false;
        if ( isset( $ht_knowledge_base_settings['display-live-search'] ) ){
            $hkb_show_knowledgebase_search = $ht_knowledge_base_settings['display-live-search'];
        } else {
            $hkb_show_knowledgebase_search = false;
        }
        return apply_filters('hkb_show_knowledgebase_search', $hkb_show_knowledgebase_search);
    }
}

if(!function_exists('hkb_archive_columns')){
    /**
    * Number of archive columns to display
    * Filterable - hkb_archive_columns
    * @return (Int) The option
    */
    function hkb_archive_columns(){
        global $ht_knowledge_base_settings;
        $hkb_archive_columns = 2;
        if ( isset( $ht_knowledge_base_settings['archive-columns'] ) ){
            $hkb_archive_columns = $ht_knowledge_base_settings['archive-columns'];
        } else {
            $hkb_archive_columns = 2;
        }
        return apply_filters('hkb_archive_columns', $hkb_archive_columns);
    }
}

if(!function_exists('hkb_archive_sortby')){
    /**
    * Sort articles by
    * Filterable - hkb_archive_sortby
    * @return (Int) The option
    */
    function hkb_archive_sortby(){
        global $ht_knowledge_base_settings;
        $hkb_archive_sortby = 2;
        if ( isset( $ht_knowledge_base_settings['sort-by'] ) ){
            $hkb_archive_sortby = $ht_knowledge_base_settings['sort-by'];
        } else {
            $hkb_archive_sortby = 'date';
        }
        return apply_filters('hkb_archive_sortby', $hkb_archive_sortby);
    }
}

if(!function_exists('hkb_archive_sortorder')){
    /**
    * Sort order
    * Filterable - hkb_archive_sortorder
    * @return (Int) The option
    */
    function hkb_archive_sortorder(){
        global $ht_knowledge_base_settings;
        $hkb_archive_sortorder = 2;
        if ( isset( $ht_knowledge_base_settings['sort-order'] ) ){
            $hkb_archive_sortorder = $ht_knowledge_base_settings['sort-order'];
        } else {
            $hkb_archive_sortorder = 'asc';
        }
        return apply_filters('hkb_archive_sortorder', $hkb_archive_sortorder);
    }
}

if(!function_exists('hkb_archive_columns_string')){
    /**
    * Number of archive columns to display (as a string)
    * Filterable - hkb_archive_columns_string
    * @return (String) The option
    */
    function hkb_archive_columns_string(){
        // Set column variable to class needed for CSS
        $hkb_archive_columns_string = hkb_archive_columns();
        if ($hkb_archive_columns_string == '1') :
            $hkb_archive_columns_string = 'one';
        elseif ($hkb_archive_columns_string == '2') :
            $hkb_archive_columns_string = 'two';
        elseif ($hkb_archive_columns_string == '3') :
            $hkb_archive_columns_string = 'three';
        elseif ($hkb_archive_columns_string == '4') :
            $hkb_archive_columns_string = 'four';
        else :
            $hkb_archive_columns_string = 'two';
        endif; 

        return apply_filters('hkb_archive_columns_string', $hkb_archive_columns_string);
    }
}

if(!function_exists('hkb_archive_display_subcategories')){
    /**
    * Get the Knowledge Base subcategory count display option
    * Filterable - hkb_archive_display_subcategories
    * @deprecated by 3.0 (no alternative available)
    * @return (Bool) The option
    */
    function hkb_archive_display_subcategories(){   
        global $ht_knowledge_base_settings;
        $hkb_archive_display_subcategories = false;
        if ( isset( $ht_knowledge_base_settings['display-sub-cats'] ) ){
            $hkb_archive_display_subcategories = $ht_knowledge_base_settings['display-sub-cats'];
        } else {
            $hkb_archive_display_subcategories = false;
        }

        return apply_filters('hkb_archive_display_subcategories', $hkb_archive_display_subcategories);
    }
}

if(!function_exists('hkb_archive_subcategory_depth')){
    /**
    * Get the Knowledge Base subcategory depth option
    * Filterable - hkb_archive_subcategory_depth
    * @deprecated by 3.0 (no alternative available)
    * @return (Int) The option
    */
    function hkb_archive_subcategory_depth(){   
        global $ht_knowledge_base_settings;
        $hkb_archive_subcategory_depth = false;
        if ( isset( $ht_knowledge_base_settings['sub-cat-depth'] ) ){
            $hkb_archive_subcategory_depth = $ht_knowledge_base_settings['sub-cat-depth'];
        } else {
            $hkb_archive_subcategory_depth = 1;
        }

        return apply_filters('hkb_archive_subcategory_depth', $hkb_archive_subcategory_depth);
    }
}

if(!function_exists('hkb_archive_display_subcategory_count')){
    /**
    * Get the Knowledge Base subcategory count display option
    * Filterable - hkb_archive_display_subcategory_count
    * @return (Bool) The option
    */
    function hkb_archive_display_subcategory_count(){
        global $ht_knowledge_base_settings;  
        $hkb_archive_display_subcategory_count = false;
        if ( isset( $ht_knowledge_base_settings['display-article-count'] ) ){
            $hkb_archive_display_subcategory_count = $ht_knowledge_base_settings['display-article-count'];
        } else {
            $hkb_archive_display_subcategory_count = false;
        }


        return apply_filters('hkb_archive_display_subcategory_count', $hkb_archive_display_subcategory_count);
    }
}

if(!function_exists('hkb_archive_display_subcategory_articles')){
    /**
    * Get the Knowledge Base subcategory list display option
    * Filterable - hkb_archive_display_subcategory_articles
    * @deprecated by 3.0 (no alternative available)
    * @return (Bool) The option
    */
    function hkb_archive_display_subcategory_articles(){    
        global $ht_knowledge_base_settings;
        $hkb_archive_display_subcategory_articles = false;
        if ( isset( $ht_knowledge_base_settings['display-sub-cat-articles'] ) ){
            $hkb_archive_display_subcategory_articles = $ht_knowledge_base_settings['display-sub-cat-articles'];
        } else {
            $hkb_archive_display_subcategory_articles = false;
        }

        return apply_filters('hkb_archive_display_subcategory_articles', $hkb_archive_display_subcategory_articles);
    }
}

if(!function_exists('hkb_archive_hide_empty_categories')){
    /**
    * Get the Knowledge Base hide empty categories option
    * Filterable - hkb_archive_hide_empty_categories
    * @return (Bool) The option
    */
    function hkb_archive_hide_empty_categories(){   
        global $ht_knowledge_base_settings;
        $hkb_archive_hide_empty_categories = false;
        if ( isset( $ht_knowledge_base_settings['hide-empty-cats'] ) ){
            $hkb_archive_hide_empty_categories = $ht_knowledge_base_settings['hide-empty-cats'];
        } else {
            $hkb_archive_hide_empty_categories = false;
        }

        return apply_filters('hkb_archive_hide_empty_categories', $hkb_archive_hide_empty_categories);
    }
}

if(!function_exists('hkb_archive_hide_uncategorized_articles')){
    /**
    * Get the Knowledge Base hide uncategorized articles option
    * Filterable - hkb_archive_hide_uncategorized_articles
    * @return (Bool) The option
    */
    function hkb_archive_hide_uncategorized_articles(){   
        global $ht_knowledge_base_settings;
        $hkb_archive_hide_uncategorized_articles = false;
        if ( isset( $ht_knowledge_base_settings['hide-uncat-articles'] ) ){
            $hkb_archive_hide_uncategorized_articles = $ht_knowledge_base_settings['hide-uncat-articles'];
        } else {
            $hkb_archive_hide_uncategorized_articles = false;
        }

        return apply_filters('hkb_archive_hide_uncategorized_articles', $hkb_archive_hide_uncategorized_articles);
    }
}

if(!function_exists('hkb_get_knowledgebase_searchbox_placeholder_text')){
    /**
    * Get the Knowledge Base searchbox placeholder text
    * Filterable - hkb_get_knowledgebase_searchbox_placeholder_text
    * @return (String) The placeholder text
    */
    function hkb_get_knowledgebase_searchbox_placeholder_text(){
        global $post, $ht_knowledge_base_settings;
        $hkb_get_knowledgebase_searchbox_placeholder_text = '';
        //there is an issue with the global ht_knowledge_base_settings not being translated, hence we'll revert to the get_option call
        $ht_knowledge_base_settings_option = get_option('ht_knowledge_base_settings');

        $hkb_get_knowledgebase_searchbox_placeholder_text = isset( $ht_knowledge_base_settings_option['search-placeholder-text'] ) ? $ht_knowledge_base_settings_option['search-placeholder-text'] : __('Search the Knowledge Base', 'ht-knowledge-base');
                                
        return apply_filters('hkb_get_knowledgebase_searchbox_placeholder_text', $hkb_get_knowledgebase_searchbox_placeholder_text);

    }
}

if(!function_exists('hkb_show_knowledgebase_breadcrumbs')){
    /**
    * Get the Knowledge Base display-breadcrumbs option
    * Filterable - hkb_show_knowledgebase_breadcrumbs
    * @param (String) $location The location of where to display (currently unused)
    * @return (Bool) The option
    */
    function hkb_show_knowledgebase_breadcrumbs($location=null){
        global $ht_knowledge_base_settings;
        $hkb_show_knowledgebase_breadcrumbs = false;
        if ( isset( $ht_knowledge_base_settings['display-breadcrumbs'] ) ){
            $hkb_show_knowledgebase_breadcrumbs = $ht_knowledge_base_settings['display-breadcrumbs'];
        } else {
            $hkb_show_knowledgebase_breadcrumbs = false;
        }
        return apply_filters('hkb_show_knowledgebase_breadcrumbs', $hkb_show_knowledgebase_breadcrumbs);
    }
}

if(!function_exists('hkb_show_usefulness_display')){
    /**
    * Get the Knowledge Base usefulness display option
    * Filterable - hkb_show_usefulness_display
    * @deprecated by 3.0 (no alternative available)
    * @param (String) $location The location of where to display (currently unused)
    * @return (Bool) The option
    */
    function hkb_show_usefulness_display($location=null){
        global $ht_knowledge_base_settings;
        $hkb_show_usefulness_display = false;
        if ( isset( $ht_knowledge_base_settings['display-article-usefulness'] ) ){
            $hkb_show_usefulness_display = $ht_knowledge_base_settings['display-article-usefulness'];
        } else {
            $hkb_show_usefulness_display = false;
        }
        return apply_filters('hkb_show_usefulness_display', $hkb_show_usefulness_display);
    }
}

if(!function_exists('hkb_show_viewcount_display')){
    /**
    * Get the Knowledge Base viewcount display option
    * Filterable - hkb_show_viewcount_display
    * @deprecated by 3.0 (no alternative available)
    * @param (String) $location The location of where to display (currently unused)
    * @return (Bool) The option
    */
    function hkb_show_viewcount_display($location=null){
        global $ht_knowledge_base_settings;
        $hkb_show_viewcount_display = false;
        if ( isset( $ht_knowledge_base_settings['display-article-views-count'] ) ){
            $hkb_show_viewcount_display = $ht_knowledge_base_settings['display-article-views-count'];
        } else {
            $hkb_show_viewcount_display = false;
        }
        return apply_filters('hkb_show_viewcount_display', $hkb_show_viewcount_display);
    }
}

if(!function_exists('hkb_show_comments_display')){
    /**
    * Get the Knowledge Base comments display option
    * Filterable - hkb_show_comments_display
    * @deprecated by 3.0 (no alternative available)
    * @param (String) $location The location of where to display (currently unused)
    * @return (Bool) The option
    */
    function hkb_show_comments_display($location=null){
        global $ht_knowledge_base_settings;
        $hkb_show_comments_display = false;
        if ( isset( $ht_knowledge_base_settings['enable-article-comments'] ) ){
            $hkb_show_comments_display = $ht_knowledge_base_settings['enable-article-comments'];
        } else {
            $hkb_show_comments_display = false;
        }
        return apply_filters('hkb_show_comments_display', $hkb_show_comments_display);
    }
}

if(!function_exists('hkb_show_author_display')){
    /**
    * Get the Knowledge Base author display option
    * Filterable - hkb_show_author_display
    * @param (String) $location The location of where to display (currently unused)
    * @return (Bool) The option
    */
    function hkb_show_author_display($location=null){
        global $ht_knowledge_base_settings;
        $hkb_show_author_display = false;
        if ( isset( $ht_knowledge_base_settings['display-article-author'] ) ){
            $hkb_show_author_display = $ht_knowledge_base_settings['display-article-author'];
        } else {
            $hkb_show_author_display = false;
        }
        return apply_filters('hkb_show_author_display', $hkb_show_author_display);
    }
}

if(!function_exists('hkb_show_related_articles')){
    /**
    * Get the Knowledge Base show related articles
    * Filterable - hkb_show_related_articles
    * @param (String) $location The location of where to display (currently unused)
    * @return (Bool) The option
    */
    function hkb_show_related_articles($location=null){
        global $ht_knowledge_base_settings;
        $hkb_show_related_articles = true;
        if ( isset( $ht_knowledge_base_settings['display-related-articles'] ) ){
            $hkb_show_related_articles = $ht_knowledge_base_settings['display-related-articles'];
        } else {
            $hkb_show_related_articles = true;
        }
        return apply_filters('hkb_show_related_articles', $hkb_show_related_articles);
    }
}

if(!function_exists('hkb_show_excerpt')){
    /**
    * Get the Knowledge Base excerpt display option for the location
    * Filterable - hkb_show_excerpt
    * @param (String) $location The location of where to display
    * @return (Bool) The option
    */
    function hkb_show_excerpt($location=null){
        global $ht_knowledge_base_settings;
        $hkb_show_excerpt = false;
        switch ($location) {
            case 'search':
                if ( isset( $ht_knowledge_base_settings['display-search-result-excerpt'] ) ){
                    $hkb_show_excerpt = $ht_knowledge_base_settings['display-search-result-excerpt'];
                }
                break;
            case 'taxonomy':
                if ( isset( $ht_knowledge_base_settings['display-taxonomy-article-excerpt'] ) ){
                    $hkb_show_excerpt = $ht_knowledge_base_settings['display-taxonomy-article-excerpt'];
                }
                break;            
            default:
                break;
        }
        return apply_filters('hkb_show_excerpt', $hkb_show_excerpt);
    }
}

if(!function_exists('hkb_highlight_excerpt')){
    /**
    * Get the Knowledge Base highlight excerpt display option for the location
    * Filterable - hkb_highlight_excerpt
    * @param (String) $location The location of where to display
    * @return (Bool) The option
    */
    function hkb_highlight_excerpt($location=null){
        global $ht_knowledge_base_settings;
        $hkb_highlight_excerpt = false;
        switch ($location) {
            case 'search':
                if ( isset( $ht_knowledge_base_settings['highlight-search-result-excerpt'] ) ){
                    $hkb_highlight_excerpt = $ht_knowledge_base_settings['highlight-search-result-excerpt'];
                }
                break;           
            default:
                break;
        }
        return apply_filters('hkb_highlight_excerpt', $hkb_highlight_excerpt);
    }
}

if(!function_exists('hkb_show_search_excerpt')){
    /**
    * Get the Knowledge Base search excerpt display option
    * Filterable - hkb_show_search_excerpt
    * @param (String) $location The location of where to display (deprecated)
    * @return (Bool) The option
    */
    function hkb_show_search_excerpt($location=null){
        $hkb_show_search_excerpt = hkb_show_excerpt('search');
        return apply_filters('hkb_show_search_excerpt', $hkb_show_search_excerpt);
    }
}

if(!function_exists('hkb_highlight_search_term_in_excerpt')){
    /**
    * Get the Knowledge Base search excerpt display option
    * Filterable - hkb_highlight_search_term_in_excerpt
    * @param (String) $location The location of where to display (deprecated)
    * @return (Bool) The option
    */
    function hkb_highlight_search_term_in_excerpt($location=null){
        $hkb_highlight_search_term_in_excerpt = hkb_highlight_excerpt('search');
        return apply_filters('hkb_highlight_search_term_in_excerpt', $hkb_highlight_search_term_in_excerpt);
    }
}

if(!function_exists('hkb_show_taxonomy_article_excerpt')){
    /**
    * Get the Knowledge Base taxonomy excerpt display option
    * Filterable - hkb_show_taxonomy_article_excerpt
    * @param (String) $location The location of where to display (deprecated)
    * @return (Bool) The option
    */
    function hkb_show_taxonomy_article_excerpt($location=null){
        $hkb_show_taxonomy_article_excerpt = hkb_show_excerpt('taxonomy');
        return apply_filters('hkb_show_taxonomy_article_excerpt', $hkb_show_taxonomy_article_excerpt);
    }
}

if(!function_exists('hkb_focus_on_search_box')){
    /**
    * Get the Knowledge Base related rating display
    * Filterable - hkb_focus_on_search_box
    * @param (String) $location The location of where to display (currently unused)
    * @return (Bool) The option
    */
    function hkb_focus_on_search_box($location=null){
        global $ht_knowledge_base_settings;
        $hkb_focus_on_search_box = false;
        if ( isset( $ht_knowledge_base_settings['focus-live-search'] ) ){
            $hkb_focus_on_search_box = $ht_knowledge_base_settings['focus-live-search'];
        } else {
            $hkb_focus_on_search_box = false;
        }
        return apply_filters('hkb_focus_on_search_box', $hkb_focus_on_search_box);
    }
}

if(!function_exists('hkb_home_articles')){
    /**
    * Number of articles to display in home
    * Filterable - hkb_home_articles
    * @return (Int) The option
    */
    function hkb_home_articles($location=null){
        global $ht_knowledge_base_settings;
        if ( isset( $ht_knowledge_base_settings['num-articles-home'] ) ){
            $hkb_home_articles = $ht_knowledge_base_settings['num-articles-home'];
        } else {
            $hkb_home_articles = get_option('posts_per_page');
        }
        return apply_filters('hkb_home_articles', $hkb_home_articles);
    }
}


if(!function_exists('hkb_category_articles')){
    /**
    * Number of articles to display in category
    * Filterable - hkb_category_articles
    * @return (Int) The option
    */
    function hkb_category_articles($location=null){
        global $ht_knowledge_base_settings;
        if ( isset( $ht_knowledge_base_settings['num-articles'] ) ){
            $hkb_category_articles = $ht_knowledge_base_settings['num-articles'];
        } else {
            $hkb_category_articles = get_option('posts_per_page');
        }
        return apply_filters('hkb_category_articles', $hkb_category_articles);
    }
}

if(!function_exists('hkb_restrict_access')){
    /**
    * Visibility restriction option
    * Filterable - hkb_restrict_access
    * @return (String) The option
    */
    function hkb_restrict_access($location=null){
        global $ht_knowledge_base_settings;
        $hkb_restrict_access = 'public';
        if ( isset( $ht_knowledge_base_settings['restrict-access'] ) ){
            $hkb_restrict_access = $ht_knowledge_base_settings['restrict-access'];
        } else {
            //do nothing
        }
        return apply_filters('hkb_restrict_access', $hkb_restrict_access);
    }
}


if(!function_exists('hkb_get_custom_styles_css')){
    /**
    * Get the Knowledge Base custom styles
    * Filterable - hkb_get_custom_styles_css
    * @deprecated - for HKB 3.0 use Appearance > Customize > Additional CSS, or better still, a child theme.
    * @return (String) Custom CSS
    */
    function hkb_get_custom_styles_css(){   
        global $ht_knowledge_base_settings;
        $hkb_get_custom_styles_css = '';
        if ( isset( $ht_knowledge_base_settings['custom-kb-styling-content']) && !empty($ht_knowledge_base_settings['custom-kb-styling-content']) ){
            $styles = '<!-- Heroic Knowledge Base custom styles -->';
            $styles .= '<style>';
            $styles .= $ht_knowledge_base_settings['custom-kb-styling-content'];
            $styles .= '</style>';
            $hkb_get_custom_styles_css = $styles;
        } else {
            $hkb_get_custom_styles_css = '';
        }
        return apply_filters('hkb_get_custom_styles_css', $hkb_get_custom_styles_css);
    }
}

if(!function_exists('hkb_custom_styles_sitewide')){
    /**
    * Whether to use custom styles sitewide
    * Filterable - hkb_custom_styles_sitewide
    * @deprecated - for HKB 3.0 use Appearance > Customize > Additional CSS, or better still, a child theme.
    * @return (Boolean) default false
    */
    function hkb_custom_styles_sitewide(){   
        global $ht_knowledge_base_settings;
        $hkb_custom_styles_sitewide = false;
        if ( isset( $ht_knowledge_base_settings['enable-kb-styling-sitewide']) ){
            $hkb_custom_styles_sitewide = $ht_knowledge_base_settings['enable-kb-styling-sitewide'];            
        } else {
            $hkb_custom_styles_sitewide = false;
        }
        return apply_filters('hkb_custom_styles_sitewide', $hkb_custom_styles_sitewide);
    }
}

if(!function_exists('hkb_kb_search_sitewide')){
    /**
    * Whether to use search in kb sitewide
    * Filterable - hkb_kb_search_sitewide
    * @return (Boolean) default false
    * @deprecated No longer used
    */
    function hkb_kb_search_sitewide(){   
        $hkb_kb_search_sitewide = false;

        return apply_filters('hkb_kb_search_sitewide', $hkb_kb_search_sitewide);
    }
}

if(!function_exists('hkb_kb_default_page_id')){
    /**
    * Filterable - hkb_kb_default_page_id
    * @return (String) option
    */
    function hkb_kb_default_page_id(){ 
        $hkb_kb_default_page_id = ht_kb_get_kb_archive_page_id( 'default' ); 

        return apply_filters('hkb_kb_default_page_id', $hkb_kb_default_page_id);
    }
}


if(!function_exists('hkb_kb_article_slug')){
    /**
    * Filterable - hkb_kb_article_slug
    * @return (String) option
    */
    function hkb_kb_article_slug(){ 
        global $ht_knowledge_base_settings;
        if ( isset( $ht_knowledge_base_settings['kb-article-slug']) ){
            $hkb_kb_article_slug = $ht_knowledge_base_settings['kb-article-slug'];            
        } else {
            $hkb_kb_article_slug = 'knowledge-base';
        }  

        return apply_filters('hkb_kb_article_slug', $hkb_kb_article_slug);
    }
}

if(!function_exists('hkb_kb_category_slug')){
    /**
    * Filterable - hkb_kb_category_slug
    * @return (String) option
    */
    function hkb_kb_category_slug(){   
        global $ht_knowledge_base_settings;
        if ( isset( $ht_knowledge_base_settings['kb-category-slug']) ){
            $hkb_kb_category_slug = $ht_knowledge_base_settings['kb-category-slug'];            
        } else {
            $hkb_kb_category_slug = 'article-categories';
        }         

        return apply_filters('hkb_kb_category_slug', $hkb_kb_category_slug);
    }
}

if(!function_exists('hkb_kb_tag_slug')){
    /**
    * Filterable - hkb_kb_tag_slug
    * @return (String) option
    */
    function hkb_kb_tag_slug(){
        global $ht_knowledge_base_settings;
        if ( isset( $ht_knowledge_base_settings['kb-tag-slug']) ){
            $hkb_kb_tag_slug = $ht_knowledge_base_settings['kb-tag-slug'];            
        } else {
            $hkb_kb_tag_slug = 'article-tags';
        }         

        return apply_filters('hkb_kb_tag_slug', $hkb_kb_tag_slug);
    }
}


/* EXITS */

if(!function_exists('ht_kb_exit_display_at_end_of_article')){
    /**
    * Filterable - ht_kb_exit_display_at_end_of_article, shiv, no longer used, to deprecate
    * @return (Boolean) option 
    */
    function ht_kb_exit_display_at_end_of_article(){
        $ht_kb_exit_display_at_end_of_article = false;       

        return apply_filters('ht_kb_exit_display_at_end_of_article', $ht_kb_exit_display_at_end_of_article);
    }
}

if(!function_exists('ht_kb_exit_url_option')){
    /**
    * Filterable - ht_kb_exit_url_option
    * @return (String) option
    */
    function ht_kb_exit_url_option(){
        global $ht_knowledge_base_settings;
        if ( isset( $ht_knowledge_base_settings['kb-transfer-url']) ){
            $ht_kb_exit_url_option = $ht_knowledge_base_settings['kb-transfer-url'];            
        } else {
            $ht_kb_exit_url_option = 'https://www.example.com/support-desk';
        }         

        return apply_filters('ht_kb_exit_url_option', $ht_kb_exit_url_option);
    }
}


if(!function_exists('ht_kb_exit_new_window_option')){
    /**
    * Filterable - ht_kb_exit_new_window_option
    * @return (Boolean) option
    */
    function ht_kb_exit_new_window_option(){
        global $ht_knowledge_base_settings;
        if ( isset( $ht_knowledge_base_settings['kb-transfer-new-window']) ){
            $ht_kb_exit_new_window_option = $ht_knowledge_base_settings['kb-transfer-new-window'];            
        } else {
            $ht_kb_exit_new_window_option = true;
        }         

        return apply_filters('ht_kb_exit_new_window_option', $ht_kb_exit_new_window_option);
    }
}


if(!function_exists('ht_kb_voting_enable_feedback')){
    /**
    * Filterable - ht_kb_voting_enable_feedback
    * @return (Boolean) option
    */
    function ht_kb_voting_enable_feedback(){
        global $ht_knowledge_base_settings;
        if ( isset( $ht_knowledge_base_settings['enable-article-feedback']) ){
            $ht_kb_voting_enable_feedback = $ht_knowledge_base_settings['enable-article-feedback'];            
        } else {
            $ht_kb_voting_enable_feedback = true;
        }         

        return apply_filters('ht_kb_voting_enable_feedback', $ht_kb_voting_enable_feedback);
    }
}

if(!function_exists('ht_kb_voting_enable_anonymous')){
    /**
    * Filterable - ht_kb_voting_enable_anonymous
    * @return (Boolean) option
    */
    function ht_kb_voting_enable_anonymous(){
        global $ht_knowledge_base_settings;
        if ( isset( $ht_knowledge_base_settings['enable-anon-article-feedback']) ){
            $ht_kb_voting_enable_anonymous = $ht_knowledge_base_settings['enable-anon-article-feedback'];            
        } else {
            $ht_kb_voting_enable_anonymous = true;
        }         

        return apply_filters('ht_kb_voting_enable_anonymous', $ht_kb_voting_enable_anonymous);
    }
}

if(!function_exists('ht_kb_voting_upvote_feedback')){
    /**
    * Filterable - ht_kb_voting_upvote_feedback
    * @return (Boolean) option
    */
    function ht_kb_voting_upvote_feedback(){
        global $ht_knowledge_base_settings;
        if ( isset( $ht_knowledge_base_settings['enable-upvote-article-feedback']) ){
            $ht_kb_voting_upvote_feedback = $ht_knowledge_base_settings['enable-upvote-article-feedback'];            
        } else {
            $ht_kb_voting_upvote_feedback = true;
        }         

        return apply_filters('ht_kb_voting_upvote_feedback', $ht_kb_voting_upvote_feedback);
    }
}

if(!function_exists('ht_kb_voting_downvote_feedback')){
    /**
    * Get the enable downvote feedback o[ption]
    * Filterable - ht_kb_voting_downvote_feedback
    * @return (Boolean) option
    */
    function ht_kb_voting_downvote_feedback(){
        global $ht_knowledge_base_settings;
        if ( isset( $ht_knowledge_base_settings['enable-downvote-article-feedback']) ){
            $ht_kb_voting_downvote_feedback = $ht_knowledge_base_settings['enable-downvote-article-feedback'];            
        } else {
            $ht_kb_voting_downvote_feedback = true;
        }         

        return apply_filters('ht_kb_voting_downvote_feedback', $ht_kb_voting_downvote_feedback);
    }
}


if(!function_exists('ht_kb_get_kb_archive_page_id')){
    /**
    * fetch knowledge base archive page id
    * Filterable - ht_kb_get_kb_archive_page_id
    * @since 3.0
    * @return (Integer) $page_id
    */
    function ht_kb_get_kb_archive_page_id( $kb_key = 'default' ){
        global $ht_knowledge_base_settings;
        //default 
        $page_id = 0;
        if ( isset( $ht_knowledge_base_settings['kb-archive-page-id'][$kb_key]) ){
            $page_id = $ht_knowledge_base_settings['kb-archive-page-id'][$kb_key];            
        }       


        return apply_filters( 'ht_kb_get_kb_archive_page_id', $page_id, $kb_key );
    }
}

if(!function_exists('hkb_get_knowledgebase_archive_header_text')){
    /**
    * Get the Knowledge Base searchbox placeholder text
    * Filterable - hkb_get_knowledgebase_archive_header_text
    * @since 3.0
    * @return (String) $hkb_get_knowledgebase_archive_header_text
    */
    function hkb_get_knowledgebase_archive_header_text(){
        global $post, $ht_knowledge_base_settings;
        $hkb_get_knowledgebase_archive_header_text = '';
        //there is an issue with the global ht_knowledge_base_settings not being translated, hence we'll revert to the get_option call
        $ht_knowledge_base_settings_option = get_option('ht_knowledge_base_settings');

        $hkb_get_knowledgebase_archive_header_text = isset( $ht_knowledge_base_settings_option['kb-archive-page-header-text'] ) ? $ht_knowledge_base_settings_option['kb-archive-page-header-text'] : __('Search the knowledge base for answers', 'ht-knowledge-base');
                                
        return apply_filters('hkb_get_knowledgebase_archive_header_text', $hkb_get_knowledgebase_archive_header_text);

    }
}

if(!function_exists('ht_kb_get_registered_knowledge_base_keys')){
    /**
     * Fetch knowledge base registered keys
     * @since 3.0
     * @return (Array) string array
     */
    function ht_kb_get_registered_knowledge_base_keys(){
        global $ht_knowledge_base_settings;
        //default 
        return apply_filters( 'ht_kb_get_registered_knowledge_base_keys', array('default') );
    }
}

/** Sidebars */

if(!function_exists('hkb_sidebar_article_position')){
    /**
     * Fetch article sidebar position
     * Filterable - hkb_sidebar_article_position
     * @since 3.0
     * @return (String) left|right|off
     */
    function hkb_sidebar_article_position($location=null){
        global $ht_knowledge_base_settings;
        $hkb_sidebar_article_position = true;
        if ( isset( $ht_knowledge_base_settings['article-sidebar-position'] ) ){
            $hkb_sidebar_article_position = $ht_knowledge_base_settings['article-sidebar-position'];
        } else {
            $hkb_sidebar_article_position = 'right';
        }
        return apply_filters('hkb_sidebar_article_position', $hkb_sidebar_article_position);
    }
}

if(!function_exists('hkb_sidebar_category_position')){
    /**
     * Fetch category sidebar position
     * Filterable - hkb_sidebar_category_position
     * @since 3.0
     * @return (String) left|right|off
     */
    function hkb_sidebar_category_position($location=null){
        global $ht_knowledge_base_settings;
        $hkb_sidebar_category_position = true;
        if ( isset( $ht_knowledge_base_settings['category-sidebar-position'] ) ){
            $hkb_sidebar_category_position = $ht_knowledge_base_settings['category-sidebar-position'];
        } else {
            $hkb_sidebar_category_position = 'right';
        }
        return apply_filters('hkb_sidebar_category_position', $hkb_sidebar_category_position);
    }
}

if(!function_exists('hkb_sidebar_archive_position')){
    /**
     * Fetch archive sidebar position
     * Filterable - hkb_sidebar_archive_position
     * @since 3.0
     * @return (String) left|right|off
     */
    function hkb_sidebar_archive_position($location=null){
        global $ht_knowledge_base_settings;
        $hkb_sidebar_archive_position = true;
        if ( isset( $ht_knowledge_base_settings['archive-sidebar-position'] ) ){
            $hkb_sidebar_archive_position = $ht_knowledge_base_settings['archive-sidebar-position'];
        } else {
            $hkb_sidebar_archive_position = 'right';
        }
        return apply_filters('hkb_sidebar_archive_position', $hkb_sidebar_archive_position);
    }
}

if(!function_exists('hkb_sidebar_sticky')){
    /**
    * Get the Knowledge Base Sticky Setting
    * Filterable - hkb_get_knowledgebase_link_color
    * @since 3.0
    * @return (String) $hkb_get_knowledgebase_link_color
    */
    function hkb_sidebar_sticky(){
        global $ht_knowledge_base_settings;

        $hkb_get_knowledgebase_sidebar_sticky = isset( $ht_knowledge_base_settings['article-sticky-sidebar'] ) ? $ht_knowledge_base_settings['article-sticky-sidebar'] : 'true';
                                
        return apply_filters('hkb_get_knowledgebase_sidebar_sticky', $hkb_get_knowledgebase_sidebar_sticky);

    }
}

if(!function_exists('hkb_get_knowledgebase_container_width')){
    /**
    * Get the Knowledge Base Container Width
    * Filterable - hkb_get_knowledgebase_container_width
    * @since 3.0
    * @return (Int) $hkb_get_knowledgebase_container_width
    */
    function hkb_get_knowledgebase_container_width(){
        global $ht_knowledge_base_settings;

        $hkb_get_knowledgebase_container_width = isset( $ht_knowledge_base_settings['kb-width'] ) ? $ht_knowledge_base_settings['kb-width'] : 1200;
                                
        return apply_filters('hkb_get_knowledgebase_container_width', $hkb_get_knowledgebase_container_width);

    }
}

if(!function_exists('hkb_get_knowledgebase_link_color')){
    /**
    * Get the Knowledge Base Link Color
    * Filterable - hkb_get_knowledgebase_link_color
    * @since 3.0
    * @return (String) $hkb_get_knowledgebase_link_color
    */
    function hkb_get_knowledgebase_link_color(){
        global $ht_knowledge_base_settings;

        $hkb_get_knowledgebase_link_color = isset( $ht_knowledge_base_settings['kb-linkcolor'] ) ? $ht_knowledge_base_settings['kb-linkcolor'] : '#1e73be';
                                
        return apply_filters('hkb_get_knowledgebase_link_color', $hkb_get_knowledgebase_link_color);

    }
}

if(!function_exists('hkb_get_knowledgebase_header_style')){
    /**
    * Get the Knowledge Base Link Color (Hover)
    * Filterable - hkb_get_knowledgebase_link_color_hover
    * @since 3.0
    * @return (String) $hkb_get_knowledgebase_link_color_hover
    */
    function hkb_get_knowledgebase_link_color_hover(){
        global $ht_knowledge_base_settings;

        $hkb_get_knowledgebase_link_color_hover = isset( $ht_knowledge_base_settings['kb-linkcolorhover'] ) ? $ht_knowledge_base_settings['kb-linkcolorhover'] : '#1e73be';
                                
        return apply_filters('hkb_get_knowledgebase_link_color_hover', $hkb_get_knowledgebase_link_color_hover);

    }
}

if(!function_exists('hkb_get_knowledgebase_header_style')){
    /**
    * Get the Knowledge Base Header Style - Solid / Gradient / Image
    * Filterable - hkb_get_knowledgebase_header_style
    * @since 3.0
    * @return (String) $hkb_get_knowledgebase_header_style - solid|gradient|image
    */
    function hkb_get_knowledgebase_header_style(){
        global $ht_knowledge_base_settings;

        $hkb_get_knowledgebase_header_style = isset( $ht_knowledge_base_settings['kb-headerstyle'] ) ? $ht_knowledge_base_settings['kb-headerstyle'] : 'solid';
                                
        return apply_filters('hkb_get_knowledgebase_header_style', $hkb_get_knowledgebase_header_style);

    }
}

if(!function_exists('hkb_get_knowledgebase_header_style_background_color')){
    /**
    * Get the Knowledge Base Header Style Background
    * Filterable - hkb_get_knowledgebase_header_style_background_color
    * @since 3.0
    * @return (String) $hkb_get_knowledgebase_header_style_background_color 
    */
    function hkb_get_knowledgebase_header_style_background_color(){
        global $ht_knowledge_base_settings;

        $hkb_get_knowledgebase_header_style_background_color = isset( $ht_knowledge_base_settings['kb-headerstyle-bg'] ) ? $ht_knowledge_base_settings['kb-headerstyle-bg'] : '#7a7a7a';
                                
        return apply_filters('hkb_get_knowledgebase_header_style_background_color', $hkb_get_knowledgebase_header_style_background_color);

    }
}

if(!function_exists('hkb_get_knowledgebase_header_style_gradient_direction')){
    /**
    * Get the Knowledge Base Header Style Gradient Direction
    * Filterable - hkb_get_knowledgebase_header_style_gradient_direction
    * @since 3.0
    * @return (Int) $hkb_get_knowledgebase_header_style_gradient_direction 
    */
    function hkb_get_knowledgebase_header_style_gradient_direction(){
        global $ht_knowledge_base_settings;

        $hkb_get_knowledgebase_header_style_gradient_direction = isset( $ht_knowledge_base_settings['kb-headerstyle-graddir'] ) ? $ht_knowledge_base_settings['kb-headerstyle-graddir'] : 90;
                                
        return apply_filters('hkb_get_knowledgebase_header_style_gradient_direction', $hkb_get_knowledgebase_header_style_gradient_direction);

    }
}

if(!function_exists('hkb_get_knowledgebase_header_style_gradient_color_1')){
    /**
    * Get the Knowledge Base Header Style Gradient Color 1
    * Filterable - hkb_get_knowledgebase_header_style_gradient_color_1
    * @since 3.0
    * @return (String) $hkb_get_knowledgebase_header_style_gradient_color_1 
    */
    function hkb_get_knowledgebase_header_style_gradient_color_1(){
        global $ht_knowledge_base_settings;

        $hkb_get_knowledgebase_header_style_gradient_color_1 = isset( $ht_knowledge_base_settings['kb-headerstyle-grad1'] ) ? $ht_knowledge_base_settings['kb-headerstyle-grad1'] : '#7a7a7a';
                                
        return apply_filters('hkb_get_knowledgebase_header_style_gradient_color_1', $hkb_get_knowledgebase_header_style_gradient_color_1);

    }
}

if(!function_exists('hkb_get_knowledgebase_header_style_gradient_color_2')){
    /**
    * Get the Knowledge Base Header Style  Gradient Color 2
    * Filterable - hkb_get_knowledgebase_header_style_gradient_color_2
    * @since 3.0
    * @return (String) $hkb_get_knowledgebase_header_style_gradient_color_2 
    */
    function hkb_get_knowledgebase_header_style_gradient_color_2(){
        global $ht_knowledge_base_settings;

        $hkb_get_knowledgebase_header_style_gradient_color_2 = isset( $ht_knowledge_base_settings['kb-headerstyle-grad2'] ) ? $ht_knowledge_base_settings['kb-headerstyle-grad2'] : '#7a7a7a';
                                
        return apply_filters('hkb_get_knowledgebase_header_style_gradient_color_2', $hkb_get_knowledgebase_header_style_gradient_color_2);

    }
}

if(!function_exists('hkb_get_knowledgebase_header_background_image_attachment_id')){
    /**
    * Get the Knowledge Base Header Background Image Attachment ID
    * Filterable - hkb_get_knowledgebase_header_background_image_attachment_id
    * @since 3.0
    * @return (Int) $hkb_get_knowledgebase_header_background_image_attachment_id 
    */
    function hkb_get_knowledgebase_header_background_image_attachment_id(){
        global $ht_knowledge_base_settings;

        $hkb_get_knowledgebase_header_background_image_attachment_id = isset( $ht_knowledge_base_settings['kb-headerstyle-img-attachment-id'] ) ? $ht_knowledge_base_settings['kb-headerstyle-img-attachment-id'] : 0;
                                
        return apply_filters('hkb_get_knowledgebase_header_background_image_attachment_id', $hkb_get_knowledgebase_header_background_image_attachment_id);

    }
}

if(!function_exists('hkb_get_knowledgebase_header_background_image_attachment_src_url')){
    /**
    * Get the Knowledge Base Header Background Image Attachment ID
    * Filterable - hkb_get_knowledgebase_header_background_image_attachment_src_url
    * @since 3.0
    * @param (String|Int[]) $size = 'thumbnail'
    * @return (Int) $hkb_get_knowledgebase_header_background_image_attachment_src_url 
    */
    function hkb_get_knowledgebase_header_background_image_attachment_src_url( $size='full' ){
        global $ht_knowledge_base_settings;

        //get the attachment id 
        $bg_image_attachment_id = hkb_get_knowledgebase_header_background_image_attachment_id();

        $hkb_get_knowledgebase_header_background_image_attachment_src_url = !empty( $bg_image_attachment_id ) ? wp_get_attachment_image_url( $bg_image_attachment_id,  $size )  : '';
                                
        return apply_filters('hkb_get_knowledgebase_header_background_image_attachment_src_url', $hkb_get_knowledgebase_header_background_image_attachment_src_url);

    }
}


if(!function_exists('hkb_get_knowledgebase_header_text_color')){
    /**
    * Get the Knowledge Base Header Text Color
    * Filterable - hkb_get_knowledgebase_header_text_color
    * @since 3.0
    * @return (String) $hkb_get_knowledgebase_header_text_color 
    */
    function hkb_get_knowledgebase_header_text_color(){
        global $ht_knowledge_base_settings;

        $hkb_get_knowledgebase_header_text_color = isset( $ht_knowledge_base_settings['kb-headercolor'] ) ? $ht_knowledge_base_settings['kb-headercolor'] : '#fff';
                                
        return apply_filters('hkb_get_knowledgebase_header_text_color', $hkb_get_knowledgebase_header_text_color);

    }
}


/** HKB CSS Variables */

if(!function_exists('hkb_get_css_variables')){
    /**
    * Get the Knowledge Base custom styles
    * Filterable - hkb_get_custom_styles_css
    * @return (String) Custom CSS
    */
    function hkb_get_css_variables(){   
        global $ht_knowledge_base_settings;
        $hkb_get_css_variables = '';

        $styles = '<!-- HKB CSS Variables -->';

        $styles .= '<style>:root {';
        //kb-width
        $styles .= '--hkb-main-container-width: '. hkb_get_knowledgebase_container_width() .'px;';
        //kb-linkcolor
        $styles .= '--hkb-link-color: '. hkb_get_knowledgebase_link_color() .';';
        //kb-linkcolorhover
        $styles .= '--hkb-link-color-hover: '. hkb_get_knowledgebase_link_color_hover() .';';

        //kb-headerstyle defined in HTML as data-attr
        //$styles .= '--hkb-header-style: '.$ht_knowledge_base_settings['kb-kb-headerstyle'] .';';
        //kb-headerstyle-bg
        $styles .= '--hkb-header-style-bg: '. hkb_get_knowledgebase_header_style_background_color() .';';
        //kb-headerstyle-graddir
        $styles .= '--hkb-header-style-graddir: '. hkb_get_knowledgebase_header_style_gradient_direction() .'deg;';
        //kb-headerstyle-grad1
        $styles .= '--hkb-header-style-grad1: '. hkb_get_knowledgebase_header_style_gradient_color_1() .';';
        //kb-headerstyle-grad2
        $styles .= '--hkb-header-style-grad2: '. hkb_get_knowledgebase_header_style_gradient_color_2() .';';

        //kb-headercolor
        $styles .= '--hkb-header-text-color: '. hkb_get_knowledgebase_header_text_color() .';';

        $styles .= '}</style>';


        $hkb_get_css_variables = $styles;

        return apply_filters('hkb_get_css_variables', $hkb_get_css_variables);
    }
}