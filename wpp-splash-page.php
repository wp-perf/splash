<?php

/**
 * Plugin Name:        Splash Page by WP Perf
 * Plugin URI:         https://wp-perf.io
 * Description:        Plugin Description
 * Version:            1.0.0
 * Requires at least:  @TODO WordPress version requirement
 * Requires PHP:       @TODO PHP version requirement
 * Author:             Peter Toi
 * Author URI:         https://petertoi.com
 * License:            GPL-2.0+
 * License URI:        http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:        plugin-name
 * Domain Path:        /public/lang
 * Network:            @TODO can this be activated network wide?
 */

use WP_Perf\Splash_Page\Plugin as Splash_Page;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

if ( ! defined( 'WP_PERF_SPLASH_PAGE_VERSION' ) ) {
    define( 'WP_PERF_SPLASH_PAGE_VERSION', '1.0.0' );
}

require_once 'autoloader.php';

/**
 * Global function providing access to the plugin.
 *
 * @since    1.0.0
 */
function wpperf_splash_page() {
    /**
     * @var $wpperf_splash_page Splash_Page
     */
    $wpperf_splash_page = Splash_Page::get_instance();

    return $wpperf_splash_page;
}

// Ready, steady, GO!
wpperf_splash_page()->initialize(
    __FILE__,
    WP_PERF_SPLASH_PAGE_VERSION
);
