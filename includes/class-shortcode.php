<?php
namespace Kearny_Events;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Shortcode handler: [kearny_events count="4" columns="2"]
 */
class Shortcode {

    public static function register() {
        add_shortcode( 'kearny_events', [ __CLASS__, 'render' ] );
    }

    public static function render( $atts ) {
        $atts = shortcode_atts( [
            'count'                 => 8,
            'columns'               => 4,
            'category'              => '',
            'show_image'            => 'yes',
            'show_date'             => 'yes',
            'show_venue'            => 'no',
            'show_excerpt'          => 'yes',
            'excerpt_length'        => 20,
            'image_ratio'           => '3/2',
            'show_button'           => 'yes',
            'button_text'           => 'View All Events',
            'button_url'            => '/events',
            'empty_message'         => 'No upcoming events. Check back soon!',
            'show_submit_button'    => 'yes',
            'submit_button_text'    => 'Submit an Event',
            'submit_button_url'     => '/events/community/add/',
            'submit_button_align'   => 'right',
            'submit_logged_in_only' => 'no',
            'layout_mode'           => 'carousel',
            'show_arrows'           => 'yes',
        ], $atts, 'kearny_events' );

        return Events_Renderer::render( [
            'count'                 => absint( $atts['count'] ),
            'columns'               => absint( $atts['columns'] ),
            'category'              => sanitize_title( $atts['category'] ),
            'show_image'            => self::truthy( $atts['show_image'] ),
            'show_date'             => self::truthy( $atts['show_date'] ),
            'show_venue'            => self::truthy( $atts['show_venue'] ),
            'show_excerpt'          => self::truthy( $atts['show_excerpt'] ),
            'excerpt_length'        => absint( $atts['excerpt_length'] ),
            'image_ratio'           => $atts['image_ratio'],
            'show_button'           => self::truthy( $atts['show_button'] ),
            'button_text'           => sanitize_text_field( $atts['button_text'] ),
            'button_url'            => esc_url_raw( $atts['button_url'] ),
            'empty_message'         => sanitize_text_field( $atts['empty_message'] ),
            'show_submit_button'    => self::truthy( $atts['show_submit_button'] ),
            'submit_button_text'    => sanitize_text_field( $atts['submit_button_text'] ),
            'submit_button_url'     => esc_url_raw( $atts['submit_button_url'] ),
            'submit_button_align'   => sanitize_key( $atts['submit_button_align'] ),
            'submit_logged_in_only' => self::truthy( $atts['submit_logged_in_only'] ),
            'layout_mode'           => sanitize_key( $atts['layout_mode'] ),
            'show_arrows'           => self::truthy( $atts['show_arrows'] ),
        ] );
    }

    private static function truthy( $value ) {
        return in_array( strtolower( (string) $value ), [ 'yes', '1', 'true', 'on' ], true );
    }
}
