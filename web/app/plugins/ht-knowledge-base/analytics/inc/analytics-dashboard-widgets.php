<?php
/**
* Analytics module dashboard widgets
*
*/

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'HKB_Analytics_Dashboard_Widgets' )) {
	class HKB_Analytics_Dashboard_Widgets {
	
		function __construct() {
			add_action( 'wp_dashboard_setup' , array( $this, 'hkb_analytics_add_dashboard_widget' ));
			//admin Scripts
        	add_action( 'admin_enqueue_scripts' , array( $this, 'hkb_analytics_dashboard_widget_admin_scripts' ) );
		}

		function hkb_analytics_add_dashboard_widget(){
			wp_add_dashboard_widget(
				 'hkb_analytics_dashboard_widget',
				 __('Knowledge Base Stats', 'ht-knowledge-base' ),
				 array( $this, 'hkb_analytics_dashboard_widget_function' )
			);	
		}

		function hkb_analytics_dashboard_widget_function(){


				echo '<ul class="hkb-analytics-dash-widget">';
				$post_type = 'ht_kb';
				$num_posts = wp_count_posts( $post_type );
				if ( $num_posts && $num_posts->publish ) {
					$text = _n( '%s Published Article', '%s Published Articles', $num_posts->publish, 'ht-knowledge-base' );
					$text = sprintf( $text, number_format_i18n( $num_posts->publish ) );
					$post_type_object = get_post_type_object( $post_type );
					if ( $post_type_object && current_user_can( $post_type_object->cap->edit_posts ) ) {
						printf( '<li class="%1$s-count hkb-analytics-dash-widget-articles"><a href="edit.php?post_type=%1$s">%2$s</a></li>', $post_type, $text );
					} else {
						printf( '<li class="%1$s-count hkb-analytics-dash-widget-articles"><span>%2$s</span></li>', $post_type, $text );
					}
				}

				$term = 'ht_kb_category';
				$num_terms = wp_count_terms( $term );
				if ( $num_terms ) {
					$num_terms_text = _n( '%s Article Category', '%s Article Categories', $num_terms, 'ht-knowledge-base' );
					$num_terms_text = sprintf( $num_terms_text, number_format_i18n( $num_terms ) );
					//@todo - restrict access to manage terms capability (doesn't exist in WordPress)
					printf( '<li class="%1$s-count hkb-analytics-dash-widget-categories"><a href="edit-tags.php?taxonomy=%1$s&post_type=ht_kb">%2$s</a></li>', $term, $num_terms_text );
					
				}

				$total_article_views = HKB_Static_Stats::hkba_get_total_article_views();
				if ( $total_article_views ) {
					$total_article_views_text = __( '%s Total Article Views', 'ht-knowledge-base' );
					$total_article_views_text = sprintf( $total_article_views_text, number_format_i18n( $total_article_views ) );
					printf( '<li class="%1$s-views hkb-analytics-dash-widget-views"><a href="edit.php?post_type=ht_kb&page=hkb-analytics">%2$s</a></li>', 'feedback-stats', $total_article_views_text );
					
				}

				$feedback_score = HKB_Static_Stats::hkba_get_feedback_overview();
				//feedback score in dashboard widget not yet implemented
				if ( false && $feedback_score ) {
					$feedback_score_text = __( '%s Satisfaction Rating', 'ht-knowledge-base' );
					$feedback_score_text = sprintf( $feedback_score_text, number_format_i18n( $feedback_score ) );
					printf( '<li class="%1$s-scores hkb-analytics-dash-widget-feedback"><a href="edit.php?post_type=ht_kb&page=hkb-analytics">%2$s</a></li>', 'feedback-stats', $feedback_score_text );
					
				}
				echo '</ul>';
				
				if( apply_filters( 'hkb_analytics_dashboard_widget_show_kb_archive_link' , true ) ){
					printf( '<a class="%1$s-item hkb-analytics-dash-widget-archive-link" href="%2$s">%3$s</a>', 'hkb-archive-link', get_permalink( ht_kb_get_kb_archive_page_id( 'default' ) ), __( 'View Knowledge Base', 'ht-knowledge-base' ) );
				}
		}

		//scripts
		function hkb_analytics_dashboard_widget_admin_scripts() {
			$screen = get_current_screen();

			//enqueue scripts
			if( is_a( $screen, 'WP_Screen' ) && 'dashboard' ===  $screen->base ) {
				wp_enqueue_style( 'analytics-admin-dashboard-widgets-style', plugins_url('/css/analytics-dashboard-widgets.css', dirname( __FILE__ ) ), array(), HT_KB_VERSION_NUMBER );
			}
		}

		
	}
}

if( class_exists( 'HKB_Analytics_Dashboard_Widgets' )) {
	//init
	$hkb_analytics_dashboard_widgets_init = new HKB_Analytics_Dashboard_Widgets();
}