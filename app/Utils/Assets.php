<?php
namespace wpPluginBoilerplate\Plugin\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles loading assets.
 * 
 * @since 1.0.0
 */
class Assets {
	/**
	 * Get the script handle to use for asset enqueuing.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $scriptHandle = 'wp-plugin-boilerplate';

	/**
	 * Whether we should load dev scripts.
	 *
	 * @since 1.0.0
	 *
	 * @var bool|null
	 */
	private $shouldLoadDevScripts = null;

	/**
	 * Holds the location of the manifest file.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $manifestFile = '';

	/**
	 * True if we are in a dev environment. This mirrors the global isDev.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	private $isDev = false;

	/**
	 * The development site domain.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $domain = '';

	/**
	 * The development server port.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	protected $port = 0;

	/**
	 * Asset handles that should load as regular JS and not as modern JS module.
	 *
	 * @since 1.0.0
	 *
	 * @var array List of handles.
	 */
	private $noModuleTag = [];

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->manifestFile = WP_PLUGIN_BOILERPLATE_DIR . '/dist/assets/manifest.json';

		if ( wpb()->isDev ) {
			$this->domain = getenv( 'VITE_DEV_SERVER_DOMAIN' );
			$this->port   = getenv( 'VITE_DEV_SERVER_PORT' );
		}

		add_filter( 'script_loader_tag', [ $this, 'scriptLoaderTag' ], 10, 3 );
	}

	/**
	 * Filter the script loader tag if this is our script.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $tag    The tag that is going to be output.
	 * @param  string $handle The handle for the script.
	 * @return string         The modified tag.
	 */
	public function scriptLoaderTag( $tag, $handle = '', $src = '' ) {
		if ( $this->skipModuleTag( $handle ) ) {
			return $tag;
		}

		$tag = str_replace( $src, $this->normalizeAssetsHost( $src ), $tag );

		// Remove the type and re-add it as module.
		$tag = preg_replace( '/type=[\'"].*?[\'"]/', '', $tag );
		$tag = preg_replace( '/<script/', '<script type="module"', $tag );

		return $tag;
	}

	/**
	 * Finds out if a handle should be loaded as regular JS and not as modern JS module.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $handle The script handle.
	 * @return bool           Should the module tag be skipped.
	 */
	public function skipModuleTag( $handle ) {
		if ( false === strpos( $handle, $this->jsHandle( '' ), 0 ) ) {
			return true;
		}

		foreach ( $this->noModuleTag as $tag ) {
			if ( false !== strpos( $handle, $this->jsHandle( '' ), 0 ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Returns the CSS asset handle.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $asset The asset to find the handle for.
	 * @return string        The asset handle.
	 */
	public function cssHandle( $asset ) {
		return "{$this->scriptHandle}/css/$asset";
	}

	/**
	 * Returns the JS asset handle.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $asset The asset to find the handle for.
	 * @return string        The asset handle.
	 */
	public function jsHandle( $asset = '' ) {
		return "{$this->scriptHandle}/js/$asset";
	}

	/**
	 * Returns the public URL base.
	 *
	 * @since 1.0.0
	 *
	 * @return string The URL base.
	 */
	private function getPublicUrlBase() {
		return $this->shouldLoadDev() ? $this->getDevUrl() . 'dist/assets/' : $this->basePath();
	}

	/**
	 * Returns the base path URL.
	 *
	 * @since 1.0.0
	 *
	 * @return string The base path URL.
	 */
	private function basePath() {
		return $this->normalizeAssetsHost( plugins_url( 'dist/assets/', WP_PLUGIN_BOILERPLATE_FILE ) );
	}

	/**
	 * The asset to load.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $asset        The asset to load.
	 * @param  array  $dependencies The list of dependencies.
	 * @param  mixed  $data         The localized data.
	 * @param  string $objectName   The object name to use when localizing.
	 * @return void
	 */
	public function load( $asset, $dependencies = [], $data = null, $objectName = 'wpBoilerplatePlugin' ) {
		$this->jsPreloadImports( $asset );
		$this->loadCss( $asset );
		$this->enqueueJs( $asset, $dependencies, $data, $objectName );
	}

	/**
	 * Preload JS imports.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $asset The asset to load imports for.
	 * @return void
	 */
	private function jsPreloadImports( $asset ) {
		$res = '';
		foreach ( $this->importsUrls( $asset ) as $url ) {
			$res .= '<link rel="modulepreload" href="' . $url . "\">\n";
		}

		if ( ! empty( $res ) ) {
			add_action( 'admin_head', function () use ( &$res ) {
				echo $res; // phpcs:ignore
			} );
			add_action( 'wp_head', function () use ( &$res ) {
				echo $res; // phpcs:ignore
			} );
		}
	}

	/**
	 * Loads CSS for an asset from the manifest file.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $asset The script to load CSS for.
	 * @return void
	 */
	public function loadCss( $asset ) {
		if ( $this->shouldLoadDev() ) {
			return;
		}

		foreach ( $this->getCssUrls( $asset ) as $file => $url ) {
			wp_enqueue_style( $this->cssHandle( $file ), $url, [], WP_PLUGIN_BOILERPLATE_VERSION );
		}
	}

	/**
	 * Register a CSS asset.
	 *
	 * @since 4.1.9
	 *
	 * @param  string $asset        The script to load CSS for.
	 * @param  array  $dependencies An array of dependencies.
	 * @return void
	 */
	public function registerCss( $asset, $dependencies = [] ) {
		$handle = $this->cssHandle( $asset );
		if ( wp_style_is( $handle, 'registered' ) ) {
			return;
		}

		$url = $this->shouldLoadDev()
			? $this->getDevUrl() . ltrim( $asset, '/' )
			: $this->assetUrl( $asset );

		if ( ! $url ) {
			return;
		}

		wp_register_style( $handle, $url, $dependencies, WP_PLUGIN_BOILERPLATE_VERSION );
	}

	/**
	 * Enqueue css.
	 *
	 * @since 4.1.9
	 *
	 * @param  string $asset        The css to load.
	 * @param  array  $dependencies An array of dependencies.
	 * @return void
	 */
	public function enqueueCss( $asset, $dependencies = [] ) {
		$this->registerCss( $asset, $dependencies );

		$handle = $this->cssHandle( $asset );
		if ( wp_style_is( $handle, 'enqueued' ) ) {
			return;
		}

		wp_enqueue_style( $handle );
	}

	/**
	 * Register the JS to enqueue.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $asset        The script to load.
	 * @param  array  $dependencies An array of dependencies.
	 * @param  mixed  $data         Any data to be localized.
	 * @param  string $objectName   The object name to use when localizing.
	 * @return void
	 */
	public function registerJs( $asset, $dependencies = [], $data = null, $objectName = 'wpBoilerplatePlugin' ) {
		$handle = $this->jsHandle( $asset );
		if ( wp_script_is( $handle, 'registered' ) ) {
			return;
		}

		$url = $this->shouldLoadDev()
			? $this->getDevUrl() . ltrim( $asset, '/' )
			: $this->jsUrl( $asset );

		if ( ! $url ) {
			return;
		}

		wp_register_script( $handle, $url, $dependencies, WP_PLUGIN_BOILERPLATE_VERSION, true );

		if ( empty( $data ) ) {
			return;
		}

		wp_localize_script(
			$handle,
			$objectName,
			$data
		);
	}

	/**
	 * Register the JS to enqueue.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $asset        The script to load.
	 * @param  array  $dependencies An array of dependencies.
	 * @param  mixed  $data         Any data to be localized.
	 * @param  string $objectName   The object name to use when localizing.
	 * @return void
	 */
	public function enqueueJs( $asset, $dependencies = [], $data = null, $objectName = 'wpBoilerplatePlugin' ) {
		$this->registerJs( $asset, $dependencies, $data, $objectName );

		$handle = $this->jsHandle( $asset );
		if ( wp_script_is( $handle, 'enqueued' ) ) {
			return;
		}

		wp_enqueue_script( $handle );
	}

	/**
	 * Returns the dev URL.
	 *
	 * @since 1.0.0
	 *
	 * @return string The dev URL.
	 */
	private function getDevUrl() {
		$protocol = is_ssl() ? 'https://' : 'http://';

		return $protocol . $this->domain . ':' . $this->port . '/';
	}

	/**
	 * Returns the asset URL.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $asset The asset to find the URL for.
	 * @return string        The URL for the asset.
	 */
	private function assetUrl( $asset ) {
		$assetManifest = $this->getAssetManifestItem( $asset );

		return ! empty( $assetManifest['file'] )
			? $this->basePath() . $assetManifest['file']
			: $this->basePath() . ltrim( $asset, '/' );
	}

	/**
	 * Returns the JS URL.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $asset The asset to find the URL for.
	 * @return string        The URL for the asset.
	 */
	public function jsUrl( $asset ) {
		$manifestAsset = $this->getManifestItem( $asset );

		return ! empty( $manifestAsset['file'] )
			? $this->basePath() . $manifestAsset['file']
			: $this->basePath() . ltrim( $asset, '/' );
	}

	/**
	 * Returns the manifest to load assets from.
	 *
	 * @since 1.0.0
	 *
	 * @return array An array of files.
	 */
	private function getManifest() {
		static $file = null;
		if ( $file ) {
			return $file;
		}

		$manifestJson = file_get_contents( $this->manifestFile );

		$file = json_decode( $manifestJson, true );

		return $file;
	}

	/**
	 * Returns an item from the asset manifest.
	 *
	 * @since 1.0.0
	 *
	 * @param  string      $item An item to retrieve.
	 * @return string|null       The asset item.
	 */
	private function getAssetManifestItem( $item ) {
		$assetManifest = $this->getManifest();

		return ! empty( $assetManifest[ $item ] ) ? $assetManifest[ $item ] : null;
	}

	/**
	 * Returns an item from the manifest.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $asset The asset to find.
	 * @return string        Manifest object.
	 */
	private function getManifestItem( $asset ) {
		$manifest = $this->getManifest();

		$asset = ltrim( $asset, '/' );

		return isset( $manifest[ $asset ] ) ? $manifest[ $asset ] : null;
	}

	/**
	 * Returns an asset's list of URLs to import.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $asset The asset to find imports for.
	 * @return array         Lists of imports.
	 */
	private function importsUrls( $asset ) {
		$urls          = [];
		$manifestAsset = $this->getManifestItem( $asset );
		if ( ! empty( $manifestAsset['imports'] ) ) {
			foreach ( $manifestAsset['imports'] as $import ) {
				$importAsset = $this->getManifestItem( $import );
				if ( ! empty( $importAsset['file'] ) ) {
					$urls[] = $this->getPublicUrlBase() . $importAsset['file'];

					// Load the import's CSS files (if needed).
					$this->loadCss( $import );
				}
			}
		}

		return $urls;
	}

	/**
	 * Returns an asset's CSS urls.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $asset The asset to find CSS URLs for.
	 * @return array         An array of CSS URLs to load.
	 */
	private function getCssUrls( $asset ) {
		$urls          = [];
		$manifestAsset = $this->getManifestItem( $asset );

		if ( ! empty( $manifestAsset['css'] ) ) {
			foreach ( $manifestAsset['css'] as $file ) {
				$urls[ $file ] = $this->getPublicUrlBase() . $file;
			}
		}

		return $urls;
	}

	/**
	 * Check if we should load the dev watcher scripts.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Whether we should load the dev watcher scripts.
	 */
	private function shouldLoadDev() {
		if ( null !== $this->shouldLoadDevScripts ) {
			return $this->shouldLoadDevScripts;
		}

		if (
			! wpb()->isDev ||
			! $this->domain ||
			! $this->port
		) {
			$this->shouldLoadDevScripts = false;

			return $this->shouldLoadDevScripts;
		}

		set_error_handler( function() {} );
		$connection = fsockopen( $this->domain, $this->port ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		restore_error_handler();

		if ( ! $connection ) {
			$this->shouldLoadDevScripts = false;

			return $this->shouldLoadDevScripts;
		}

		$this->shouldLoadDevScripts = true;

		return $this->shouldLoadDevScripts;
	}

	/**
	 * Returns the path for the assets.
	 *
	 * @since 1.0.0
	 *
	 * @param  bool   $maybeDev Whether to try and load dev scripts.
	 * @return string           The path for the assets.
	 */
	public function getAssetsPath( $maybeDev = true ) {
		return $maybeDev && $this->shouldLoadDev()
			? $this->getDevUrl()
			: $this->basePath();
	}

	/**
	 * Normalize the assets host. Some sites manually set the WP_PLUGINS_URL
	 * and if that domain has www. and the site_url does not, then it will fail to load
	 * our assets. This doesn't fix the issue 100% because it will still fail on
	 * sub-domains that don't have the proper CORS headers. Those sites will need
	 * manual fixes.
	 *
	 * 1.0.0
	 *
	 * @param  string $path The path to normalize.
	 * @return string       The normalized path.
	 */
	public function normalizeAssetsHost( $path ) {
		static $paths = [];
		if ( isset( $paths[ $path ] ) ) {
			return $paths[ $path ];
		}

		// We need to verify the domain on the $path attribute matches
		// what's in site_url() for our assets or they won't load.
		$siteUrl        = site_url();
		$siteUrlEscaped = preg_quote( $siteUrl, '/' );
		if ( preg_match( "/^$siteUrlEscaped/i", $path ) ) {
			$paths[ $path ] = $path;

			return $paths[ $path ];
		}

		// We now know that the path doesn't contain the site_url().
		$newPath        = $path;
		$siteUrlParsed  = wp_parse_url( $siteUrl );
		$host           = preg_quote( str_replace( 'www.', '', $siteUrlParsed['host'] ), '/' );
		$scheme         = preg_quote( $siteUrlParsed['scheme'], '/' );

		$siteUrlHasWww = preg_match( "/^{$scheme}:\/\/www\.$host/", $siteUrl );
		$pathHasWww    = preg_match( "/^{$scheme}:\/\/www\.$host/", $path );

		// Check if the path contains www.
		if ( $pathHasWww && ! $siteUrlHasWww ) {
			// If the path contains www., we want to strip it out.
			$newPath = preg_replace( "/^({$scheme}:\/\/)(www\.)($host)/", '$1$3', $path );
		}

		// Check if the site_url contains www.
		if ( $siteUrlHasWww && ! $pathHasWww ) {
			// If the site_url contains www., we want to add it in to the path.
			$newPath = preg_replace( "/^({$scheme}:\/\/)($host)/", '$1www.$2', $path );
		}

		$paths[ $path ] = $newPath;

		return $paths[ $path ];
	}

	/**
	 * Get all the CSS files which a JS asset depends on.
	 * This won't work properly unless you've run `npm run build` first.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $asset The asset to find the CSS dependencies for.
	 * @return array         All the asset's CSS dependencies if any.
	 */
	public function getJsAssetCssQueue( $asset ) {
		$queue = [];

		foreach ( $this->getCssUrls( $asset ) as $file => $url ) {
			$queue[] = [
				'handle' => $this->cssHandle( $file ),
				'url'    => $url
			];
		}

		$manifestAsset = $this->getManifestItem( $asset );
		if ( ! empty( $manifestAsset['imports'] ) ) {
			foreach ( $manifestAsset['imports'] as $subAsset ) {
				foreach ( $this->getCssUrls( $subAsset ) as $file => $url ) {
					$queue[] = [
						'handle' => $this->cssHandle( $file ),
						'url'    => $url
					];
				}
			}
		}

		return $queue;
	}
}