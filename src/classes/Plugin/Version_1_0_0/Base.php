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
declare( strict_types = 1 ); // ｡･:*:･ﾟ★.
namespace WP_Groove\Framework\Plugin\Version_1_0_0;

/**
 * Utilities.
 *
 * @since 2021-12-15
 */
use Clever_Canyon\Utilities\OOPs\{Version_1_0_0 as U};
use Clever_Canyon\Utilities\OOP\Version_1_0_0\{Exception};
use WP_Groove\Framework\Utilities\OOPs\Version_1_0_0 as UU;

// </editor-fold>

/**
 * Base class for a WordPress plugin.
 *
 * @since 2021-12-15
 */
abstract class Base extends \Clever_Canyon\Utilities\OOP\Version_1_0_0\Base {
	/**
	 * Brand info.
	 *
	 * @since 2021-12-15
	 *
	 * @final Starting w/ PHP 8.1.0.
	 */
	protected const BRAND = [
		'name' => 'WP Groove',

		'slug'        => 'wpgroove',
		'slug_prefix' => 'wpgroove-',

		'var'        => 'wpgroove',
		'var_prefix' => 'wpgroove_',
	];

	/**
	 * Absolute file path.
	 *
	 * @since 2021-12-15
	 */
	protected string $file;

	/**
	 * Absolute dir path.
	 *
	 * @since 2021-12-15
	 */
	protected string $dir;

	/**
	 * URL to directory.
	 *
	 * @since 2021-12-15
	 */
	protected string $url;

	/**
	 * Plugin subpath.
	 *
	 * @since 2021-12-15
	 */
	protected string $subpath;

	/**
	 * Name (e.g., My Plugin).
	 *
	 * @since 2021-12-15
	 */
	protected string $name;

	/**
	 * Slug (e.g., wpgroove-my-plugin).
	 *
	 * @since 2021-12-15
	 */
	protected string $slug;

	/**
	 * Slug prefix (e.g., wpgroove-my-plugin--).
	 *
	 * @since 2021-12-15
	 */
	protected string $slug_prefix;

	/**
	 * Var (e.g., wpgroove_my_plugin).
	 *
	 * @since 2021-12-15
	 */
	protected string $var;

	/**
	 * Var prefix (e.g., wpgroove_my_plugin__).
	 *
	 * @since 2021-12-15
	 */
	protected string $var_prefix;

	/**
	 * Brand name (i.e., WP Groove).
	 *
	 * @since 2021-12-15
	 */
	protected string $brand_name;

	/**
	 * Brand slug (i.e., wpgroove).
	 *
	 * @since 2021-12-15
	 */
	protected string $brand_slug;

	/**
	 * Brand slug prefix (i.e., wpgroove-).
	 *
	 * @since 2021-12-15
	 */
	protected string $brand_slug_prefix;

	/**
	 * Brand var (i.e., wpgroove).
	 *
	 * @since 2021-12-15
	 */
	protected string $brand_var;

	/**
	 * Brand var prefix (i.e., wpgroove_).
	 *
	 * @since 2021-12-15
	 */
	protected string $brand_var_prefix;

	/**
	 * Unbranded slug (e.g., my-plugin).
	 *
	 * @since 2021-12-15
	 */
	protected string $unbranded_slug;

	/**
	 * Unbranded var (e.g., my_plugin).
	 *
	 * @since 2021-12-15
	 */
	protected string $unbranded_var;

	/**
	 * Version.
	 *
	 * @since 2021-12-15
	 */
	protected string $version;

	/**
	 * Plugins loaded hook priority.
	 *
	 * @since 2021-12-15
	 */
	protected int $plugins_loaded_hook_priority;

	/**
	 * After setup theme hook priority.
	 *
	 * @since 2021-12-15
	 */
	protected int $after_setup_theme_hook_priority;

	/**
	 * Init hook priority.
	 *
	 * @since 2021-12-15
	 */
	protected int $init_hook_priority;

	/**
	 * Plugin instances.
	 *
	 * @since 2021-12-15
	 */
	protected static array $instances;

	// --- Instance -----------------------------------------------------------

	/**
	 * Adds plugin instance hooks.
	 *
	 * @since 2021-12-15
	 *
	 * @param string ...$args {@see __construct()} for details.
	 *
	 * @throws Exception If missing instance args. {@see load()} for details.
	 */
	final public static function add_instance_hooks( string ...$args ) : void {
		assert( ! empty( $args[ 0 ] ) && is_file( $args[ 0 ] ) );

		// Saves instance args for {@see load()}.
		// May also be used by {@see on_uninstall_plugin_base()}.

		$class = get_called_class();

		if ( ! isset( static::$instances[ $class ] ) ) {
			static::$instances                     ??= [];
			static::$instances[ $class ]           ??= [];
			static::$instances[ $class ][ 'args' ] = $args;
		}
		// Registers activation/deactivation hooks.
		// The activation hook is what registers the uninstall hook.

		register_activation_hook(
			$args[ 0 ], // The plugin file.
			function ( bool $network_wide ) {
				do_action( static::load()->var_prefix . 'activation', $network_wide );
			}
		);
		register_deactivation_hook(
			$args[ 0 ], // The plugin file.
			function ( bool $network_wide ) {
				do_action( static::load()->var_prefix . 'deactivation', $network_wide );
			}
		);
		// These fire when loaded by `wp-settings.php` in the normal course of things.
		// Neither of these will fire on activation/deactivation, or when the plugin is loaded during an uninstall in WordPress.
		// Activation/deactivation are handled above. Uninstall is a separate method below — {@see on_uninstall_plugin_base()}.

		add_action(
			'network_plugin_loaded', // Network active.
			function ( string $loaded_file ) use ( $args ) {
				realpath( $loaded_file ) === $args[ 0 ] && static::load();
			}
		);
		add_action(
			'plugin_loaded', // Active on a specific site.
			function ( string $loaded_file ) use ( $args ) {
				realpath( $loaded_file ) === $args[ 0 ] && static::load();
			}
		);
	}

	/**
	 * Sets plugin instance, loads plugin.
	 *
	 * @since 2021-12-15
	 *
	 * @param bool $maybe_setup_hooks Control over hook setup. {@see __construct()}.
	 *                                Default is `true`. Set to `false` when uninstalling.
	 *
	 * @throws Exception If missing instance args.
	 * @return self Plugin instance for the called class.
	 */
	final public static function load( bool $maybe_setup_hooks = true ) : self {
		$class = get_called_class();

		if ( ! isset( static::$instances[ $class ][ '&' ] ) ) {
			if ( ! isset( static::$instances[ $class ][ 'args' ] ) ) {
				throw new Exception( 'Missing plugin instance args for: `' . $class . '`.' );
			}
			$args                               = array_slice( static::$instances[ $class ][ 'args' ], 0, 4 );
			$args                               = array_merge( $args, [ $maybe_setup_hooks ] );
			static::$instances[ $class ][ '&' ] = new static( ...$args );
		}
		return static::$instances[ $class ][ '&' ];
	}

	/**
	 * Gets plugin instance.
	 *
	 * @since 2021-12-15
	 *
	 * @throws Exception If missing instance.
	 * @return self Plugin instance for the called class.
	 */
	final public static function instance() : self {
		$class = get_called_class();

		if ( ! isset( static::$instances[ $class ][ '&' ] ) ) {
			throw new Exception( 'Missing plugin instance for: `' . $class . '`.' );
		}
		return static::$instances[ $class ][ '&' ];
	}

	// --- Instantiation ------------------------------------------------------

	/**
	 * Plugin class constructor.
	 *
	 * @since 2021-12-15
	 *
	 * @param string $file              Absolute file path.
	 * @param string $name              Plugin name; e.g., `My Plugin`.
	 * @param string $slug              Plugin slug; e.g., `wpgroove-my-plugin`.
	 * @param string $version           Current version string; e.g., `1.0.0`.
	 * @param bool   $maybe_setup_hooks Maybe setup hooks? Set to `false` when uninstalling.
	 */
	final protected function __construct( string $file, string $name, string $slug, string $version, bool $maybe_setup_hooks ) {
		parent::__construct();

		assert( $file && is_file( $file ) );
		assert( $name && U\Str::is_name( $name ) );
		assert( $slug && U\Str::is_slug( $slug, $this::BRAND[ 'slug_prefix' ] ) );
		assert( $version && U\Str::is_version( $version ) );

		$this->file    = U\Fs::normalize( $file );
		$this->dir     = U\Dir::name( $this->file );
		$this->url     = rtrim( plugins_url( '', $this->file ), '/' );
		$this->subpath = U\Fs::normalize( plugin_basename( $this->file ) );

		$this->name = $name; // e.g., `My Plugin`.
		$this->slug = $slug; // e.g., `wpgroove-my-plugin`.
		$this->var  = str_replace( '-', '_', $this->slug );

		$this->slug_prefix = $this->slug . '--';
		$this->var_prefix  = $this->var . '__';

		$this->brand_name = $this::BRAND[ 'name' ];
		$this->brand_slug = $this::BRAND[ 'slug' ];
		$this->brand_var  = $this::BRAND[ 'var' ];

		$this->brand_slug_prefix = $this::BRAND[ 'slug_prefix' ];
		$this->brand_var_prefix  = $this::BRAND[ 'var_prefix' ];

		$this->unbranded_slug = mb_substr( $this->slug, mb_strlen( $this->brand_slug_prefix ) );
		$this->unbranded_var  = str_replace( '-', '_', $this->unbranded_slug );

		$this->version = $version; // e.g., `1.0.0`.

		$this->plugins_loaded_hook_priority    ??= 10;
		$this->after_setup_theme_hook_priority ??= 10;
		$this->init_hook_priority              ??= 10;

		if ( $maybe_setup_hooks && $this->should_setup_hooks_base() ) {
			$this->setup_hooks();
		}
	}

	/**
	 * Should setup hooks?
	 *
	 * @since 2021-12-15
	 *
	 * @return bool Should run setup?
	 */
	final protected function should_setup_hooks_base() : bool {
		return $this->should_setup_hooks();
	}

	/**
	 * Should setup hooks?
	 *
	 * @since 2021-12-15
	 *
	 * @return bool Should run setup?
	 *
	 * @note  DO NOT POPULATE. This is for extenders only.
	 */
	protected function should_setup_hooks() : bool {
		return true; // DO NOT POPULATE. This is for extenders only.
	}

	/**
	 * Sets up hooks.
	 *
	 * @since 2021-12-15
	 */
	final protected function setup_hooks() : void {
		add_action( $this->var_prefix . 'activation', [ $this, 'on_plugin_activation_base' ] );
		add_action( $this->var_prefix . 'activation', [ $this, 'on_plugin_activation' ] );

		add_action( $this->var_prefix . 'deactivation', [ $this, 'on_plugin_deactivation' ] );
		add_action( $this->var_prefix . 'deactivation', [ $this, 'on_plugin_deactivation_base' ] );

		add_action( 'plugins_loaded', [ $this, 'on_plugins_loaded_base' ], $this->plugins_loaded_hook_priority );
		add_action( 'plugins_loaded', [ $this, 'on_plugins_loaded' ], $this->plugins_loaded_hook_priority );

		add_action( 'after_setup_theme', [ $this, 'on_after_setup_theme_base' ], $this->after_setup_theme_hook_priority );
		add_action( 'after_setup_theme', [ $this, 'on_after_setup_theme' ], $this->after_setup_theme_hook_priority );

		add_action( 'init', [ $this, 'on_init_base' ], $this->init_hook_priority );
		add_action( 'init', [ $this, 'on_init' ], $this->init_hook_priority );
	}

	// --- Activation Hook ----------------------------------------------------

	/**
	 * On `{$this->var_prefix}activation` hook.
	 *
	 * @since 2021-12-15
	 *
	 * @param bool $network_wide Network-wide.
	 */
	final public function on_plugin_activation_base( bool $network_wide ) : void {
		update_option( $this->var_prefix . 'previous_version', (string) get_option( $this->var_prefix . 'version' ), false );
		update_option( $this->var_prefix . 'version', $this->version, true );

		// This fires {@see on_uninstall_plugin()} before it runs.
		register_uninstall_hook( $this->file, [ static::class, 'on_uninstall_plugin_base' ] );
	}

	/**
	 * On `{$this->var_prefix}activation` hook.
	 *
	 * @since 2021-12-15
	 *
	 * @param bool $network_wide Network-wide.
	 *
	 * @note  DO NOT POPULATE. This is for extenders only.
	 */
	public function on_plugin_activation( bool $network_wide ) : void {
		// DO NOT POPULATE. This is for extenders only.
	}

	// --- Deactivation Hook --------------------------------------------------

	/**
	 * On `{$this->var_prefix}deactivation` hook.
	 *
	 * @since 2021-12-15
	 *
	 * @param bool $network_wide Network-wide.
	 */
	final public function on_plugin_deactivation_base( bool $network_wide ) : void {
		// Nothing for now.
	}

	/**
	 * On `{$this->var_prefix}deactivation` hook.
	 *
	 * @since 2021-12-15
	 *
	 * @param bool $network_wide Network-wide.
	 *
	 * @note  DO NOT POPULATE. This is for extenders only.
	 */
	public function on_plugin_deactivation( bool $network_wide ) : void {
		// DO NOT POPULATE. This is for extenders only.
	}

	// --- Uninstall Hook -----------------------------------------------------

	/**
	 * On `uninstall_{$this->subpath}` hook.
	 *
	 * @since 2021-12-15
	 */
	final public static function on_uninstall_plugin_base() : void {
		global $wpdb;

		// Load w/o hook setup.

		try { // Fail softly.
			$plugin = static::load( false );
		} catch ( Exception $exception ) {
			error_log( 'Failed to load plugin on: `' . current_action() . '`.' );
			return; // Fail software.
		}
		// Plugin-specific uninstall routines.

		static::on_uninstall_plugin( $plugin );

		// Base uninstall routines.

		$meta_key_tables_columns = [
			'sitemeta'    => 'meta_key',
			'usermeta'    => 'meta_key',
			'postmeta'    => 'meta_key',
			'termmeta'    => 'meta_key',
			'commentmeta' => 'meta_key',
			'options'     => 'option_name',
		];
		$meta_keys_like          = [
			$wpdb->esc_like( $plugin->var_prefix ) . '%',
			$wpdb->esc_like( '_' . $plugin->var_prefix ) . '%',

			$wpdb->esc_like( '_transient_' . $plugin->var_prefix ) . '%',
			$wpdb->esc_like( '_transient__' . $plugin->var_prefix ) . '%',

			$wpdb->esc_like( '_transient_timeout_' . $plugin->var_prefix ) . '%',
			$wpdb->esc_like( '_transient_timeout__' . $plugin->var_prefix ) . '%',

			$wpdb->esc_like( '_site_transient_' . $plugin->var_prefix ) . '%',
			$wpdb->esc_like( '_site_transient__' . $plugin->var_prefix ) . '%',

			$wpdb->esc_like( '_site_transient_timeout_' . $plugin->var_prefix ) . '%',
			$wpdb->esc_like( '_site_transient_timeout__' . $plugin->var_prefix ) . '%',
		];
		foreach ( $meta_key_tables_columns as $_meta_key_table => $_meta_key_column ) {
			if ( in_array( $_meta_key_table, [ 'sitemeta' ], true ) && ! is_multisite() ) {
				continue; // ^ These keys are multisite-only.
			}
			if ( ! isset( $wpdb->{$_meta_key_table} ) ) {
				error_log( 'Missing `$wpdb->' . $_meta_key_table . '` while uninstalling plugin: `' . $plugin->slug . '`.' );
				continue; // Not possible.
			}
			foreach ( $meta_keys_like as $_meta_key_like ) {
				if ( false === $wpdb->query( $wpdb->prepare( 'DELETE FROM `' . esc_sql( $wpdb->{$_meta_key_table} ) . '` WHERE `' . esc_sql( $_meta_key_column ) . '` LIKE %s', $_meta_key_like ) ) ) { // phpcs:ignore -- query ok.
					error_log( '`$wpdb->' . $_meta_key_table . '` query failure on column `' . $_meta_key_column . '` while uninstalling plugin: `' . $plugin->slug . '`.' );
				}
			}
		}
	}

	/**
	 * On `uninstall_{$this->subpath}` hook, via {@see on_uninstall_plugin_base()}.
	 *
	 * @since 2021-12-15
	 *
	 * @param self $plugin Plugin instance.
	 *
	 * @note  DO NOT POPULATE. This is for extenders only.
	 */
	public static function on_uninstall_plugin( self $plugin ) : void {
		// DO NOT POPULATE. This is for extenders only.
	}

	// --- Plugins Loaded Hook ------------------------------------------------

	/**
	 * On `plugins_loaded` hook.
	 *
	 * @since 2021-12-15
	 */
	final public function on_plugins_loaded_base() : void {
		if ( version_compare( get_option( $this->var_prefix . 'version' ) ?: '', $this->version, '<' ) ) {
			$this->on_plugin_activation_base( false );
			$this->on_plugin_activation( false );
		}
	}

	/**
	 * On `plugins_loaded` hook.
	 *
	 * @since 2021-12-15
	 *
	 * @note  DO NOT POPULATE. This is for extenders only.
	 */
	public function on_plugins_loaded() : void {
		// DO NOT POPULATE. This is for extenders only.
	}

	// --- After Setup Theme Hook ---------------------------------------------

	/**
	 * On `after_setup_theme` hook.
	 *
	 * @since 2021-12-15
	 */
	final public function on_after_setup_theme_base() : void {
		// Nothing for now.
	}

	/**
	 * On `after_setup_theme` hook.
	 *
	 * @since 2021-12-15
	 *
	 * @note  DO NOT POPULATE. This is for extenders only.
	 */
	public function on_after_setup_theme() : void {
		// DO NOT POPULATE. This is for extenders only.
	}

	// --- Init Hook ----------------------------------------------------------

	/**
	 * On `init` hook.
	 *
	 * @since 2021-12-15
	 */
	final public function on_init_base() : void {
		if ( is_dir( U\Dir::join( $this->dir, '/languages' ) ) ) {
			load_plugin_textdomain( $this->slug, false, U\Dir::name( $this->subpath, '/languages' ) );
		}
	}

	/**
	 * On `init` hook.
	 *
	 * @since 2021-12-15
	 *
	 * @note  DO NOT POPULATE. This is for extenders only.
	 */
	public function on_init() : void {
		// DO NOT POPULATE. This is for extenders only.
	}
}
