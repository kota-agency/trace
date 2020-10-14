<!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>
<head>
    <meta HTTP-EQUIV="Content-type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=9;IE=10;IE=Edge,chrome=1"/>
    <title><?php wp_title(); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>

    <?php wp_head(); ?>
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.14.0/css/all.css"
          integrity="sha384-VhBcF/php0Z/P5ZxlxaEx1GwqTQVIBu4G4giRWxTKOCjTxsPFETUDdVL5B6vYvOt" crossorigin="anonymous">
</head>

<body <?php body_class(); ?>>

<?php

$logo = get_field('logo', 'options');

?>

<header class="masthead">
    <div class="container">
        <div class="row align-items-center">
            <div class="col">
                <?php if ($logo) : ?>
                    <div class="masthead__logo">
                        <a href="<?php echo home_url('/'); ?>" class="masthead__logo-link">tracesolutions</a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-auto">
                <nav class="masthead__nav d-none d-lg-block">
                    <?php wp_nav_menu(array('theme_location' => 'header', 'container' => false, 'walker' => new Header_Walker)); ?>
                </nav>

                <?php get_component('burger'); ?>
            </div>
        </div>
    </div>
</header><!-- .masthead -->

<?php

get_component('mobile-navigation');
get_component('cookie-banner');

?>

<div class="site-outer">
    <div class="container">
        <main class="site-wrapper">
