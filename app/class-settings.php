<?php
/**
 * Filename class-settings.php
 *
 * @package dev
 * @author  Peter Toi <peter@petertoi.com>
 */

namespace WP_Perf\Splash;

class Settings {

    const OPTIONS_KEY = 'wpp_splash_settings';

    /**
     * API Settings
     *
     * @var array
     */
    protected $settings;

    /**
     * Settings constructor.
     */
    public function __construct() {
        $this->settings = get_option(
            self::OPTIONS_KEY,
            [
                'enabled'     => false,
                'mode'        => 'splash',
                'template'    => 'default',
                'custom_path' => '',
                'tags'        => [
                    'logo'  => null,
                    'title' => '',
                ],
            ]
        );
    }

    /**
     * Save settings to options table. Optionally, set the value of this variable.
     *
     * @param array|null $api (optional) API settings.
     */
    public function save( $settings = null ) {
        if ( isset( $settings ) ) {
            $this->settings = $settings;
        }
        update_option( self::OPTIONS_KEY, $this->settings );
    }

    /**
     * Get Enabled
     *
     * @return bool
     */
    public function get_enabled() {
        if ( defined( 'WPP_SPLASH_ENABLED' ) && WPP_SPLASH_ENABLED ) {
            return WPP_SPLASH_ENABLED;
        }

        return (bool) $this->settings['enabled'] ?? '';
    }

    /**
     * Set Enabled
     *
     * @param bool $enabled
     * @param bool $save
     */
    public function set_enabled( $enabled, $save = false ) {
        $this->settings['enabled'] = (bool) $enabled;
        if ( $save ) {
            $this->save();
        }

        return $this;
    }

    /**
     * Get Mode
     *
     * @return string
     */
    public function get_mode() {
        if ( defined( 'WPP_SPLASH_ENABLED' ) && WPP_SPLASH_ENABLED ) {
            return WPP_SPLASH_ENABLED;
        }

        return $this->settings['mode'] ?? '';
    }

    /**
     * Set Mode
     *
     * @param      $mode
     * @param bool $save
     */
    public function set_mode( $mode, $save = false ) {
        $this->settings['mode'] = sanitize_title( $mode );
        if ( $save ) {
            $this->save();
        }

        return $this;
    }

    /**
     * Get Template
     *
     * @return string
     */
    public function get_template() {
        if ( defined( 'WPP_SPLASH_TEMPLATE' ) && WPP_SPLASH_TEMPLATE ) {
            return WPP_SPLASH_TEMPLATE;
        }

        return $this->settings['template'] ?? '';
    }

    /**
     * Set Template
     *
     * @param      $template
     * @param bool $save
     */
    public function set_template( $template, $save = false ) {
        $this->settings['template'] = sanitize_title( $template );
        if ( $save ) {
            $this->save();
        }

        return $this;
    }

    /**
     * Get Custom Path
     *
     * @return string
     */
    public function get_custom_path() {
        if ( defined( 'WPP_SPLASH_CUSTOM_PATH' ) && WPP_SPLASH_CUSTOM_PATH ) {
            return WPP_SPLASH_CUSTOM_PATH;
        }

        return $this->settings['custom_path'] ?? '';
    }

    /**
     * Set Custom Path
     *
     * @param      $custom_path
     * @param bool $save
     */
    public function set_custom_path( $custom_path, $save = false ) {
        $this->settings['custom_path'] = sanitize_text_field( $custom_path );
        if ( $save ) {
            $this->save();
        }

        return $this;
    }

    /**
     * Get Logo
     *
     * @return string
     */
    public function get_logo() {
        if ( defined( 'WPP_SPLASH_LOGO' ) && WPP_SPLASH_LOGO ) {
            return WPP_SPLASH_LOGO;
        }

        return (int) $this->settings['tag']['logo'] ?? 0;
    }

    /**
     * Set Logo
     *
     * @param      $logo
     * @param bool $save
     */
    public function set_logo( $logo, $save = false ) {
        $this->settings['tag']['logo'] = absint( $logo );
        if ( $save ) {
            $this->save();
        }

        return $this;
    }

    /**
     * Get Title
     *
     * @return string
     */
    public function get_title() {
        if ( defined( 'WPP_SPLASH_TITLE' ) && WPP_SPLASH_TITLE ) {
            return WPP_SPLASH_TITLE;
        }

        return $this->settings['tag']['title'] ?? '';
    }

    /**
     * Set Title
     *
     * @param      $title
     * @param bool $save
     */
    public function set_title( $title, $save = false ) {
        $this->settings['tag']['title'] = sanitize_text_field( $title );
        if ( $save ) {
            $this->save();
        }

        return $this;
    }

    /**
     * Is Enabled?
     *
     * @return bool
     */
    public function is_enabled() {
        return (bool) $this->get_enabled();
    }

}
