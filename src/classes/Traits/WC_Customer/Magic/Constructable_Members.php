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
	 * @param WPG\A6t\App $x_app      App.
	 * @param int|null    $id         Customer ID. Default is `null` (current user).
	 * @param bool        $is_session True if this is the customer session; {@see \WC_Customer}.
	 *
	 * @noinspection PhpMultipleClassDeclarationsInspection
	 *
	 * @throws U\Fatal_Exception If customer cannot be found by ID.
	 */
	public function __construct( WPG\A6t\App $x_app, /* int|null */ ?int $id = null, bool $is_session = false ) {
		$this->x_app = $x_app;
		$id          ??= get_current_user_id();

		try { // Fail softly.
			parent::__construct( $id, $is_session );
		} catch ( \Exception $exception ) {
			throw new U\Fatal_Exception( $exception->getMessage() );
		}
		if ( ! $this->get_id() ) {
			throw new U\Fatal_Exception( 'Unknown customer ID.' );
		}
	}
}
