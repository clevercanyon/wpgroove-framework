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
trait On_Admin_Init_Members {
	/**
	 * Plugin|Theme: on `admin_init` hook.
	 *
	 * @since 2021-12-15
	 */
	final public function fw_on_admin_init() : void {
		$this->fw_on_admin_init_webpack_style();
		$this->fw_on_admin_init_webpack_script();
	}

	/**
	 * Plugin|Theme: on `admin_init` hook.
	 *
	 * Handles admin webpack style.
	 *
	 * @since 2021-12-15
	 */
	final protected function fw_on_admin_init_webpack_style() : void {
		if ( ! $this->needs[ 'admin_webpack' ] ) {
			return; // Not applicable.
		}
		$style_var   = $this->var_prefix . 'admin_webpack_style';
		$style_slug  = $this->slug_prefix . 'admin-webpack-style';
		$style_nonce = u\if_string( U\URL::current_query_var( $style_var ), '' );

		if ( ! $style_nonce || ! wp_verify_nonce( $style_nonce, $style_slug ) ) {
			return; // Not applicable.
		}
		$style_file          = U\Dir::join( $this->framework_dir, '/src/assets/admin/webpack/index.min.css' );
		$style_file_contents = $this->apply_filters( 'admin_webpack_style_file_contents', U\File::read( $style_file ) );
		$style_file_contents = preg_replace( '/\.slug-prefix-/u', '.' . $this->slug_prefix, $style_file_contents );

		U\HTTP::enable_caching();
		U\HTTP::prep_for_output();

		header( 'content-type: ' . U\File::content_type( $style_file ) );
		header( 'content-length: ' . strlen( $style_file_contents ) );

		exit( $style_file_contents ); // phpcs:ignore -- output ok.
	}

	/**
	 * Plugin|Theme: on `admin_init` hook.
	 *
	 * Handles admin webpack script.
	 *
	 * @since 2021-12-15
	 */
	final protected function fw_on_admin_init_webpack_script() : void {
		if ( ! $this->needs[ 'admin_webpack' ] ) {
			return; // Not applicable.
		}
		$script_var   = $this->var_prefix . 'admin_webpack_script';
		$script_slug  = $this->slug_prefix . 'admin-webpack-script';
		$script_nonce = u\if_string( U\URL::current_query_var( $script_var ), '' );

		if ( ! $script_nonce || ! wp_verify_nonce( $script_nonce, $script_slug ) ) {
			return; // Not applicable.
		}
		$script_file          = U\Dir::join( $this->framework_dir, '/src/assets/admin/webpack/index.min.js' );
		$script_file_contents = $this->apply_filters( 'admin_webpack_script_file_contents', U\File::read( $script_file ) );
		$script_file_contents = preg_replace( '/%%wp_localize_script_var%%/u', $script_var, $script_file_contents );

		U\HTTP::enable_caching();
		U\HTTP::prep_for_output();

		header( 'content-type: ' . U\File::content_type( $script_file ) );
		header( 'content-length: ' . strlen( $script_file_contents ) );

		exit( $script_file_contents ); // phpcs:ignore -- output ok.
	}
}
