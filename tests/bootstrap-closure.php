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
	echo 'Not a PHPUnit environment.';
	exit( 1 ); // Error status.
}

/**
 * WP Docker?
 *
 * @since 2021-12-15
 */
if ( ! is_file( '/wp-docker/image/setup-complete' )
	|| ! getenv( 'WP_DOCKER_WORDPRESS_DIR' ) ) {
	echo 'Not a WP Docker environment.';
	exit( 1 ); // Error status.
}

/**
 * Bootstrap closure.
 *
 * Loads WP + plugin|theme (if not loaded already).
 *
 * This leverages preinitialized hooks in WordPress core.
 * {@see https://o5p.me/fFC1I7} {@see https://o5p.me/OIrafd}.
 *
 * At priority `9998` this fires right before the
 * {@see \WP_Groove\Framework\A6t\App::load()} hook added by
 * {@see \WP_Groove\Framework\A6t\App::add_plugin_instance_hooks()}.
 *
 * For projects that already have their app active in WordPress,
 * this won't do any harm, as it uses `require_once()`.
 *
 * @since 2022-02-21
 */
return function ( string $file, string $type ) {
	$GLOBALS[ 'wp_filter' ] ??= [];

	if ( 'plugin' === $type ) {
		$GLOBALS[ 'wp_filter' ][ 'plugins_loaded' ]                              ??= [];
		$GLOBALS[ 'wp_filter' ][ 'plugins_loaded' ][ -( PHP_INT_MAX - 9998 ) ]   ??= [];
		$GLOBALS[ 'wp_filter' ][ 'plugins_loaded' ][ -( PHP_INT_MAX - 9998 ) ][] = [
			'accepted_args' => 0,
			'function'      => function () use ( $file, $type ) {
				require_once dirname( $file, 2 ) . '/vendor/autoload.php';
				require_once dirname( $file, 2 ) . '/trunk/plugin.php';
			},
		];
	} elseif ( 'theme' === $type ) {
		$GLOBALS[ 'wp_filter' ][ 'after_setup_theme' ]                              ??= [];
		$GLOBALS[ 'wp_filter' ][ 'after_setup_theme' ][ -( PHP_INT_MAX - 9998 ) ]   ??= [];
		$GLOBALS[ 'wp_filter' ][ 'after_setup_theme' ][ -( PHP_INT_MAX - 9998 ) ][] = [
			'accepted_args' => 0,
			'function'      => function () use ( $file, $type ) {
				require_once dirname( $file, 2 ) . '/vendor/autoload.php';
				require_once dirname( $file, 2 ) . '/trunk/theme.php';
			},
		];
	}
	require_once getenv( 'WP_DOCKER_WORDPRESS_DIR' ) . '/wp-load.php';
};
