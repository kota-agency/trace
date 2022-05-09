<?php
/**
 * Voting form template
 *
 * @package hkb-templates/
 */

?>

<?php
global $post_id, $voting_nonce, $feedback_nonce;

$new_vote            = ht_kb_voting_get_new_vote();
$votes               = ht_voting_get_post_votes( $post_id );
$allow_anon          = ht_kb_voting_enable_anonymous();
$vote_enabled_class  = ( ! $allow_anon && ! is_user_logged_in() ) ? 'disabled' : 'enabled';
$user_vote_direction = ht_kb_voting_get_users_post_vote_direction( $post_id );
?>

<?php if ( ! $allow_anon && ! is_user_logged_in() ) : ?>	
	<div class="voting-login-required" data-ht-voting-must-log-in-msg="<?php esc_html_e( 'You must log in to vote', 'ht-knowledge-base' ); ?>">
		<?php esc_html_e( 'You must log in to vote', 'ht-knowledge-base' ); ?>
	</div>
<?php endif; ?>

<div class="ht-voting-links ht-voting-<?php echo esc_attr( $user_vote_direction );  ?>">
	<a class="ht-voting-upvote <?php echo esc_attr( $vote_enabled_class ); ?> <?php echo ( 'up' == $user_vote_direction ) ? 'active' : ''; ?>" rel="nofollow" role="button" data-direction="up" data-type="post" data-nonce="<?php echo esc_attr( $voting_nonce ); ?>" data-id="<?php echo esc_attr( $post_id ); ?>" data-allow="<?php echo esc_attr( $allow_anon ); ?>" data-display="standard" href="<?php echo '#'; // $this->vote_post_link('up', $post_id, $allow); ?>">
		<span class="ht-voting-upvote-icon">
			<svg viewBox="0 0 16 16"><g fill="none" stroke="#444" stroke-linecap="round" stroke-linejoin="round"><path d="M0.5 7.5H3.5V15.5H0.5z"/><path d="M5.5,15.5h6.9a2,2,0,0,0,1.952-1.566l1.111-5A2,2,0,0,0,13.507,6.5H9.5v-4a2,2,0,0,0-2-2l-2,6"/></g></svg>
		</span>
		<span class="ht-voting-upvote-label"><?php esc_html_e( 'Yes', 'ht-knowledge-base' ); ?></span>
	</a>
	<a class="ht-voting-downvote <?php echo esc_attr( $vote_enabled_class ); ?> <?php echo ( 'down' == $user_vote_direction ) ? 'active' : ''; ?>" rel="nofollow" role="button" data-direction="down" data-type="post" data-nonce="<?php echo esc_attr( $voting_nonce ); ?>" data-id="<?php echo esc_attr( $post_id ); ?>" data-allow="<?php echo esc_attr( $allow_anon ); ?>" data-display="standard" href="<?php echo '#'; // $this->vote_post_link('down', $post_id, $allow); ?>">
		<span class="ht-voting-downvote-icon">
			<svg viewBox="0 0 16 16"><g fill="none" stroke="#444" stroke-linecap="round" stroke-linejoin="round"><path d="M0.5 0.5H3.5V8.5H0.5z"/><path d="M5.5.5h6.9a2,2,0,0,1,1.952,1.566l1.111,5A2,2,0,0,1,13.507,9.5H9.5v4a2,2,0,0,1-2,2l-2-6"/></g></svg>
		</span>
		<span class="ht-voting-downvote-label"><?php esc_html_e( 'No', 'ht-knowledge-base' ); ?></span>
	</a>
</div>

<?php if ( empty( $new_vote ) ) : ?>
	<!-- no new vote -->
<?php elseif ( ht_kb_voting_show_feedback_form() ) : ?>
	<div class="ht-voting-comment <?php echo esc_attr( $vote_enabled_class ); ?>" data-nonce="<?php echo esc_attr( $feedback_nonce ); ?>"  data-vote-key="<?php echo esc_attr( $new_vote->key ); ?>" data-id="<?php echo esc_attr( $post_id ); ?>">
		<textarea class="ht-voting-comment__textarea" rows="4" cols="50" placeholder="<?php esc_html_e( 'Thanks for your feedback, add a comment here to help improve the article', 'ht-knowledge-base' ); ?>">
																								<?php
																								if ( isset( $new_vote->comments ) ) {
																									$new_vote->comments;}
																								?>
		</textarea>
		<?php do_action( 'ht_voting_before_submit_button' ); ?>
		<button class="ht-voting-comment__submit" type="button" role="button"><?php esc_html_e( 'Send Feedback', 'ht-knowledge-base' ); ?></button>
	</div>
<?php else : ?>
		<div class="ht-voting-thanks"><?php esc_html_e( 'Thanks for your feedback', 'ht-knowledge-base' ); ?></div>
<?php endif;// vote_key ?>
