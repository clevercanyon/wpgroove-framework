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
namespace WP_Groove\Framework\Traits\A6t\App\Hooks;

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
trait On_Init_Members {
	/**
	 * Plugin|Theme: on `init` hook.
	 *
	 * @since 2021-12-15
	 */
	final public function fw_on_init() : void {
		if ( $this instanceof WPG\A6t\Plugin ) {
			if ( is_dir( U\Dir::join( $this->dir, '/languages' ) ) ) {
				load_plugin_textdomain( $this->slug, false, U\Dir::name( $this->file_subpath, '/languages' ) );
			}
		} elseif ( $this instanceof WPG\A6t\Theme ) {
			if ( is_dir( U\Dir::join( $this->dir, '/languages' ) ) ) {
				load_theme_textdomain( $this->slug, U\Dir::join( $this->dir, '/languages' ) );
			}
		}
	}
}
