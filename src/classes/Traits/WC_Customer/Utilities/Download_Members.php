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
	 * Can customer download file name?
	 *
	 * @since 2021-12-15
	 *
	 * @param string $file_name File name.
	 *
	 * @return bool `true` if customer can download file name.
	 */
	public function wpg_can_download_file_name( string $file_name ) : bool {
		if ( '' === $file_name ) {
			return false; // Clearly not possible.
		}
		foreach ( wc_get_customer_download_permissions( $this->get_id() ) as $_download ) {
			$_download = new \WC_Customer_Download( $_download );

			if ( ! ( $_order = wc_get_order( $_download->get_order_id() ) )
				|| ! $_order->is_download_permitted() ) {
				continue; // Not permitted at this time.
			}
			if ( ! ( $_product = wc_get_product( $_download->get_product_id() ) )
				|| ! $_product->exists() // In case product no longer exists.
				|| ! $_product->has_file( $_download->get_download_id() ) ) {
				continue; // Product or file no longer exists; or not downloadable.
			}
			/** @var \WC_Product_Download $_product_download */ // phpcs:ignore.
			$_product_download = $_product->get_file( $_download->get_download_id() );

			$_file_name = $_product_download ? $_product_download->get_name() : '';
			$_file_path = $_product_download ? $_product_download->get_file() : '';
			$_file_path = $_file_path && U\URL::is( $_file_path ) ? U\URL::parse( $_file_path, PHP_URL_PATH ) : $_file_path;

			if ( ! $_product_download || ( $_file_name !== $file_name && basename( $_file_path ) !== $file_name ) ) {
				continue; // File no longer exists or name isn't a match.
			}
			if ( is_int( $_downloads_remaining = $_download->get_downloads_remaining() ) && $_downloads_remaining <= 0 ) {
				continue; // No more downloads remaining as this time.
			}
			if ( ( $_access_expires = $_download->get_access_expires() ) instanceof \WC_DateTime
				&& $_access_expires->getTimestamp() < U\Time::utc( 'midnight' ) ) {
				continue; // Download access has expired now.
			}
			return true;
		}
		return false;
	}
}
