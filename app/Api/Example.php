<?php
namespace wpPluginBoilerplate\Plugin\Api;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Contains all Example related endpoints.
 *
 * @since 1.0.0
 */
class Example {
	/**
	 * Returns all Example posts.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_REST_Request $request The request object.
	 *
	 * @return \WP_REST_Response The response object.
	 */
	public function all( $request ) {
		$posts = [];

		return new \WP_REST_Response( [
			'success' => true,
			'data'    => $posts
		], 200 );
	}
}