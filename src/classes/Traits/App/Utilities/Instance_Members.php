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
trait Instance_Members {
	/**
	 * Plugin|Theme: gets app type.
	 *
	 * @since 2021-12-30
	 *
	 * @throws U\Fatal_Exception On failure.
	 * @return string Either `plugin` or `theme`.
	 */
	final protected static function app_type() : string {
		$cls = get_called_class();

		if ( is_a( $cls, WPG\I7e\Plugin::class, true ) ) {
			return 'plugin';
		} elseif ( is_a( $cls, WPG\I7e\Theme::class, true ) ) {
			return 'theme';
		}
		throw new U\Fatal_Exception( 'Unable to determine app type for class: `' . $cls . '`.' );
	}

	/**
	 * Plugin|Theme: adds app instance hooks.
	 *
	 * @since 2021-12-15
	 *
	 * @param string ...$args {@see WPG\Traits\App\Magic\Constructable_Members::__construct()}.
	 *
	 * @throws U\Fatal_Exception On failure to determine app type.
	 */
	final public static function add_instance_hooks( string ...$args ) : void {
		assert( ! empty( $args[ 0 ] ) && is_string( $args[ 0 ] ) && is_file( $args[ 0 ] ) );
		$args[ 0 ] = U\Fs::realize( $args[ 0 ] ); // Canonicalize and normalize.
		assert( ! empty( $args[ 0 ] ) && is_file( $args[ 0 ] ) );

		// Saves instance args for {@see WPG\A6t\App::load()}.
		// Args also be used by {@see WPG\A6t\App::on_uninstall_base()}.

		$app_type = static::app_type();
		$cls      = get_called_class();

		if ( ! isset( static::$instances[ $cls ] ) ) {
			static::$instances                   ??= [];
			static::$instances[ $cls ]           ??= [];
			static::$instances[ $cls ][ 'args' ] = $args;
		}
		if ( 'plugin' === $app_type ) {
			static::add_plugin_instance_hooks( ...$args );
		} elseif ( 'theme' === $app_type ) {
			static::add_theme_instance_hooks( ...$args );
		} else {
			throw new U\Fatal_Exception( 'Unable to determine app type for class: `' . $cls . '`.' );
		}
	}

	/**
	 * Plugin: adds plugin instance hooks.
	 *
	 * @since 2021-12-15
	 *
	 * @param string ...$args {@see WPG\Traits\App\Magic\Constructable_Members::__construct()}.
	 */
	final protected static function add_plugin_instance_hooks( string ...$args ) : void {
		// Registers activation/deactivation hooks.
		// The activation hook is what registers the uninstall hook.

		register_activation_hook(
			$args[ 0 ], // Plugin file.
			function ( bool $network_wide ) {
				do_action( static::load()->var_prefix . 'activation', $network_wide );
			}
		);
		register_deactivation_hook(
			$args[ 0 ], // Plugin file.
			function ( bool $network_wide ) {
				do_action( static::load()->var_prefix . 'deactivation', $network_wide );
			}
		);
		// These fire when loaded by `wp-settings.php` in the normal course of things.
		// Neither of these actions will fire on activation, deactivation, or uninstallation.
		// ... which is as it should be. We have separate handlers for those events.

		add_action(
			'network_plugin_loaded', // Network active.
			function ( string $loaded_file ) use ( $args ) {
				U\Fs::realize( $loaded_file ) === $args[ 0 ] && static::load();
			}
		);
		add_action(
			'plugin_loaded', // Active on specific blog.
			function ( string $loaded_file ) use ( $args ) {
				U\Fs::realize( $loaded_file ) === $args[ 0 ] && static::load();
			}
		);
	}

	/**
	 * Theme: adds theme instance hooks.
	 *
	 * @since 2021-12-15
	 *
	 * @param string ...$args {@see WPG\Traits\App\Magic\Constructable_Members::__construct()}.
	 */
	final protected static function add_theme_instance_hooks( string ...$args ) : void {
		// Adds theme instance hooks (just one for now) and loads theme.
		// There is no `plugin_loaded` equivalent for themes, so we create one.

		add_action(
			static::BRAND[ 'var_prefix' ] . 'theme_loaded',
			function ( string $loaded_file ) use ( $args ) {
				U\Fs::realize( $loaded_file ) === $args[ 0 ] && static::load();
			}
		);
		do_action( static::BRAND[ 'var_prefix' ] . 'theme_loaded', $args[ 0 ] );
	}

	/**
	 * Plugin|Theme: sets app instance, loads app.
	 *
	 * @since 2021-12-15
	 *
	 * @param bool $maybe_setup_hooks Control over hook setup.
	 *                                Default is `true`. Set to `false` when uninstalling.
	 *                                {@see WPG\Traits\App\Magic\Constructable_Members::__construct()}.
	 *
	 * @throws U\Fatal_Exception If missing instance args.
	 * @return WPG\I7e\App App instance for the called class.
	 */
	final public static function load( bool $maybe_setup_hooks = true ) : WPG\I7e\App {
		$cls = get_called_class();

		if ( ! isset( static::$instances[ $cls ][ '&' ] ) ) {
			if ( ! isset( static::$instances[ $cls ][ 'args' ] ) ) {
				throw new U\Fatal_Exception( 'Missing ' . static::app_type() . ' instance args for class: `' . $cls . '`.' );
			}
			$args                             = array_slice( static::$instances[ $cls ][ 'args' ], 0, 4 );
			$args                             = array_merge( $args, [ $maybe_setup_hooks ] );
			static::$instances[ $cls ][ '&' ] = new static( ...$args );
		}
		return static::$instances[ $cls ][ '&' ];
	}

	/**
	 * Plugin|Theme: gets app instance.
	 *
	 * @since 2021-12-15
	 *
	 * @throws U\Fatal_Exception If missing instance.
	 * @return WPG\I7e\App App instance for the called class.
	 */
	final public static function instance() : WPG\I7e\App {
		$cls = get_called_class();

		if ( ! isset( static::$instances[ $cls ][ '&' ] ) ) {
			throw new U\Fatal_Exception( 'Missing ' . static::app_type() . ' instance for class: `' . $cls . '`.' );
		}
		return static::$instances[ $cls ][ '&' ];
	}
}
