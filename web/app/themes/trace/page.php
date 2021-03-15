<?php

get_header();

if(post_password_required()){
	the_content();
} else {
	get_template_part('lib/blocks/page-builder');	
}


get_footer();
