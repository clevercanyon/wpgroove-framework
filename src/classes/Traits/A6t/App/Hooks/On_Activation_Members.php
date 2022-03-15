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
trait On_Activation_Members {
	/**
	 * Plugin|Theme: activation hooks.
	 *
	 * - Plugin: on `{$this->var_prefix}activation` hook.
	 * - Theme:  on `{$this->var_prefix}activation` hook via `after_switch_theme`.
	 *
	 * @since 2021-12-15
	 *
	 * @param bool $network_wide True if activated network-wide.
	 */
	final public function fw_on_activation( bool $network_wide ) : void {
		$previous_version = u\if_string( $this->get_option( 'version' ), '' );
		$this->update_option( 'previous_version', $previous_version, false );
		$this->update_option( 'version', $this->version, true );

		if ( $this instanceof WPG\A6t\Plugin ) {
			register_uninstall_hook( $this->file, [ static::class, 'fw_on_uninstall' ] );
		}
	}
}
