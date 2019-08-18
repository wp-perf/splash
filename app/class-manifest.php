<?php
/**
 * Filename class-manifest.php
 *
 * @package WP_Perf\Splash_Page;
 * @author  Peter Toi <peter@petertoi.com>
 */

namespace WP_Perf\Splash;


class Manifest {
    static $manifest;

    public function __construct( $manifest_path ) {
        if ( ! isset( self::$manifest ) ) {
            if ( file_exists( $manifest_path ) ) {
                self::$manifest = json_decode( file_get_contents( $manifest_path ), true );
            } else {
                self::$manifest = [];
            }
        }
    }

    public function get() {
        return self::$manifest;
    }
}
