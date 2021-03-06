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

// </editor-fold>

/**
 * Class members.
 *
 * @since 2021-12-15
 *
 * @see   WPG\WC
 */
trait Product_Members {
	/**
	 * Gets a WooCommerce product by slug.
	 *
	 * @since 2022-03-12
	 *
	 * @param string $slug Product slug.
	 *
	 * @return \WC_Product|null Product, else `null`.
	 */
	public function product_by_slug( string $slug ) : ?\WC_Product {
		if ( ! U\Env::is_woocommerce() ) {
			return null; // Not possible.
		}
		if ( ! $slug ) {
			return null; // Not possible.
		}
		$product = get_page_by_path( $slug, OBJECT, 'product' );
		$product = $product ? wc_get_product( $product ) : null;

		return $product instanceof \WC_Product && $product->exists() && $product->get_id() ? $product : null;
	}

	/**
	 * Product has a file name?
	 *
	 * @since 2022-03-28
	 *
	 * @param \WC_Product $product   Product.
	 * @param string      $file_name Profile file name.
	 *
	 * @return bool `true` if product has file name.
	 */
	public function product_has_file_name( \WC_Product $product, string $file_name ) : bool {
		foreach ( $product->get_downloads() ?: [] as $_download_id => $_file ) {
			/** @var \WC_Product_Download $_product_download */ // phpcs:ignore.
			$_product_download = $product->get_file( $_download_id );

			$_file_name = $_product_download ? $_product_download->get_name() : '';
			$_file_path = $_product_download ? $_product_download->get_file() : '';
			$_file_path = $_file_path && U\URL::is( $_file_path ) ? U\URL::parse( $_file_path, PHP_URL_PATH ) : $_file_path;

			if ( $_product_download && ( $_file_name === $file_name || basename( $_file_path ) === $file_name ) ) {
				return true; // Product has file.
			}
		}
		return false;
	}
}
