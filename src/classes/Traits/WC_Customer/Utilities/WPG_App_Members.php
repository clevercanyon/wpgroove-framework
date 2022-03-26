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
namespace WP_Groove\Framework\Traits\WC_Customer\Utilities;

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
trait WPG_App_Members {
	/**
	 * Factory class invocation.
	 *
	 * @since 2021-12-15
	 *
	 * @param string $class   Fully-qualified class name.
	 * @param mixed  ...$args Optional args to class constructor.
	 *                        If passed, a 'new' instance is returned.
	 *
	 * @return object Requested class instance; {@see WPG\A6t\App::__invoke()}.
	 */
	public function wpg_app( string $class, ...$args ) : object {
		$wpg_app = $this->wpg_app;
		return $wpg_app( ...$args );
	}
}
