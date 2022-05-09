<?php 
/**
* Article helper functions
*/

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if(!function_exists('get_most_helpful_article_id')){
    /**
    * Get the id of the most helpful article
    * @pluggable
    * @return (Int) ID of most helpful article
    */
    function get_most_helpful_article_id(){
        $most_helpful_article_id = 0;
        
        $most_helpful = new WP_Query('meta_key=_ht_kb_usefulness&post_type=ht_kb&orderby=meta_value_num&order=DESC');
        if ($most_helpful->have_posts()) : 
            while ($most_helpful->have_posts()) : $most_helpful->the_post(); 
                $most_helpful_article_id = get_the_ID();
                //this is the most helpful, break.
                break;
            endwhile; 
        endif;
        wp_reset_postdata();
        return $most_helpful_article_id;
    }
}

if(!function_exists('is_most_helpful_article_id')){
    /**
    * Is the ID of the most helpful article
    * @pluggable
    * @param (Int) $article_id The test article ID
    * @return (Boolean) True when article ID matches most helpful article ID
    */
    function is_most_helpful_article_id($article_id){
        $most_helpful_article_id = get_most_helpful_article_id();
        return $most_helpful_article_id == $article_id;
    }
}

if(!function_exists('display_is_most_helpful_article')){
    /**
    * Displays badge if most helpful article
    * @pluggable
    */
    function display_is_most_helpful_article(){
        global $post;
        if(is_most_helpful_article_id($post->ID)){
            ?>
            <span class="ht-kb-most-helpful-article"><?php _e('Most Helpful Article', 'ht-knowledge-base'); ?></span>
            <?php
        }
    }
}

if(!function_exists('get_most_viewed_article_id')){
    /**
    * Get the id of the most viewed article
    * @pluggable
    * @return (Int) ID of most viewed article
    */
    function get_most_viewed_article_id(){
        
        $most_viewed_article_id = 0;
        $most_viewed = new WP_Query('meta_key=' . HT_KB_POST_VIEW_COUNT_KEY . '&post_type=ht_kb&orderby=meta_value_num&order=DESC');
        if ($most_viewed->have_posts()) : 
            while ($most_viewed->have_posts()) : $most_viewed->the_post(); 
                $most_viewed_article_id = get_the_ID();
                //this is the most viewed, break.
                break;
            endwhile; 
        endif;
        wp_reset_postdata();
        return $most_viewed_article_id;

        return 0;
    }
}

if(!function_exists('is_most_viewed_article_id')){
    /**
    * Is the id of the most viewed article
    * @pluggable
    * @param (Int) $article_id The test article ID
    * @return (Boolean) True when article ID matches most viewed article ID
    */
    function is_most_viewed_article_id($article_id){
        $most_viewed_article_id = get_most_viewed_article_id();
        return $most_viewed_article_id == $article_id;
    }
}

if(!function_exists('display_is_most_viewed_article')){
    /**
    * Display badge if most viewed article
    * @pluggable
    */
    function display_is_most_viewed_article(){
        global $post;
        if(is_most_viewed_article_id($post->ID)){ ?>
            <span class="ht-kb-most-viewed-article"><?php _e('Most Viewed Article', 'ht-knowledge-base'); ?></span>
        <?php   }
    }
}

if(!function_exists('get_most_helpful_user_id')){
    /**
    * Get the id helpful user id
    * @pluggable
    * @return (Int) ID of most helpful user
    */
    function get_most_helpful_user_id(){
        //start here use WP_User_Query
        //this *should* be orderby meta_value_num, but not available 
        $users = get_users('meta_key=_ht_kb_usefulness&orderby=meta_value&order=DESC');
        if (!empty($users)) : 
            foreach ($users as $key => $user) {
                return $user->ID;
            }
        endif;
        return 0;
    }
}

if(!function_exists('is_most_helpful_user_id')){
    /**
    * Is the id of the most helpful user
    * @pluggable
    * @param (String) $user_id The test user ID
    * @return (Boolean) True when user ID matches most helpful user ID
    */
    function is_most_helpful_user_id($user_id){
        $most_helpful_user_id = get_most_helpful_user_id();
        return $most_helpful_user_id == $user_id;
    }
}

if(!function_exists('display_is_most_helpful_user')){
    /**
    * Is the id of the most helpful user
    * @pluggable
    * @param (String) $user_id The test user ID
    */
    function display_is_most_helpful_user($user_id){
        if( is_most_helpful_user_id( $user_id ) ){ ?>
            <span class="ht-kb-most-helpful-user"><?php _e('Most Helpful User', 'ht-knowledge-base'); ?></span>
        <?php   }
    }
}