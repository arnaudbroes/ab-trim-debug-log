<?php
namespace abTrimDebugLog\Plugin\Utils;

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
	 * Holds the helpers class.
	 *
	 * @since 1.0.0
	 *
	 * @var abTrimDebugLog\Plugin\Utils\Helpers
	 */
	public $helpers = null;

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->helpers = new Helpers;
	}
}