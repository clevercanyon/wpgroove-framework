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
namespace WP_Groove\Framework\Utilities\OOP\Version_1_0_0\Abstracts;

/**
 * Utilities.
 *
 * @since 2021-12-15
 */
use Clever_Canyon\Utilities\STC\{Version_1_0_0 as U};
use Clever_Canyon\Utilities\OOP\Version_1_0_0\{Offsets, Generic, Error, Exception, Fatal_Exception};
use Clever_Canyon\Utilities\OOP\Version_1_0_0\Abstracts\{A6t_Base, A6t_Offsets, A6t_Generic, A6t_Error, A6t_Exception};
use Clever_Canyon\Utilities\OOP\Version_1_0_0\Interfaces\{I7e_Base, I7e_Offsets, I7e_Generic, I7e_Error, I7e_Exception};

/**
 * WP Groove utilities.
 *
 * @since 2021-12-15
 */
use WP_Groove\Framework\Utilities\STC\{Version_1_0_0 as UU};
use WP_Groove\Framework\Theme\Version_1_0_0\Abstracts\{AA6t_Theme};
use WP_Groove\Framework\Plugin\Version_1_0_0\Abstracts\{AA6t_Plugin};

// </editor-fold>

/**
 * Plugin|Theme (i.e., app) base class.
 *
 * @since 2021-12-15
 */
abstract class AA6t_App extends A6t_Base {
	/**
	 * OOP traits.
	 *
	 * @since 2021-12-15
	 */
	use \Clever_Canyon\Utilities\OOP\Version_1_0_0\Traits\I7e_Base\Magic\Readable_Members;

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
	 * Plugin|Theme: absolute file path.
	 *
	 * @since 2021-12-15
	 */
	protected string $file;

	/**
	 * Plugin|Theme: absolute dir path.
	 *
	 * @since 2021-12-15
	 */
	protected string $dir;

	/**
	 * Plugin|Theme: URL to directory.
	 *
	 * @since 2021-12-15
	 */
	protected string $url;

	/**
	 * Plugin: subpath (i.e., plugin basename).
	 *
	 * @since 2021-12-15
	 */
	protected string $subpath;

	/**
	 * Plugin|Theme: name (e.g., My App).
	 *
	 * @since 2021-12-15
	 */
	protected string $name;

	/**
	 * Plugin|Theme: slug (e.g., wpgroove-my-app).
	 *
	 * @since 2021-12-15
	 */
	protected string $slug;

	/**
	 * Plugin|Theme: slug prefix (e.g., wpgroove-my-app--).
	 *
	 * @since 2021-12-15
	 */
	protected string $slug_prefix;

	/**
	 * Plugin|Theme: var (e.g., wpgroove_my_app).
	 *
	 * @since 2021-12-15
	 */
	protected string $var;

	/**
	 * Plugin|Theme: var prefix (e.g., wpgroove_my_app__).
	 *
	 * @since 2021-12-15
	 */
	protected string $var_prefix;

	/**
	 * Plugin|Theme: brand name (i.e., WP Groove).
	 *
	 * @since 2021-12-15
	 */
	protected string $brand_name;

	/**
	 * Plugin|Theme: brand slug (i.e., wpgroove).
	 *
	 * @since 2021-12-15
	 */
	protected string $brand_slug;

	/**
	 * Plugin|Theme: brand slug prefix (i.e., wpgroove-).
	 *
	 * @since 2021-12-15
	 */
	protected string $brand_slug_prefix;

	/**
	 * Plugin|Theme: brand var (i.e., wpgroove).
	 *
	 * @since 2021-12-15
	 */
	protected string $brand_var;

	/**
	 * Plugin|Theme: brand var prefix (i.e., wpgroove_).
	 *
	 * @since 2021-12-15
	 */
	protected string $brand_var_prefix;

	/**
	 * Plugin|Theme: unbranded slug (e.g., my-app).
	 *
	 * @since 2021-12-15
	 */
	protected string $unbranded_slug;

	/**
	 * Plugin|Theme: unbranded var (e.g., my_app).
	 *
	 * @since 2021-12-15
	 */
	protected string $unbranded_var;

	/**
	 * Plugin|Theme: version string.
	 *
	 * @since 2021-12-15
	 */
	protected string $version;

	/**
	 * Plugin: plugins loaded hook priority.
	 *
	 * @since 2021-12-15
	 */
	protected int $plugins_loaded_hook_priority;

	/**
	 * Plugin|Theme: after setup theme hook priority.
	 *
	 * @since 2021-12-15
	 */
	protected int $after_setup_theme_hook_priority;

	/**
	 * Plugin|Theme: init hook priority.
	 *
	 * @since 2021-12-15
	 */
	protected int $init_hook_priority;

	/**
	 * Plugin|Theme: static app instances.
	 *
	 * @since 2021-12-15
	 */
	protected static array $instances;

	// --- Instance -----------------------------------------------------------

	/**
	 * Plugin|Theme: gets app type.
	 *
	 * @since 2021-12-30
	 *
	 * @throws Fatal_Exception On failure to determine app type.
	 * @return string Either `plugin` or `theme`.
	 */
	final protected static function app_type() : string {
		$cls = get_called_class();

		if ( is_a( $cls, AA6t_Plugin::class, true ) ) {
			return 'plugin';
		} elseif ( is_a( $cls, AA6t_Theme::class, true ) ) {
			return 'theme';
		}
		throw new Fatal_Exception( 'Unable to determine app type based on class: `' . $cls . '`.' );
	}

	/**
	 * Plugin|Theme: adds app instance hooks.
	 *
	 * @since 2021-12-15
	 *
	 * @param string ...$args {@see __construct()} for details.
	 *
	 * @throws Fatal_Exception On failure to determine app type.
	 */
	final public static function add_instance_hooks( string ...$args ) : void {
		// Saves instance args for {@see load()}.
		// Args also be used by {@see on_uninstall_base()}.

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
			throw new Fatal_Exception( 'Unable to determine app type based on class: `' . $cls . '`.' );
		}
	}

	/**
	 * Plugin: adds plugin instance hooks.
	 *
	 * @since 2021-12-15
	 *
	 * @param string ...$args {@see __construct()} for details.
	 */
	final protected static function add_plugin_instance_hooks( string ...$args ) : void {
		assert( ! empty( $args[ 0 ] ) && is_file( $args[ 0 ] ) );

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
	 * Theme: adds theme instance hooks.
	 *
	 * @since 2021-12-15
	 *
	 * @param string ...$args {@see __construct()} for details.
	 */
	final protected static function add_theme_instance_hooks( string ...$args ) : void {
		assert( ! empty( $args[ 0 ] ) && is_file( $args[ 0 ] ) );

		// Adds theme instance hooks (just one for now) and loads theme.
		// There is no `plugin_loaded` equivalent for themes, so we create one.

		add_action(
			static::BRAND[ 'var_prefix' ] . 'theme_loaded',
			function ( string $loaded_file ) use ( $args ) {
				realpath( $loaded_file ) === $args[ 0 ] && static::load();
			}
		);
		do_action( static::BRAND[ 'var_prefix' ] . 'theme_loaded', $args[ 0 ] );
	}

	/**
	 * Plugin|Theme: sets app instance, loads app.
	 *
	 * @since 2021-12-15
	 *
	 * @param bool $maybe_setup_hooks Control over hook setup. {@see __construct()}.
	 *                                Default is `true`. Set to `false` when uninstalling.
	 *
	 * @throws Fatal_Exception If missing instance args.
	 * @return AA6t_App App instance for the called class.
	 */
	final public static function load( bool $maybe_setup_hooks = true ) : AA6t_App {
		$cls = get_called_class();

		if ( ! isset( static::$instances[ $cls ][ '&' ] ) ) {
			if ( ! isset( static::$instances[ $cls ][ 'args' ] ) ) {
				throw new Fatal_Exception( 'Missing ' . static::app_type() . ' instance args for class: `' . $cls . '`.' );
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
	 * @throws Fatal_Exception If missing instance.
	 * @return AA6t_App App instance for the called class.
	 */
	final public static function instance() : AA6t_App {
		$cls = get_called_class();

		if ( ! isset( static::$instances[ $cls ][ '&' ] ) ) {
			throw new Fatal_Exception( 'Missing ' . static::app_type() . ' instance for class: `' . $cls . '`.' );
		}
		return static::$instances[ $cls ][ '&' ];
	}

	// --- Instantiation ------------------------------------------------------

	/**
	 * Plugin|Theme: class constructor.
	 *
	 * @since 2021-12-15
	 *
	 * @param string $file              Absolute file path.
	 * @param string $name              App name; e.g., `My App`.
	 * @param string $slug              App slug; e.g., `wpgroove-my-app`.
	 * @param string $version           Current version string; e.g., `1.0.0`.
	 * @param bool   $maybe_setup_hooks Maybe setup hooks? Set to `false` when uninstalling.
	 *
	 * @throws Fatal_Exception On failure to determine app type.
	 */
	final protected function __construct( string $file, string $name, string $slug, string $version, bool $maybe_setup_hooks ) {
		parent::__construct();

		assert( $file && is_file( $file ) );
		assert( $name && U\Str::is_name( $name ) );
		assert( $slug && U\Str::is_slug( $slug, $this::BRAND[ 'slug_prefix' ] ) );
		assert( $version && U\Str::is_version( $version ) );

		$this->file = U\Fs::normalize( $file );
		$this->dir  = U\Dir::name( $this->file );

		if ( $this instanceof AA6t_Plugin ) {
			$this->url     = rtrim( plugins_url( '', $this->file ), '/' );
			$this->subpath = U\Fs::normalize( plugin_basename( $this->file ) );

		} elseif ( $this instanceof AA6t_Theme ) {
			$this->url     = rtrim( get_template_directory_uri(), '/' );
			$this->subpath = ''; // Not applicable.
		} else {
			throw new Fatal_Exception( 'Unable to determine app type based on class: `' . get_class( $this ) . '`.' );
		}
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
	 * Plugin|Theme: should setup hooks?
	 *
	 * @since 2021-12-15
	 *
	 * @return bool Should run setup?
	 */
	final protected function should_setup_hooks_base() : bool {
		return $this->should_setup_hooks();
	}

	/**
	 * Plugin|Theme: should setup hooks?
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
	 * Plugin|Theme: setup hooks on instantiation.
	 *
	 * @since 2021-12-15
	 */
	final protected function setup_hooks() : void {
		if ( $this instanceof AA6t_Plugin ) {
			add_action( $this->var_prefix . 'activation', [ $this, 'on_activation_base' ] );
			add_action( $this->var_prefix . 'activation', [ $this, 'on_plugin_activation' ] );

			add_action( $this->var_prefix . 'deactivation', [ $this, 'on_plugin_deactivation' ] );
			add_action( $this->var_prefix . 'deactivation', [ $this, 'on_plugin_deactivation_base' ] );

			add_action( 'plugins_loaded', [ $this, 'on_plugins_loaded_base' ], $this->plugins_loaded_hook_priority );
			add_action( 'plugins_loaded', [ $this, 'on_plugins_loaded' ], $this->plugins_loaded_hook_priority );

		} elseif ( $this instanceof AA6t_Theme ) {
			add_action( 'after_switch_theme', [ $this, 'on_activation_base' ] );
			add_action( 'after_switch_theme', [ $this, 'on_theme_activation' ] );

			add_action( 'switch_theme', [ $this, 'on_theme_deactivation' ] );
			add_action( 'switch_theme', [ $this, 'on_theme_deactivation_base' ] );
		}
		// The following apply to both app types.

		add_action( 'after_setup_theme', [ $this, 'on_after_setup_theme_base' ], $this->after_setup_theme_hook_priority );
		add_action( 'after_setup_theme', [ $this, 'on_after_setup_theme' ], $this->after_setup_theme_hook_priority );

		add_action( 'init', [ $this, 'on_init_base' ], $this->init_hook_priority );
		add_action( 'init', [ $this, 'on_init' ], $this->init_hook_priority );
	}

	// --- App Activation Hooks -----------------------------------------------

	/**
	 * Plugin|Theme: activation hooks.
	 *
	 * - Plugin: on `{$this->var_prefix}activation` hook.
	 * - Theme: on `after_switch_theme` hook.
	 *
	 * @since 2021-12-15
	 *
	 * @param bool $network_wide True if activated network-wide.
	 *                           This arg applicable to plugins only.
	 */
	final public function on_activation_base( bool $network_wide = false ) : void {
		$previous_version = (string) get_option( $this->var_prefix . 'version' );
		update_option( $this->var_prefix . 'previous_version', $previous_version, false );
		update_option( $this->var_prefix . 'version', $this->version, true );

		if ( $this instanceof AA6t_Plugin ) {
			// This fires {@see on_uninstall_plugin()} before it runs.
			register_uninstall_hook( $this->file, [ static::class, 'on_uninstall_base' ] );
		}
	}

	/**
	 * Plugin: on `{$this->var_prefix}activation` hook.
	 *
	 * @since 2021-12-15
	 *
	 * @param bool $network_wide True if activated network-wide.
	 *
	 * @note  DO NOT POPULATE. This is for extenders only.
	 */
	public function on_plugin_activation( bool $network_wide ) : void {
		// DO NOT POPULATE. This is for extenders only.
	}

	/**
	 * Theme: `after_switch_theme` hook.
	 *
	 * @since 2021-12-15
	 *
	 * @note  DO NOT POPULATE. This is for extenders only.
	 */
	public function on_theme_activation() : void {
		// DO NOT POPULATE. This is for extenders only.
	}

	// --- Deactivation Hooks -------------------------------------------------

	/**
	 * Plugin: on `{$this->var_prefix}deactivation` hook.
	 *
	 * @since 2021-12-15
	 *
	 * @param bool $network_wide True if deativated network-wide.
	 */
	final public function on_plugin_deactivation_base( bool $network_wide ) : void {
		// Nothing for now.
	}

	/**
	 * Plugin: on `{$this->var_prefix}deactivation` hook.
	 *
	 * @since 2021-12-15
	 *
	 * @param bool $network_wide True if deativated network-wide.
	 *
	 * @note  DO NOT POPULATE. This is for extenders only.
	 */
	public function on_plugin_deactivation( bool $network_wide ) : void {
		// DO NOT POPULATE. This is for extenders only.
	}

	/**
	 * Theme: on `switch_theme` hook.
	 *
	 * @since 2021-12-15
	 *
	 * @param string    $new_template New template name.
	 * @param \WP_Theme $new_theme    New theme instance.
	 * @param \WP_Theme $old_theme    Old theme instance.
	 */
	final public function on_theme_deactivation_base( string $new_template, \WP_Theme $new_theme, \WP_Theme $old_theme ) : void {
		if ( get_option( $this->var_prefix . 'uninstall_on_deactivation' ) ) {
			static::on_uninstall_base();
		}
	}

	/**
	 * Theme: on `switch_theme` hook.
	 *
	 * @since 2021-12-15
	 *
	 * @param string    $new_template New template name.
	 * @param \WP_Theme $new_theme    New theme instance.
	 * @param \WP_Theme $old_theme    Old theme instance.
	 *
	 * @note  DO NOT POPULATE. This is for extenders only.
	 */
	public function on_theme_deactivation( string $new_template, \WP_Theme $new_theme, \WP_Theme $old_theme ) : void {
		// DO NOT POPULATE. This is for extenders only.
	}

	// --- Uninstall Hook -----------------------------------------------------

	/**
	 * Plugin|Theme: uninstall hooks.
	 *
	 * - Plugin: on `uninstall_{$this->subpath}` hook.
	 * - Theme: on `switch_theme` hook.
	 *
	 * @since 2021-12-15
	 */
	final public static function on_uninstall_base() : void {
		global $wpdb;

		// Load w/o hook setup.

		try { // Fail softly.
			$app = static::load( false );

		} catch ( Fatal_Exception $exception ) {
			error_log( 'Failed to load ' . static::app_type() . ' on: `' . current_action() . '`.' );
			return; // Fail software.
		}
		// App-specific uninstall routines.

		if ( $app instanceof AA6t_Theme ) {
			static::on_uninstall_theme( $app );

		} elseif ( $app instanceof AA6t_Plugin ) {
			static::on_uninstall_plugin( $app );
		}
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
			$wpdb->esc_like( $app->var_prefix ) . '%',
			$wpdb->esc_like( '_' . $app->var_prefix ) . '%',

			$wpdb->esc_like( '_transient_' . $app->var_prefix ) . '%',
			$wpdb->esc_like( '_transient__' . $app->var_prefix ) . '%',

			$wpdb->esc_like( '_transient_timeout_' . $app->var_prefix ) . '%',
			$wpdb->esc_like( '_transient_timeout__' . $app->var_prefix ) . '%',

			$wpdb->esc_like( '_site_transient_' . $app->var_prefix ) . '%',
			$wpdb->esc_like( '_site_transient__' . $app->var_prefix ) . '%',

			$wpdb->esc_like( '_site_transient_timeout_' . $app->var_prefix ) . '%',
			$wpdb->esc_like( '_site_transient_timeout__' . $app->var_prefix ) . '%',
		];
		foreach ( $meta_key_tables_columns as $_meta_key_table => $_meta_key_column ) {
			if ( in_array( $_meta_key_table, [ 'sitemeta' ], true ) && ! is_multisite() ) {
				continue; // ^ These keys are multisite-only.
			}
			if ( ! isset( $wpdb->{$_meta_key_table} ) ) {
				error_log( 'Missing `$wpdb->' . $_meta_key_table . '` while uninstalling ' . static::app_type() . ': `' . $app->slug . '`.' );
				continue; // Not possible.
			}
			foreach ( $meta_keys_like as $_meta_key_like ) {
				if ( false === $wpdb->query( $wpdb->prepare( 'DELETE FROM `' . esc_sql( $wpdb->{$_meta_key_table} ) . '` WHERE `' . esc_sql( $_meta_key_column ) . '` LIKE %s', $_meta_key_like ) ) ) { // phpcs:ignore -- query ok.
					error_log( '`$wpdb->' . $_meta_key_table . '` query failure on column `' . $_meta_key_column . '` while uninstalling ' . static::app_type() . ': `' . $app->slug . '`.' );
				}
			}
		}
	}

	/**
	 * Plugin: on `uninstall_{$this->subpath}` hook, via {@see on_uninstall_base()}.
	 *
	 * @since 2021-12-15
	 *
	 * @param AA6t_Plugin $plugin Plugin instance.
	 *
	 * @note  DO NOT POPULATE. This is for extenders only.
	 */
	public static function on_uninstall_plugin( AA6t_Plugin $plugin ) : void {
		// DO NOT POPULATE. This is for extenders only.
	}

	/**
	 * Theme: on `switch_theme` hook, via {@see on_uninstall_base()}.
	 *
	 * @since 2021-12-15
	 *
	 * @param AA6t_Theme $theme Theme instance.
	 *
	 * @note  DO NOT POPULATE. This is for extenders only.
	 */
	public static function on_uninstall_theme( AA6t_Theme $theme ) : void {
		// DO NOT POPULATE. This is for extenders only.
	}

	// --- Plugins Loaded Hook ------------------------------------------------
	// This particular hook is applicable to plugins only — themes load later.

	/**
	 * Plugin: on `plugins_loaded` hook.
	 *
	 * @since 2021-12-15
	 */
	final public function on_plugins_loaded_base() : void {
		if ( $this instanceof AA6t_Plugin ) {
			if ( version_compare( get_option( $this->var_prefix . 'version' ) ?: '', $this->version, '<' ) ) {
				$this->on_activation_base( false );
				$this->on_plugin_activation( false );
			}
		}
	}

	/**
	 * Plugin: on `plugins_loaded` hook.
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
	 * Plugin|Theme: on `after_setup_theme` hook.
	 *
	 * @since 2021-12-15
	 */
	final public function on_after_setup_theme_base() : void {
		if ( $this instanceof AA6t_Theme ) {
			if ( version_compare( get_option( $this->var_prefix . 'version' ) ?: '', $this->version, '<' ) ) {
				$this->on_activation_base();
				$this->on_theme_activation();
			}
		}
	}

	/**
	 * Plugin|Theme: on `after_setup_theme` hook.
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
	 * Plugin|Theme: on `init` hook.
	 *
	 * @since 2021-12-15
	 */
	final public function on_init_base() : void {
		if ( $this instanceof AA6t_Plugin ) {
			if ( is_dir( U\Dir::join( $this->dir, '/languages' ) ) ) {
				load_plugin_textdomain( $this->slug, false, U\Dir::name( $this->subpath, '/languages' ) );
			}
		} elseif ( $this instanceof AA6t_Theme ) {
			if ( is_dir( U\Dir::join( $this->dir, '/languages' ) ) ) {
				load_theme_textdomain( $this->slug, U\Dir::join( $this->dir, '/languages' ) );
			}
		}
	}

	/**
	 * Plugin|Theme: on `init` hook.
	 *
	 * @since 2021-12-15
	 *
	 * @note  DO NOT POPULATE. This is for extenders only.
	 */
	public function on_init() : void {
		// DO NOT POPULATE. This is for extenders only.
	}
}
