<?php
/**
 * Plugin Name:       Coeditor Airship
 * Plugin URI:        https://coeditor.com/
 * Description:       Integrate your Airship notifications setup with WordPress.
 * Version:           1.1.0
 * Requires at least: 5.7
 * Requires PHP:      8.0
 * Author:            Coeditor
 * Author URI:        https://coeditor.com/
 */

define( 'COEDITOR_AIRSHIP_VERSION', '1.0.0' );
define( 'COEDITOR_AIRSHIP_PATH', plugin_dir_path( __FILE__ ) );
define( 'COEDITOR_AIRSHIP_INCLUDES', COEDITOR_AIRSHIP_PATH . 'includes/' );
define( 'COEDITOR_AIRSHIP_ASSETS', plugin_dir_url( __FILE__ ) . 'assets/' );
define( 'COEDITOR_AIRSHIP_ADMIN_PAGES', COEDITOR_AIRSHIP_INCLUDES . 'templates/' );

require_once COEDITOR_AIRSHIP_PATH . 'vendor/autoload.php';

$classes = [
    \Coeditor\WordpressAirship\Classes\NavMenu::class,
];

foreach ( $classes as $class ) {
    if ( class_exists( $class ) ) {
        $class_instance = new $class();
        $class_instance->register();
    }
}

add_action(
    'admin_enqueue_scripts',
    function () {
        wp_enqueue_style( 'coeditor-airship', COEDITOR_AIRSHIP_ASSETS . 'css/index.css', [], COEDITOR_AIRSHIP_VERSION );
        wp_enqueue_script( 'coeditor-airship', COEDITOR_AIRSHIP_ASSETS . 'js/index.js', [], COEDITOR_AIRSHIP_VERSION, true );
    }
);

add_action(
    'admin_init',
    function () {
        register_setting( 'coeditor_airship_settings', 'coeditor_airship_app_key', [ 'sanitize_callback' => 'sanitize_text_field' ] );
        register_setting( 'coeditor_airship_settings', 'coeditor_airship_master_secret', [ 'sanitize_callback' => 'sanitize_text_field' ] );
        register_setting( 'coeditor_airship_settings', 'coeditor_airship_vapid_key', [ 'sanitize_callback' => 'sanitize_text_field' ] );
        register_setting( 'coeditor_airship_settings', 'coeditor_airship_sdk_app_key', [ 'sanitize_callback' => 'sanitize_text_field' ] );
        register_setting( 'coeditor_airship_settings', 'coeditor_airship_sdk_token', [ 'sanitize_callback' => 'sanitize_text_field' ] );
        register_setting( 'coeditor_airship_settings', 'coeditor_airship_custom_worker_js', [
            'sanitize_callback' => function ( $input ) {
                return is_string( $input ) ? trim( stripslashes( $input ) ) : '';
            },
        ] );
    }
);

add_action( 'plugins_loaded', [ \Coeditor\WordpressAirship\Classes\AirshipPushNotifications::class, 'register' ] );

add_action( 'admin_post_coeditor_airship_delete_worker', function() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Unauthorized user' );
    }

    check_admin_referer( 'coeditor_airship_delete_worker_action' );

    delete_option( 'coeditor_airship_custom_worker_js' );

    wp_safe_redirect( add_query_arg( 'coeditor_airship_message', 'deleted', wp_get_referer() ) );
    exit;
} );

add_action( 'admin_notices', function() {
    if ( isset( $_GET['coeditor_airship_message'] ) && $_GET['coeditor_airship_message'] === 'deleted' ) {
        echo '<div class="notice notice-success is-dismissible"><p>Push-worker script deleted.</p></div>';
    }
} );

add_action( 'init', function () {
    if ( isset( $_SERVER['REQUEST_URI'] ) && strpos( $_SERVER['REQUEST_URI'], '/push-worker.js' ) !== false ) {
        $script = get_option( 'coeditor_airship_custom_worker_js', '' );

        if ( ! empty( $script ) ) {
            header( 'Content-Type: application/javascript' );
            echo $script;
        } else {
            header( 'HTTP/1.1 404 Not Found' );
            echo '// No push-worker.js configured';
        }
        exit;
    }
} );
