<?php

/**
 * Component: Icon List
 */

?>

<?php if (have_rows('icon_list')) : ?>
    <div class="icon-list">
        <ul>
            <?php while (have_rows('icon_list')) : the_row(); ?>
                <?php

                $icon = get_sub_field('icon');
                $text = get_sub_field('text');
                $link = get_sub_field('link');

                ?>
                <li class="icon-list__item <?= !$icon ? 'icon-list__item--no-icon' : ''; ?>">
                    <div>
                        <?= $link ? '<a href="' . $link['url'] . '" ' . $link['target'] . '>' : ''; ?>
                        <?php if ($icon) : ?>
                            <span><?= $icon; ?></span>
                        <?php endif; ?>
                        <?php if ($text) : ?>
                            <span><?= $text; ?></span>
                        <?php endif; ?>
                        <?= $link ? '</a>' : ''; ?>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
<?php endif; ?>
