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
trait On_Admin_Enqueue_Scripts_Members {
	/**
	 * Plugin|Theme: on `admin_enqueue_scripts` hook.
	 *
	 * @since 2021-12-15
	 */
	final public function on_admin_enqueue_scripts_base() : void {
		$this->on_admin_enqueue_scripts_base_webpack();
	}

	/**
	 * Plugin|Theme: on `admin_enqueue_scripts` hook.
	 *
	 * Handles enqueing of base webpack.
	 *
	 * @since 2021-12-15
	 */
	final protected function on_admin_enqueue_scripts_base_webpack() : void {
		if ( ! $this->needs[ 'admin_base_webpack' ] ) {
			return; // Not applicable.
		}
		$style_var  = $this->var_prefix . 'admin_base_webpack_style';
		$style_slug = $this->slug_prefix . 'admin-base-webpack-style';

		$script_var  = $this->var_prefix . 'admin_base_webpack_script';
		$script_slug = $this->slug_prefix . 'admin-base-webpack-script';

		$self_admin_url = self_admin_url(); // Used multipe times.
		$style_url      = wp_nonce_url( $self_admin_url, $style_slug, $style_var );
		$script_url     = wp_nonce_url( $self_admin_url, $script_slug, $script_var );

		wp_enqueue_style( $style_slug, $style_url, [], $this->version, 'all' );
		wp_enqueue_script( $script_slug, $script_url, [ 'jquery' ], $this->version, true );

		wp_localize_script( $script_slug, $script_var, [
			'app'  => [
				'slugPrefix' => $this->slug_prefix,
				'varPrefix'  => $this->var_prefix,
			],
			'ajax' => [
				'nonces' => [
					'adminNoticeDismiss' => wp_create_nonce( 'wp_ajax_' . $this->var_prefix . 'admin_notice_dismiss' ),
				],
				'url'    => rtrim( $self_admin_url, '/' ) . '/admin-ajax.php',
			],
		] );
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
