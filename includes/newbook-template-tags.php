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

function newbook_get_endpoint() {
    $settings = get_option( 'newbook_settings' );

    return apply_filters( 'newbook_endpoint', empty( $settings['endpoint'] ) ? 'DEV' : esc_attr( $settings['endpoint'] ) );
}

function newbook_get_api_key() {
    $settings = get_option( 'newbook_settings' );
    
    return apply_filters( 'newbook_api_key', empty( $settings['api_key'] ) ? '' : esc_attr( $settings['api_key'] ) );
}

function newbook_get_auth() {
    $settings = get_option( 'newbook_settings' );

    $auth = wp_parse_args( (array) $settings['auth'], array(
        'username'      => '',
        'password'      => '',
    ) );
    
    return apply_filters( 'newbook_user_auth', base64_encode( implode( ':', $auth ) ) );
}

function newbook_get_date_format() {
    $settings = get_option( 'newbook_settings' );

    return apply_filters( 'newbook_date_format', empty( $settings['date_format'] ) ? 'Y-m-d' : esc_attr( $settings['date_format'] ) );
}

function newbook_convert_date_format_js() {
    switch( newbook_get_date_format() ) {
        case 'm/d/Y' :
            return 'm/d/yy';
            break;
        case 'm-d-Y' :
            return 'm-d-yy';
            break;
        case 'Y-m-d' :
            return 'yy-m-d';
            break;
    }
}

function newbook_get_bookings_validate() {
    $errors = [];

    $errors = apply_filters( 'newbook_bookings_notices', $errors );

    ob_start();

    array_walk( $errors, function( $error ) {
        printf( '<div class="newbook-notice"><p>%s</p></div>', $error );
    } );
    
    return ob_get_clean();
}

function newbook_add_booking_notice( $notice = '' ) {
    add_filter( 'newbook_bookings_notices', function( $notices ) use( $notice ) {
        $notices[] = esc_html__( $notice, 'newbook' );

        return $notices;
    });
}

function newbook_template_get_categories() {
    ob_start();

    include NEWBOOK_DIR_PATH . 'templates/newbook-bookings.php';

    return ob_get_clean();
}

function newbook_get_category_field( $field, $category = [], $raw = false ) {
    if( empty( $category ) ) {
        global $category;
    }

    if( array_key_exists( $field, (array) $category ) ) {
        if( $raw ) {
            return $category[ $field ];
        }

        return esc_html( $category[ $field ] );
    }

    return '';
}


function newbook_get_category_price( $category = [] ) {
    if( empty( $category ) ) {
        global $category;
    }

    $price = __( 'Price Unavailable', 'newbook' );

    if( ! empty( $category['tariffs_available'] ) ) {
        foreach( $category['tariffs_available'] as $tariff ) {
            if( $tariff['tariff_success'] === 'true' ) {
                if( sizeof( $category['sites_available'] ) > 1 ) {
                    $price = round( (float) $tariff['tariff_total'] / sizeof( $category['sites_available'] ) );
                }else {
                    $price = round( (float) $tariff['tariff_total'] );
                }
                
            }
        }
    }

    return apply_filters( 'newbook_category_price', $price, $category );
}

function newbook_get_category_price_html( $category ) {
    $price = newbook_get_category_price( $category );
    $currency = apply_filters( 'newbook_currency_symbol', '$' );

    if( is_float( $price ) || is_integer( $price ) ) {
        $html = sprintf( '<span class="category-price"><span>%s%s</span> %s</span>', $currency, $price, __( 'per night', 'newbook' ) );
    }else {
        $html = sprintf( '<span class="category-price">%s</span>', $price );
    }

    return apply_filters( 'newbook_category_price_html', $html, $price, $category );
}

function newbook_get_category_description( $category, $trim = 50 ) {
    if( empty( $category ) ) {
        global $category;
    }

    $description = newbook_get_category_field( 'category_description', $category, true );
    ?>

    <?php if( $trim && str_word_count( $description ) > $trim ) : ?><input type="checkbox" value="<?php _e( 'More Details', 'newbook' ); ?>" class="description-more" /><?php endif; ?>
    <div class="category-description-reveal"><?php print apply_filters( 'comment_text', $description ); ?></div>

    <?php
}

/**
 * Return status of category availability
 *
 * @param array $category
 * @return int  0 - No availability
 *              1 - Partial availability
 *              2 - Full availability
 */
function newbook_get_category_availability( $category ) {
    $availability = 0;

    if( ! empty( $category['sites_available'] ) ) {
        $sites_available = array_filter( (array) $category['sites_available'] );
        $sites_diff = array_diff( (array) $category['sites_available'], $sites_available );

        if( sizeof( $sites_diff ) == 0 ) {
            $availability = 2;
        }elseif( $sites_diff > 0 && $sites_diff < sizeof( (array) $category['sites_available'] ) ) {
            $availability = 1;
        }    
    }

    return apply_filters( 'newbook_get_category_availability', $availability, $category );
}

function newbook_get_category_booking_button( $category ) {
    $button_text = __( 'Booking Unavailable', 'newbook' );
    $booking_url = '#';

    if( ! empty( $category['online_booking_url'] ) ) {
        $booking_url  = esc_url( $category['online_booking_url'] );
        $availability = newbook_get_category_availability( $category );

        if( $availability == 2 ) {
            $button_text = __( 'Click to Book', 'newbook' );
        }elseif( $availability == 1 ) {
            $button_text = __( 'Partial Booking Available', 'newbook' );
        }
    }

    return apply_filters( 'newbook_get_category_booking_button', sprintf( '<a href="%s" target="_blank" rel="nofollow" class="newbook-button button">%s</a>', $booking_url, $button_text ), $category );
}