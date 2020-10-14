<?php

/**
 * Block: Split Content
 */

$classes = ['full-width', padding_classes()];

?>
<?php if (have_rows('rows')) : $i = 0; ?>
	<section <?= block_id(); ?> class="split-content <?= implode(' ', $classes); ?>">

		<?php while (have_rows('rows')) : the_row(); ?>
			<?php

			$heading = get_sub_field('heading');
			$copy = get_sub_field('copy');
			$image = wp_get_attachment_image(get_sub_field('image'), 'full');

			?>
			<div class="split-content__item <?= $i % 2 === 0 ? 'tear-border bg-secondary theme-secondary' : ''; ?>" data-aos="fade-up">
				<div class="container">
					<div class="row align-items-center">
						<div class="col-lg-6 align-self-stretch">
							<?php if ($image) : ?>
								<div class="split-content__image">
									<?= $image; ?>
								</div>
							<?php endif; ?>
						</div>
						<div class="col-lg-6">
							<div class="split-content__content">
								<?php if ($heading) : ?>
									<h3><?= $heading; ?></h3>
								<?php endif; ?>
								<?php if ($copy) : ?>
									<div>
										<?= $copy; ?>
									</div>
								<?php endif; ?>
								<div class="btn-wrap">
									<a href="#" class="btn"><?= __('Find out more', 'trace'); ?></a>
								</div>
							</div>

						</div>
					</div>
				</div>
			</div>
			<?php $i++; endwhile; ?>

	</section><!-- .split-content -->
	<div class="split-content__modals">
		<?php while (have_rows('rows')) : the_row(); ?>
			<?php

			/**
			 * Component: Modal
			 */


			$title = get_query_var('data') ? get_query_var('data') : get_mixed_field('heading');


			?>
			<?php if (have_rows('modal')) : ?>
				<?php while (have_rows('modal')) : the_row(); ?>
					<?php

                    $id = get_sub_field('id');
					$subtitle = get_sub_field('subtitle');

					?>
					<div <?= $id ? 'id="' . $id . '"' : ''; ?> class="modal theme-secondary">
						<div class="modal__inner">
							<div class="modal__close"></div>
							<?php if ($title) : ?>
								<h3 class="modal__heading"><?= $title; ?></h3>
							<?php endif; ?>
							<?php if ($subtitle) : ?>
								<h5 class="text-tertiary modal__subheading text-uppercase"><?= $subtitle; ?></h5>
							<?php endif; ?>
							<?php if (have_rows('columns')) : ?>
								<div class="row">
									<?php while (have_rows('columns')) : the_row(); ?>
										<?php $copy = get_sub_field('copy'); ?>
										<?php if ($copy) : ?>
											<div class="col-md-6">
												<div>
													<?= $copy; ?>
												</div>
											</div>
										<?php endif; ?>
									<?php endwhile; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endwhile; ?>
			<?php endif; ?>
		<?php endwhile; ?>
	</div>
<?php endif; ?>


