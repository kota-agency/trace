<?php global $hkb_current_term_id; ?>

<?php $tax_terms = hkb_get_archive_tax_terms(); ?>
<?php $ht_kb_category_count = count($tax_terms); ?>
<?php $columns = hkb_archive_columns_string(); ?>
<?php $cat_counter = 0; ?>

<!-- .hkb-archive -->
<div class="hkb-archive hkb-archive--<?php echo $columns; ?>-cols">
	<?php foreach ($tax_terms as $key => $tax_term): ?>
	<?php 
		//set hkb_current_term_id
		$hkb_current_term_id = $tax_term->term_id;
		$hkb_current_term_class = apply_filters( 'hkb_current_term_class_prefix', 'hkb-category--', 'archive' ) . $hkb_current_term_id;
		$hkb_current_term_class = apply_filters( 'hkb_current_term_class', $hkb_current_term_class, $hkb_current_term_id );
		$ht_kb_tax_desc =  $tax_term->description;
		if( !empty($ht_kb_tax_desc) ): 
			$ht_kb_cat_desc_data_attr = 'true';
		else :
			$ht_kb_cat_desc_data_attr = 'false';
		endif;

		$ht_kb_tax_icon =  hkb_has_category_custom_icon( $hkb_current_term_id );
		if( !empty($ht_kb_tax_icon) ): 
			$ht_kb_tax_icon = 'true';
		else :
			$ht_kb_tax_icon = 'false';
		endif;

	?>

		<div class="hkb-category <?php echo $hkb_current_term_class; ?>" data-hkb-cat-icon="<?php echo $ht_kb_tax_icon; ?>" data-hkb-cat-desc="<?php echo $ht_kb_cat_desc_data_attr; ?>">
		<div class="hkb-categoryhead">
			<?php if( hkb_has_category_custom_icon( $hkb_current_term_id ) ) : ?>
				<div class="hkb-categoryhead__icon">
					<?php hkb_category_thumb_img( $hkb_current_term_id ); ?>
				</div>
			<?php endif; ?>
			<div>
				<div class="hkb-categoryhead__content">
					<h2 class="hkb-categoryhead__title">
						<a href="<?php echo esc_attr(get_term_link($tax_term, 'ht_kb_category')) ?>"><?php echo $tax_term->name ?></a>
					</h2>

					<?php if ( hkb_archive_display_subcategory_count() ) : ?>
						<span class="hkb-categoryhead__count"><?php echo sprintf( _n( '1 Article', '%s Articles', $tax_term->count, 'ht-knowledge-base' ), $tax_term->count ); ?></span>
					<?php endif; ?>

				</div>

				<?php if( !empty($ht_kb_tax_desc) ): ?>
					<p class="hkb-categoryhead__description"><?php echo $ht_kb_tax_desc ?></p>
				<?php endif; ?>
				
			</div>
		</div>

		<?php $cat_posts = hkb_get_archive_articles($tax_term, null, null, 'kb_home'); ?>

		<?php if( !empty( $cat_posts ) && !is_a( $cat_posts, 'WP_Error' ) ): ?>

			<ul class="hkb-article-list">
				<?php foreach( $cat_posts as $post ) : ?>                            
					<li>
						<a href="<?php echo get_permalink($post->ID); ?>"><?php echo get_the_title($post->ID); ?></a>
					</li>
				<?php endforeach; ?>
			</ul>

			<a class="hkb-category__view-all" href="<?php echo esc_attr(get_term_link($tax_term, 'ht_kb_category')) ?>"><?php _e( 'View all', 'ht-knowledge-base' ); ?></a>

		<?php endif; ?>
		
		</div>

	<?php endforeach; ?>
</div> 
<!-- /.hkb-archive -->

<?php $uncat_posts = hkb_get_uncategorized_articles(); ?>
<?php if( !empty( $uncat_posts ) && !is_a( $uncat_posts, 'WP_Error' ) ): ?>
	<div class="hkb-uncatlist">
		<div class="hkb-category">
			<div class="hkb-categoryhead">
				<h2 class="hkb-categoryhead__title">
					<?php _e( 'Uncategorized', 'ht-knowledge-base'); ?>
				</h2>
			</div>
			<ul class="hkb-article-list">
				<?php foreach( $uncat_posts as $post ) : ?>                            
						<li class="hkb-article-list__<?php hkb_post_format_class($post->ID); ?>">
							<a href="<?php echo get_permalink($post->ID); ?>"><?php echo get_the_title($post->ID); ?></a>
						</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
<?php endif; //uncat posts ?>


<?php $no_public_posts = hkb_no_public_posts(); ?>
<?php if( $no_public_posts ): ?>
	<div class="hkb-category">
		<div class="hkb-category__header">
			<h2 class="hkb-category__title">
				<?php _e('There are no published articles... yet', 'ht-knowledge-base'); ?>
			</h2>
		</div>
	</div>
<?php endif; //uncat posts ?>