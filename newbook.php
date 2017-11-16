<?php
/**
 * Plugin Name: NewBook
 * Description: Integrate your WordPress site with NewBook.cloud to check availability and create new bookings
 * Author: David Jensen
 * Author URI: https://dkjensen.com
 * Version: 1.0.0
 */

define( 'NEWBOOK_WP_VER', '1.0.0' );

define( 'NEWBOOK_DIR_PATH', plugin_dir_path( __FILE__ ) );

define( 'NEWBOOK_DIR_URL', plugin_dir_url( __FILE__ ) );

require_once 'includes/newbook-template-tags.php';
require_once 'includes/newbook-template-functions.php';
require_once 'includes/shortcodes/newbook-shortcode-availability.php';

// NewBook API
require_once 'includes/class-newbook-rest-api.php';
require_once 'includes/class-newbook-category.php';
require_once 'includes/class-newbook-bookings.php';

if( is_admin() ) {
    require_once 'includes/admin/newbook-settings.php';
}

