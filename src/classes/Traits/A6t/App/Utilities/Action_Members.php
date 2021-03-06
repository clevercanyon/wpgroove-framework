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
namespace WP_Groove\Framework\Traits\A6t\App\Utilities;

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
trait Action_Members {
	/**
	 * Plugin|Theme: {@see add_action()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $hook_name {@see add_action()} for further details.
	 *                          This gets auto-prefixed using app's `var_prefix`.
	 *
	 * @param mixed  ...$args   {@see add_action()} for further details.
	 *
	 * @return bool {@see add_action()}.
	 */
	final public function add_action( string $hook_name, /* mixed */ ...$args ) : bool {
		return add_action( $this->var_prefix . $hook_name, ...$args );
	}

	/**
	 * Plugin|Theme: {@see do_action()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $hook_name {@see do_action()} for further details.
	 *                          This gets auto-prefixed using app's `var_prefix`.
	 *
	 * @param mixed  ...$args   {@see do_action()} for further details.
	 */
	final public function do_action( string $hook_name, /* mixed */ ...$args ) : void {
		do_action( $this->var_prefix . $hook_name, ...$args );
	}
}
