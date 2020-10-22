<?php
//Template Name: Contact
get_header();?>

<style>
	.page-header.page-header.page-header{
		padding-top: 0;
	}


</style>

<?php

/**
 * Block: Contact Details
 */

$classes = ['full-width', padding_classes()];
$address = get_field('address', 'options');
$address_link = get_field('address_link', 'options');
$label = get_field('label');
$title = get_field('title');
$image = wp_get_attachment_image(get_field('image'), 'full');

?>


<section <?= block_id(); ?> class="contact-details contact-hero-mobile-position <?= implode(' ', $classes); ?>">
    <?php if ($image) : ?>
        <div class="contact-details__image">
            <div data-aos="fade-down-left">
                <?= $image; ?>
            </div>
        </div>
    <?php endif; ?>
    <div class="contact-details__inner">
        <div class="container">
            <div class="row">
                <div class="col-12 contact-details__col header-title-pos">
                    <?php if ($label) : ?>
                        <h6 class="label"><?= $label; ?></h6>
                    <?php endif; ?>
                    <?php if ($title) : ?>
                        <h1><?= $title; ?></h1>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-lg-5 contact-details__col">
                    <div class="contact-details__content">
                        <h2><?= __('Call Us:', 'trace'); ?></h2>
                        <?php if (have_rows('numbers', 'options')) : ?>
                            <ul class="list-unstyled">
                                <?php while (have_rows('numbers', 'options')) : the_row(); ?>
                                    <?php

                                    $label = get_sub_field('label');
                                    $number = get_sub_field('number');

                                    ?>
                                    <li>
                                        <?php if ($label) : ?>
                                            <span class="d-block text-uppercase font-weight-black"><?= $label; ?></span>
                                        <?php endif; ?>
                                        <?php if ($number) : ?>
                                            <a href="<?= $number['url']; ?>" <?= $number['target'] ? 'target="_blank"' : ''; ?>><?= $number['title']; ?></a>
                                        <?php endif; ?>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6 col-lg-7 contact-details__col">
                    <div class="contact-details__content">
                        <h2><?= __('Visit Us:', 'trace'); ?></h2>
                        <?php if ($address) : ?>
                            <div class="copy-l">
                                <?= $address; ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($address_link) : ?>
                            <?php get_component('button', $address_link); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section><!-- .contact-details -->

<?php

/**
 * Block: Page Header
 */

$classes = ['full-width', padding_classes()];

// $label = get_field('label');
// $title = get_field('title');
// $image = wp_get_attachment_image(get_field('image'), 'full');
$copy = get_field('copy');
$form_id = get_field('form_id');

?>

<section <?= block_id(); ?> class="page-header <?= implode(' ', $classes); ?>">
	<div class="container" data-aos="fade">
		<?php if ($copy) : ?>
			<div class="row">
				<div class="col-lg-6 page-header__copy">
					<?= $copy; ?>
				</div>
			</div>
		<?php endif; ?>
		<?php if ($form_id) : ?>
            <h2><?= __('Message Us:', 'trace'); ?></h2>
			<div class="page-header__form">
				<?php gravity_form($form_id, false, false, false, null, true); ?>
			</div>
		<?php endif; ?>
	</div>
</section><!-- .page-header -->




<?php get_footer();?>
