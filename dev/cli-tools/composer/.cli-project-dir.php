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
declare( strict_types = 1 );
namespace WP_Groove\Framework\Dev\CLI_Tools\Composer;

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
 */
if ( is_dir( dirname( __FILE__, 4 ) . '/vendor' ) ) {
	return require dirname( __FILE__, 4 ) . '/vendor/clevercanyon/php-js-utilities/dev/cli-tools/composer/.cli-project-dir.php';
} else { // Not root. We're inside a /vendor directory.
	return require dirname( __FILE__, 7 ) . '/vendor/clevercanyon/php-js-utilities/dev/cli-tools/composer/.cli-project-dir.php';
}
