<?php

// bunny.net WordPress Plugin
// Copyright (C) 2024  BunnyWay d.o.o.
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.

declare(strict_types=1);

/*
 * Functions imported from WordPress 6.4
 *
 * @see https://github.com/WordPress/wordpress-develop/blob/6.4/src/wp-includes/functions.php
 * @see https://github.com/WordPress/wordpress-develop/blob/6.4/src/license.txt
 */

if (!function_exists('wp_get_admin_notice')) {
    /**
     * Creates and returns the markup for an admin notice.
     *
     * @since 6.4.0
     *
     * @param string $message The message.
     * @param array  $args {
     *     Optional. An array of arguments for the admin notice. Default empty array.
     *
     *     @type string   $type               Optional. The type of admin notice.
     *                                        For example, 'error', 'success', 'warning', 'info'.
     *                                        Default empty string.
     *     @type bool     $dismissible        Optional. Whether the admin notice is dismissible. Default false.
     *     @type string   $id                 Optional. The value of the admin notice's ID attribute. Default empty string.
     *     @type string[] $additional_classes Optional. A string array of class names. Default empty array.
     *     @type string[] $attributes         Optional. Additional attributes for the notice div. Default empty array.
     *     @type bool     $paragraph_wrap     Optional. Whether to wrap the message in paragraph tags. Default true.
     * }
     * @return string The markup for an admin notice.
     */
    function wp_get_admin_notice( $message, $args = array() ) {
        $defaults = array(
            'type'               => '',
            'dismissible'        => false,
            'id'                 => '',
            'additional_classes' => array(),
            'attributes'         => array(),
            'paragraph_wrap'     => true,
        );

        $args = wp_parse_args( $args, $defaults );

        /**
         * Filters the arguments for an admin notice.
         *
         * @since 6.4.0
         *
         * @param array  $args    The arguments for the admin notice.
         * @param string $message The message for the admin notice.
         */
        $args       = apply_filters( 'wp_admin_notice_args', $args, $message );
        $id         = '';
        $classes    = 'notice';
        $attributes = '';

        if ( is_string( $args['id'] ) ) {
            $trimmed_id = trim( $args['id'] );

            if ( '' !== $trimmed_id ) {
                $id = 'id="' . $trimmed_id . '" ';
            }
        }

        if ( is_string( $args['type'] ) ) {
            $type = trim( $args['type'] );

            if ( str_contains( $type, ' ' ) ) {
                _doing_it_wrong(
                    __FUNCTION__,
                    sprintf(
                    /* translators: %s: The "type" key. */
                        __( 'The %s key must be a string without spaces.' ),
                        '<code>type</code>'
                    ),
                    '6.4.0'
                );
            }

            if ( '' !== $type ) {
                $classes .= ' notice-' . $type;
            }
        }

        if ( true === $args['dismissible'] ) {
            $classes .= ' is-dismissible';
        }

        if ( is_array( $args['additional_classes'] ) && ! empty( $args['additional_classes'] ) ) {
            $classes .= ' ' . implode( ' ', $args['additional_classes'] );
        }

        if ( is_array( $args['attributes'] ) && ! empty( $args['attributes'] ) ) {
            $attributes = '';
            foreach ( $args['attributes'] as $attr => $val ) {
                if ( is_bool( $val ) ) {
                    $attributes .= $val ? ' ' . $attr : '';
                } elseif ( is_int( $attr ) ) {
                    $attributes .= ' ' . esc_attr( trim( $val ) );
                } elseif ( $val ) {
                    $attributes .= ' ' . $attr . '="' . esc_attr( trim( $val ) ) . '"';
                }
            }
        }

        if ( false !== $args['paragraph_wrap'] ) {
            $message = "<p>$message</p>";
        }

        $markup = sprintf( '<div %1$sclass="%2$s"%3$s>%4$s</div>', $id, $classes, $attributes, $message );

        /**
         * Filters the markup for an admin notice.
         *
         * @since 6.4.0
         *
         * @param string $markup  The HTML markup for the admin notice.
         * @param string $message The message for the admin notice.
         * @param array  $args    The arguments for the admin notice.
         */
        return apply_filters( 'wp_admin_notice_markup', $markup, $message, $args );
    }
}

if (!function_exists('wp_admin_notice')) {
    /**
     * Outputs an admin notice.
     *
     * @since 6.4.0
     *
     * @param string $message The message to output.
     * @param array  $args {
     *     Optional. An array of arguments for the admin notice. Default empty array.
     *
     *     @type string   $type               Optional. The type of admin notice.
     *                                        For example, 'error', 'success', 'warning', 'info'.
     *                                        Default empty string.
     *     @type bool     $dismissible        Optional. Whether the admin notice is dismissible. Default false.
     *     @type string   $id                 Optional. The value of the admin notice's ID attribute. Default empty string.
     *     @type string[] $additional_classes Optional. A string array of class names. Default empty array.
     *     @type bool     $paragraph_wrap     Optional. Whether to wrap the message in paragraph tags. Default true.
     * }
     */
    function wp_admin_notice( $message, $args = array() ) {
        /**
         * Fires before an admin notice is output.
         *
         * @since 6.4.0
         *
         * @param string $message The message for the admin notice.
         * @param array  $args    The arguments for the admin notice.
         */
        do_action( 'wp_admin_notice', $message, $args );

        echo wp_kses_post( wp_get_admin_notice( $message, $args ) );
    }
}
