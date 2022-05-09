<?php
/**
 * Template for displaying the tags
 *
 * @package hkb-templates/
 */

?>

<div class="hkb-article-tags">
	<?php echo get_the_term_list( $post->ID, 'ht_kb_tag', __( 'Tagged: ', 'ht-knowledge-base' ), '', '' ); ?>
</div>
