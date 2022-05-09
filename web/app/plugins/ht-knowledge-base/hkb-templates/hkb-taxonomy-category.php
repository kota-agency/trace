<?php
/**
 * Main template for kb category display
 *
 * @package hkb-templates/
 */

?>

<?php get_header(); ?>

<!-- #ht-kb -->
<div id="hkb" class="hkb-template-category">
<div class="hkb-fullwcontainer">

	<?php hkb_get_template_part( 'hkb-subheader', 'category' ); ?>

	<div class="hkb-mainpage-wrapper">

		<div class="hkb-container">

			<div class="hkb-mainpage hkb-mainpage--sidebar<?php echo hkb_sidebar_category_position(); ?>">

				<div class="hkb-mainpage__main">
					<?php hkb_get_template_part( 'hkb-content', 'taxonomy' ); ?>
				</div>

				<div class="hkb-mainpage__sidebar">
					<?php dynamic_sidebar( 'ht-kb-sidebar-taxonomy' ); ?>
				</div>

			</div>

		</div>

	</div>

</div>
</div>
<!-- /#ht-kb -->

<?php get_footer(); ?>
