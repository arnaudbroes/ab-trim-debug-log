<?php
/**
 * Plugin Name: AB Truncate Debug Log
 * Plugin URI: https://github.com/arnaudbroes/ab-truncate-debug-log
 * Description: Simple plugin to prevent the WP debug log file from growing too large.
 * Version: 1.0.0
 * Requires at least: 4.9.0
 * Requires PHP: 7.4
 * Author: Broes Consulting
 * Author URI: https://broes.consulting
 * Text Domain: ab-truncate-debug-log
 *
 * License: GPL-3.0-or-later
 * URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'AB_TRUNCATE_DEBUG_LOG_FILE' ) ) {
	define( 'AB_TRUNCATE_DEBUG_LOG_FILE', __FILE__ );
}

if ( ! defined( 'AB_TRUNCATE_DEBUG_LOG_DIR' ) ) {
	define( 'AB_TRUNCATE_DEBUG_LOG_DIR', dirname( AB_TRUNCATE_DEBUG_LOG_FILE ) );
}

require_once( AB_TRUNCATE_DEBUG_LOG_DIR . '/app/abTruncateDebugLog.php' );

abTruncateDebugLog();