<?php
/**
 * Plugin Name: AB Trim Debug Log
 * Plugin URI: https://github.com/arnaudbroes/ab-trim-debug-log
 * Description: Simple plugin that trims the debug log when it becomes too large.
 * Version: 1.0.0
 * Requires at least: 4.9.0
 * Requires PHP: 7.4
 * Author: Broes Consulting
 * Author URI: https://broes.consulting
 * Text Domain: ab-trim-debug-log
 *
 * License: GPL-3.0-or-later
 * URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'AB_TRIM_DEBUG_LOG_FILE' ) ) {
	define( 'AB_TRIM_DEBUG_LOG_FILE', __FILE__ );
}

if ( ! defined( 'AB_TRIM_DEBUG_LOG_DIR' ) ) {
	define( 'AB_TRIM_DEBUG_LOG_DIR', dirname( AB_TRIM_DEBUG_LOG_FILE ) );
}

require_once( AB_TRIM_DEBUG_LOG_DIR . '/app/abTrimDebugLog.php' );

abTrimDebugLog();