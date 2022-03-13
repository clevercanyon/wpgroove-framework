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
trait Display_Members {
	/**
	 * Displays notice (maybe).
	 *
	 * @since 2022-01-28
	 */
	public function maybe_display() : void {
		if ( $this->should_display() ) {
			$this->display();
		}
	}

	/**
	 * Displays notice.
	 *
	 * @since 2022-01-28
	 */
	protected function display() : void {
		$this->displayed = U\Time::utc();
		echo $this->markup(); // phpcs:ignore -- output ok.
	}

	/**
	 * Should display notice?
	 *
	 * @since 2022-01-28
	 *
	 * @return bool True or false.
	 */
	protected function should_display() : bool {
		if ( ! $this->idx || ! $this->type || ! $this->markup || ! $this->conditions || ! $this->generated ) {
			return false; // Data is missing.
		}
		if ( $this->displayed && ( ! $this->persistent || $this->dismissed ) ) {
			return false; // Displayed already, or dismissed already.
		}
		if ( $this->blog_id && get_current_blog_id() !== $this->blog_id ) {
			return false; // Not applicable.
		}
		if ( $this->user_id && get_current_user_id() !== $this->user_id ) {
			return false; // Not applicable.
		}
		foreach ( $this->conditions as $_condition ) {
			if ( $_condition instanceof U\Code_Stream_Closure ) {
				if ( ! $_condition->call( $this ) ) {
					return false; // Not applicable.
				}
			} elseif ( ! is_callable( $_condition ) || ! $_condition() ) {
				return false; // Not applicable.
			}
		}
		return true;
	}
}
