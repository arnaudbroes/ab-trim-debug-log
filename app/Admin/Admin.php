<?php
namespace wpPluginBoilerplate\Plugin\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles all admin logic.
 * 
 * @since 1.0.0
 */
class Admin {
	/**
	 * Holds the shortcode class instance.
	 *
	 * @since 1.0.0
	 *
	 * @var wpPluginBoilerplate\Plugin\Admin\Shortcode
	 */
	public $shortcode = null;

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->shortcode = new Shortcode;

		add_action( 'init', [ $this, 'registerCpts' ] );
	}

	/**
	 * Registers all CPTs and custom taxonomies.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function registerCpts() {
		register_post_type( 'exampleDoc', [
			'label'  => 'ExampleDocs',
			'labels' => [
				'singular_name' => 'ExampleDoc'
			],
			'public'       => true,
			'hierarchical' => false,
			'show_in_rest' => true,
			'taxonomies'   => [
				'exampledoccat'
			],
			'supports'     => [
				'excerpt',
				'author',
				'thumbnail',
				'revisions',
				'title',
				'editor'
			]
		] );

		register_taxonomy( 'exampledoccat', 'exampledoc', [
			'label'  => 'Example Doc Categories',
			'labels' => [
				'singular_name' => 'Example Doc Category'
			],
			'public'       => true,
			'hierarchical' => true,
			'show_in_rest' => true
		] );
	}
}