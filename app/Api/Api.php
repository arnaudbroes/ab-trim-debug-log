<?php
namespace wpPluginBoilerplate\Plugin\Api;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers our routes.
 *
 * @since 1.0.0
 */
class Api {
	/**
	 * The namespace for our routes.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $namespace = 'example/v1';

	/**
	 * Holds all routes.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $routes = [];

	/**
	 * Initializes the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->setRoutes();

		add_filter( 'rest_allowed_cors_headers', [ $this, 'allowedHeaders' ] );
		add_action( 'rest_api_init', [ $this, 'registerRoutes' ] );
	}

	/**
	 * Sets all routes.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	// TODO: Consider adding support for static classes.
	private function setRoutes() {
		$exampleInstance = new Example;

		$this->routes = [
			'GET'  => [
				'example/all' => [ 'callback' => [ $exampleInstance, 'all' ] ]
			]
		];
	}

	/**
	 * Adds the X-WP-Nonce header to the allowed headers.
	 *
	 * @since 1.0.0
	 *
	 * @param  array  $allowHeaders The allowed headers.
	 * @return array                The allowed headers.
	 */
	public function allowedHeaders( $allowHeaders ) {
		if ( ! array_search( 'X-WP-Nonce', $allowHeaders, true ) ) {
			$allowHeaders[] = 'X-WP-Nonce';
		}

		return $allowHeaders;
	}

	/**
	 * Registers all routes.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function registerRoutes() {
		foreach ( $this->routes as $method => $routes ) {
			foreach ( $routes as $route => $data ) {
				register_rest_route(
					$this->namespace,
					$route,
					[
						'methods'             => $method,
						'callback'            => $data['callback'],
						'permission_callback' => [ $this, 'validateRequest' ]
					]
				);
			}
		}
	}

	/**
	 * Validates the request.
	 *
	 * @since 1.0.0
	 *
	 * @param  WP_REST_Request $request The request.
	 * @return bool                     Whether the request is valid.
	 */
	public function validateRequest( $request ) {
		// TODO: Add more complex, strict checks here if needed.
		return is_user_logged_in();
	}
}