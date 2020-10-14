<?php

/*
Directory
- path to the current directory
 */
define('DIR', dirname(__FILE__));

/*
General Functions
- basic theme functions
 */
include_once(DIR . '/lib/classes/Mobile_Detect.php');
include_once(DIR . '/lib/functions/general.php');
include_once(DIR . '/lib/functions/actions.php');
include_once(DIR . '/lib/functions/filters.php');
include_once(DIR . '/lib/functions/helpers.php');
include_once(DIR . '/lib/functions/post-types.php');
include_once(DIR . '/lib/functions/builder.php');
include_once(DIR . '/lib/classes/Header_Walker.php');

