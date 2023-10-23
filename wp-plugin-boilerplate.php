<?php
/**
 * Plugin Name: WP Plugin Boilerplate
 * Plugin URI: https://broes.consulting
 * Description: Personal boilerplate for WordPress plugins.
 * Version: 1.0.0
 * Requires at least: 4.9.0
 * Requires PHP: 7.4
 * Author: Broes Consulting
 * Author URI: https://broes.consulting
 * Text Domain: wp-plugin-boilerplate
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! defined( 'WP_PLUGIN_BOILERPLATE_FILE' ) ) {
	define( 'WP_PLUGIN_BOILERPLATE_FILE', __FILE__ );
}

if ( ! defined( 'WP_PLUGIN_BOILERPLATE_DIR' ) ) {
	define( 'WP_PLUGIN_BOILERPLATE_DIR', dirname( WP_PLUGIN_BOILERPLATE_FILE ) );
}

require_once( WP_PLUGIN_BOILERPLATE_DIR . '/app/wpPluginBoilerplate.php' );

wpb();