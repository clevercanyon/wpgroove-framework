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
namespace WP_Groove\Framework\Traits\App\Utilities;

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
 * Interface members.
 *
 * @since 2021-12-15
 *
 * @see   WPG\I7e\App
 */
trait Transient_Members {
	/**
	 * Plugin|Theme: {@see get_transient()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $transient {@see get_transient()}.
	 *
	 * @return mixed {@see get_transient()}.
	 */
	final public function get_transient( string $transient ) /* : mixed */ {
		return get_transient( $this->var_prefix . $transient );
	}

	/**
	 * Plugin|Theme: {@see set_transient()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $transient {@see set_transient()}.
	 * @param mixed  ...$args   {@see set_transient()}.
	 *
	 * @return bool {@see set_transient()}.
	 */
	final public function set_transient( string $transient, /* mixed */ ...$args ) : bool {
		return set_transient( $this->var_prefix . $transient, ...$args );
	}

	/**
	 * Plugin|Theme: {@see delete_transient()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $transient {@see delete_transient()}.
	 *
	 * @return bool {@see delete_transient()}.
	 */
	final public function delete_transient( string $transient ) : bool {
		return delete_transient( $this->var_prefix . $transient );
	}
}
