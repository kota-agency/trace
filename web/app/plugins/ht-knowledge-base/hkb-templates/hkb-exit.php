<?php
/**
 * Template for displaying exit options at the end of articles
 *
 * @package hkb-templates/
 */

// Load variables, these can be overwritten here or in the template below.
$url        = ht_kb_exit_url_option();
$new_window = ht_kb_exit_new_window_option() ? 'target="_blank"' : '';
?>

<h2 class="hkb-exits">
	<?php esc_html_e( 'Not the solution you were looking for?', 'ht-knowledge-base' ); ?>
</h2>

<div class="hkb-exit-shortcode-text">
	<?php esc_html_e( 'You can submit a ticket in our help center', 'ht-knowledge-base' ); ?>
</div>

<a class="hkb-exit-shortcode-button button" href="<?php echo apply_filters( HKB_EXITS_URL_FILTER_TAG, $url, 'end' ); ?>" <?php echo esc_attr( $new_window ); ?> > 
	<?php esc_html_e( 'Submit Ticket', 'ht-knowledge-base' ); ?>
</a>
