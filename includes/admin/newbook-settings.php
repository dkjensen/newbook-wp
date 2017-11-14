<?php

if( ! defined( 'ABSPATH' ) ) exit;


function newbook_admin_menu() {
    add_options_page( __( 'NewBook', 'newbook' ), __( ' NewBook', 'newbook' ), 'manage_options', 'newbook', 'newbook_admin_settings' );
}
add_action( 'admin_menu', 'newbook_admin_menu' );


function newbook_admin_settings_fields() {
    register_setting( 'newbook_settings', 'newbook_settings', 'newbook_settings_validation' );
    add_settings_section( 'settings_section', __( 'NewBook Settings', 'newbook' ), function() {
        printf( '<p>Documentation can be found here: <a href="%1$s" target="_blank" rel="nofollow">%1$s</a></p>', esc_url( 'http://developers.newbook.cloud/rest.php' ) );   
    }, __FILE__ );

    /**
     * API Key
     */
    add_settings_field( 'auth', __( 'NewBook Authentication', 'newbook' ), function() {
        $settings = get_option( 'newbook_settings' );

        // Username
        printf( '<p><input type="text" name="newbook_settings[%s]" value="%s" class="regular-text" placeholder="%s" /></p>', 'auth][username', esc_attr( $settings['auth']['username'] ), __( 'Username', 'newbook' ) );
        
        // Password
        printf( '<p><input type="password" name="newbook_settings[%s]" value="%s" class="regular-text" placeholder="%s" /></p>', 'auth][password', esc_attr( $settings['auth']['password'] ), __( 'Password', 'newbook' ) );
    }, __FILE__, 'settings_section' );


    /**
     * NewBook Property
     */
    add_settings_field( 'api_key', __( 'NewBook Property', 'newbook' ), function() {
        $settings   = get_option( 'newbook_settings' );
        $properties = get_option( 'newbook_properties' );

        if( ! empty( $properties ) ) {
        ?>
        <fieldset>
            <legend class="screen-reader-text"><span><?php _e( 'NewBook Property', 'newbook' ); ?></span></legend>
            <?php
            foreach( (array) $properties as $api_key => $property ) {
                printf( '<label><input type="radio" name="newbook_settings[%s]" value="%s" %s /> %s</label><br>', 'api_key', esc_attr( $api_key ), checked( $api_key, $settings['api_key'], false ), esc_attr( $property ) );
            }
            ?>
        </fieldset>
        <?php
        }else {
            printf( '<p><em>%s</em></p>', __( 'Please authenticate your NewBook credentials before selecting a property.', 'newbook' ) );
        }
        
    }, __FILE__, 'settings_section' );

    /**
     * Endpoints
     */
    add_settings_field( 'endpoint', __( 'NewBook Endpoint', 'newbook' ), function() {
        $settings = get_option( 'newbook_settings' );

        $endpoints = array(
            'AU'        => __( 'Australia', 'newbook' ),
            'AP'        => __( 'Asia-Pacific', 'newbook' ),
            'EU'        => __( 'Europe', 'newbook' ),
            'US'        => __( 'United States', 'newbook' ),
            'DEV'       => __( 'Development', 'newbook' )
        );

        ?>
        <fieldset>
            <legend class="screen-reader-text"><span><?php _e( 'NewBook Endpoint', 'newbook' ); ?></span></legend>
            <?php
                foreach( $endpoints as $value => $label ) {
                    printf( '<label><input type="radio" name="newbook_settings[%s]" value="%s" %s /> %s</label><br>', 'endpoint', $value, checked( $value, newbook_get_endpoint(), false ), $label );
                }
            ?>
        </fieldset>
        <?php

    }, __FILE__, 'settings_section' );

    /**
     * Bookings Page
     */
    add_settings_field( 'bookings_page', __( 'Bookings Page', 'newbook' ), function() {
        $pages = get_posts( array(
            'post_type'         => 'page',
            'posts_per_page'    => -1,
            'post_status'       => 'publish',
        ) );

        ?>
        <select name="newbook_settings[bookings_page]" class="regular-text">
            <option value=""><?php printf( '-- %s --', __( 'Select a page', 'newbook' ) ); ?></option>
            <?php
                foreach( $pages as $page ) {
                    printf( '<option value="%s" %s />%s</option>', $page->ID, selected( $page->ID, newbook_get_bookings_page(), false ), sprintf( '%s (ID: %d)', $page->post_title, $page->ID ) );
                }
            ?>
        </select>
        <?php

    }, __FILE__, 'settings_section' );

    /**
     * Availability Date Format
     */
    add_settings_field( 'date_format', __( 'Availability Date Format', 'newbook' ), function() {
        $formats = array(
            'm/d/Y',
            'm-d-Y',
            'Y-m-d',
        );

        ?>
        <select name="newbook_settings[date_format]" class="regular-text">
            <?php
                foreach( $formats as $format ) {
                    printf( '<option value="%s" %s />%s</option>', $format, selected( $format, newbook_get_date_format(), false ), sprintf( '%s (%s)', $format, date( $format ) ) );
                }
            ?>
        </select>
        <?php

    }, __FILE__, 'settings_section' );
}
add_action( 'admin_init', 'newbook_admin_settings_fields' );

function newbook_admin_settings() {
?>

    <div class="wrap">
        <h1><?php _e( 'NewBook Settings', 'newbook' ); ?></h1>

        <form action="options.php" method="post">
            <?php settings_fields( 'newbook_settings' ); ?>
            <?php do_settings_sections( __FILE__ ); ?>

            <?php submit_button(); ?>
        </form>

        
    </div>

<?php
}

function newbook_settings_validation( $fields ) {
    if( ! empty( $fields['auth']['username'] ) && ! empty( $fields['auth']['password'] ) ) {
        $newbook = new NewBook_REST_API();
        $newbook->setEndpoint( $fields['endpoint'] );
        $newbook->authenticate( $fields['auth']['username'], $fields['auth']['password'] );

        if( sizeof( $newbook->getData() ) === 1 && is_array( $newbook->getData() ) ) {
            $fields['api_key'] = key( $newbook->getData() );
        }

        update_option( 'newbook_properties', $newbook->getData() );
    }

    return $fields;
}