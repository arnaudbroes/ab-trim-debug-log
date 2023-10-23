<?php
namespace wpPluginBoilerplate\Plugin\Utils\Traits;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Contains all mail related functions.
 * 
 * @since 1.0.0
 */
trait Mail {
	/**
	 * Sends an e-mail to the given addresses.
	 * Ensures the encoding is valid to prevent character issues.
	 *
	 * @since 1.0.0
	 *
	 * @param  string|array $addresses    The addresses.
	 * @param  string       $subject      The subject.
	 * @param  string       $content      The content.
	 * @param  array        $extraHeaders Extra headers to add to the e-mail.
	 * @return void
	 */
	public function wpMail( $addresses, $subject, $content, $extraHeaders = [] ) {
		return wp_mail(
			$addresses,
			utf8_encode( $subject ),
			utf8_encode( $content ),
			array_merge( [ 'Content-Type: text/html; charset=UTF-8' ], $extraHeaders )
		);
	}
}