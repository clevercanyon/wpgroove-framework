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
 * File utilities.
 *
 * @since 2021-12-15
 */
final class File extends U\A6t\Stc_Utilities {
	/**
	 * Gets file MIME type.
	 *
	 * @since 2021-12-15
	 *
	 * @param string $file    File path.
	 * @param string $default Optional default (fallback) MIME type.
	 *                        Default is `application/octet-stream`.
	 *
	 * @return string MIME type.
	 */
	public static function mime_type( string $file, string $default = 'application/octet-stream' ) : string {
		$mime_type = $default;
		$ext       = U\File::ext( $file );

		foreach ( get_allowed_mime_types() as $_mime => $_type ) {
			$_mimes = explode( '|', $_mime );

			if ( in_array( $ext, $_mimes, true ) ) {
				$mime_type = $_type;
				break;
			}
		}
		return $mime_type;
	}
}
