<?php
/**
 * Template for kb searchbox
 *
 * @package hkb-templates/
 */

?>


<?php /* important - load live search scripts */ do_action( 'ht_knowledge_base_activate_live_search' ); ?>
<form class="hkb-site-search" method="get" action="<?php echo home_url( '/' ); ?>">
	<label class="hkb-screen-reader-text" for="s"><?php esc_html_e( 'Search For', 'ht-knowledge-base' ); ?></label>
	<input class="hkb-site-search__field" type="text" value="<?php echo get_search_query(); ?>" placeholder="<?php echo esc_attr( hkb_get_knowledgebase_searchbox_placeholder_text() ); ?>" name="s" autocomplete="off">
	<input type="hidden" name="ht-kb-search" value="1" />
	<input type="hidden" name="lang" value="<?php ht_print_icl_language_code(); ?>"/>
	<button class="hkb-site-search__button" type="submit"><span><?php esc_html_e( 'Search', 'ht-knowledge-base' ); ?></span></button>       
	<svg class="hkb-site-search__icon" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1216 832q0-185-131.5-316.5t-316.5-131.5-316.5 131.5-131.5 316.5 131.5 316.5 316.5 131.5 316.5-131.5 131.5-316.5zm512 832q0 52-38 90t-90 38q-54 0-90-38l-343-342q-179 124-399 124-143 0-273.5-55.5t-225-150-150-225-55.5-273.5 55.5-273.5 150-225 225-150 273.5-55.5 273.5 55.5 225 150 150 225 55.5 273.5q0 220-124 399l343 343q37 37 37 90z"/></svg>
</form>
