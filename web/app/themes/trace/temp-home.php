<?php
//Template Name: Home
get_header(); ?>

<?php

/**
 * Block: Hero
 */

$classes = ['full-width', 'theme-primary', 'bg-primary', padding_classes()];

$title = get_field('title');
$background_text = get_field('background_text');
$video = get_field('hero_video', false, false);

$image = wp_get_attachment_image(get_field('image'), 'full');
?>

<section class="hero home-hero-mobile-position <?= implode(' ', $classes); ?>">
    <div class="container">
        <?php if ($background_text) : ?>
            <h2 class="background-text"><?= $background_text; ?></h2>
        <?php endif; ?>
        <div class="row">
            <div class="col-md-9">

                <div class="hero__content" data-aos="fade-up">
                    <?php if ($title) : ?>
                        <h1 class="text-uppercase"><?= str_replace(['<p>', '</p>'], '', $title); ?></h1>
                    <?php endif; ?>

                </div>
            </div>
        </div>
        <?php if ($image) : ?>

            <div class="hero__image">
                <div data-aos="fade-left" data-aos-delay="500">
                    <?= $image; ?>
                    <?php if($video): ?>
                        <a href="<?= $video ?>" data-fancybox>
                            <span class="play"></span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

        <?php endif; ?>
    </div>
</section>


<?php

/**
 * Block: Image & Content
 */

$classes2 = ['full-width', padding_classes()];

$wide_heading2 = get_field('image_&_content_wide_heading');
$heading2 = get_field('image_&_content_heading');
$image2 = wp_get_attachment_image(get_field('image_&_content_image'), 'full');
$mobile_image2 = wp_get_attachment_image(get_field('image_&_content_image_mobile'), 'full');
$copy2 = get_field('image_&_content_copy');

if ($wide_heading2) {
    $classes2[] = 'image-content--wide-heading';
}

?>

<section class="image-content <?= implode(' ', $classes2); ?>">
    <div class="image-content__inner">
        <div class="container">
            <?php if ($mobile_image2) : ?>
                <div class="image-content__image d-md-none">
                    <div data-aos="fade-up">
                        <?= $mobile_image2; ?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="row">
                <div class="<?= $wide_heading2 ? 'col-md-9' : 'col-md-6'; ?> position-static">
                    <?php if ($heading2) : ?>
                        <h2 data-aos="fade"><?= $heading2; ?></h2>
                    <?php endif; ?>
                    <?php if ($image2) : ?>
                        <div class="image-content__image image-content__image--desktop d-none d-md-block">
                            <div data-aos="fade-right">
                                <?= $image2; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
            <div class="row">
                <div class="offset-md-7 col-md-5">
                    <div class="image-content__content" data-aos="fade-up">
                        <?php if ($copy2) : ?>
                            <div class="image-content__copy">
                                <?= $copy2; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (have_rows('image_&_content_icon_list')) : ?>
                            <div class="icon-list">
                                <ul>
                                    <?php while (have_rows('image_&_content_icon_list')) : the_row(); ?>
                                        <?php

                                        $icon = get_sub_field('icon');
                                        $text = get_sub_field('text');
                                        $link = get_sub_field('link');

                                        ?>
                                        <li class="icon-list__item <?= !$icon ? 'icon-list__item--no-icon' : ''; ?>">
                                            <div>
                                                <?= $link ? '<a href="' . $link['url'] . '" ' . $link['target'] . '>' : ''; ?>
                                                <?php if ($icon) : ?>
                                                    <span><?= $icon; ?></span>
                                                <?php endif; ?>
                                                <?php if ($text) : ?>
                                                    <span><?= $text; ?></span>
                                                <?php endif; ?>
                                                <?= $link ? '</a>' : ''; ?>
                                            </div>
                                        </li>
                                    <?php endwhile; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if (have_rows('links_links')) : ?>
                            <div class="buttons">
                                <?php while (have_rows('links_links')) : the_row(); ?>
                                    <?php

                                    $button = get_sub_field('link');
                                    $link_type = get_sub_field('style');


                                    if ($button) {
                                        switch ($link_type) {
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
                </div>
            </div>
        </div>
    </div>
</section><!-- .image-content -->

<?php

/**
 * Block: Video
 */

$classes5 = ['full-width', padding_classes()];

$embed5 = get_field('video_vimeo_embed');
$placeholder5 = wp_get_attachment_image_url(get_field('video_placeholder'), 'full');

?>

<?php if ($embed5 && $placeholder5) : ?>
    <section <?= block_id(); ?> class="video <?= implode('', $classes5); ?>" data-aos="trigger" data-aos-delay="1000"
                                data-aos-offset="500">
        <div class="container">
            <div class="video__wrapper">
                <?= $embed5; ?>
                <div class="video__image bg-cover" style="background-image: url(<?= $placeholder5; ?>);">
                    <span class="play"></span>
                </div>
            </div>
        </div>

        <?php if (have_rows('links_v_links')) : ?>
            <div class="buttons">
                <?php while (have_rows('links_v_links')) : the_row(); ?>
                    <?php

                    $button = get_sub_field('link');
                    $link_type = get_sub_field('style');

                    if ($button) {
                        switch ($link_type) {
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
    </section><!-- .video -->
<?php endif; ?>


<?php

/**
 * Block: Testimonials
 */

$classes6 = ['full-width', padding_classes()];

?>

<section class="testimonials <?= implode(' ', $classes6); ?>" data-aos="zoom-in">
    <?php if (have_rows('testimonials_items')) : ?>
        <div class="testimonials__items">
            <?php while (have_rows('testimonials_items')) : the_row(); ?>
                <?php

                $text = get_sub_field('text');
                $author = get_sub_field('author');
                $company = get_sub_field('company');

                ?>
                <div class="testimonials__item">
                    <?php if ($text) : ?>
                        <h2><?= $text; ?></h2>
                    <?php endif; ?>
                    <div class="container">
                        <?php if ($author) : ?>
                            <strong><?= $author; ?><?= ($author && $company) ? ',' : ''; ?></strong>
                        <?php endif; ?>
                        <?php if ($company) : ?>
                            <span><?= $company; ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <div class="testimonials__arrows"></div>
    <?php endif; ?>
</section><!-- .testimonials -->

<?php

/**
 * Block: Family
 */

$classes3 = ['full-width', padding_classes()];

$heading3 = get_field('family_heading');
$background_text3 = get_field('family_background_text');
$logos3 = get_field('family_logos');

?>

<section class="family <?= implode(' ', $classes3); ?>">
    <div class="container">
        <div class="family__wrapper" data-aos="fade-up">
            <?php if ($heading3) : ?>
                <h2 class="no-margin"><?= $heading3; ?></h2>
            <?php endif; ?>
            <?php if ($background_text3) : ?>
                <h2 class="background-text background-text--large"><?= $background_text3; ?></h2>
            <?php endif; ?>

            <?php if ($logos3) : ?>
                <div class="family__logos">
                <?php $index = 0; ?>
                    <?php foreach ($logos3 as $logo_id) : ?>
                        <?php $logo = wp_get_attachment_image($logo_id, 'logo'); ?>
                        <?php if ($logo) : ?>
                            <div class="
                            family__logo family__logo<?php echo $index ?>" data-logo-index="<?php echo $index ?>">
                                <?= $logo; ?>
                            </div>
                        <?php endif; ?>
                        <?php $index++; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="family__path">


            <!-- <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                viewBox="0 0 1395.4 558.7" xml:space="preserve">
            <path class="st0" id="path-anim" d="M698.9,0.5c0,0-698.4,15.3-698.4,279.8s698.4,277.9,698.4,277.9s696,1.6,696-277.9S698.9,0.5,698.9,0.5z"/>
            </svg> -->

            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                viewBox="0 0 1395.4 500.3" xml:space="preserve">
                <path class="st0" id="path-anim-1" d="M698.9,0.7c0,0-698.5-11.5-698.4,250.3c0.1,248.2,698.4,248.6,698.4,248.6s696.5,15.6,696-248.6
                C1394.3-16.8,698.9,0.7,698.9,0.7z"/>
            </svg>
            <!-- <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                viewBox="0 0 1395.4 500.3" xml:space="preserve">
                <path class="st0" id="path-anim-2" d="M698.9,0.7c0,0-698.5-11.5-698.4,250.3c0.1,248.2,698.4,248.6,698.4,248.6s696.5,15.6,696-248.6
                C1394.3-16.8,698.9,0.7,698.9,0.7z"/>
            </svg>
            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                viewBox="0 0 1395.4 500.3" xml:space="preserve">
                <path class="st0" id="path-anim-3" d="M698.9,0.7c0,0-698.5-11.5-698.4,250.3c0.1,248.2,698.4,248.6,698.4,248.6s696.5,15.6,696-248.6
                C1394.3-16.8,698.9,0.7,698.9,0.7z"/>
            </svg>
            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                viewBox="0 0 1395.4 500.3" xml:space="preserve">
                <path class="st0" id="path-anim-4" d="M698.9,0.7c0,0-698.5-11.5-698.4,250.3c0.1,248.2,698.4,248.6,698.4,248.6s696.5,15.6,696-248.6
                C1394.3-16.8,698.9,0.7,698.9,0.7z"/>
            </svg>
            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                viewBox="0 0 1395.4 500.3" xml:space="preserve">
                <path class="st0" id="path-anim-5" d="M698.9,0.7c0,0-698.5-11.5-698.4,250.3c0.1,248.2,698.4,248.6,698.4,248.6s696.5,15.6,696-248.6
                C1394.3-16.8,698.9,0.7,698.9,0.7z"/>
            </svg>
            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                viewBox="0 0 1395.4 500.3" xml:space="preserve">
                <path class="st0" id="path-anim-6" d="M698.9,0.7c0,0-698.5-11.5-698.4,250.3c0.1,248.2,698.4,248.6,698.4,248.6s696.5,15.6,696-248.6
                C1394.3-16.8,698.9,0.7,698.9,0.7z"/>
            </svg>
            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                viewBox="0 0 1395.4 500.3" xml:space="preserve">
                <path class="st0" id="path-anim-7" d="M698.9,0.7c0,0-698.5-11.5-698.4,250.3c0.1,248.2,698.4,248.6,698.4,248.6s696.5,15.6,696-248.6
                C1394.3-16.8,698.9,0.7,698.9,0.7z"/>
            </svg>
            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                viewBox="0 0 1395.4 500.3" xml:space="preserve">
                <path class="st0" id="path-anim-8" d="M698.9,0.7c0,0-698.5-11.5-698.4,250.3c0.1,248.2,698.4,248.6,698.4,248.6s696.5,15.6,696-248.6
                C1394.3-16.8,698.9,0.7,698.9,0.7z"/>
            </svg>
            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                viewBox="0 0 1395.4 500.3" xml:space="preserve">
                <path class="st0" id="path-anim-9" d="M698.9,0.7c0,0-698.5-11.5-698.4,250.3c0.1,248.2,698.4,248.6,698.4,248.6s696.5,15.6,696-248.6
                C1394.3-16.8,698.9,0.7,698.9,0.7z"/>
            </svg> -->
           <!-- <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                 viewBox="0 0 1395.4 500.3" style="enable-background:new 0 0 1395.4 500.3;" xml:space="preserve">
                <path id="path-anim-2" d="M710,18.6c0,0-687.4-11.1-687.3,241.3C22.8,499.2,710,499.6,710,499.6s685.4,15,684.9-239.7
                C1394.3,1.7,710,18.6,710,18.6z"/>
            </svg>
            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                 viewBox="0 0 1395.4 553.6" style="enable-background:new 0 0 1395.4 553.6;" xml:space="preserve">
                <path id="path-anim-3" d="M667.5,32c0,0-644.9-12-644.8,261.4c0.1,259.2,644.8,259.6,644.8,259.6s643,16.3,642.6-259.6
                    C1309.5,13.7,667.5,32,667.5,32z"/>
            </svg> -->

        </div>
    </div>
</section><!-- .family -->

<?php

/**
 * Block: Sectors
 */

$classes4 = ['full-width', padding_classes()];

$footnote4 = get_field('sectors_footnote');
$button_sector_cta = get_field('sectors_button');

?>

<section class="sectors <?= implode(' ', $classes4); ?>" data-aos="fade-up">
    <div class="container">
        <?php if ($footnote4) : ?>
            <div class="text-center sectors__footnote" data-aos="fade-up">
                <?= $footnote4; ?>
            </div>
        <?php endif; ?>
        <?php if (have_rows('sectors_items')) : ?>
            <div class="row justify-content-center">
                <?php while (have_rows('sectors_items')) : the_row(); ?>
                    <div class="col-md-6 col-lg-4 sectors__col">
                        <?php get_component('sector-tab'); ?>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
        <?php if ($button_sector_cta) : ?>
            <div class="text-center"  data-aos="fade-up">
                <?php get_component('button', $button_sector_cta); ?>
            </div>
        <?php endif; ?>
    </div>
</section><!-- .sectors -->


<?php

/**
 * Block: Content & Video
 */


$classses7 = ['content-video--mojo', 'full-width', 'theme-secondary', 'bg-secondary', 'tear-border', padding_classes()];

$heading7 = get_field('content_&_video_heading');
// $logo7 = get_field('content_&_video_logo');
$video_url7 = get_field('content_&_video_video_url');
$video_image7 = wp_get_attachment_image_url(get_field('content_&_video_video_image'), 'content_image');
$graphic7 = get_field('content_&_video_graphic');
$copy7 = get_field('content_&_video_copy');
$items7 = get_field('content_&_video_items');

?>

<section class="content-video <?= implode(' ', $classses7); ?>">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">

                <?php if ($video_image7 && $video_url7) : ?>
                    <div class="content-video__video d-none d-lg-block" data-aos="fade">
                        <a href="<?= $video_url7 ?>" data-fancybox>
                            <div class="content-video__image bg-cover"
                                 style="background-image: url(<?= $video_image7; ?>);">
                                <span class="play"></span>
                            </div>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-lg-6 pl-lg-6" data-aos="fade">

                <div class="" data-aos="fade">
                    <?php if ($heading7) : ?>
                        <h2 class="content-video__heading"><?= $heading7; ?></h2>
                    <?php endif; ?>   
                </div>
                
                <?php if ($logo7) : ?>
                    <div class="content-video__logo">
                        <img src="<?= $logo7['url']; ?>" alt="<?= $logo7['alt']; ?>">
                    </div>
                <?php endif; ?>

                <?php if ($copy7) : ?>
                    <div class="content-video__copy">
                        <?= $copy7; ?>
                    </div>
                <?php endif; ?>
                <?php if ($video_image7 && $video_url7) : ?>
                    <div class="content-video__video d-lg-none">
                        <a href="<?= $video_url7 ?>" data-fancybox>
                            <div class="content-video__image bg-cover"
                                 style="background-image: url(<?= $video_image7; ?>);">
                                <span class="play"></span>
                            </div>
                        </a>
                    </div>
                <?php endif; ?>

                <?php if(!empty($items7)): ?>
                    <ul class="content-video__list">
                        <?php foreach($items7 as $item): ?>
                            <li class="content-video__list-item">
                                <?php echo $item['text']; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <?php if (have_rows('links_c_links')) : ?>
                    <div class="buttons">
                        <?php $button_index = 1; ?>
                        <?php while (have_rows('links_c_links')) : the_row(); ?>
                            <?php

                            $button = get_sub_field('link');
                            $link_type = get_sub_field('style');

                            if ($button) {
                                switch ($link_type) {
                                    case "Button":

                                        if($button_index == 2) {
                                            $button['classes'] = 'btn-secondary';
                                        }
                                        get_component('button', $button);
                                        break;
                                    case "Link":
                                        if($button_index == 2) {
                                            $button['classes'] = 'btn-secondary';
                                        }
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
                            <?php $button_index++; ?>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($graphic7) : ?>
            <div class="content-video__graphic">
                <div data-aos="fade">
                    <img src="<?= $graphic7['url']; ?>" alt="<?= $graphic7['alt']; ?>">
                </div>
            </div>
        <?php endif; ?>
    </div>
</section><!-- .content-video -->


<?php

/**
 * Block: Image & Content
 */

$classes8 = ['full-width', padding_classes()];

$wide_heading8 = get_field('image_&_content2_wide_heading');
$heading8 = get_field('image_&_content2_heading');
$image8 = wp_get_attachment_image(get_field('image_&_content2_image'), 'full');
$mobile_image8 = wp_get_attachment_image(get_field('image_&_content2_image_mobile'), 'full');
$copy8 = get_field('image_&_content2_copy');

if ($wide_heading8) {
    $classes8[] = 'image-content--wide-heading';
}

?>

<section class="image-content <?= implode(' ', $classes8); ?>">
    <div class="image-content__inner">
        <div class="container">
            <?php if ($mobile_image8) : ?>
                <div class="image-content__image d-md-none">
                    <div data-aos="fade-up">
                        <?= $mobile_image8; ?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="row">
                <div class="<?= $wide_heading8 ? 'col-md-9' : 'col-md-6'; ?> position-static">
                    <?php if ($heading8) : ?>
                        <h2 data-aos="fade"><?= $heading8; ?></h2>
                    <?php endif; ?>
                    <?php if ($image8) : ?>
                        <div class="image-content__image image-content__image--desktop d-none d-md-block">
                            <div data-aos="fade-right">
                                <?= $image8; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
            <div class="row">
                <div class="offset-md-7 col-md-5">
                    <div class="image-content__content" data-aos="fade-up">
                        <?php if ($copy8) : ?>
                            <div class="image-content__copy">
                                <?= $copy8; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (have_rows('image_&_content2_icon_list')) : ?>
                            <div class="icon-list">
                                <ul>
                                    <?php while (have_rows('image_&_content2_icon_list')) : the_row(); ?>
                                        <?php

                                        $icon = get_sub_field('icon');
                                        $text = get_sub_field('text');
                                        $link = get_sub_field('link');

                                        ?>
                                        <li class="icon-list__item <?= !$icon ? 'icon-list__item--no-icon' : ''; ?>">
                                            <div>
                                                <?= $link ? '<a href="' . $link['url'] . '" ' . $link['target'] . '>' : ''; ?>
                                                <?php if ($icon) : ?>
                                                    <span><?= $icon; ?></span>
                                                <?php endif; ?>
                                                <?php if ($text) : ?>
                                                    <span><?= $text; ?></span>
                                                <?php endif; ?>
                                                <?= $link ? '</a>' : ''; ?>
                                            </div>
                                        </li>
                                    <?php endwhile; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if (have_rows('links_i_links')) : ?>
                            <div class="buttons">
                                <?php while (have_rows('links_i_links')) : the_row(); ?>
                                    <?php

                                    $button = get_sub_field('link');
                                    $link_type = get_sub_field('style');


                                    if ($button) {
                                        switch ($link_type) {
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
                </div>
            </div>
        </div>
    </div>
</section><!-- .image-content -->

<?php

/**
 * Block: CTA
 */

$classes9 = ['full-width', 'text-center', padding_classes()];

$larger_text9 = get_field('cta_larger_text');
$logo9 = get_field('cta_logo');
$background_text9 = get_field('cta_background_text');
$heading9 = get_field('cta_heading');
$copy9 = get_field('cta_copy');
$graphics9 = get_field('cta_graphics');

if ($larger_text9) {
    $classes9[] = 'cta--larger';
}

?>

<section <?= block_id() ?> class="cta <?= implode(' ', $classes9); ?>">
    <div class="container" data-aos="fade-up">
        <?php if ($logo9) : ?>
            <div class="cta__logo">
                <img src="<?= $logo9['url']; ?>" alt="<?= $logo9['alt']; ?>">
            </div>
        <?php endif; ?>
        <div class="cta__heading-wrapper">
            <?php if ($heading9) : ?>
                <?php if ($larger_text9) : ?>
                    <h2><?= $heading9; ?></h2>
                <?php else : ?>
                    <h2><?= $heading9; ?></h2>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($background_text9) : ?>
                <h2 class="background-text background-text--large"><?= $background_text9; ?></h2>
            <?php endif; ?>
            <?php if ($graphics9) : ?>
                <div class="cta__graphics">
                    <?php foreach ($graphics9 as $graphic) : ?>
                        <div class="cta__graphic">
                            <img src="<?= $graphic['url']; ?>" alt="<?= $graphic['alt']; ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php if ($copy9) : ?>
            <div class="cta__copy <?= $larger_text9 ? 'copy--xxl' : ''; ?>">
                <?= $copy9; ?>
            </div>
        <?php endif; ?>
        <?php if (have_rows('links_ct_links')) : ?>
            <div class="buttons">
                <?php while (have_rows('links_ct_links')) : the_row(); ?>
                    <?php

                    $button = get_sub_field('link');
                    $link_type = get_sub_field('style');


                    if ($button) {
                        switch ($link_type) {
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
</section><!-- .cta -->

<?php

/**
 * Block: News
 */

$classes10 = ['full-width', 'btn-space', 'text-center', padding_classes()];

$heading10 = get_field('news_heading');
$_posts10 = get_field('news_posts');

if (!$_posts10) {
    $_posts10 = get_posts([
        'post_type' => 'post',
        'post_status' => 'publish',
        'numberposts' => 3
    ]);
}

?>

<section class="news <?= implode(' ', $classes10); ?>">
    <div class="container">
        <?php if ($heading10) : ?>
            <h2 class="news__heading text-center heading-width" data-aos="fade-up"><?= $heading10; ?></h2>
        <?php endif; ?>
        <?php if ($_posts10) : ?>
            <div class="news__items">
                <div class="row">
                    <?php foreach ($_posts10 as $_post) : ?>
                        <div class="col-12 news__col" data-aos="fade-up">
                            <?php get_component('card-wide', $_post); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        <div data-aos="fade">
            <?php if (have_rows('links_n_links')) : ?>
                <div class="buttons">
                    <?php while (have_rows('links_n_links')) : the_row(); ?>
                        <?php

                        $button = get_sub_field('link');
                        $link_type = get_sub_field('style');


                        if ($button) {
                            switch ($link_type) {
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
    </div>
</section><!-- .news -->

<?php

/**
 * Block: Form
 */

$classes11 = ['full-width', padding_classes()];

$heading11 = get_field('form_heading');
$form_id11 = get_field('form_form_id');
$home_copy_form = get_field('home_copy_form');

?>

<section class="form <?= implode(' ', $classes11); ?>" data-aos="fade">
    <div class="container">
        <?php if ($heading11) : ?>
            <h2 class="heading-width text-center"><?= $heading11; ?></h2>
            <div class="form-copy">
                <?= $home_copy_form; ?>
            </div>
        <?php endif; ?>
        <?php if ($form_id11) : ?>
            <div class="form__wrapper">
                <?php gravity_form($form_id11, false, false, false, null, true); ?>
            </div>
        <?php endif; ?>
    </div>
</section><!-- .form -->


<?php
/** Get in touch component, data comming from site settings tab  **/
    // get_component('get-in-touch');
?>


<?php get_footer(); ?>
