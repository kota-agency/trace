<?php
/**
 * Main template for archive display
 *
 * @package hkb-templates/
 */

?>

<?php get_header(); ?>

<!-- #ht-kb -->
<div id="hkb" class="hkb-template-archive">
	<div class="hkb-fullwcontainer">

		<?php hkb_get_template_part( 'hkb-header' ); ?>

			<div class="hkb-mainpage-wrapper">

				<div class="hkb-container">

					<div class="hkb-mainpage hkb-mainpage--sidebar<?php echo esc_attr( hkb_sidebar_archive_position() ); ?>">

						<div class="hkb-mainpage__main">
							<h2 class="hkb-archivetitle"><?php esc_html_e( 'Help Topics', 'ht-knowledge-base' ); ?></h2>
							<?php hkb_get_template_part( 'hkb-content', 'archive' ); ?>
						</div>

						<div class="hkb-mainpage__sidebar">
							<?php dynamic_sidebar( 'ht-kb-sidebar-archive' ); ?>
						</div>

					</div>

				</div>

			</div>

	</div>
</div>
<!-- /#ht-kb -->

<?php get_footer(); ?>
