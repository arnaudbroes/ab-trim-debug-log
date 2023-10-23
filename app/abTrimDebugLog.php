<?php
namespace abTrimDebugLog\Plugin {
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	/**
	 * The main class.
	 *
	 * @since 1.0.0
	 */
	class abTrimDebugLog {
		/**
		 * Holds the singleton instance.
		 *
		 * @since 1.0.0
		 *
		 * @var abTrimDebugLog\Plugin\abTrimDebugLog
		 */
		private static $instance;

		/**
		 * Holds the Config class.
		 *
		 * @since 1.0.0
		 *
		 * @var abTrimDebugLog\Plugin\Config\Config
		 */
		public $config = null;

		/**
		 * Holds the Utils class.
		 *
		 * @since 1.0.0
		 *
		 * @var abTrimDebugLog\Plugin\Utils\Utils
		 */
		public $utils = null;

		/**
		 * Holds the Admin class.
		 *
		 * @since 1.0.0
		 *
		 * @var abTrimDebugLog\Plugin\Admin\Admin
		 */
		public $admin = null;

		/**
		 * Holds the API class.
		 *
		 * @since 1.0.0
		 *
		 * @var abTrimDebugLog\Plugin\Api\Api
		 */
		public $api = null;

		/**
		 * Holds the dev environment status.
		 *
		 * @since 1.0.0
		 *
		 * @var bool
		 */
		public $isDev = false;

		/**
		 * Returns singleton main class instance.
		 *
		 * @since 1.0.0
		 *
		 * @return abTrimDebugLog The instance.
		 */
		public static function instance() {
			if ( null === self::$instance || ! self::$instance instanceof self ) {
				self::$instance = new self();
				self::$instance->init();
			}

			return self::$instance;
		}

		/**
		 * Initializes the plugin.
		 * 
		 * @since 1.0.0
		 *
		 * @return void
		 */
		private function init() {
			$this->defineConstants();
			$this->includeDependencies();
			$this->checkIsDev();

			$this->config = new Config\Config;
			$this->utils  = new Utils\Utils;
			$this->admin  = new Admin\Admin;
			$this->api    = new Api\Api;
		}

		/**
		 * Sets all plugin constants.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		private function defineConstants() {
			$headers = [
				'name'    => 'Plugin Name',
				'version' => 'Version'
			];

			$pluginInfo = get_file_data( AB_TRIM_DEBUG_LOG_FILE, $headers );

			$constants = [
				'AB_TRIM_DEBUG_LOG_URL'     => plugin_dir_url( AB_TRIM_DEBUG_LOG_FILE ),
				'AB_TRIM_DEBUG_LOG_NAME'    => $pluginInfo['name'],
				'AB_TRIM_DEBUG_LOG_VERSION' => $pluginInfo['version']
			];

			foreach ( $constants as $name => $value ) {
				if ( ! defined( $name ) ) {
					define( $name, $value );
				}
			}
		}

		/**
		 * Include all dependencies.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		private function includeDependencies() {
			$dependencies = [
				'/vendor/autoload.php' => true
			];

			foreach ( $dependencies as $path => $shouldRequire ) {
				if ( ! file_exists( AB_TRIM_DEBUG_LOG_DIR . $path ) ) {
					// Something is not right.
					status_header( 500 );
					wp_die( esc_html__( 'Plugin is missing required dependencies. Please contact the developer for more information.', 'ab-trim-debug-log' ) );
				}

				if ( $shouldRequire ) {
					require_once AB_TRIM_DEBUG_LOG_DIR . $path;
				}
			}
		}

		/**
		 * Checks if the plugin is oeprating in a dev environment.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		private function checkIsDev() {
			if ( ! file_exists( AB_TRIM_DEBUG_LOG_DIR . '/.env' ) ) {
				return;
			}

			$dotenv = \Dotenv\Dotenv::createUnsafeImmutable( AB_TRIM_DEBUG_LOG_DIR, '/.env' );
			$dotenv->load();

			$this->isDev = getenv( 'VITE_DEV_SERVER_DOMAIN' ) ? true : false;
		}
	}
};

namespace {
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	/**
	 * Returns singleton main class instance.
	 *
	 * @return abTrimDebugLog The instance.
	 */
	function abTrimDebugLog() {
		return \abTrimDebugLog\Plugin\abTrimDebugLog::instance();
	}
}
