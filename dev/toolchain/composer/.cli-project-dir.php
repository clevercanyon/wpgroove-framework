#!/usr/bin/env php
<?php
/**
 * WP Groove™ {@see https://wpgroove.com}
 *  _       _  ___       ___
 * ( )  _  ( )(  _`\    (  _`\
 * | | ( ) | || |_) )   | ( (_) _ __   _      _    _   _    __  ™
 * | | | | | || ,__/'   | |___ ( '__)/'_`\  /'_`\ ( ) ( ) /'__`\
 * | (_/ \_) || |       | (_, )| |  ( (_) )( (_) )| \_/ |(  ___/
 * `\___x___/'(_)       (____/'(_)  `\___/'`\___/'`\___/'`\____)
 */
// <editor-fold desc="Strict types, namespace, use statements, and other headers.">

/**
 * Declarations & namespace.
 *
 * @since 2021-12-25
 */
declare( strict_types = 1 ); // ｡･:*:･ﾟ★.
namespace WP_Groove\Framework\Dev\Toolchain\Composer;

// </editor-fold>

/**
 * CLI mode only.
 *
 * @since 2021-12-15
 */
if ( 'cli' !== PHP_SAPI ) {
	exit( 1 ); // CLI mode only.
}

/**
 * Locates project directory.
 *
 * @since 2021-12-15
 *
 * @throws \Exception On any failure.
 * @returns string Absolute project directory path.
 */
return ( function () : string {
	global $argv;

	// Potentially relative.
	$script_file = $argv[ 0 ];
	$script_dir  = dirname( $script_file );

	// Confirm that we are starting from a good location.
	// If either of these are false we have a serious problem.
	if ( ! is_file( $script_file ) || ! is_dir( $script_dir ) ) {
		throw new \Exception( 'Failed to acquire script file|directory: `' . $script_file . '`.' );
	}
	// Look for nearest project directory that's not a symlink.
	// Symlinks are bypassed because we use them locally for development.
	// e.g., `[project-dir]/vendor/clevercanyon/[project-symlink]/composer.json`.
	for ( $_i = 0; $_i <= 25; $_i++ ) {
		$_project_file          = $script_dir . str_repeat( '/..', $_i ) . '/composer.json';
		$_project_autoload_file = dirname( $_project_file ) . '/vendor/autoload.php';

		if ( is_file( $_project_file ) && is_file( $_project_autoload_file ) ) {
			$_project_dir = dirname( $_project_file );

			if ( ! is_link( $_project_dir ) ) {
				return realpath( $_project_dir );
			}
		}
	} // Throw exception on any failure.
	throw new \Exception( 'Failed to acquire project directory.' );
} )();
