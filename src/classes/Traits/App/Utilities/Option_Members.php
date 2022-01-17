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
trait Option_Members {
	/**
	 * Plugin|Theme: {@see get_option()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $option  {@see get_option()}.
	 * @param mixed  ...$args {@see get_option()}.
	 *
	 * @return mixed {@see get_option()}.
	 */
	final public function get_option( string $option, /* mixed */ ...$args ) /* : mixed */ {
		return get_option( $this->var_prefix . $option, ...$args );
	}

	/**
	 * Plugin|Theme: {@see add_option()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $option  {@see add_option()}.
	 * @param mixed  ...$args {@see add_option()}.
	 *
	 * @return bool {@see add_option()}.
	 */
	final public function add_option( string $option, /* mixed */ ...$args ) : bool {
		return add_option( $this->var_prefix . $option, ...$args );
	}

	/**
	 * Plugin|Theme: {@see update_option()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $option  {@see update_option()}.
	 * @param mixed  ...$args {@see update_option()}.
	 *
	 * @return bool {@see update_option()}.
	 */
	final public function update_option( string $option, /* mixed */ ...$args ) : bool {
		return update_option( $this->var_prefix . $option, ...$args );
	}

	/**
	 * Plugin|Theme: {@see delete_option()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $option {@see delete_option()}.
	 *
	 * @return bool {@see delete_option()}.
	 */
	final public function delete_option( string $option ) : bool {
		return delete_option( $this->var_prefix . $option );
	}
}
