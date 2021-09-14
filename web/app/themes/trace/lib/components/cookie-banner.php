<?php

/**
 * Component: Cookie Banner
 */

$heading = get_field('cookie_heading', 'options');
$copy = str_replace(['<p>', '</p>'], '', get_field('cookie_copy', 'options'));

?>

<?php if ( $_COOKIE['trace_cookie_consent'] != 'on' ) : ?>
    <?php if ($heading || $copy) : ?>
        <div class="cookie-banner theme-dark">
            <div class="cookie-banner__inner">
                <div class="cookie-banner__row">
                    <?php if ($heading) : ?>
                        <h6 class="milli text-uppercase cookie-banner__heading font-weight-black"><?= $heading; ?></h6>
                    <?php endif; ?>
                    <?php if ($copy) : ?>
                        <div class="cookie-banner__copy milli">
                            <?= $copy; ?>
                        </div>
                    <?php endif; ?>
                    <div class="btn-wrap">
                        <span id="acceptCookies" class="btn btn--secondary"><?= __('I agree', 'trace'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>
