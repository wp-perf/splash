<?php

/**
 * Plugin Name:        Splash by WP Perf
 * Plugin URI:         https://wp-perf.io
 * Description:        Plugin Description
 * Version:            1.0.0
 * Requires at least:  @TODO WordPress version requirement
 * Requires PHP:       @TODO PHP version requirement
 * Author:             WP Perf
 * Author URI:         https://wp-perf.io
 * License:            GPL-2.0+
 * License URI:        http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:        splash
 * Domain Path:        /public/lang
 * Network:            @TODO can this be activated network wide?
 */

use WP_Perf\Splash\Plugin as Splash;

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
 * @return Splash
 * @since    1.0.0
 *
 */
function wpp_splash() {
    /**
     * @var $wpperf_splash_page Splash
     */
    $splash = Splash::get_instance();

    return $splash;
}

// Ready, steady, GO!
wpp_splash()->initialize(
    __FILE__,
    WP_PERF_SPLASH_PAGE_VERSION
);
