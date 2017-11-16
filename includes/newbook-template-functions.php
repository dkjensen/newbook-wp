<?php

if( ! defined( 'ABSPATH' ) ) exit;


function newbook_bookings_endpoints() {
    add_rewrite_endpoint( apply_filters( 'newbook_category_endpoint', 'view' ), EP_PAGES );
}  
add_action( 'init', 'newbook_bookings_endpoints' );

function newbook_scripts() {
    wp_register_style( 'newbook-frontend', NEWBOOK_DIR_URL . 'assets/css/newbook-frontend.css', array( 'dashicons' ), NEWBOOK_WP_VER );

    if( get_queried_object_id() === newbook_get_bookings_page() ) {
        wp_enqueue_style( 'newbook-frontend' );
    }
}
add_action( 'wp_enqueue_scripts', 'newbook_scripts' );


function newbook_bookings_page_content( $content ) {
    global $post;

    if( ! is_singular() || is_admin() || $post->ID !== newbook_get_bookings_page() || ! is_main_query() ) {
        return $content;
    }

    // Show the availability form above results
    $content .= apply_filters( 'newbook_bookings_show_availability_form', '[newbook-availability]' );

    $content .= apply_filters( 'newbook_bookings_after_content', $content );

    return $content;
}
add_filter( 'the_content', 'newbook_bookings_page_content' );


function newbook_bookings_parse_request() {
    $GLOBALS['newbook_ap'] = '';

    if( ! empty( $_GET['_nbsearch'] ) ) {

        add_filter( 'newbook_bookings_after_content', 'newbook_get_bookings_validate' );

        if( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'newbook_af' ) ) {
            newbook_add_booking_notice( 'Nonce validation failed, please try again.' );
            
            return false;
        }

        switch( (int) $_GET['_nbsearch'] ) {
            
            // Search availability/pricing
            case 1 :
                $availability = new NewBook_Bookings();
                $availability->availabilityPricing( $_GET['available_from'], $_GET['available_to'], $_GET['adults'], $_GET['children'], $_GET['infants'], $_GET['animals'] )
                             ->send();
                             
                if( $availability->isSuccess() ) {
                    $availability = $availability->getData();
            
                    $category = new NewBook_Category();
                    $category->categoryList()
                             ->send();
            
                    if( $category->isSuccess() ) {
                        $categories = $category->getData();

                        foreach( $categories as $key => $category ) {
                            if( ! array_key_exists( $category['category_id'], $availability ) ) {
                                unset( $categories[$key] );
                            }else {
                                $categories[$key] = array_merge( $categories[$key], $availability[$category['category_id']] );
                            }
                        }
            
                        if( ! empty( $categories ) ) {
                            $GLOBALS['newbook_ap'] = $categories;

                            add_filter( 'newbook_bookings_after_content', 'newbook_template_get_categories', 20 );
                        }
                    }else {
                        newbook_add_booking_notice( $category->getMessage() );
                    }
                }else {
                    newbook_add_booking_notice( $availability->getMessage() );
                }
                break;
        }
    }
}
add_action( 'init', 'newbook_bookings_parse_request' );