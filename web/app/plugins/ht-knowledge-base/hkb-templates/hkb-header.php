<?php
/**
 * Template for the header
 *
 * @package hkb-templates/
 */

?>


<?php $hkb_header_style = hkb_get_knowledgebase_header_style(); ?>

<?php if ( $hkb_header_style == 'image' ) : ?>
	<div class="hkb-searchbox-wrapper" data-hkb-header-type="<?php echo hkb_get_knowledgebase_header_style(); ?>" style="background-image: url(<?php echo hkb_get_knowledgebase_header_background_image_attachment_src_url(); ?>);">
<?php else : ?>
	<div class="hkb-searchbox-wrapper" data-hkb-header-type="<?php echo hkb_get_knowledgebase_header_style(); ?>">
<?php endif; ?> 

	<div class="hkb-container">

		<div class="hkb-searchbox hkb-searchbox--center">

			<h1 class="hkb-searchbox__title"><?php echo hkb_get_knowledgebase_archive_header_text(); ?></h1>

			<?php hkb_get_template_part( 'hkb-searchbox', 'archive' ); ?>

		</div>

	</div>

</div>
