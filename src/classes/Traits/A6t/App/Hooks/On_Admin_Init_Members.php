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
trait On_Admin_Init_Members {
	/**
	 * Plugin|Theme: on `admin_init` hook.
	 *
	 * @since 2021-12-15
	 */
	final public function on_admin_init_base() : void {
		$this->on_admin_init_css_base();
	}

	/**
	 * Plugin|Theme: on `admin_init` hook.
	 *
	 * Handles base CSS generation.
	 *
	 * @since 2021-12-15
	 */
	final protected function on_admin_init_css_base() : void {
		if ( ! $this->needs[ 'admin_base_css' ] ) {
			return; // Not applicable.
		}
		$nonce_action = $this->var_prefix . 'admin_base_css';
		$nonce        = u\if_string( U\URL::current_query_var( $nonce_action ), '' );

		if ( ! $nonce || ! wp_verify_nonce( $nonce, $nonce_action ) ) {
			return; // Not applicable.
		}
		U\HTTP::enable_caching();
		header( 'content-type: text/css; charset=utf-8' );

		$css_file = U\Dir::join( $this->framework_dir, '/src/assets/admin/webpack/index.min.css' );
		$css      = is_readable( $css_file ) ? file_get_contents( $css_file ) : '';
		$css      = preg_replace( '/\.slug-prefix-/u', '.' . $this->slug_prefix, $css );

		exit( $css ); // phpcs:ignore -- output ok.
	}

	/**
	 * Plugin|Theme: on `admin_init` hook.
	 *
	 * DO NOT POPULATE. This is for extenders only.
	 *
	 * @since 2021-12-15
	 */
	public function on_admin_init() : void {
		// DO NOT POPULATE. This is for extenders only.
	}
}
