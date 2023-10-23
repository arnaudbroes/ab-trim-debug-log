<?php
namespace wpPluginBoilerplate\Plugin\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles our utils.
 * 
 * @since 1.0.0
 */
class Utils {
	/**
	 * Holds the assets class.
	 *
	 * @since 1.0.0
	 *
	 * @var wpPluginBoilerplate\Plugin\Utils\Assets
	 */
	public $assets = null;

	/**
	 * Holds the helpers class.
	 *
	 * @since 1.0.0
	 *
	 * @var wpPluginBoilerplate\Plugin\Utils\Helpers
	 */
	public $helpers = null;

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->assets  = new Assets;
		$this->helpers = new Helpers;
	}
}