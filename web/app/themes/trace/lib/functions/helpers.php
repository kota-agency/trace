<?php


/**
 * Converts hex code to RGB
 *
 * @param $hex
 * @return array
 */
function hex2rgb($hex)
{
    $hex = str_replace("#", "", $hex);

    if (strlen($hex) == 3) {
        $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
        $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
        $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
    } else {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
    }
    $rgb = array($r, $g, $b);
    //return implode(",", $rgb); // returns the rgb values separated by commas
    return $rgb; // returns an array with the rgb values
}


/**
 * Format string to URL
 *
 * @param $string
 * @return string|string[]|null
 */
function seoUrl($string)
{
    //Lower case everything
    $string = strtolower($string);
    //Make alphanumeric (removes all other characters)
    $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
    //Clean up multiple dashes or whitespaces
    $string = preg_replace("/[\s-]+/", " ", $string);
    //Convert whitespaces and underscore to dash
    $string = preg_replace("/[\s_]/", "-", $string);
    return $string;
}


/**
 * Converts integer into words
 *
 * @param $number
 * @return bool|mixed|string|null
 */
function convert_number_to_words($number)
{

    $hyphen = '-';
    $conjunction = ' and ';
    $separator = ', ';
    $negative = 'negative ';
    $decimal = ' point ';
    $dictionary = array(
        0 => 'zero',
        1 => 'one',
        2 => 'two',
        3 => 'three',
        4 => 'four',
        5 => 'five',
        6 => 'six',
        7 => 'seven',
        8 => 'eight',
        9 => 'nine',
        10 => 'ten',
        11 => 'eleven',
        12 => 'twelve',
        13 => 'thirteen',
        14 => 'fourteen',
        15 => 'fifteen',
        16 => 'sixteen',
        17 => 'seventeen',
        18 => 'eighteen',
        19 => 'nineteen',
        20 => 'twenty',
        30 => 'thirty',
        40 => 'fourty',
        50 => 'fifty',
        60 => 'sixty',
        70 => 'seventy',
        80 => 'eighty',
        90 => 'ninety',
        100 => 'hundred',
        1000 => 'thousand',
        1000000 => 'million',
        1000000000 => 'billion',
        1000000000000 => 'trillion',
        1000000000000000 => 'quadrillion',
        1000000000000000000 => 'quintillion'
    );

    if (!is_numeric($number)) {
        return false;
    }

    if (($number >= 0 && (int)$number < 0) || (int)$number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . convert_number_to_words(abs($number));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens = ((int)($number / 10)) * 10;
            $units = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convert_number_to_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int)($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_words($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string)$fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }

    return $string;
}

/**
 * Limit the post excerpt
 *
 * @param $limit
 * @param $id
 * @return array|string|string[]|null
 */
function excerpt($limit, $id)
{

    if (!has_excerpt($id)) {
        return '';
    }

    $excerpt = explode(' ', get_the_excerpt($id), $limit);
    if (count($excerpt) >= $limit) {
        array_pop($excerpt);
        $excerpt = implode(" ", $excerpt) . '...';
    } else {
        $excerpt = implode(" ", $excerpt);
    }
    $excerpt = preg_replace('`\[[^\]]*\]`', '', $excerpt);

    return $excerpt;
}

/**
 *
 * get the post content by ID and apply filters
 *
 * @param $id
 * @return mixed
 */
function get_content_by_id($id)
{
    $content_post = get_post($id);
    $content = $content_post->post_content;
    $content = apply_filters('the_content', $content);
    return $content;
}

/**
 * Convert terms into a comma separated string
 *
 * @param $id
 * @param $taxonomy
 * @return string
 */
function get_terms_string($id, $taxonomy, $parent = true)
{
    $terms = get_the_terms($id, $taxonomy);

    if($terms) {

        if (!$parent) {
            foreach ($terms as $key => $term) {
                if ($term->parent === 0) {
                    unset($terms[$key]);
                }
            }
        }


        $terms_count = count($terms);
        $terms_string = '';
        $c = 1;
        if ($terms) {
            foreach ($terms as $term) {


                if ($terms_count > $c) {
                    $terms_string .= $term->name . ', ';
                } else {
                    $terms_string .= $term->name;
                }


                $c++;
            }
        }

        return $terms_string;
    }
}


/**
 * Get the padding classes
 *
 * @return string
 */
function padding_classes()
{

    $padding_options = get_mixed_field('padding_options');

    $classes_string = ' block-space';

    if($padding_options) {

        if (in_array('Extra Top Padding', $padding_options)) {
            $classes_string .= ' block-space--extra-top';
        }

        if (in_array('Extra Bottom Padding', $padding_options)) {
            $classes_string .= ' block-space--extra-bottom';
        }

        if (in_array('No Top Padding', $padding_options)) {
            $classes_string .= ' block-space--no-top';
        }

        if (in_array('No Bottom Padding', $padding_options)) {
            $classes_string .= ' block-space--no-bottom';
        }

        if (in_array('No Padding', $padding_options)) {
            $classes_string = '';
        }
    }

    return $classes_string;

}


function accent_classes()
{

    $accent_colour = get_field('accent_colour');
    $background_colour = get_field('background_colour');

    $classes_string = ' ';

    if($accent_colour) {
        $classes_string .= ' accent-' . seoUrl($accent_colour);
    }

    if($background_colour) {
        $classes_string .= ' accent-background';
    }

    return $classes_string;
}

/**
 * Wrapper to pass variable to template part
 *
 * @param string $file
 * @param object|array $var
 * @param string $suffix
 */
function get_component($file, $var = [], $suffix = '') {

    if($var) {
        set_query_var('data', $var);
    }

    get_template_part('lib/components/' . $file, $suffix);

    if($var) {
        set_query_var('data', '');
    }
}


/**
 * Get user defined block ID
 *
 * @return string
 */
function block_id()
{

    $_id = get_field('id') ? get_field('id') : get_sub_field('id');
    $output = '';

    if ($_id) {
        $output = 'id="' . $_id . '"';
    }

    return $output;

}


/**
 * Get field or sub field
 *
 * @param string $name
 * @return bool|mixed|string
 */
function get_mixed_field($name = '') {

    $field = '';

    if($name) {
        $field = get_field($name) ? get_field($name) : get_sub_field($name);
    }

    return $field;

}
