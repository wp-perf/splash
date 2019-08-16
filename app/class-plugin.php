<?php
/**
 * Filename class-plugin.php
 *
 * @package WP_Perf\Splash_Page
 * @author  Peter Toi <peter@petertoi.com>
 */

namespace WP_Perf\Splash_Page;

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
                'plugin-name',
                false,
                $this->get_plugin_rel_path( 'languages' )
            );
        }, 100 );

        /**
         * Enqueue assets
         */
//        add_action( 'wp_enqueue_scripts', function () {
//            wp_enqueue_script( 'plugin-name/main', Assets::get_url( 'js/main.js' ), [ 'jquery' ], null, true );
//            wp_enqueue_style( 'plugin-name/main', Assets::get_url( 'css/main.css' ), [], null );
//        } );

        /**
         * Enqueue admin assets
         */
//        add_action( 'admin_enqueue_scripts', function () {
//        } );

        add_action( 'template_redirect', function () {

            $protocol = $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.0';
            header( $protocol . ' 503 Service Unavailable', true, 503 );
            header( 'Status: 503 Service Temporarily Unavailable' );
            header( 'Retry-After: 3600' );

            $template = file_get_contents( $this->get_plugin_path( 'resources/views/frontend/fallback.php' ) );

            $tags = [
                '{{language_attributes}}' => get_language_attributes(),
                '{{charset}}'             => get_bloginfo( 'charset' ),
                '{{the_title}}'           => __( 'Coming Soon', '' ),
                '{{the_content}}'         => __( 'Content', '' ),
            ];

            $html = strtr(
                $template,
                $tags
            );
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
     * Get the plugin slug, effectively the plugin's root folder name.
     *
     * @return string
     */
    public function get_plugin_slug() {
        return basename( dirname( $this->plugin_file ) );
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
