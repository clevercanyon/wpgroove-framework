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
namespace WP_Groove\Framework\Traits\Admin_Notice\Utilities;

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
 * @see   WPG\Admin_Notice
 */
trait Markup_Members {
	/**
	 * Gets prepared markup.
	 *
	 * @since 2022-02-12
	 *
	 * @return string Prepared markup.
	 */
	protected function markup() : string {
		$markup = ''; // Initialize.

		$markup .= '<div class="' . esc_attr( $this->classes() ) . '" data-idx="' . esc_attr( $this->idx ) . '">' . "\n";
		$markup .= '    <div class="-app-label" hidden>' . esc_html( $this->app->name ) . '</div>' . "\n";
		$markup .= '    ' . U\HTML::markup( $this->markup ) . "\n";
		$markup .= '</div>' . "\n";

		return $markup;
	}

	/**
	 * Get prepared CSS classes.
	 *
	 * @since 2022-01-28
	 */
	protected function classes() : string {
		$classes = [ 'notice' ];

		$classes[] = $this->app->brand->slug_prefix . 'notice';
		$classes[] = $this->app->slug_prefix . 'notice';

		switch ( $this->type ) {
			case 'success':
				$classes[] = 'notice-success';
				break;

			case 'warning':
				$classes[] = 'notice-warning';
				break;

			case 'error':
				$classes[] = 'notice-error';
				break;

			case 'info':
			default: // Default type.
				$classes[] = 'notice-info';
		}
		if ( $this->persistent ) {
			$classes[] = 'is-persistent';
		}
		if ( $this->dismissable ) {
			$classes[] = 'is-dismissible';
		}
		return implode( ' ', $classes );
	}
}
