<?php
/**
 * Plugin Name: Admin Tour
 * Plugin URI: https://wordpress.org/plugins/admin-tour/
 * Description: Admin Tour helps you to create a tour for admin. Admin user can go through the tour and they will get the knowledge about how to use the admin panel.
 * Version: 1.3
 * Author: KrishaWeb
 * Author URI: https://krishaweb.com/
 * Text Domain: admin-tour
 * Domain Path: /languages
 *
 * @package WordPress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

define( 'WAT_ABSPATH', __DIR__ );

// Require files.
require_once WAT_ABSPATH . '/includes/pointers.php';
require_once WAT_ABSPATH . '/includes/class-wp-admin-tour.php';

/**
 * Register activation.
 */
function wat_activation() {
	add_option( 'wat-activated', true );
}
register_activation_hook( __FILE__, 'wat_activation' );

/**
 * Register deactivation.
 */
function wat_deactivation() {
	delete_option( 'wat-activated' );
}
register_deactivation_hook( __FILE__, 'wat_deactivation' );

/**
 * Register uninstall.
 */
function wat_uninstall() {
	delete_option( 'wat-activated' );
}
register_uninstall_hook( __FILE__, 'wat_uninstall' );

/**
 * Plugin loaded.
 */
function wat_plugin_loaded() {
	// Register text domain.
	load_plugin_textdomain( 'admin-tour', false, basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'wat_plugin_loaded' );

/**
 * Init.
 */
function wat_init() {
	if ( class_exists( '\WP_Admin_Tour' ) ) {
		return new \WP_Admin_Tour();
	}
	return null;
}

$wat_init = wat_init();
if ( method_exists( $wat_init, 'init_hooks' ) ) {
	$wat_init->init_hooks();
}
