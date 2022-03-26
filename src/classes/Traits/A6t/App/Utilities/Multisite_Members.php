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
namespace WP_Groove\Framework\Traits\A6t\App\Utilities;

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
 * @see   WPG\A6t\App
 */
trait Multisite_Members {
	/**
	 * Plugin|Theme: Is network active?
	 *
	 * @since 2022-03-12
	 *
	 * @return bool `true` if network active.
	 *
	 * @throws U\Fatal_Exception On unexpected app type.
	 */
	final public function is_network_active() : bool {
		if ( ! is_multisite() ) {
			return false; // Not applicable.
		}
		if ( $this instanceof WPG\A6t\Plugin ) {
			return U\Env::is_wp_plugin_network_active( $this->file_subpath );

		} elseif ( $this instanceof WPG\A6t\Theme ) {
			return U\Env::is_wp_theme_network_active( $this->dir_basename );
		}
		throw new U\Fatal_Exception( 'Unable to determine app type for class: `' . static::class . '`.' );
	}
}
