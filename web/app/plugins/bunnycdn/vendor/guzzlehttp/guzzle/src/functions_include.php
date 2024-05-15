<?php

namespace {
    // Don't redefine the functions if included multiple times.
    if (!\function_exists('Bunny_WP_Plugin\\GuzzleHttp\\describe_type')) {
        require __DIR__ . '/functions.php';
    }
}
