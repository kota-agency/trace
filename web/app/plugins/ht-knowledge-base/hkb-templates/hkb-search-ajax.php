<?php
/**
 * Template for ajax search results
 *
 * @package hkb-templates/
 */

?>

<ul id="hkb" class="hkb-searchresults" role="listbox">
	<?php $total_results = 0; ?>
		<?php if ( have_posts() ) : ?>
				<?php $counter = 0; ?>
				<?php $total_results += (int) $wp_query->posts; ?>
					<?php
					while ( have_posts() && $counter < 10 ) :
						the_post();
						?>
							<li class="hkb-searchresults__article <?php hkb_post_type_class(); ?>" role="option">
								<a href="<?php the_permalink(); ?>">
									<span class="hkb-searchresults__title"><?php the_title(); ?></span>
									<?php if ( hkb_show_search_excerpt() && hkb_get_the_excerpt() ) : ?>
										<span class="hkb-searchresults__excerpt"><?php hkb_the_excerpt( get_search_query() ); ?></span>
									<?php endif; ?>
								</a>
							</li>
							<?php $counter++; ?>
					<?php endwhile; ?>
		<?php endif; ?>

		<?php if ( $total_results > 0 ) : ?>
			<li class="hkb-searchresults__showall" role="option">
				<a href="<?php echo apply_filters( 'hkb_search_url', $s ); ?>">
					<?php _e( 'Show all results', 'ht-knowledge-base' ); ?>
						
					</a> 
			</li>
		<?php else : ?>
			<li class="hkb-searchresults__noresults" role="option">
				<span>
					<?php _e( 'No Results', 'ht-knowledge-base' ); ?>						
				</span>
			</li>
		<?php endif; ?>
</ul>
