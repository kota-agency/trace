<?php
/**
 * Article item in a list
 *
 * @package hkb-templates/
 */

?>

<div id="post-<?php the_ID(); ?>" class="hkb-articlepreview">

	<h2 class="hkb-articlepreview__title">
		<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
	</h2>
	<?php if ( hkb_show_taxonomy_article_excerpt() && hkb_get_the_excerpt() ) : ?>
		<div class="hkb-articlepreview__excerpt"><?php hkb_the_excerpt(); ?></div>
	<?php endif; ?>

</div>
