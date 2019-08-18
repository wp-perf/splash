<?php
/**
 * Filename class-admin.php
 *
 * @package dev
 * @author  Peter Toi <peter@petertoi.com>
 */

namespace WP_Perf\Splash;

class Admin {
    /**
     * Admin constructor.
     */
    public function __construct() {
        add_action( 'init', [ $this, 'save_settings' ], 999 );
        add_action( 'admin_menu', [ $this, 'settings_page_menu' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
    }

    /**
     * Register SplashAdmin Page.
     */
    public function settings_page_menu() {
        add_options_page(
            __( 'Splash Settings', '' ),
            __( 'Splash', '' ),
            'manage_options',
            'splash',
            [ $this, 'settings_page_callback' ]
        );
    }

    /**
     * SplashAdmin Page Callback.
     */
    public function settings_page_callback() {
        Template::load( 'admin/settings' );
    }

    /**
     * Enqueue scripts.
     *
     * @param string $hook_suffix Page hook.
     */
    public function admin_enqueue_scripts( $hook_suffix ) {
        if (
            'settings_page_splash' !== $hook_suffix
            && 'post.php' !== $hook_suffix
        ) {
            return;
        }
        wp_enqueue_media();
        wp_enqueue_style( 'wpp-splash-admin', wpp_splash()->get_asset_url( 'css/admin.css' ), [], null );
        wp_enqueue_script( 'wpp-splash-admin', wpp_splash()->get_asset_url( 'js/admin.js' ), [ 'jquery', 'jquery-ui-tabs' ], null, true );
    }

    /**
     * Save Settings.
     *
     * @return bool
     */
    public function save_settings() {
        if ( ! is_admin() ) {
            return false;
        }

        if ( ! isset( $_REQUEST['page'] ) || 'splash' !== $_REQUEST['page'] ) { // phpcs:ignore
            return false;
        }

        if ( ! isset( $_REQUEST['action'] ) || 'update' !== $_REQUEST['action'] ) { // phpcs:ignore
            return false;
        }

        check_admin_referer( 'splash-settings' );

        // Find out which action was submitted.
        $actions = filter_input( INPUT_POST, 'wpp_splash_action', FILTER_DEFAULT, [ 'flags' => FILTER_REQUIRE_ARRAY ] );
        $action  = key( $actions );

        $settings = filter_input( INPUT_POST, 'wpp_splash_settings', FILTER_DEFAULT, [ 'flags' => FILTER_REQUIRE_ARRAY ] );

        $status = false;
        switch ( $action ) {
            case 'save_settings':
                wpp_splash()->settings
                    ->set_enabled( isset( $settings['enabled'] ) )
                    ->set_mode( $settings['mode'] ?? 'splash' )
                    ->set_template( $settings['template'] ?? 'default' )
                    ->set_custom_path( $settings['custom_path'] ?? '' )
                    ->set_logo( $settings['tags']['logo'] ?? null )
                    ->set_title( $settings['tags']['title'] ?? '' )
                    ->save();
                break;
            default:
                break;
        }

        return $status;
    }
}
