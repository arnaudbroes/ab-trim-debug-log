<?php
namespace wpPluginBoilerplate\Plugin\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles all shortcode logic.
 * 
 * @since 1.0.0
 */
class Shortcode {
	/**
	 * Holds the shortcode class instance.
	 *
	 * @since 1.0.0
	 *
	 * @var wpPluginBoilerplate\Plugin\Admin\Shortcode
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'register' ] );
	}

	/**
	 * Registers all shortcodes.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register() {
		add_shortcode( 'example_component', [ $this, 'exampleComponent' ] );
	}

	/**
	 * Executes the [example_component] shortcode.
	 *
	 * @since 1.0.0
	 *
	 * @return string The example component.
	 */
	public function exampleComponent() {
		ob_start();
		if ( is_admin() ) {
			return ob_get_clean();
		}

		echo '<div id="example"></div>';
		wpb()->utils->assets->load( 'src/exampleComponent/main.js', [], [
			'nonce'   => wp_create_nonce( 'wp_rest' ),
			'restUrl' => get_rest_url() . wpb()->api->namespace
		] );

		return ob_get_clean();
	}
}
