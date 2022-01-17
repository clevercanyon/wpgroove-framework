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
namespace WP_Groove\Framework\A6t;

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
 * Plugin|Theme (i.e., app) base class.
 *
 * @since 2021-12-15
 */
abstract class App extends U\A6t\Base implements WPG\I7e\App {
	/**
	 * Brand info.
	 *
	 * @since 2021-12-15
	 *
	 * @final Starting w/ PHP 8.1.0.
	 */
	private const BRAND = [
		'name' => 'WP Groove',

		'slug'        => 'wpgroove',
		'slug_prefix' => 'wpgroove-',

		'var'        => 'wpgroove',
		'var_prefix' => 'wpgroove_',
	];

	/**
	 * Traits.
	 *
	 * @since 2021-12-15
	 */
	use WPG\Traits\App\Members;
}
