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
	public static function product_by_slug( string $slug ) : ?\WC_Product {
		if ( ! class_exists( \WC_Product::class ) ) {
			return null; // Not possible.
		}
		if ( ! $slug ) {
			return null; // Not possible.
		}
		$product = get_page_by_path( $slug, OBJECT, 'product' );
		$product = $product ? wc_get_product( $product ) : null;

		return $product instanceof \WC_Product ? $product : null;
	}
}
