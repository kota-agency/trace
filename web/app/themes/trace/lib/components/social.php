<?php

/**
 * Component: Social
 */

$social = get_field('social', 'options');

?>

<?php if ($social) : ?>
    <div class="social">
        <ul class="social__list">
            <?php foreach ($social as $item) : ?>
                <?php

                $icon = $item['icon'];
                $link = $item['link'];

                ?>

                <?php if ($icon && $link) : ?>
                    <li class="social__item">
                        <a href="<?php echo $link['url']; ?>" <?php echo ($link['target']) ? 'target="_blank"' : ''; ?>><?php echo $icon; ?></a>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
