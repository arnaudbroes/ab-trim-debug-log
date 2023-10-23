<?php
namespace wpPluginBoilerplate\Plugin {
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	/**
	 * The main class.
	 *
	 * @since 1.0.0
	 */
	class wpPluginBoilerplate {
		/**
		 * Holds the singleton instance.
		 *
		 * @since 1.0.0
		 *
		 * @var wpPluginBoilerplate\Plugin\wpPluginBoilerplate
		 */
		private static $instance;

		/**
		 * Holds the Config class.
		 *
		 * @since 1.0.0
		 *
		 * @var wpPluginBoilerplate\Plugin\Config\Config
		 */
		public $config = null;

		/**
		 * Holds the Utils class.
		 *
		 * @since 1.0.0
		 *
		 * @var wpPluginBoilerplate\Plugin\Utils\Utils
		 */
		public $utils = null;

		/**
		 * Holds the Admin class.
		 *
		 * @since 1.0.0
		 *
		 * @var wpPluginBoilerplate\Plugin\Admin\Admin
		 */
		public $admin = null;

		/**
		 * Holds the API class.
		 *
		 * @since 1.0.0
		 *
		 * @var wpPluginBoilerplate\Plugin\Api\Api
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
		 * @return wpPluginBoilerplate The instance.
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

			$pluginInfo = get_file_data( WP_PLUGIN_BOILERPLATE_FILE, $headers );

			$constants = [
				'WP_PLUGIN_BOILERPLATE_URL'     => plugin_dir_url( WP_PLUGIN_BOILERPLATE_FILE ),
				'WP_PLUGIN_BOILERPLATE_NAME'    => $pluginInfo['name'],
				'WP_PLUGIN_BOILERPLATE_VERSION' => $pluginInfo['version']
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
				if ( ! file_exists( WP_PLUGIN_BOILERPLATE_DIR . $path ) ) {
					// Something is not right.
					status_header( 500 );
					wp_die( esc_html__( 'Plugin is missing required dependencies. Please contact the developer for more information.', 'wp-plugin-boilerplate' ) );
				}

				if ( $shouldRequire ) {
					require_once WP_PLUGIN_BOILERPLATE_DIR . $path;
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
			if ( ! file_exists( WP_PLUGIN_BOILERPLATE_DIR . '/.env' ) ) {
				return;
			}

			$dotenv = \Dotenv\Dotenv::createUnsafeImmutable( WP_PLUGIN_BOILERPLATE_DIR, '/.env' );
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
	 * @return wpPluginBoilerplate The instance.
	 */
	function wpb() {
		return \wpPluginBoilerplate\Plugin\wpPluginBoilerplate::instance();
	}
}
