<?php

get_header();

get_the_content() ? the_content() : get_component('error-block');

get_footer();

