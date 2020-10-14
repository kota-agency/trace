<?php

/**
 * Components: Buttons
 */


?>

<?php if (have_rows('links')) : ?>
    <div class="buttons">
        <?php while (have_rows('links')) : the_row(); ?>
            <?php

            $button = get_sub_field('link');
            $link_type = get_sub_field('style');



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
