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
 * Lint configuration.
 *
 * @since 2021-12-25
 *
 * phpcs:disable
 */

/**
 * Declarations & namespace.
 *
 * @since 2021-12-25
 */
declare( strict_types = 1 );
namespace WP_Groove\Framework\Tests;

// </editor-fold>

/**
 * PHPUnit?
 *
 * @since 2021-12-15
 */
if ( ! getenv( 'IS_PHPUNIT' ) ) {
	exit( 'Not a PHPUnit environment.' );
}

/**
 * WP Docker?
 *
 * @since 2021-12-15
 */
if ( ! is_file( '/wp-docker/image/setup-complete' )
	|| ! getenv( 'WP_DOCKER_WORDPRESS_DIR' ) ) {
	exit( 'Not a WP Docker environment.' );
}

/**
 * Bootstrap.
 *
 * Loads WP + plugin (if it hasn't loaded already).
 *
 * This leverages preinitialized hooks in WordPress core.
 * {@see https://o5p.me/fFC1I7} {@see https://o5p.me/OIrafd}.
 *
 * At priority `10001` this fires right after a plugin is supposed to
 * have already been loaded by `WPG\A6t\App::add_plugin_instance_hooks()`.
 *
 * This is helpful when integration testing. WordPress doesn't load like
 * it does in the normal course of things when running integration tests.
 * The code below ensures that it does. It's also loading WordPress.
 *
 * @since 2022-02-21
 */
$GLOBALS[ 'wp_filter' ]                                                 ??= [];
$GLOBALS[ 'wp_filter' ][ 'plugins_loaded' ]                             ??= [];
$GLOBALS[ 'wp_filter' ][ 'plugins_loaded' ][ -( PHP_INT_MAX - 10001 ) ] ??= [];

$GLOBALS[ 'wp_filter' ][ 'plugins_loaded' ][ -( PHP_INT_MAX - 10001 ) ][] = [
	'accepted_args' => 0,
	'function'      => function () {
		require_once dirname( __FILE__, 2 ) . '/vendor/autoload.php';
		require_once dirname( __FILE__, 2 ) . '/trunk/plugin.php';

		echo 'WordPress loaded.';
		exit;
	},
];
require_once getenv( 'WP_DOCKER_WORDPRESS_DIR' ) . '/wp-load.php';
