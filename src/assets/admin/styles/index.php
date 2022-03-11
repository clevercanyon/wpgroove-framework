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

// </editor-fold>

/**
 * Headers.
 */
header_remove( 'expires' );
header_remove( 'last-modified' );

header( 'content-type: text/css; charset=utf-8' );
header( 'cache-control: public, must-revalidate, max-age=31536000, s-maxage=31536000, stale-while-revalidate=604800, stale-if-error=604800' );

/**
 * Sanitize slug prefix.
 */
$slug_prefix = $_GET[ 'slug_prefix' ] ?? ''; // phpcs:ignore.
$slug_prefix = htmlspecialchars( strval( $slug_prefix ) );
$slug_prefix = preg_replace( '/[^a-z0-9\-]/u', '', $slug_prefix );

if ( ! $slug_prefix ) {
	return; // Not possible.
}

/**
 * CSS inclusion.
 */
$css_file = dirname( __FILE__, 2 ) . '/webpack/index.min.css';

if ( ! is_readable( $css_file ) ) {
	return; // Not possible.
}
$css = file_get_contents( $css_file );
$css = preg_replace( '/\.slug-prefix-/u', '.' . $slug_prefix, $css );

echo $css; // phpcs:ignore -- output ok.
