</main>
</div>

<span class="scroll-top"><i class="fas fa-arrow-up"></i></span>

<?php

get_component('form-modals');

$logo = get_field('logo', 'options');
$social = get_field('social', 'options');
$accreditations = get_field('accreditations', 'options');
$copyright = get_field('copyright_text', 'options');
$agency_logo = get_field('agency_logo', 'options');
$agency_link = get_field('agency_link', 'options');

?>

<footer class="mastfoot">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <?php if ($logo) : ?>
                    <div class="mastfoot__logo">
                        <a href="<?php echo home_url('/'); ?>" class="mastfoot__logo-link">tracesolutions</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg">
                <nav class="mastfoot__nav mastfoot__nav--main">
                    <?php wp_nav_menu(array('theme_location' => 'footer', 'container' => false)); ?>
                </nav>
            </div>
            <?php if (have_rows('social', 'options')) : ?>
                <div class="col-lg-2">
                    <div class="mastfoot__extra">
                        <nav class="mastfoot__nav">
                            <ul>
                                <li>
                                    <a href="#"><?= __('Follow Us', 'trace'); ?></a>

                                    <ul>
                                        <?php while (have_rows('social', 'options')) : the_row(); ?>
                                            <?php $link = get_sub_field('link'); ?>
                                            <?php if ($link) : ?>
                                                <li>
                                                    <a href="<?= $link['url']; ?>" <?= $link['target'] ? 'target="_blank"' : ''; ?>><?= $link['title']; ?></a>
                                                </li>
                                            <?php endif; ?>
                                        <?php endwhile; ?>
                                    </ul>
                                </li>
                            </ul>
                        </nav>
                        <div class="link-to-ref">
                            <?php if ($agency_link && $agency_logo) : ?>
                                <a href="<?= $agency_link['url']; ?>" <?= $agency_link['target'] ? 'target="_blank"' : ''; ?>
                                class="mastfoot__agency">
                                    <span class="milli"><?= __('Site by', 'trace'); ?></span>
                                    <img src="<?= $agency_logo['url']; ?>" alt="<?= $agency_logo['alt']; ?>">
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="row mastfoot__bottom">
            <div class="col-lg">
                <?php if ($copyright) : ?>
                    <div class="copy-xs last-margin mastfoot__copyright">
                        <?= $copyright; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-lg-2">

            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?php if ($accreditations) : ?>
                    <div class="mastfoot__accreditations">
                        <?php foreach ($accreditations as $accreditation) : ?>
                            <div class="mastfoot__accreditation">
                                <img src="<?= $accreditation['url']; ?>" alt="<?= $accreditation['alt']; ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</footer><!-- .mastfoot -->
</div>

<?php get_component('form-modal'); ?>

<?php wp_footer(); ?>
</body>
</html>
