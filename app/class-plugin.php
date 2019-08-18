<?php
/**
 * Filename class-plugin.php
 *
 * @package WP_Perf\Splash_Page
 * @author  Peter Toi <peter@petertoi.com>
 */

namespace WP_Perf\Splash;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    WP_Perf\Splash_Page
 * @author     Peter Toi <peter@petertoi.com>
 */
class Plugin {

    use Singleton;

    /**
     * Plugin version
     *
     * @var string
     */
    protected $plugin_version;

    /**
     * Absolute path to the plugin file
     *
     * @var string
     */
    protected $plugin_file;

    /**
     * Assets manifest
     *
     * @var array
     */
    protected $assets;

    /**
     * Plugin settings utility.
     *
     * @var Settings
     */
    public $settings;

    /**
     * Initialize plugin
     *
     * Needs to be called explicitly after the first instantiation to init the plugin.
     *
     * @param $plugin_file
     * @param $plugin_version
     *
     * @return Plugin The singleton instance.
     */
    public function initialize( $plugin_file, $plugin_version ) {
        // Only initialize once.
        if ( isset( $this->plugin_file ) ) {
            return $this;
        }

        $this->plugin_file = $plugin_file;

        $this->plugin_version = $plugin_version;

        $this->settings = new Settings();

        /**
         * Plugin lifecycle hooks
         */
        \register_activation_hook( $plugin_file, __NAMESPACE__ . '\\Plugin::activation' );
        \register_deactivation_hook( $plugin_file, __NAMESPACE__ . '\\Plugin::deactivation' );
        \register_uninstall_hook( $plugin_file, __NAMESPACE__ . '\\Plugin::uninstall' );

        /**
         * Translations
         *
         * @see plugins_loaded
         */
        \add_action( 'plugins_loaded', function () {
            \load_plugin_textdomain(
                'splash',
                false,
                $this->get_plugin_rel_path( 'public/languages' )
            );
        }, 100 );

        if ( is_admin() ) {
            new Admin();
        }

        add_action( 'template_redirect', function () {

            if ( ! $this->settings->is_enabled() ) {
                return;
            }

            /**
             * Redirect sub-pages to home, temporarily via 302 Temporary Redirect
             * This is in place for both Splash and Maintenance modes
             */
            if ( ! is_front_page() ) {
                wp_safe_redirect( '/', 302 );
                exit();
            }

            /**
             * Set Status 503 Service Unavailable Headers for Maintenance mode.
             */
            $mode = $this->settings->get_mode();
            if ( 'maintenance' === $mode ) {
                $protocol = $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.0';
                header( $protocol . ' 503 Service Unavailable', true, 503 );
                header( 'Status: 503 Service Temporarily Unavailable' );
                header( 'Retry-After: 3600' );
            }

            /**
             * Load Template
             */
            $template = $this->settings->get_template();
            if ( 'custom' === $template ) {
                $custom_path = $this->settings->get_custom_path();
                if ( ! file_exists( ABSPATH . $custom_path ) ) {
                    die( 'Custom index file not found.' );
                }
                $html_raw     = file_get_contents( ABSPATH . $custom_path );
                $template_url = home_url( dirname( $custom_path ) );

            } else {
                $html_raw     = file_get_contents( $this->get_plugin_path( 'resources/views/templates/' . $template . '/index.php' ) );
                $template_url = $this->get_plugin_url( 'resources/views/templates/' . $template );
            }

            /**
             * Search/Replace {{Template Tags}}
             */
            $tags = [
                '{{language_attributes}}' => get_language_attributes(),
                '{{charset}}'             => get_bloginfo( 'charset' ),
                '{{template_url}}'        => $template_url,
                '{{title}}'               => wpp_splash()->settings->get_title() ?? '',
                '{{logo}}'                => wp_get_attachment_image( wpp_splash()->settings->get_logo() ),
            ];

            $html = strtr(
                $html_raw,
                $tags
            );

            /**
             * Output
             */
            echo $html;

            exit();
        } );

        /**
         * Return reference to the instance
         */
        return $this;
    }

    /**
     * Get plugin version
     *
     * @return string semver
     */
    public function get_version() {
        return $this->plugin_version;
    }

    /**
     * Get the absolute path to the plugin folder.
     *
     * @param string $file File or path fragment to append to absolute file path.
     *
     * @return string
     */
    public function get_plugin_path( $file = '' ) {
        return plugin_dir_path( $this->plugin_file ) . trim( $file, '/' );
    }

    /**
     * Get the relative path to the plugin folder from WP_PLUGIN_DIR
     *
     * @param string $file File or path fragment to append to relative file path.
     *
     * @return string
     */
    public function get_plugin_rel_path( $file = '' ) {
        return substr( $this->get_plugin_path(), strlen( WP_PLUGIN_DIR ) ) . trim( $file, '/' );
    }

    /**
     * Get the absolute url path.
     *
     * @param string $file File or path fragment to append to absolute web path.
     *
     * @return string
     */
    public function get_plugin_url( $file = '' ) {
        return plugin_dir_url( $this->plugin_file ) . $file;
    }

    /**
     * Get the plugin slug, effectively the plugin's root folder name.
     *
     * @return string
     */
    public function get_plugin_slug() {
        return basename( dirname( $this->plugin_file ) );
    }

    /**
     * Get path to an asset in the Public directory
     *
     * @param $filename
     *
     * @return string
     */
    public function get_asset_path( $filename ) {
        $filename    = str_replace( '//', '/', "/$filename" );
        $public_path = wpp_splash()->get_plugin_path( 'public' );

        if ( empty( $manifest ) ) {
            $manifest_path = wpp_splash()->get_plugin_path( 'public/mix-manifest.json' );
            $manifest      = new Manifest( $manifest_path );
        }

        if ( array_key_exists( $filename, $manifest->get() ) ) {
            return $public_path . $manifest->get()[ $filename ];
        } else {
            return $public_path . $filename;
        }
    }

    /**
     * Get URL to an asset in the Public directory
     *
     * @param $filename
     *
     * @return string
     */
    public function get_asset_url( $filename ) {
        $filename   = str_replace( '//', '/', "/$filename" );
        $public_uri = wpp_splash()->get_plugin_url( 'public' );
        static $manifest;

        if ( empty( $manifest ) ) {
            $manifest_path = wpp_splash()->get_plugin_path( 'public/mix-manifest.json' );
            $manifest      = new Manifest( $manifest_path );
        }

        if ( array_key_exists( $filename, $manifest->get() ) ) {
            return $public_uri . $manifest->get()[ $filename ];
        } else {
            return $public_uri . $filename;
        }
    }

    static function activation() {
        return true;
    }

    static function deactivation() {
        return true;
    }

    static function uninstall() {
        return true;
    }
}
