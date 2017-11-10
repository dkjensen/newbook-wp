<?php

if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get the booking results page ID
 *
 * @return int
 */
function newbook_get_bookings_page() {
    $settings = get_option( 'newbook_settings' );

    return (int) apply_filters( 'newbook_bookings_page', $settings['bookings_page'] );
}

/**
 * Get the booking results page URL
 *
 * @return string
 */
function newbook_get_bookings_url() {
    if( empty( newbook_get_bookings_page() ) ) {
        return home_url();
    }

    return get_permalink( newbook_get_bookings_page() );
}


function newbook_get_date_format() {
    $settings = get_option( 'newbook_settings' );

    return apply_filters( 'newbook_date_format', empty( $settings['date_format'] ) ? 'mm/dd/yy' : esc_attr( $settings['date_format'] ) );
}


function newbook_get_bookings_validate() {
    $errors = [];

    if( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'newbook_af' ) ) {
        $errors[] = __( 'Nonce validation failed, please try again.', 'newbook' );
    }
    
    return empty( $errors ) ? true : $errors;
}