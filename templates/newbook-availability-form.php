<?php

if( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="newbook-availability-form">
    <form method="get" action="<?php print newbook_get_bookings_url(); ?>" id="newbook-availability">
        <?php
        /**
         * Arrival date
         */
        if( $atts['available_from'] !== 'false' && $atts['available_from'] !== false ) : ?>
            <div class="newbook-af-field" data-field="available_from">
                <input type="text" class="datepicker" name="available_from" placeholder="<?php _e( 'Arrival Date', 'newbook' ); ?>" value="<?php print $atts['available_from']; ?>" />
            </div>
        <?php
        endif;

        /**
         * Departure date
         */
        if( $atts['available_to'] !== 'false' && $atts['available_to'] !== false ) : ?>
            <div class="newbook-af-field" data-field="available_to">
                <input type="text" class="datepicker" name="available_to" placeholder="<?php _e( 'Departure Date', 'newbook' ); ?>" value="<?php print $atts['available_to']; ?>" />
            </div>
        <?php
        endif;
        
        /**
         * Adults
         */
        if( $atts['adults'] !== 'false' && $atts['adults'] !== false ) : ?>
            <div class="newbook-af-field" data-field="adults">
                <select name="adults">
                    <option value=""><?php printf( '-- %s --', __( 'Adults', 'newbook' ) ); ?></option>
                    <?php for( $i = 1; $i <= 20; $i++ ) printf( '<option value="%1$d" %2$s>%1$d</option>', $i, selected( $i, $atts['adults'], false ) ); ?>
                </select>
            </div>
        <?php
        endif;

        /**
         * Children
         */
        if( $atts['children'] !== 'false' && $atts['children'] !== false ) : ?>
            <div class="newbook-af-field" data-field="children">
                <select name="children">
                    <option value=""><?php printf( '-- %s --', __( 'Children', 'newbook' ) ); ?></option>
                    <?php for( $i = 0; $i <= 20; $i++ ) printf( '<option value="%1$d" %2$s>%1$d</option>', $i, selected( $i, $atts['children'], false ) ); ?>
                </select>
            </div>
        <?php
        endif;

        /**
         * Infants
         */
        if( $atts['infants'] !== 'false' && $atts['infants'] !== false ) : ?>
            <div class="newbook-af-field" data-field="infants">
                <select name="infants">
                    <option value=""><?php printf( '-- %s --', __( 'Infants', 'newbook' ) ); ?></option>
                    <?php for( $i = 0; $i <= 20; $i++ ) printf( '<option value="%1$d" %2$s>%1$d</option>', $i, selected( $i, $atts['infants'], false ) ); ?>
                </select>
            </div>
        <?php
        endif;

        /**
         * Animals field
         */
        if( $atts['animals'] !== 'false' && $atts['animals'] !== false ) : ?>
            <div class="newbook-af-field" data-field="animals">
                <select name="animals">
                    <option value=""><?php printf( '-- %s --', __( 'Animals', 'newbook' ) ); ?></option>
                    <?php for( $i = 0; $i <= 20; $i++ ) printf( '<option value="%1$d" %2$s>%1$d</option>', $i, selected( $i, $atts['animals'], false ) ); ?>
                </select>
            </div>
        <?php endif; ?>

        <?php wp_nonce_field( 'newbook_af' ); ?>

        <div class="newbook-submit">
            <input type="submit" class="button newbook-button submit" value="<?php _e( 'Check Availability', 'newbook' ); ?>" />
        </div>
    </form>
</div>