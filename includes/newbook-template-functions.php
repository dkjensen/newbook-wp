<?php

if( ! defined( 'ABSPATH' ) ) exit;


function newbook_scripts() {
    wp_register_style( 'newbook-frontend', NEWBOOK_DIR_URL . 'assets/css/newbook-frontend.css', array( 'dashicons' ), NEWBOOK_WP_VER );

    if( get_queried_object_id() === newbook_get_bookings_page() ) {
        wp_enqueue_style( 'newbook-frontend' );
    }
}
add_action( 'wp_enqueue_scripts', 'newbook_scripts' );


function newbook_bookings_page_content( $content ) {
    global $post;

    if( ! is_singular() || is_admin() || $post->ID !== newbook_get_bookings_page() ) {
        return $content;
    }

    // Show the availability form above results
    $content .= apply_filters( 'newbook_bookings_show_availability_form', '[newbook-availability]' );

    // Verify nonce and other stuff
    if( true !== newbook_get_bookings_validate() ) {
        foreach( newbook_get_bookings_validate() as $message ) {
            $content .= sprintf( '<div class="newbook-message message"><p>%s</p></div>', $message );
        }
    }else {
        ob_start();

        require_once NEWBOOK_DIR_PATH . 'templates/newbook-bookings.php';

        $content .= ob_get_contents();

        ob_end_clean();
    }

    return $content;
}
add_filter( 'the_content', 'newbook_bookings_page_content' );