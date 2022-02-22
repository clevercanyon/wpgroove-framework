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
namespace WP_Groove\Framework;

/**
 * Utilities.
 *
 * @since 2021-12-15
 */
use Clever_Canyon\{Utilities as U};

/**
 * Framework.
 *
 * @since 2021-12-15
 */
use WP_Groove\{Framework as WPG};

// </editor-fold>

/**
 * WP Docker in test mode?
 *
 * @since 2021-12-15
 */
if ( ! U\Env::is_wp_docker() || ! U\Env::in_test_mode( 'phpunit' ) ) {
	return; // Not applicable.
}

/**
 * Loads plugin; if it hasn't loaded already.
 *
 * This leverages preinitialized hooks in WordPress core.
 * {@see https://o5p.me/fFC1I7} {@see https://o5p.me/OIrafd}.
 *
 * @since 2022-02-21
 */
$GLOBALS[ 'wp_filter' ]                                                ??= [];
$GLOBALS[ 'wp_filter' ][ 'plugins_loaded' ]                            ??= [];
$GLOBALS[ 'wp_filter' ][ 'plugins_loaded' ][ -( PHP_INT_MAX - 1001 ) ] ??= [];

$GLOBALS[ 'wp_filter' ][ 'plugins_loaded' ][ -( PHP_INT_MAX - 1001 ) ][] = [
	'accepted_args' => 0,
	'function'      => function () {
		require_once U\Dir::join( __DIR__, '/trunk/plugin.php' );
	},
];
require_once U\Dir::join( '/var/www/html/wp-load.php' );
