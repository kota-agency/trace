<?php
/**
 * Template for display of subcategories
 *
 * @package hkb-templates/
 */

?>

<?php $subcategories = hkb_get_subcategories(); ?>
<?php if ( $subcategories && ( hkb_archive_display_subcategories() || is_tax( 'ht_kb_category' ) ) ) : ?>

	<!--.hkb-subcats-->
	<?php $columns = 'two'; ?>
	<div class="hkb-subcats hkb-subcats--<?php echo $columns; ?>-cols">
		<?php foreach ( $subcategories as $term ) : ?>

				<?php
					$hkb_current_term_id    = $term->term_id;
					$hkb_current_term_class = apply_filters( 'hkb_current_term_class_prefix', 'hkb-category--', 'subcategories' ) . $hkb_current_term_id;
					$hkb_current_term_class = apply_filters( 'hkb_current_term_class', $hkb_current_term_class, $hkb_current_term_id );
				?>
				
				<div class="hkb-categoryhead">
					<?php if ( hkb_has_category_custom_icon( $hkb_current_term_id ) ) : ?>
						<div class="hkb-categoryhead__icon" data-hkb-cat-icon="<?php echo hkb_has_category_custom_icon( $hkb_current_term_id ); ?>">
						  <?php hkb_category_thumb_img( $hkb_current_term_id ); ?>
						</div>
					<?php endif; ?>

					<div>
						<div class="hkb-categoryhead__content">
							<h2 class="hkb-categoryhead__title">
								<a class="hkb-subcats__cat-title <?php echo $hkb_current_term_class; ?>" href="<?php echo esc_attr( get_term_link( $term, 'ht_kb_category' ) ); ?>"><?php echo $term->name; ?></a
							></h2>                    
					
							<?php if ( hkb_archive_display_subcategory_count() ) : ?>
								<span class="hkb-categoryhead__count"><?php echo sprintf( _n( '1 Article', '%s Articles', $term->count, 'ht-knowledge-base' ), $term->count ); ?></span>
							<?php endif; ?>
						</div>

						<?php $ht_kb_tax_desc = $term->description; ?>
						<?php if ( ! empty( $ht_kb_tax_desc ) ) : ?>
							<p class="hkb-categoryhead__description"><?php echo $ht_kb_tax_desc; ?></p>
						<?php endif; ?>
					</div>

			</div> <!--  /.hkb-categoryhead -->

		<?php endforeach; ?>
	</div>
	<!--/.hkb-subcats-->
<?php endif; ?>
