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
                    printf( '<label><input type="radio" name="newbook_settings[%s]" value="%s" %s /> %s</label><br>', 'endpoint', $value, checked( $value, $settings['endpoint'], false ), $label );
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
        printf( '<p><input type="text" name="newbook_settings[%s]" value="%s" class="regular-text" placeholder="%s" /></p>', 'date_format', newbook_get_date_format(), __( 'mm/dd/yyyy', 'newbook' ) );
        printf( '<p class="description">%s <a href="%2$s" target="_blank" rel="nofollow">%2$s</a></p>', __( 'Allowed date formats:', 'newbook' ), esc_url( 'http://api.jqueryui.com/datepicker/#utility-formatDate' ) );

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
        $newbook->authenticate( $fields['auth']['username'], $fields['auth']['password'] );

        var_dump( $newbook->getResponse() );
        exit;
    }

    return $fields;
}