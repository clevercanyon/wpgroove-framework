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
namespace WP_Groove\Framework\Traits\WC_Customer\Magic;

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
 * @see   WPG\WC_Customer
 */
trait Constructable_Members {
	/**
	 * Constructor.
	 *
	 * @param WPG\A6t\App $wpg_app WP Groove app.
	 * @param int         $user_id WordPress user ID.
	 *
	 * @throws U\Fatal_Exception If customer cannot be found by ID.
	 * @noinspection PhpMultipleClassDeclarationsInspection
	 */
	public function __construct( WPG\A6t\App $wpg_app, int $user_id ) {
		$this->wpg_app = $wpg_app;

		try { // Fail softly.
			parent::__construct( $user_id );
		} catch ( \Throwable $throwable ) {
			throw new U\Fatal_Exception( $throwable->getMessage() );
		}
		if ( ! $this->get_id() ) {
			throw new U\Fatal_Exception( 'Unknown customer ID.' );
		}
	}
}
