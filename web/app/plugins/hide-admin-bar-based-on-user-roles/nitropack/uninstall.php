<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

$nitropackOptions = array('nitropack-siteId', 'nitropack-siteSecret', 'nitropack-enableCompression', 'nitropack-webhookToken', 'nitropack-checkedCompression', 'nitropack-cacheablePostTypes');
if (defined('MULTISITE') && MULTISITE) {
    $blogs = array_map(function($blog) { return $blog->blog_id; }, get_sites());

    foreach ($nitropackOptions as $optionName) {
        foreach ($blogs as $blogId) {
            delete_blog_option($blogId, $optionName);
        }
    }
} else {
    foreach ($nitropackOptions as $optionName) {
        delete_option($optionName);
    }
}

require_once 'constants.php';
require_once 'nitropack-sdk/autoload.php';
NitroPack\SDK\Filesystem::deleteDir(NITROPACK_DATA_DIR);
