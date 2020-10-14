<?php

/**
 * Component: Mobile Navigation
 */

?>

<div class="mobile-navigation theme-secondary">
    <span class="mobile-navigation__close"><i class="far fa-times"></i></span>
    <nav class="mobile-navigation__nav">
        <ul>
            <li><a href="<?= home_url('/'); ?>"><?= __("Home", 'trace'); ?></a></li>
            <?php wp_nav_menu(array('theme_location' => 'header', 'container' => false, 'items_wrap' => '%3$s', 'walker' => new Header_Walker)); ?>
        </ul>
    </nav>
</div><!-- .mobile-navigation -->
