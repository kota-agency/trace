<?php

/**
 * Component: Page Link
 */

$heading = get_sub_field('heading');
$link = get_sub_field('link');
$copy = get_sub_field('copy');
$redirect = get_sub_field('link_redirect');

?>


<?php if ($link) : ?>
    <div class="page-link <?php if($redirect == true) { echo 'redirect-link'; } ?>">
        <a href="<?= $link['url']; ?>" <?= $link['target'] ? 'target="_blank"' : ''; ?>>
            <?php if ($heading) : ?>
                <h3><?= $heading; ?></h3>
            <?php endif; ?>
            <?php if ($copy) : ?>
                <div class="page-link__copy">
                    <?= $copy; ?>
                </div>
            <?php endif; ?>
            <div class="btn-wrap">
                <span class="arrow"><i class="fas fa-arrow-right"></i></span>
            </div>
            <div class="page-link__overlay theme-secondary">
                <?php if ($heading) : ?>
                    <h3 class="larger"><?= $heading; ?></h3>
                <?php endif; ?>
                <?php if ($copy) : ?>
                    <div class="copy-l page-link__copy">
                        <?= $copy; ?>
                    </div>
                <?php endif; ?>
                <div class="btn-wrap">
                    <span class="arrow-to-btn"><span class="arrow-to-btn__arrow"><i
                                    class="fas fa-arrow-right"></i></span><span
                                class="arrow-to-btn__text"><?= $link['title'] ? $link['title'] : __('Find out more', 'trace'); ?></span></span>
                </div>
            </div>
        </a>
    </div>
<?php endif; ?>
