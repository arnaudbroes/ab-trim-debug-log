<?php
namespace abTrimDebugLog\Plugin\Cron;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles all WP Cron logic.
 * 
 * @since 1.0.0
 */
class Cron {
	/**
	 * The cron job name.
	 *
	 * @since 1.0.0
	 */
	private string $cronJobName = 'ab_trim_debug_log';

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'scheduleCronJob' ] );
		add_action( 'ab_trim_debug_log', [ $this, 'trimDebugLog' ] );
	}

	/**
	 * Schedules our cron job.
	 *
	 * @since 1.0.0
	 */
	public function scheduleCronJob() : void {
		if ( ! wp_next_scheduled( 'ab_trim_debug_log' ) ) {
			wp_schedule_event( time(), 'weekly', 'ab_trim_debug_log' );
		}
	}

	/**
	 * Trims the debug log.
	 *
	 * @since 1.0.0
	 */
	public function trimDebugLog() : void {
		if ( ! defined( 'WP_DEBUG' ) || ! defined( 'WP_CONTENT_DIR' ) ) {
			return;
		}

		$debugLogPath = WP_CONTENT_DIR . '/debug.log';
		if ( ! file_exists( $debugLogPath ) ) {
			return;
		}

		$fileSize         = filesize( $debugLogPath );
		$fileSizeTreshold = apply_filters( 'ab_trim_debug_log_maximum_file_size', 100000000 ); // Default is 100 MB.
		if ( $fileSize < $fileSizeTreshold ) {
			return;
		}

		// First, attempt to use the tail command as this is the fastest approach.
		if ( $this->doesCommandExist( 'tail' ) ) {
			try {
				shell_exec( sprintf( 'tail -n 1000 %s > debug.log.tmp', escapeshellarg( $debugLogPath ) ) );

				unlink( $debugLogPath );
				rename( WP_CONTENT_DIR . '/debug.log.tmp', $debugLogPath );
			} catch( \Exception $e ) {
				// Do nothing.
			}
		}

		// If the filesize is still too big, we use a PHP approach. We'll stream the contents and keep the last 1.000 lines.
		$fileSize = filesize( $debugLogPath );
		if ( $fileSize < $fileSizeTreshold ) {
			return;
		}

		$fp = fopen( $debugLogPath, 'r' );

		// Initialize the circular buffer.
		$buffer      = array_fill( 0, 1000, '' );
		$bufferIndex = 0;

		// Iterate over each line.
		while ( $line = fgets( $fp ) ) {
			// Add the line to the buffer.
			$buffer[ $bufferIndex ] = $line;

			// Increment the buffer index.
			$bufferIndex = ( $bufferIndex + 1 ) % 1000;
		}

		fclose( $fp );

		// Write the last 1000 lines to the file.
		file_put_contents( $debugLogPath, implode( '', $buffer ) );
	}

	/**
	 * Checks if a command exists.
	 *
	 * @since 1.0.0
	 */
	private function doesCommandExist( string $commandExist ) : bool {
		$return = shell_exec( sprintf( "which %s", escapeshellarg( $commandExist ) )) ;

		return ! empty( $return );
	}
}