<?php
/**
 * Filename class-template.php
 *
 * @package dev
 * @author  Peter Toi <peter@petertoi.com>
 */

namespace WP_Perf\Splash;

class Template {
    /**
     * Loads a template
     *
     * @param string $slug The template relative path/name without extension.
     */
    public static function load( $slug, $data = false ) {
        $file = self::locate( $slug );
        if ( $file ) {
            require $file;
        }
    }

    /**
     * Locates a template
     *
     * @param string $slug The template relative path/name without extension.
     *
     * @return bool|string
     */
    public static function locate( $slug ) {
        $located = false;
        if ( file_exists( wpp_splash()->get_plugin_path( 'resources/views/' . $slug . '.php' ) ) ) {
            $located = wpp_splash()->get_plugin_path( 'resources/views/' . $slug . '.php' );
        }

        return $located;
    }

    /**
     * Render a HTML Table Row.
     *
     * @param string $heading The table row header.
     * @param string $content The table row content.
     * @param array  $atts    The table row attributes, see $default_args for format.
     *
     * @return string The table row.
     */
    public static function render_admin_table_row( $heading, $content, $atts = [] ) {
        $default_atts = [
            'tr' => [
                'id'    => '',
                'class' => '',
            ],
            'th' => [
                'id'    => '',
                'class' => '',
            ],
            'td' => [
                'id'    => '',
                'class' => '',
            ],
        ];

        $parsed_atts = wp_parse_args( $atts, $default_atts );

        $th = sprintf( '<th id="%s" class="%s">%s</th>',
            $parsed_atts['th']['id'],
            $parsed_atts['th']['class'],
            $heading
        );
        $td = sprintf( '<td id="%s" class="%s">%s</td>',
            $parsed_atts['td']['id'],
            $parsed_atts['td']['class'],
            $content
        );

        $tr = sprintf( '<tr id="%s" class="%s">%s%s</tr>',
            $parsed_atts['tr']['id'],
            $parsed_atts['tr']['class'],
            $th,
            $td
        );

        return $tr;

    }

    /**
     * Render a HTML field
     *
     * @param string $type  Field type: text, number, email, hidden, textarea, or select.
     * @param string $name  Field name.
     * @param string $id    Field ID.
     * @param string $value Field value attribute.
     * @param array  $atts  Attributes.
     *
     * @return string The rendered field.
     */
    public static function render_field( $type, $name, $id, $value = '', $atts = [] ) {

        switch ( $type ) {
            case 'text':
            case 'number':
            case 'email':
            case 'password':
            case 'checkbox':
            case 'radio':
            case 'hidden':
                $field = self::render_input( $type, $name, $id, $value, $atts );
                break;
            case 'textarea':
                $field = self::render_textarea( $name, $id, $value, $atts );
                break;
            case 'select':
                // TODO: Implement Select Field?
                $field = self::render_select( $name, $id, $atts['options'], $value, $atts );
                break;
            default:
                _doing_it_wrong(
                    __FUNCTION__,
                    esc_html__( 'That field type is not supported.', 'system5' ),
                    '2.0.0'
                );
                break;
        }

        return $field;
    }

    /**
     * Render a HTML Input field
     *
     * @param string $type  Input type attribute.
     *                      Options: hidden, text, number, email, password.
     * @param string $name  Input name attribute.
     * @param string $id    Input id attribute.
     * @param string $value Input value attribute.
     * @param array  $atts  Other attributes passed as an associative array ($key => $value).
     *                      Classes: large-text, regular-text, small-text, tiny-text.
     *
     * @return string The input field.
     */
    public static function render_input( $type, $name, $id, $value = '', $atts = [] ) {
        $formatted_atts = [];
        if ( isset( $atts['checked'] ) && false === $atts['checked'] ) {
            unset( $atts['checked'] );
        }
        foreach ( $atts as $k => $v ) {
            $formatted_atts[] = esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
        }

        $input = sprintf( '<input type="%s" name="%s" id="%s" value="%s" %s>',
            $type,
            $name,
            $id,
            $value,
            join( ' ', $formatted_atts )
        );

        return $input;
    }

    /**
     * Render a HTML Textarea field
     *
     * @param string $name  Name attribute.
     * @param string $id    Id attribute.
     * @param string $value Value.
     * @param array  $atts  Other attributes passed as an associative array ($key => $value).
     *                      Classes: large-text, regular-text, small-text, tiny-text.
     *
     * @return string The input field.
     */
    public static function render_textarea( $name, $id, $value = '', $atts = [] ) {
        $formatted_atts = [];
        foreach ( $atts as $k => $v ) {
            $formatted_atts[] = esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
        }

        $textarea = sprintf( '<textarea name="%s" id="%s" %s>%s</textarea>',
            $name,
            $id,
            join( ' ', $formatted_atts ),
            $value
        );

        return $textarea;
    }

    /**
     * Render a HTML Select field
     *
     * @param string $name    Name attribute.
     * @param string $id      Id attribute.
     * @param array  $options Array of options in the format ['value' => 'foo', 'label' => 'Bar'].
     * @param string $value   Value.
     * @param array  $atts    Other attributes passed as an associative array ($key => $value).
     *                        Classes: large-text, regular-text, small-text, tiny-text.
     *
     * @return string The input field.
     */
    public static function render_select( $name, $id, $options, $value = '', $atts = [] ) {
        $formatted_atts = [];
        foreach ( $atts as $k => $v ) {
            if ( is_string( $v ) ) {
                $formatted_atts[] = esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
            }
        }

        $formatted_options = [];

        foreach ( $options as $option_value => $option_label ) {
            $formatted_options[] = sprintf( '<option value="%s" %s>%s</option>',
                $option_value,
                ( $value === $option_value ) ? 'selected="selected"' : '',
                $option_label
            );
        }

        $select = sprintf( '<select name="%s" id="%s" %s>%s</select>',
            $name,
            $id,
            join( ' ', $formatted_atts ),
            join( '', $formatted_options )
        );

        return $select;
    }

    public static function render_media_field( $name, $id, $value = '', $atts = [] ) {
        $template = <<<HTML
    <div class="image-preview-wrapper">
      <img id="image-preview" src="%s" height="100">
    </div>
    <input id="upload_image_button" type="button" class="button" value="%s" />
    <input type="hidden" name="%s" id="%s" value="%s">
HTML;

        $media = sprintf( $template,
            esc_attr( wp_get_attachment_url( $value ) ),
            __( 'Upload image', '' ),
            esc_attr( $name ),
            'image_attachment_id',
            esc_attr( $value )
        );

        return $media;
    }
}
