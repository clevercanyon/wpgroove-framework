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
trait Queue_Members {
	/**
	 * Should dequeue?
	 *
	 * @since 2022-01-28
	 *
	 * @return bool True or false.
	 */
	public function should_dequeue() : bool {
		if ( ! $this->idx || ! $this->type || ! $this->markup || ! $this->conditions || ! $this->generated ) {
			return true; // Data is missing.
		}
		if ( $this->displayed && ( ! $this->persistent || $this->dismissed ) ) {
			return true; // Displayed already, or dismissed already.
		}
		if ( $this->generated < U\Time::utc( '-90 days' ) ) {
			return true; // Don't hold forever.
		}
		foreach ( $this->conditions as $_condition ) {
			if ( $_condition instanceof U\Code_Stream_Closure ) {
				$_condition = $_condition->get_closure();
			}
			if ( ! is_callable( $_condition ) ) {
				return true; // Invalid condition.
			}
		}
		return false;
	}
}
