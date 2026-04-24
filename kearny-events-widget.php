<?php
/**
 * Plugin Name: Kearny Events Widget
 * Description: Custom Elementor widget and shortcode for displaying The Events Calendar events with featured images.
 * Version:     1.2.1
 * Author:      Oscar C
 * Text Domain: kearny-events
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'KEARNY_EVENTS_VERSION', '1.2.1' );

/**
 * Plugin Update Checker — pulls updates from the GitHub repo automatically.
 */
require_once plugin_dir_path( __FILE__ ) . 'vendor/plugin-update-checker/plugin-update-checker.php';
$kearny_update_checker = YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
    'https://github.com/oscar-chiriboga/kearny-events-widget/',
    __FILE__,
    'kearny-events-widget'
);
$kearny_update_checker->setBranch( 'master' );
define( 'KEARNY_EVENTS_PATH', plugin_dir_path( __FILE__ ) );
define( 'KEARNY_EVENTS_URL', plugin_dir_url( __FILE__ ) );

/**
 * Bust the events query cache whenever an event is saved or deleted.
 * This ensures the front-end never shows stale data after an edit.
 */
add_action( 'save_post_tribe_events', 'kearny_events_bust_cache' );
add_action( 'delete_post',           'kearny_events_bust_cache' );
function kearny_events_bust_cache() {
    global $wpdb;
    $wpdb->query(
        "DELETE FROM {$wpdb->options}
         WHERE option_name LIKE '_transient_kearny_events_%'
            OR option_name LIKE '_transient_timeout_kearny_events_%'"
    );
}

/**
 * Check dependencies on admin_init and show notice if missing.
 */
add_action( 'admin_init', function () {
    if ( ! class_exists( 'Tribe__Events__Main' ) ) {
        add_action( 'admin_notices', function () {
            echo '<div class="notice notice-error"><p><strong>Kearny Events Widget:</strong> The Events Calendar plugin must be active.</p></div>';
        } );
    }
} );

/**
 * Register the Elementor widget.
 */
add_action( 'elementor/widgets/register', function ( $widgets_manager ) {
    if ( ! class_exists( 'Tribe__Events__Main' ) ) {
        return;
    }
    require_once KEARNY_EVENTS_PATH . 'includes/class-events-query.php';
    require_once KEARNY_EVENTS_PATH . 'includes/class-events-renderer.php';
    require_once KEARNY_EVENTS_PATH . 'includes/class-elementor-widget.php';
    $widgets_manager->register( new \Kearny_Events\Elementor_Widget() );
} );

/**
 * Register a custom category in the Elementor panel so the widget has a home.
 */
add_action( 'elementor/elements/categories_registered', function ( $elements_manager ) {
    $elements_manager->add_category(
        'kearny-events',
        [
            'title' => __( 'Kearny Events', 'kearny-events' ),
            'icon'  => 'fa fa-calendar',
        ]
    );
} );

/**
 * Register the shortcode on init.
 */
add_action( 'init', function () {
    if ( ! class_exists( 'Tribe__Events__Main' ) ) {
        return;
    }
    require_once KEARNY_EVENTS_PATH . 'includes/class-events-query.php';
    require_once KEARNY_EVENTS_PATH . 'includes/class-events-renderer.php';
    require_once KEARNY_EVENTS_PATH . 'includes/class-shortcode.php';
    \Kearny_Events\Shortcode::register();
} );

/**
 * Enqueue front-end assets.
 */
add_action( 'wp_enqueue_scripts', function () {
    wp_register_style(
        'kearny-events-widget',
        KEARNY_EVENTS_URL . 'assets/style.css',
        [],
        KEARNY_EVENTS_VERSION
    );
    wp_register_script(
        'kearny-events-widget',
        KEARNY_EVENTS_URL . 'assets/script.js',
        [],
        KEARNY_EVENTS_VERSION,
        true
    );
} );
