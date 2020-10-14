<?php

/**
 * Block: Content Modals
 */

$classes = ['full-width', 'tear-border', 'theme-secondary', 'bg-secondary'];

$heading = get_field('heading');
$background_text = get_field('background_text');
$graphics = get_field('graphics');

?>

<section <?= block_id(); ?> class="content-modals <?= implode(' ', $classes); ?>">
	<div class="container">
		<?php if ($background_text) : ?>
			<h2 class="background-text background-text--large"><?= $background_text; ?></h2>
		<?php endif; ?>
		<?php if ($heading) : ?>
			<div class="row">
				<div class="col-md-6" data-aos="fade-up">
					<h2><?= $heading; ?></h2>
				</div>
			</div>
		<?php endif; ?>
		<?php if (have_rows('items')) : $i = 0;
			$j = 0; ?>
			<div class="content-modals__items row" data-aos="fade-up">
				<div class="content-modals__column col-md-6">
					<?php while (have_rows('items')) : the_row(); ?>
						<?php if ($i % 2 === 0) : ?>
							<?php

							$item_heading = get_sub_field('heading');
							$snippet = get_sub_field('snippet');

							?>
							<div class="content-modals__item" data-item="<?= $i; ?>">
								<?php if ($heading) : ?>
									<span class="d-block font-weight-demi"><?= $item_heading; ?></span>
								<?php endif; ?>
								<?php if ($snippet) : ?>
									<div class="last-margin">
										<?= $snippet; ?>
									</div>
								<?php endif; ?>
								<div class="btn-wrap">
									<span class="link"><?= __('Discover more'); ?></span>
								</div>

							</div>
						<?php endif; ?>
						<?php $i++; endwhile; ?>
				</div>
				<div class="content-modals__column col-md-6">
					<?php while (have_rows('items')) : the_row(); ?>
						<?php if ($j % 2 !== 0) : ?>
							<?php

							$heading = get_sub_field('heading');
							$snippet = get_sub_field('snippet');

							?>
							<div class="content-modals__item" data-item="<?= $j; ?>">
								<?php if ($heading) : ?>
									<span class="d-block font-weight-demi"><?= $heading; ?></span>
								<?php endif; ?>
								<?php if ($snippet) : ?>
									<div class="last-margin">
										<?= $snippet; ?>
									</div>
								<?php endif; ?>
								<div class="btn-wrap">
									<span class="link"><?= __('Discover more'); ?></span>
								</div>
								<?php get_component('modal'); ?>
							</div>
						<?php endif; ?>
						<?php $j++; endwhile; ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ($graphics) : ?>
			<div class="content-modals__graphics">
				<?php foreach ($graphics as $graphic) : ?>
					<div class="content-modals__graphic">
						<div data-aos="fade">
							<img src="<?= $graphic['url']; ?>" alt="<?= $graphic['alt']; ?>">
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section><!-- .content-modals -->
<?php if (have_rows('items')) : ?>
	<div class="content-modals__modals">
		<?php while (have_rows('items')) : the_row(); ?>
			<?php $modal_heading = get_sub_field('heading'); ?>
			<?php get_component('modal', $modal_heading); ?>
		<?php endwhile; ?>
	</div>
<?php endif; ?>
