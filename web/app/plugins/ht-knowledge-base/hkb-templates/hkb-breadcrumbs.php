<?php
/**
 * Breadcrumbs template
 *
 * @package hkb-templates/
 */

?>

<?php if ( hkb_show_knowledgebase_breadcrumbs() ) : ?>
<div class="hkb-breadcrumbs-wrap">
<!-- .hkb-breadcrumbs -->
	<?php $breadcrumbs_paths = ht_kb_get_ancestors(); ?>
	<?php foreach ( $breadcrumbs_paths as $index => $paths ) : ?>
		<ol class="hkb-breadcrumbs" <?php ht_echo_schema( false, true, 'BreadcrumbList' ); ?> >
			<?php $last_item_index = count( $paths ) - 1; ?>
			<?php foreach ( $paths as $key => $component ) : ?>
				<li <?php ht_echo_schema( 'itemListElement', true, 'ListItem' ); ?>>
					<?php if ( $key == $last_item_index || empty( $component['link'] ) ) : ?>
						<span>
							<span <?php ht_echo_schema( 'name', false, false ); ?> ><?php echo esc_html( $component['label'] ); ?></span>
							<link <?php ht_echo_schema( 'item', false, false ); ?> href="<?php the_permalink(); ?>" />
						</span> 
					<?php else : ?>
						<a <?php ht_echo_schema( 'item', false, false ); ?> href="<?php echo esc_html( $component['link'] ); ?>">
							<span <?php ht_echo_schema( 'name', false, false ); ?> ><?php echo esc_html( $component['label'] ); ?></span>
						</a>
					<?php endif; ?>
					<meta <?php ht_echo_schema( 'position', false, false ); ?> content="<?php echo esc_attr( $key + 1 ); ?>" />
				</li>               
			<?php endforeach; ?>
		</ol>
	<?php endforeach; ?>
<!-- /.hkb-breadcrumbs -->
</div>
<?php endif; ?>
