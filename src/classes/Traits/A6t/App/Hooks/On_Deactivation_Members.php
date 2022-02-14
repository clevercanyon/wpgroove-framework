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
 * Interface members.
 *
 * @since 2021-12-15
 *
 * @see   WPG\I7e\App
 */
trait On_Deactivation_Members {
	/**
	 * Plugin: on `{$this->var_prefix}deactivation` hook.
	 *
	 * @since 2021-12-15
	 *
	 * @param bool $network_wide True if deativated network-wide.
	 */
	final public function on_plugin_deactivation_base( bool $network_wide ) : void {
		// Nothing for now.
	}

	/**
	 * Plugin: on `{$this->var_prefix}deactivation` hook.
	 *
	 * DO NOT POPULATE. This is for extenders only.
	 *
	 * @since 2021-12-15
	 *
	 * @param bool $network_wide True if deativated network-wide.
	 */
	public function on_plugin_deactivation( bool $network_wide ) : void {
		// DO NOT POPULATE. This is for extenders only.
	}

	/**
	 * Theme: on `switch_theme` hook.
	 *
	 * @since 2021-12-15
	 *
	 * @param string    $new_template New template name.
	 * @param \WP_Theme $new_theme    New theme instance.
	 * @param \WP_Theme $old_theme    Old theme instance.
	 */
	final public function on_theme_deactivation_base( string $new_template, \WP_Theme $new_theme, \WP_Theme $old_theme ) : void {
		if ( $this->get_option( 'uninstall_on_deactivation' ) ) {
			static::on_uninstall_base();
		}
	}

	/**
	 * Theme: on `switch_theme` hook.
	 *
	 * DO NOT POPULATE. This is for extenders only.
	 *
	 * @since 2021-12-15
	 *
	 * @param string    $new_template New template name.
	 * @param \WP_Theme $new_theme    New theme instance.
	 * @param \WP_Theme $old_theme    Old theme instance.
	 */
	public function on_theme_deactivation( string $new_template, \WP_Theme $new_theme, \WP_Theme $old_theme ) : void {
		// DO NOT POPULATE. This is for extenders only.
	}
}
