<?php
/**
 * Filename settings.php
 *
 * @package dev
 * @author  Peter Toi <peter@petertoi.com>
 */

use WP_Perf\Splash\Template;

?>
<div id="wpp_slash_settings" class="wrap">
  <h1><?php esc_html_e( 'Splash Settings', '' ); ?></h1>
  <form method="post" action="">
    <?php wp_nonce_field( 'splash-settings' ); ?>
    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="option_page" value="splash" />
    <h2><?php esc_html_e( 'General', '' ); ?></h2>
    <table class="form-table">
      <tbody>

      <?php
      echo Template::render_admin_table_row(
        __( 'Enable', '' ),
        Template::render_field(
          'checkbox',
          'wpp_splash_settings[enabled]',
          'enabled',
          true,
          [
            'checked' => wpp_splash()->settings->get_enabled(),
          ]
        )
      );

      echo Template::render_admin_table_row(
        __( 'Mode', '' ),
        Template::render_field(
          'select',
          'wpp_splash_settings[mode]',
          'mode',
          wpp_splash()->settings->get_mode(),
          [
            'options' => [
              'splash'      => __( 'Splash', '' ),
              'maintenance' => __( 'Maintenance', '' ),
            ]
          ]
        )
      );

      echo Template::render_admin_table_row(
        __( 'Template', '' ),
        Template::render_field(
          'select',
          'wpp_splash_settings[template]',
          'template',
          wpp_splash()->settings->get_template(),
          [
            'options' => [
              'default' => __( 'Default', '' ),
              'custom'  => __( 'Custom', '' ),
            ]
          ]
        )
      );

      echo Template::render_admin_table_row(
        __( 'Custom Path', '' ),
        Template::render_field(
          'text',
          'wpp_splash_settings[custom_path]',
          'custom_path',
          wpp_splash()->settings->get_custom_path(),
          []
        )
      );

      ?>
      </tbody>
    </table>

    <h2><?php esc_html_e( 'Template Tags', '' ); ?></h2>
    <table class="form-table">
      <tbody>

      <?php

      echo Template::render_admin_table_row(
        __( 'Logo', '' ),
        Template::render_media_field(
          'wpp_splash_settings[tags][logo]',
          'logo',
          wpp_splash()->settings->get_logo(),
          []
        )
      );

      echo Template::render_admin_table_row(
        __( 'Title', '' ),
        Template::render_field(
          'text',
          'wpp_splash_settings[tags][title]',
          'title',
          wpp_splash()->settings->get_title(),
          [
            'class' => 'regular-text',
          ]
        )
      ); ?>
      </tbody>
    </table>


    <?php submit_button( __( 'Save Settings', '' ), 'primary', 'wpp_splash_action[save_settings]' ); ?>

  </form>

</div>
