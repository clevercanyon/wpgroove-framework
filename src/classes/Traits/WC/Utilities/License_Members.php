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
use LicenseManagerForWooCommerce\Models\Resources\License as WC_License;

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
	 * Is license manager active?
	 *
	 * @since 2022-03-25
	 *
	 * @return bool `true` if license manager is active.
	 */
	public function is_license_manager_active() : bool {
		static $is; // Memoize.

		if ( null !== $is ) {
			return $is; // Saves times.
		}
		return $is = U\Env::is_woocommerce()
			&& U\Env::is_wp_plugin_active( 'license-manager-for-woocommerce/license-manager-for-woocommerce.php' );
	}

	/**
	 * Is license key?
	 *
	 * @since 2022-03-29
	 *
	 * @param string $str    String to check.
	 * @param bool   $strict Strict definition? Default is `false`.
	 *
	 * @return bool `true` if valid format for a license key.
	 */
	public function is_license_key( string $str, bool $strict = false ) : bool {
		if ( $strict ) { // 8 chunks of 8 chars: `0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ`; separated by `-`.
			return U\Str::is_valid_helper( $str, 71, 71, '/^[A-Z0-9]{8}(?:-[A-Z0-9]{8}){7}$/u' );
		}
		return U\Str::is_valid_helper( $str, 1, 128, '/^[^\v]+$/u' );
	}

	/**
	 * Gets a WooCommerce license by key.
	 *
	 * @since 2022-03-12
	 *
	 * @param string $key License key.
	 *
	 * @return WC_License|null Product, else `null`.
	 */
	public function license_by_key( string $key ) : ?WC_License {
		if ( ! $this->is_license_manager_active() ) {
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
		return $license instanceof WC_License && $license->getId() ? $license : null;
	}
}
