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
trait Instance_Members {
	/**
	 * Plugin|Theme: gets app type.
	 *
	 * @since 2021-12-30
	 *
	 * @return string Either `plugin` or `theme`.
	 *
	 * @throws U\Fatal_Exception On failure.
	 */
	final protected static function app_type() : string {
		static $type; // Memoize.

		if ( null !== $type ) {
			return $type; // Saves time.
		}
		if ( is_a( static::class, WPG\A6t\Plugin::class, true ) ) {
			return $type = 'plugin';
		} elseif ( is_a( static::class, WPG\A6t\Theme::class, true ) ) {
			return $type = 'theme';
		}
		throw new U\Fatal_Exception( 'Unable to determine app type for class: `' . static::class . '`.' );
	}

	/**
	 * Plugin|Theme: adds app instance hooks.
	 *
	 * This runs as the plugin is being loaded (i.e., require'd in `wp-settings.php`).
	 * WordPress hasn't updated {@see mb_internal_encoding()} at this point in `wp-settings.php`.
	 * Thus, it's important to steer away from `mb_*` functions. Or, pass an explicit encoding if necessary.
	 *
	 * @since 2021-12-15
	 *
	 * @param string ...$args {@see WPG\A6t\App::__construct()}.
	 *
	 * @throws U\Fatal_Exception On failure to determine app realpath.
	 * @throws U\Fatal_Exception On failure to determine app type.
	 */
	final public static function add_instance_hooks( string ...$args ) : void {
		assert( ! empty( $args[ 0 ] ) && is_string( $args[ 0 ] ) && is_file( $args[ 0 ] ) );
		$args[ 0 ] = U\Fs::realize( $args[ 0 ] ); // Canonicalize and normalize.
		assert( ! empty( $args[ 0 ] ) && is_file( $args[ 0 ] ) );

		if ( ! $args[ 0 ] ) { // Failure to realize?
			throw new U\Fatal_Exception( 'Failed to locate realpath for class: `' . static::class . '`.' );
		}
		// Saves instance args for {@see WPG\A6t\App::load()}.
		// Args also be used by {@see WPG\A6t\App::on_uninstall_base()}.

		if ( ! isset( static::$instances[ static::class ] ) ) {
			static::$instances                            ??= [];
			static::$instances[ static::class ]           ??= [];
			static::$instances[ static::class ][ 'args' ] = $args;
		}
		if ( 'plugin' === static::app_type() ) {
			static::add_plugin_instance_hooks( ...$args );
		} elseif ( 'theme' === static::app_type() ) {
			static::add_theme_instance_hooks( ...$args );
		} else {
			throw new U\Fatal_Exception( 'Unable to determine app type for class: `' . static::class . '`.' );
		}
	}

	/**
	 * Plugin: adds plugin instance hooks.
	 *
	 * @since 2021-12-15
	 *
	 * @param string ...$args {@see WPG\A6t\App::__construct()}.
	 */
	final protected static function add_plugin_instance_hooks( string ...$args ) : void {
		// Registers activation/deactivation hooks.
		// The activation hook is what registers the uninstall hook.

		register_activation_hook(
			$args[ 0 ], // Plugin file.
			function ( ?bool $network_wide = null ) {
				static::load()->do_action( 'activation', $network_wide ?: false );
			}
		);
		register_deactivation_hook(
			$args[ 0 ], // Plugin file.
			function ( ?bool $network_wide = null ) {
				static::load()->do_action( 'deactivation', $network_wide ?: false );
			}
		);
		// `plugins_loaded` will not fire on activation, deactivation, or uninstallation.
		// That's as it should be. The WP Groove framework has separate handlers for those events.
		add_action( 'plugins_loaded', [ static::class, 'load' ], -( PHP_INT_MAX - 10000 ), 0 );
	}

	/**
	 * Theme: adds theme instance hooks.
	 *
	 * @since 2021-12-15
	 *
	 * @param string ...$args {@see WPG\A6t\App::__construct()}.
	 */
	final protected static function add_theme_instance_hooks( string ...$args ) : void {
		// `after_setup_theme` will not fire on activation, deactivation, or uninstallation.
		// That's as it should be. The WP Groove framework has separate handlers for those events.
		add_action( 'after_setup_theme', [ static::class, 'load' ], -( PHP_INT_MAX - 10000 ), 0 );
	}

	/**
	 * Plugin|Theme: sets app instance, loads app.
	 *
	 * @since 2021-12-15
	 *
	 * @param bool $maybe_setup_hooks Control over hook setup.
	 *                                Default is `true`. Set to `false` when uninstalling.
	 *                                {@see WPG\A6t\App::__construct()}.
	 *
	 * @return WPG\A6t\App App instance for the called class.
	 *
	 * @throws U\Fatal_Exception If missing instance args.
	 */
	final public static function load( bool $maybe_setup_hooks = true ) : WPG\A6t\App {
		if ( ! isset( static::$instances[ static::class ][ '&' ] ) ) {
			if ( ! isset( static::$instances[ static::class ][ 'args' ] ) ) {
				throw new U\Fatal_Exception( 'Missing ' . static::app_type() . ' instance args for class: `' . static::class . '`.' );
			}
			$args                                      = array_slice( static::$instances[ static::class ][ 'args' ], 0, 4 );
			$args                                      = array_merge( $args, [ $maybe_setup_hooks ] );
			static::$instances[ static::class ][ '&' ] = new static( ...$args );
		}
		return static::$instances[ static::class ][ '&' ];
	}

	/**
	 * Plugin|Theme: gets app instance.
	 *
	 * @since 2021-12-15
	 *
	 * @return WPG\A6t\App App instance for the called class.
	 *
	 * @throws U\Fatal_Exception If missing instance.
	 */
	final public static function instance() : WPG\A6t\App {
		if ( ! isset( static::$instances[ static::class ][ '&' ] ) ) {
			throw new U\Fatal_Exception( 'Missing ' . static::app_type() . ' instance for class: `' . static::class . '`.' );
		}
		return static::$instances[ static::class ][ '&' ];
	}
}
