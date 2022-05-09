<!-- Number of Results template -->
<script type="text/html" id="ht-kb-suggest-num-results-results-template">
	<div><?php _e('We found ', 'ht-kb-integrations'); ?><%= resultsCount %><?php _e(' results that may help:', 'ht-kb-integrations'); ?></div>
</script>
<!-- Results template (Single item) -->
<script type="text/html" id="ht-kb-suggest-results-template">
	<li>
		<a href="<%= item.link %>" target="_blank"><%= item.title.rendered %></a>
	</li>
</script>
<!-- No Results template -->
<script type="text/html" id="ht-kb-suggest-no-results-template">
	<div><?php _e('Please try something else or proceed to contact the support team.', 'ht-kb-integrations'); ?></div>
</script>
<!-- Loading Results template -->
<script type="text/html" id="ht-kb-suggest-loading-results-template">
	<div class="hkb-gfsuggest__resultsloading"></div>
</script>
<!-- All Results template -->
<script type="text/html" id="ht-kb-suggest-all-results-template">
	<div class="hkb-gfsuggest__allresults">
    <a href="<?php echo apply_filters('ht_kb-suggest_all-results_base', site_url('/?s=') ); ?><%= searchTerms %><?php echo apply_filters('ht_kb-suggest_all-results_suffix', '&ht-kb-search=1' ); ?>" target="_blank">
      <?php _e('See all results', 'ht-kb-integrations'); ?>
    </a>
    </div>
</script>