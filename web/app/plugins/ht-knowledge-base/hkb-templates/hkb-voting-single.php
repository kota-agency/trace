<?php
/**
 * Template for voting actions
 *
 * @package hkb-templates/
 */

?>

<div class="hkb-feedback">
	<div class="hkb-feedback__title"><?php esc_html_e( 'Was this article helpful?', 'ht-knowledge-base' ); ?></div>
	<?php do_action( 'ht_voting_post' ); ?>
</div>
