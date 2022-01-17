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
trait Site_Option_Members {
	/**
	 * Plugin|Theme: {@see get_site_option()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $option  {@see get_site_option()}.
	 * @param mixed  ...$args {@see get_site_option()}.
	 *
	 * @return mixed {@see get_site_option()}.
	 */
	final public function get_site_option( string $option, /* mixed */ ...$args ) /* : mixed */ {
		return get_site_option( $this->var_prefix . $option, ...$args );
	}

	/**
	 * Plugin|Theme: {@see add_site_option()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $option  {@see add_site_option()}.
	 * @param mixed  ...$args {@see add_site_option()}.
	 *
	 * @return bool {@see add_site_option()}.
	 */
	final public function add_site_option( string $option, /* mixed */ ...$args ) : bool {
		return add_site_option( $this->var_prefix . $option, ...$args );
	}

	/**
	 * Plugin|Theme: {@see update_site_option()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $option  {@see update_site_option()}.
	 * @param mixed  ...$args {@see update_site_option()}.
	 *
	 * @return bool {@see update_site_option()}.
	 */
	final public function update_site_option( string $option, /* mixed */ ...$args ) : bool {
		return update_site_option( $this->var_prefix . $option, ...$args );
	}

	/**
	 * Plugin|Theme: {@see delete_site_option()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $option {@see delete_site_option()}.
	 *
	 * @return bool {@see delete_site_option()}.
	 */
	final public function delete_site_option( string $option ) : bool {
		return delete_site_option( $this->var_prefix . $option );
	}
}
