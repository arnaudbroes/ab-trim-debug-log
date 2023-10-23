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
		 */
		private static $instance;

		/**
		 * Holds the Cron class.
		 *
		 * @since 1.0.0
		 */
		public Cron\Cron $cron;

		/**
		 * Returns singleton main class instance.
		 *
		 * @since 1.0.0
		 *
		 * @return abTrimDebugLog The instance.
		 */
		public static function instance() : self {
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
		 */
		private function init() : void {
			$this->defineConstants();
			$this->includeDependencies();

			$this->cron = new Cron\Cron;
		}

		/**
		 * Sets all plugin constants.
		 *
		 * @since 1.0.0
		 */
		private function defineConstants() : void {
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
		 */
		private function includeDependencies() : void {
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
	}
};

namespace {
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	/**
	 * Returns singleton main class instance.
	 */
	function abTrimDebugLog() : abTrimDebugLog\Plugin\abTrimDebugLog {
		return \abTrimDebugLog\Plugin\abTrimDebugLog::instance();
	}
}
