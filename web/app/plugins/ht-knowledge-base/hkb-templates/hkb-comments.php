<?php
/**
 * Comments template for theme integration only
 *
 * @package hkb-templates/
 */

// If comments are open or we have at least one comment, load up the comment template.
if ( comments_open() || '0' != get_comments_number() ) {
	comments_template();
}
