<?php
/**
 * Template for top searches list
 *
 * @package hkb-templates/
 */

?>

<?php if ( true ) : // @todo add option to display top searches, eg hkb_show_topsearches() ?>
	<?php $top_searches = HKB_Static_Stats::hkba_get_top_searches(); ?>
	<ul class="hkb-top-searches__list">
		<?php foreach ( $top_searches as $key => $item ) : ?>
			<li class="hkb-top-search__item"><a href="<?php echo $item['link']; ?>"><?php echo $item['terms']; ?></a></li>
		<?php endforeach ?>
	</ul>
<?php endif; ?>
