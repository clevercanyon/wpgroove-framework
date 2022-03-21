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
namespace WP_Groove\Framework\Traits\WC\Utilities;

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

/**
 * File-specific.
 *
 * @since 2022-03-12
 */
use LicenseManagerForWooCommerce\Models\Resources\License as LMFWC_License;

// </editor-fold>

/**
 * Class members.
 *
 * @since 2021-12-15
 *
 * @see   WPG\WC
 */
trait License_Members {
	/**
	 * Gets a WooCommerce license by key.
	 *
	 * @since 2022-03-12
	 *
	 * @param string $key License key.
	 *
	 * @return LMFWC_License|null Product, else `null`.
	 */
	public function license_by_key( string $key ) : ?LMFWC_License {
		if ( ! U\Env::is_wp_plugin_active( 'license-manager-for-woocommerce/license-manager-for-woocommerce.php' ) ) {
			return null; // Not possible.
		}
		if ( ! $key ) {
			return null; // Not possible.
		}
		try {
			$license = lmfwc_get_license( $key );
		} catch ( \Exception $exception ) {
			$license = null; // Fail softly.
		}
		return $license instanceof LMFWC_License ? $license : null;
	}
}
