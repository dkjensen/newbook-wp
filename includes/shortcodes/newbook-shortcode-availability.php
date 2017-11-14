<?php

if( ! defined( 'ABSPATH' ) ) exit;

if( ! function_exists( 'newbook_shortcode_availability' ) ) {

    function newbook_shortcode_availability( $atts, $content = '' ) {
        $atts = shortcode_atts( array(
            'available_from'        => isset( $_GET['available_from'] ) ? esc_attr( $_GET['available_from'] ) : '',
            'available_to'          => isset( $_GET['available_to'] ) ? esc_attr( $_GET['available_to'] ) : '',
            'adults'                => isset( $_GET['adults'] ) ? (int) $_GET['adults'] : '',
            'children'              => isset( $_GET['children'] ) ? (int) $_GET['children'] : '',
            'infants'               => isset( $_GET['infants'] ) ? (int) $_GET['infants'] : '',
            'animals'               => isset( $_GET['animals'] ) ? (int) $_GET['animals'] : ''
        ), $atts );

        wp_enqueue_style( 'newbook-frontend' );
        wp_enqueue_script( 'jquery-ui-datepicker' );

        add_action( 'wp_footer', function() {
            print "<script>(function($) { $('.datepicker').datepicker({ dateFormat: '" . newbook_convert_date_format_js() . "' }); } )(jQuery);</script>";
        }, 999 );

        ob_start();

        require_once NEWBOOK_DIR_PATH . 'templates/newbook-availability-form.php';

        $content = ob_get_contents();

        ob_end_clean();

        return apply_filters( 'newbook_shortcode_availability', $content, $atts );
    }

}
add_shortcode( 'newbook-availability', 'newbook_shortcode_availability' );