<?php

$heading = get_field('404_heading', 'options');
$copy = get_field('404_copy', 'options');
$button = get_field('404_button', 'options');

?>

<section class="error-block block-space full-width" data-aos="fade">
    <div class="container">
        <div class="error-block__content">
            <?php if ($heading) : ?>
                <div class="error-block__heading"><?php echo $heading; ?></div>
            <?php endif; ?>
            <?php if ($copy) : ?>
                <div class="error-block__copy">
                    <?php echo $copy; ?>
                </div>
            <?php endif; ?>
            <?php if ($button) : ?>
                <?php

                $button['classes'] = 'btn--small';

                ?>
               <?php get_component('button', $button); ?>
            <?php endif; ?>
        </div>
    </div>
</section>
