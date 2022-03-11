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
		$url = $this->url . '/vendor/clevercanyon/' . $this->brand_slug_prefix . 'framework/src/assets/admin/styles/';
		$url = U\URL::add_query_vars( [ 'slug_prefix' => $this->slug_prefix ], $url );
		wp_enqueue_style( $this->slug_prefix . 'base', $url, [], $this->version, 'all' );
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
