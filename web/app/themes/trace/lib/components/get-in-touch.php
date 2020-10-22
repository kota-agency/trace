<?php

$classes = ['full-width', 'text-center', padding_classes()];

$larger_text = get_field('larger_text', 'options');
$logo = get_field('logo', 'options');
$background_text = get_field('background_text', 'options');
$heading = get_field('heading', 'options');
$copy = get_field('copy', 'options');
$graphics = get_field('graphics', 'options');

if ($larger_text) {
    $classes[] = 'cta--larger';
}

?>

<section <?= block_id() ?> class="cta <?= implode(' ', $classes); ?>">
    <div class="container" data-aos="fade-up">
        <?php if ($logo) : ?>
            <div class="cta__logo">
                <img src="<?= $logo['url']; ?>" alt="<?= $logo['alt']; ?>">
            </div>
        <?php endif; ?>
        <div class="cta__heading-wrapper">
            <?php if ($heading) : ?>
                <?php if ($larger_text) : ?>
                    <h1><?= $heading; ?></h1>
                <?php else : ?>
                    <h2><?= $heading; ?></h2>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($background_text) : ?>
                <h2 class="background-text background-text--large"><?= $background_text; ?></h2>
            <?php endif; ?>
            <?php if ($graphics) : ?>
                <div class="cta__graphics">
                    <?php foreach ($graphics as $graphic) : ?>
                        <div class="cta__graphic">
                            <img src="<?= $graphic['url']; ?>" alt="<?= $graphic['alt']; ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php if ($copy) : ?>
            <div class="cta__copy <?= $larger_text ? 'copy--xxl' : ''; ?>">
                <?= $copy; ?>
            </div>
        <?php endif; ?>

        <?php if (have_rows('links', 'options')) : ?>
            <div class="buttons">
                <?php while (have_rows('links', 'options')) : the_row(); ?>
                    <?php

                    $button = get_sub_field('link', 'options');
                    $link_type = get_sub_field('style', 'options');



                    if ($button) {
                        switch($link_type) {
                            case "Button":
                                get_component('button', $button);
                                break;
                            case "Link":
                                get_component('link', $button);
                                break;
                            case "Video Link":
                                $button['attr'] = 'data-fancybox';
                                $button['classes'] = 'link--video';
                                $button['icon'] = '<i class="far fa-play-circle"></i>';
                                get_component('link', $button);
                                break;
                            default:
                                get_component('button', $button);
                        }

                    }

                    ?>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

    </div>
</section>
