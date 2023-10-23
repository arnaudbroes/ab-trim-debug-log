<?php
// Load the autoload file for Composer so that we can access RoboCI.
if ( ! file_exists( 'vendor/autoload.php' ) ) {
	header( 500 );
	die( 'RoboFile is missing required dependencies.' );
}

require 'vendor/autoload.php';

define( 'ROBO_DIR', __DIR__ );

/**
 * Registers and defines the commands we use to build the plugin.
 *
 * @since 1.0.0
 */
class RoboFile extends \Robo\Tasks {
	/**
	 * The plugin slug.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $pluginSlug = 'ab-truncate-debug-log';

	/**
	 * The version number.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $version = '1.0.0';

	/**
	 * Creates a new ZIP file for the plugin that can be used on production.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $version The version number.
	 * @return void
	 */
	public function build( $version = '1.0.0' ) {
		$this->version = $version;

		$this->start( 'Starting to generate ZIP' );

		$this->deleteCache();

		$this->deleteTempDirs();
		$this->moveFilesToTempDirs();
		$this->createArchive();
		$this->deleteTempDirs();

		$this->success( 'Finished generating ZIP' );
	}

	/**
	 * Deletes cached files.
	 *
	 * @return void
	 */
	private function deleteCache() {
		$this->collectionBuilder()
			->taskDeleteDir( './node_modules/.cache' )
			->taskDeleteDir( './node_modules/.vite' )
			->run();
	}

	/**
	 * Deletes the temp directories.
	 *
	 * @return void
	 */
	private function deleteTempDirs() {
		$this->collectionBuilder()
			->taskDeleteDir( '_robo-temp' )
			->taskDeleteDir( '_robo-working-dir' )
			->run();
	}


	/**
	 * Moves the files to the temp directories.
	 *
	 * @return void
	 */
	private function moveFilesToTempDirs() {
		$this->collectionBuilder()
			->taskCopyDir( [ './app' => '_robo-temp/app' ] )
			->taskFilesystemStack()
				->copy( "./{$this->pluginSlug}.php", "_robo-temp/{$this->pluginSlug}.php" )
				->copy( './composer.json', '_robo-temp/composer.json' )
				->copy( './composer.lock', '_robo-temp/composer.lock' )
			->completion(
				// Update Composer dependencies.
				$this->taskComposerInstall()
					->noDev()
					->noInteraction()
					->dir( '_robo-temp' )
			)
			->run();

			// Now, remove the Composer files.
			$this->collectionBuilder()
			->taskFilesystemStack()
				->remove( '_robo-temp/composer.json' )
				->remove( '_robo-temp/composer.lock' )
			->run();

		// Update version in main plugin file.
		$this->collectionBuilder()
		->taskReplaceInFile( "_robo-temp/{$this->pluginSlug}.php" )
			->regex( '/ \* Version:([\s]*).*$/im' )
			->to( ' * Version:${1}' . $this->version )
		->run();
	}

	/**
	 * Creates the ZIP archive.
	 *
	 * @return void
	 */
	private function createArchive() {
		$this->info( 'Creating ZIP file' );

		$collection  = $this->collectionBuilder();
		$workingPath = $collection->workDir( '_robo-working-dir' );

		$collection
			->taskCopyDir( [ '_robo-temp/app' => "{$workingPath}/app" ] )
			->taskCopyDir( [ '_robo-temp/vendor' => "{$workingPath}/vendor" ] )
			->taskFilesystemStack()
				->copy( "_robo-temp/{$this->pluginSlug}.php", "{$workingPath}/{$this->pluginSlug}.php" )
			->run();

		$zip = "_builds/{$this->pluginSlug}-v{$this->version}.zip";

		// Delete existing archive file and then create a new one.
		$collection
			->taskFilesystemStack()
				->remove( $zip )
			->taskPack( $zip )
				->addDir( $this->pluginSlug, '_robo-working-dir' )
				->exclude( [ '.DS_Store' ] )
			->run();
	}

	/**
	 * Yells an info message.
	 *
	 * @param  string $message The message.
	 * @return void
	 */
	private function info( $message ) {
		$this->yell( $message, 60, 'blue' );
	}

	/**
	 * Yells a start message.
	 *
	 * @param  string $message The message.
	 * @return void
	 */
	private function start( $message ) {
		$this->yell( $message, 60, 'cyan' );
	}

	/**
	 * Yells a success message.
	 *
	 * @param  string $message The message.
	 * @return void
	 */
	private function success( $message ) {
		$this->yell( $message, 60, 'green' );
	}

	/**
	 * Yells an error message.
	 *
	 * @param  string $message The message.
	 * @return void
	 */
	private function error( $message ) {
		$this->yell( $message, 60, 'red' );
	}
}