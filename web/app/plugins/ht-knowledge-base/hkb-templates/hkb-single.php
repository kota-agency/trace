<?php
/**
 * Main template for single knowledge base article page
 *
 * @package hkb-templates/
 */

?>

<?php get_header(); ?>

<!-- #ht-kb -->
<div id="hkb" class="hkb-template-single">
	<div class="hkb-fullwcontainer">

		<?php hkb_get_template_part( 'hkb-subheader', 'single' ); ?>

		<div class="hkb-mainpage-wrapper">

			<div class="hkb-container">			

				<div class="hkb-mainpage hkb-mainpage--sidebar<?php echo hkb_sidebar_article_position(); ?>">

					<div class="hkb-mainpage__main">
						<?php hkb_get_template_part( 'hkb-content', 'single' ); ?>
					</div>

					<div class="hkb-mainpage__sidebar">
						<?php if ( hkb_sidebar_sticky() == true ) : ?>
							<div class="hkb-sticky">
						<?php endif; ?>
							<?php dynamic_sidebar( 'ht-kb-sidebar-article' ); ?>
						<?php if ( hkb_sidebar_sticky() == true ) : ?>
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
