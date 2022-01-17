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
namespace WP_Groove\Framework\Traits\App\Hooks;

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
 * Interface members.
 *
 * @since 2021-12-15
 *
 * @see   WPG\I7e\App
 */
trait On_Init_Members {
	/**
	 * Plugin|Theme: on `init` hook.
	 *
	 * @since 2021-12-15
	 */
	final public function on_init_base() : void {
		if ( $this instanceof WPG\I7e\Plugin ) {
			if ( is_dir( U\Dir::join( $this->dir, '/languages' ) ) ) {
				load_plugin_textdomain( $this->slug, false, U\Dir::name( $this->subpath, '/languages' ) );
			}
		} elseif ( $this instanceof WPG\I7e\Theme ) {
			if ( is_dir( U\Dir::join( $this->dir, '/languages' ) ) ) {
				load_theme_textdomain( $this->slug, U\Dir::join( $this->dir, '/languages' ) );
			}
		}
	}

	/**
	 * Plugin|Theme: on `init` hook.
	 *
	 * @since 2021-12-15
	 *
	 * @note  DO NOT POPULATE. This is for extenders only.
	 */
	public function on_init() : void {
		// DO NOT POPULATE. This is for extenders only.
	}
}
