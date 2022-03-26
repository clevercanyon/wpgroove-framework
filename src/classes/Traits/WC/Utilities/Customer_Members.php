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
namespace WP_Groove\Framework\Traits\WC\Utilities;

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
 * @see   WPG\WC
 */
trait Customer_Members {
	/**
	 * Gets a {@see WPG\WC_Customer}.
	 *
	 * @since        2022-03-12
	 *
	 * @param int $user_id User ID.
	 *
	 * @return WPG\WC_Customer|null Customer; else `null`.
	 *
	 * @noinspection PhpRedundantCatchClauseInspection
	 */
	public function customer( int $user_id ) /* : WPG\WC_Customer|null */ : ?WPG\WC_Customer {
		if ( ! U\Env::is_woocommerce() ) {
			return null; // Not possible.
		}
		if ( ! $user_id ) {
			return null; // Not possible.
		}
		try { // Catch invalid user IDs.
			$customer = $this->app( WPG\WC_Customer::class, $user_id );
		} catch ( U\I7e\Exception $exception ) {
			$customer = null; // Fail softly.
		}
		return $customer && $customer->get_id() ? $customer : null;
	}

	/**
	 * Gets current {@see WPG\WC_Customer}.
	 *
	 * @since 2022-03-12
	 *
	 * @return WPG\WC_Customer|null Current customer; else `null`.
	 */
	public function current_customer() /* : WPG\WC_Customer|null */ : ?WPG\WC_Customer {
		return is_user_logged_in() ? $this->customer( get_current_user_id() ) : null;
	}
}
