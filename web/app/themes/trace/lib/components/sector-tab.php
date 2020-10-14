<?php

/**
 * Component: Sector Tab
 */

$heading = get_sub_field('heading');
$logos = get_sub_field('logos');

$detect = new Mobile_Detect;

?>

<div class="sector-tab <?= !$detect->isMobile() ? 'sector-tab--desktop' : 'sector-tab--mobile'; ?>">
    <?php if ($heading) : ?>
        <div class="sector-tab__top">
            <div class="sector-tab__heading">
                <h4><?= $heading; ?></h4>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($logos) : ?>
        <div class="sector-tab__bottom">
            <div class="sector-tab__logos">
                <?php foreach ($logos as $logo_id) : ?>
                    <?php $logo = wp_get_attachment_image($logo_id, 'logo'); ?>
                    <?php if ($logo) : ?>
                        <div class="sector-tab__logo">
                            <?= $logo; ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <span class="minus-toggle d-md-none"></span>
        </div>
    <?php endif; ?>
    <span class="plus-toggle"></span>
    <span class="arrow d-none "><i class="fas fa-arrow-right"></i></span>
</div>
