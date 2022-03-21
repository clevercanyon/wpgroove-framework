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
namespace WP_Groove\Framework\Traits\WC_Customer\Utilities;

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
 * Class members.
 *
 * @since 2021-12-15
 *
 * @see   WPG\WC_Customer
 */
trait Download_Members {
	/**
	 * Can customer download a file by name?
	 *
	 * @since 2021-12-15
	 *
	 * @param string $name File name.
	 *
	 * @return bool `true` if customer can download file name.
	 */
	public function x_can_download_file_name( string $name ) : bool {
		if ( ! $name ) {
			return false; // Not possible.
		}
		$names = [ $name, $this->x_app->brand->slug_prefix . $name ];

		foreach ( $this->get_downloadable_products() as $_dp ) {
			if ( in_array( $_dp[ 'download_name' ], $names, true ) ) {
				return true;
			}
		}
		return false;
	}
}
