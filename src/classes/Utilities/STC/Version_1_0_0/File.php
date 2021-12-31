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
namespace WP_Groove\Framework\Utilities\STC\Version_1_0_0;

/**
 * Utilities.
 *
 * @since 2021-12-15
 */
use Clever_Canyon\Utilities\STC\{Version_1_0_0 as U};
use Clever_Canyon\Utilities\OOP\Version_1_0_0\{Offsets, Generic, Error, Exception, Fatal_Exception};
use Clever_Canyon\Utilities\OOP\Version_1_0_0\Abstracts\{A6t_Base, A6t_Offsets, A6t_Generic, A6t_Error, A6t_Exception};
use Clever_Canyon\Utilities\OOP\Version_1_0_0\Interfaces\{I7e_Base, I7e_Offsets, I7e_Generic, I7e_Error, I7e_Exception};

/**
 * WP Groove utilities.
 *
 * @since 2021-12-15
 */
use WP_Groove\Framework\Utilities\STC\{Version_1_0_0 as UU};
use WP_Groove\Framework\Theme\Version_1_0_0\Abstracts\{AA6t_Theme};
use WP_Groove\Framework\Plugin\Version_1_0_0\Abstracts\{AA6t_Plugin};
use WP_Groove\Framework\Utilities\OOP\Version_1_0_0\Abstracts\{AA6t_App};

// </editor-fold>

/**
 * File utilities.
 *
 * @since 2021-12-15
 */
class File extends \Clever_Canyon\Utilities\STC\Version_1_0_0\Abstracts\A6t_Stc_Base {
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