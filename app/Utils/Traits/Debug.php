<?php
namespace wpPluginBoilerplate\Plugin\Utils\Traits;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Contains all debug functions.
 * 
 * @since 1.0.0
 */
trait Debug {
	/**
	 * Logs a message to the error log and notifies the admin (if required).
	 *
	 * @since 1.0.0
	 *
	 * @param  mixed  $message The message.
	 * @param  string $origin  The code source where the message origins from.
	 * @param  bool   $sendMail Whether to send an email to the admin.
	 * @return void
	 */
	public function log( $message, $origin = '', $sendMail = false ) {
		// If the message content is not a string, encode it.
		if ( ! is_string( $message ) && ! is_numeric( $message ) ) {
			$message = wp_json_encode( $message );
		}

		if ( $origin ) {
			$message = $origin . ":\r\n\r\n" . $message;
		}

		// Store in error log and e-mail to admin.
		error_log( $message );
		if ( $sendMail ) {
			$siteName = get_bloginfo( 'name' );
			$siteUrl  = home_url();
			error_log( $message, 1, "arnaud@broes.consulting", "From: {$siteName} - {$siteUrl}" );
		}
	}
}