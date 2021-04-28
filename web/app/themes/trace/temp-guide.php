<?php
//Template Name: Guide
get_header();?>


<?php

/**
 * Block: Page Header
 */

$classes = ['full-width', 'tear-border', 'theme-secondary', 'bg-secondary',padding_classes()];

$header = get_field('page_header');
$label = $header['label'];
$title = $header['title'];
// $cta = $header['cta'];
$image = wp_get_attachment_image($header['image'], 'full');
$copy = $header['copy'];
$form_id = $header['form_id'];
$cta_label = $header['cta_label'];

?>

<section <?= block_id(); ?> class="page-header product-hero-mobile-position <?= implode(' ', $classes); ?>">
    <div class="container" data-aos="fade">
        <div class="row">
            <div class="col-12 col-md-6">
                <?php if ($label) : ?>
                    <h1><?= $label; ?></h1>
                <?php endif; ?>
                <?php if ($title) : ?>
                    <h6><?= $title; ?></h6>
                <?php endif; ?>

                 <?php if ($image) : ?>
                    <div class="page-header__image mobile-only">
                        <div data-aos="fade-down-left">
                            <?= $image; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if($form_id): ?>
                    <a 
                        data-gravity-form="<?php echo $form_id ?>" 
                        class="btn gated-file"
                        >
                        <?= $cta_label ?>
                    </a>
                <?php endif; ?>

                <?php /*if ($cta) : ?>
                    <a class="btn" href="<?= $cta['url'] ?>" target="<?= $cta['target'] ?>">
                        <?= $cta['title']; ?>
                    </a>
                <?php endif; */?>
            </div>
        </div>

        <?php if ($image) : ?>
            <div class="page-header__image">
                <div data-aos="fade-down-left">
                    <?= $image; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section><!-- .page-header -->

<?php $classes2 = ['full-width', 'block-copy', padding_classes()]; ?>
<section <?= block_id(); ?> class=" <?= implode(' ', $classes2); ?>">
    <div class="container" data-aos="fade">
        <div class="row">
            <div class="col-12 col-md-6">
                <?php if($copy): ?>
                    <?php echo $copy; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>



<?php get_footer();?>
