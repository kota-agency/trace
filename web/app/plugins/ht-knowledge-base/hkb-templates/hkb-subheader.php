<?php
/**
 * Template for knowledge base sub header
 *
 * @package hkb-templates/
 */

?>

<?php $hkb_header_style = hkb_get_knowledgebase_header_style(); ?>
  
<?php if ( $hkb_header_style == 'image' ) : ?>

	<div class="hkb-subheader" data-hkb-header-type="<?php echo hkb_get_knowledgebase_header_style(); ?>" style="background-image: url(<?php echo hkb_get_knowledgebase_header_background_image_attachment_src_url(); ?>);">

<?php else : ?>

	<div class="hkb-subheader" data-hkb-header-type="<?php echo hkb_get_knowledgebase_header_style(); ?>">

<?php endif; ?> 

	<div class="hkb-container">      

	<?php hkb_get_template_part( 'hkb-searchbox', 'single' ); ?>

	<?php hkb_get_template_part( 'hkb-breadcrumbs', 'single' ); ?>

	</div>

</div>
