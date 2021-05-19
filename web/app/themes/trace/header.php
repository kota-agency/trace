<!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>
<head>
    <meta HTTP-EQUIV="Content-type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=9;IE=10;IE=Edge,chrome=1"/>
    <title><?php wp_title(); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-5740928-1"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'UA-5740928-1');
    </script>

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-TLL3QZN');</script>
    <!-- End Google Tag Manager -->

    <!-- A1 script -->
    <script>
    var cid = 6596;
    (function() {
    window.a1wObj = 'a1w';
    window.a1w = window.a1w || function(){
    (window.a1w.q = window.ga.q || []).push(arguments)
    },
    window.a1w.l = 1 * new Date();
    var a = document.createElement('script');
    var m = document.getElementsByTagName('script')[0];
    a.async = 1;
    a.src = "https://api1.websuccess-data.com/tracker.js";
    m.parentNode.insertBefore(a,m)
    })()
    </script>
    <!-- End A1 script -->

    <!-- Lucky orange script -->
    <!-- <script type='text/javascript'>
    window.__lo_site_id = 177182;

    (function() {
    var wa = document.createElement('script'); wa.type = 'text/javascript'; wa.async = true;
    wa.src = 'https://d10lpsik1i8c69.cloudfront.net/w.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(wa, s);
    })();
    </script> -->
    <!-- End Lucky orange script -->

    <!-- Script for browser update -->
    <script> 
        var $buoop = {required:{e:-4,f:-3,o:-3,s:-1,c:-3},insecure:true,api:2020.10 }; 
        function $buo_f(){ 
        var e = document.createElement("script"); 
        e.src = "//browser-update.org/update.min.js"; 
        document.body.appendChild(e);
        };
        try {document.addEventListener("DOMContentLoaded", $buo_f,false)}
        catch(e){window.attachEvent("onload", $buo_f)}
    </script>
    <!-- End Script for browser update -->

    <?php wp_head(); ?>
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.14.0/css/all.css"
          integrity="sha384-VhBcF/php0Z/P5ZxlxaEx1GwqTQVIBu4G4giRWxTKOCjTxsPFETUDdVL5B6vYvOt" crossorigin="anonymous">

    <script type='text/javascript'>
        /* <![CDATA[ */
        var ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ) ?>'
        /* ]]> */
    </script>

    <!-- <script type="text/javascript" src="https://www.bugherd.com/sidebarv2.js?apikey=zgfxsijgx8wxzlejxmhpxw" async="true"></script> -->

</head>

<body <?php body_class(); ?>>

    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TLL3QZN"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

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
