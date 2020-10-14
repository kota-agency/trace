<?php

/**
 * Component: Button
 */

$button = get_query_var('data');

?>

<div class="btn-wrap">
    <a href="<?= $button['url']; ?>" <?= (!empty($button['target'])) ? 'target="_blank"' : ''; ?>
       class="btn <?= (!empty($button['classes'])) ? $button['classes'] : ''; ?>" <?= (!empty($button['attr'])) ? $button['attr'] : ''; ?>><span><?= $button['title']; ?></span></a>
</div>
