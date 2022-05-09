<?php
/**
 * Compat template for displaying heroic knowledgebase single item content
 *
 * @package hkb-templates/
 */

?>

<div class="hkb-singletemp">

	<?php
	while ( have_posts() ) :
		the_post();
		?>

		<div class="hkb-article">

			<h1 class="hkb-article__title"><?php the_title(); ?></h1>

			<?php hkb_get_template_part( 'hkb-entry-content', 'single' ); ?>

			<?php hkb_get_template_part( 'hkb-single-attachments' ); ?>

			<?php // hkb_get_template_part('hkb-single-tags'); ?> 

			<?php do_action( 'ht_kb_end_article' ); ?>
						
			<?php hkb_get_template_part( 'hkb-single-author' ); ?>

			<?php hkb_get_template_part( 'hkb-single-related' ); ?>

			<?php hkb_get_template_part( 'hkb-comments' ); ?>

		</div>	

	<?php endwhile; ?>

</div>
