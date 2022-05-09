<?php
/**
 * Main template for main search results
 *
 * @package hkb-templates/
 */

?>

<?php get_header(); ?>

<!-- #ht-kb -->
<div id="hkb" class="hkb-template-search">
	<div class="hkb-fullwcontainer">

		<?php hkb_get_template_part( 'hkb-subheader', 'search' ); ?>

		<div class="hkb-mainpage-wrapper">

			<div class="hkb-container">      

				<div class="hkb-mainpage hkb-mainpage--sidebaroff">

					<div class="hkb-mainpage__main">

						<?php if ( have_posts() ) : ?>

							<div class="hkb-article-grid">

								<?php
								while ( have_posts() ) :
									the_post();
									?>

									<?php hkb_get_template_part( 'hkb-content-article' ); ?>

								<?php endwhile; ?>

							</div>

							<?php hkb_posts_nav_link(); ?>

						<?php else : ?>

							<div class="hkb-search-noresults">
								<h2 class="hkb-search-noresults__title">
									<?php _e( 'No Results', 'ht-knowledge-base' ); ?>
								</h2>
								<p><?php printf( __( 'Your search for "%s" returned no results. Perhaps try something else?', 'ht-knowledge-base' ), get_search_query() ); ?></p>
							</div>

						<?php endif; ?>

					</div>

				</div>

			</div>

		</div>

	</div>
</div>
<!-- /#ht-kb -->

<?php get_footer(); ?>
