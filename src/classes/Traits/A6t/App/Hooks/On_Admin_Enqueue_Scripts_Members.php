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
trait On_Admin_Enqueue_Scripts_Members {
	/**
	 * Plugin|Theme: on `admin_enqueue_scripts` hook.
	 *
	 * @since 2021-12-15
	 */
	final public function on_admin_enqueue_scripts_base() : void {
		$this->on_admin_enqueue_scripts_base_css();
	}

	/**
	 * Plugin|Theme: on `admin_enqueue_scripts` hook.
	 *
	 * Handles enqueing of base CSS.
	 *
	 * @since 2021-12-15
	 */
	final protected function on_admin_enqueue_scripts_base_css() : void {
		if ( ! $this->needs[ 'admin_base_css' ] ) {
			return; // Not applicable.
		}
		$nonce_action = $this->var_prefix . 'admin_base_css';
		$url          = wp_nonce_url( self_admin_url(), $nonce_action, $nonce_action );

		wp_enqueue_style( $this->slug_prefix . 'admin-base', $url, [], $this->version, 'all' );
	}

	/**
	 * Plugin|Theme: on `admin_enqueue_scripts` hook.
	 *
	 * DO NOT POPULATE. This is for extenders only.
	 *
	 * @since 2021-12-15
	 */
	public function on_admin_enqueue_scripts() : void {
		// DO NOT POPULATE. This is for extenders only.
	}
}
