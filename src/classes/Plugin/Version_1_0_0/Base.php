<?php
/** WP Groove™ <https://wpgroove.com>
 *  _       _  ___       ___
 * ( )  _  ( )(  _`\    (  _`\
 * | | ( ) | || |_) )   | ( (_) _ __   _      _    _   _    __  ™
 * | | | | | || ,__/'   | |___ ( '__)/'_`\  /'_`\ ( ) ( ) /'__`\
 * | (_/ \_) || |       | (_, )| |  ( (_) )( (_) )| \_/ |(  ___/
 * `\___x___/'(_)       (____/'(_)  `\___/'`\___/'`\___/'`\____)
 */
namespace WP_Groove\Framework\Plugin\Version_1_0_0;

/**
 * Dependencies.
 *
 * @since 1.0.0
 */
use Clever_Canyon\Utilities\OOPs\Version_1_0_0 as U;
use WP_Groove\Framework\Utilities\OOPs\Version_1_0_0 as UU;
use Clever_Canyon\Utilities\OOP\Version_1_0_0\{ Base as OOP_Base };

/**
 * Base class.
 *
 * @since 1.0.0
 */
abstract class Base extends OOP_Base {
	/**
	 * Absolute file path.
	 *
	 * @since 1.0.0
	 */
	protected string $file;

	/**
	 * Absolute dir path.
	 *
	 * @since 1.0.0
	 */
	protected string $dir;

	/**
	 * URL to directory.
	 *
	 * @since 1.0.0
	 */
	protected string $url;

	/**
	 * Plugin basename.
	 *
	 * @since 1.0.0
	 */
	protected string $basename;

	/**
	 * Slug (e.g., wpgroove-my-plugin).
	 *
	 * @since 1.0.0
	 */
	protected string $slug;

	/**
	 * Var (e.g., wpgroove_my_plugin).
	 *
	 * @since 1.0.0
	 */
	protected string $var;

	/**
	 * Slug (i.e., wpgroove).
	 *
	 * @since 1.0.0
	 */
	protected string $brand_slug;

	/**
	 * Slug (i.e., wpgroove).
	 *
	 * @since 1.0.0
	 */
	protected string $brand_var;

	/**
	 * Slug (e.g., my-plugin).
	 *
	 * @since 1.0.0
	 */
	protected string $unbranded_slug;

	/**
	 * Var (e.g., my_plugin).
	 *
	 * @since 1.0.0
	 */
	protected string $unbranded_var;

	/**
	 * Version.
	 *
	 * @since 1.0.0
	 */
	protected string $version;

	/**
	 * Plugins loaded hook priority.
	 *
	 * @since 1.0.0
	 */
	protected int $plugins_loaded_hook_priority;

	/**
	 * After setup theme hook priority.
	 *
	 * @since 1.0.0
	 */
	protected int $after_setup_theme_hook_priority;

	/**
	 * Init hook priority.
	 *
	 * @since 1.0.0
	 */
	protected int $init_hook_priority;

	/**
	 * Plugin instances.
	 *
	 * @since 1.0.0
	 */
	protected static array $instances;

	// --- Instance Methods ---------------------------------------------------

	/**
	 * Adds plugin instance hooks.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $file    Absolute file path.
	 * @param  string $version Current version string.
	 */
	final public static function add_plugin_instance_hooks( string $file, string $version ) : void {
		register_activation_hook( $file, function( bool $network_wide ) use ( $file, $version ) {
			do_action( static::load( $file, $version )->var . '__activation', $network_wide );
		} );
		register_deactivation_hook( $file, function( bool $network_wide ) use ( $file, $version ) {
			do_action( static::load( $file, $version )->var . '__deactivation', $network_wide );
		} );
		add_action( 'network_plugin_loaded', function( string $loaded_file ) use ( $file, $version ) {
			realpath( $loaded_file ) === $file && static::load( $file, $version );
		} );
		add_action( 'plugin_loaded', function( string $loaded_file ) use ( $file, $version ) {
			realpath( $loaded_file ) === $file && static::load( $file, $version );
		} );
	}

	/**
	 * Loads instance.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $file    Absolute file path.
	 * @param  string $version Current version string.
	 *
	 * @return self            Instance.
	 */
	final public static function load( string $file, string $version ) : self {
		$class = get_called_class();

		if ( ! isset( static::$instances[ $class ] ) ) {
			static::$instances         ??= [];
			static::$instances[ $class ] = new static( $file, $version );
		}
		return static::$instances[ $class ];
	}

	/**
	 * Gets instance.
	 *
	 * @since 1.0.0
	 *
	 * @return self       Instance.
	 *
	 * @throws \Exception If missing `$instance`.
	 */
	final public static function instance() : self {
		$class = get_called_class();

		if ( ! isset( static::$instances[ $class ] ) ) {
			throw new \Exception( 'Missing plugin instance for: `' . $class . '`.' );
		}
		return static::$instances[ $class ];
	}

	// --- Instantiation ------------------------------------------------------

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $file    Plugin's absolute file path.
	 * @param  string $version Plugin's current version string.
	 *
	 * @throws \Exception      If plugin has a highly unexpected basename, slug, or var.
	 */
	final protected function __construct( string $file, string $version ) {
		parent::__construct();

		$this->file     = U\Fs::normalize( $file );
		$this->dir      = dirname( $this->file );
		$this->basename = plugin_basename( $this->file );
		$this->url      = rtrim( plugins_url( '', $this->file ), '/' );

		$this->slug = basename( dirname( $this->basename ) );
		$this->var  = str_replace( '-', '_', $this->slug );

		$this->brand_slug = 'wpgroove'; // WP Groove™
		$this->brand_var  = str_replace( '-', '_', $this->brand_slug );

		$this->unbranded_slug = preg_replace( '/^' . preg_quote( $this->brand_slug . '-', '/' ) . '/ui', '', $this->slug );
		$this->unbranded_var  = str_replace( '-', '_', $this->unbranded_slug );

		$this->version = $version; // e.g., `1.0.0`.

		$this->plugins_loaded_hook_priority    ??= 10;
		$this->after_setup_theme_hook_priority ??= 10;
		$this->init_hook_priority              ??= 10;

		if ( 1 !== mb_substr_count( $this->basename, '/' ) || 0 !== mb_stripos( $this->basename, $this->brand_slug . '-' ) ) {
			throw new \Exception( 'Unexpected plugin basename: `' . $this->basename . '`.' );
		}
		if ( 0 !== mb_stripos( $this->slug, $this->brand_slug . '-' ) || 0 !== mb_stripos( $this->var, $this->brand_var . '_' ) ) {
			throw new \Exception( 'Unexpected plugin slug: `' . $this->slug . '`, or var: `' . $this->var . '`.' );
		}
		if ( $this->should_setup_hooks_base() ) {
			$this->setup_hooks();
		}
	}

	/**
	 * Should setup hooks?
	 *
	 * @since 1.0.0
	 *
	 * @return bool Should run setup?
	 */
	final protected function should_setup_hooks_base() : bool {
		return $this->should_setup_hooks();
	}

	/**
	 * Should setup hooks?
	 *
	 * @since 1.0.0
	 *
	 * @return bool Should run setup?
	 *
	 * @internal    DO NOT POPULATE. This is for extenders only.
	 */
	protected function should_setup_hooks() : bool {
		return true; // DO NOT POPULATE. This is for extenders only.
	}

	/**
	 * Sets up hooks.
	 *
	 * @since 1.0.0
	 */
	final protected function setup_hooks() : void {
		add_action( $this->var . '__activation', [ $this, 'on_plugin_activation_base' ] );
		add_action( $this->var . '__activation', [ $this, 'on_plugin_activation' ] );

		add_action( $this->var . '__deactivation', [ $this, 'on_plugin_deactivation' ] );
		add_action( $this->var . '__deactivation', [ $this, 'on_plugin_deactivation_base' ] );

		add_action( 'plugins_loaded', [ $this, 'on_plugins_loaded_base' ], $this->plugins_loaded_hook_priority );
		add_action( 'plugins_loaded', [ $this, 'on_plugins_loaded' ], $this->plugins_loaded_hook_priority );

		add_action( 'after_setup_theme', [ $this, 'on_after_setup_theme_base' ], $this->after_setup_theme_hook_priority );
		add_action( 'after_setup_theme', [ $this, 'on_after_setup_theme' ], $this->after_setup_theme_hook_priority );

		add_action( 'init', [ $this, 'on_init_base' ], $this->init_hook_priority );
		add_action( 'init', [ $this, 'on_init' ], $this->init_hook_priority );
	}

	// --- Hook Methods (`{$this->var}__activation`) --------------------------

	/**
	 * On `{$this->var}__activation` hook.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $network_wide Network-wide?
	 */
	final public function on_plugin_activation_base( bool $network_wide ) : void {
		update_option( $this->var . '__previous_version', (string) get_option( $this->var . '__version' ), false );
		update_option( $this->var . '__version', $this->version, true );

		// This fires {@link on_uninstall_plugin()} before it runs.
		register_uninstall_hook( $this->file, [ static::class, 'on_uninstall_plugin_base' ] );
	}

	/**
	 * On `{$this->var}__activation` hook.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $network_wide Network-wide?
	 *
	 * @internal                 DO NOT POPULATE. This is for extenders only.
	 */
	public function on_plugin_activation( bool $network_wide ) : void {
		// DO NOT POPULATE. This is for extenders only.
	}

	// --- Hook Methods (`{$this->var}__deactivation`) ------------------------

	/**
	 * On `{$this->var}__deactivation` hook.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $network_wide Network-wide?
	 */
	final public function on_plugin_deactivation_base( bool $network_wide ) : void {
		// Nothing for now.
	}

	/**
	 * On `{$this->var}__deactivation` hook.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $network_wide Network-wide?
	 *
	 * @internal                 DO NOT POPULATE. This is for extenders only.
	 */
	public function on_plugin_deactivation( bool $network_wide ) : void {
		// DO NOT POPULATE. This is for extenders only.
	}

	// --- Hook Methods (`uninstall_{$this->basename}`) -----------------------

	/**
	 * On `uninstall_{$this->basename}` hook.
	 *
	 * @since 1.0.0
	 */
	final public static function on_uninstall_plugin_base() : void {
		global $wpdb;

		$class    = get_called_class();
		$basename = preg_replace( '/^uninstall_/u', '', current_action() );

		$slug = basename( dirname( $basename ) );
		$var  = str_replace( '-', '_', $slug );

		if ( 1 !== mb_substr_count( $basename, '/' ) || 0 !== mb_stripos( $basename, 'wpgroove-' ) ) {
			error_log( 'Unexpected plugin basename: `' . $basename . '`.' );
		}
		if ( 0 !== mb_stripos( $slug, 'wpgroove-' ) || 0 !== mb_stripos( $var, 'wpgroove_' ) ) {
			error_log( 'Unexpected plugin slug: `' . $slug . '`, or var: `' . $var . '`.' );
		}

		// Plugin-specific uninstall routines.

		static::on_uninstall_plugin( $basename, $slug, $var );

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
			'var_name_like'                         => $wpdb->esc_like( $var . '__' ) . '%',
			'_var_name_like'                        => $wpdb->esc_like( '_' . $var . '__' ) . '%',

			'transient_var_name_like'               => $wpdb->esc_like( '_transient_' . $var . '__' ) . '%',
			'_transient_var_name_like'              => $wpdb->esc_like( '_transient__' . $var . '__' ) . '%',

			'transient_timeout_var_name_like'       => $wpdb->esc_like( '_transient_timeout_' . $var . '__' ) . '%',
			'_transient_timeout_var_name_like'      => $wpdb->esc_like( '_transient_timeout__' . $var . '__' ) . '%',

			'site_transient_var_name_like'          => $wpdb->esc_like( '_site_transient_' . $var . '__' ) . '%',
			'_site_transient_var_name_like'         => $wpdb->esc_like( '_site_transient__' . $var . '__' ) . '%',

			'site_transient_timeout_var_name_like'  => $wpdb->esc_like( '_site_transient_timeout_' . $var . '__' ) . '%',
			'_site_transient_timeout_var_name_like' => $wpdb->esc_like( '_site_transient_timeout__' . $var . '__' ) . '%',
		];
		foreach ( $meta_key_tables_columns as $_meta_key_table => $_meta_key_column ) {
			if ( in_array( $_meta_key_table, [ 'sitemeta' ], true ) && ! is_multisite() ) {
				// ^ These keys are multisite-only.
				continue; // Not applicable.
			}
			if ( ! isset( $wpdb->{$_meta_key_table} ) ) {
				error_log( 'Missing `$wpdb->' . $_meta_key_table . '` while uninstalling plugin slug: `' . $slug . '`.' );
				continue; // Not possible.
			}
			foreach ( $meta_keys_like as $_meta_key_like ) {
				if ( false === $wpdb->query( $wpdb->prepare( 'DELETE FROM `' . esc_sql( $wpdb->{$_meta_key_table} ) . '` WHERE `' . esc_sql( $_meta_key_column ) . '` LIKE %s', $_meta_key_like ) ) ) { // phpcs:ignore -- query ok.
					error_log( '`$wpdb->' . $_meta_key_table . '` query failure on column `' . $_meta_key_column . '` while uninstalling plugin slug: `' . $slug . '`.' );
				}
			}
		}
	}

	/**
	 * On `uninstall_{$this->basename}` hook (by way of {@link on_uninstall_plugin_base()}).
	 *
	 * @since 1.0.0
	 *
	 * @param string $basename Plugin's basename, passed by {@link on_uninstall_plugin_base()}.
	 * @param string $slug     Plugin's slug, passed by {@link on_uninstall_plugin_base()}.
	 * @param string $var      Plugin's var, passed by {@link on_uninstall_plugin_base()}.
	 *
	 * @internal               DO NOT POPULATE. This is for extenders only.
	 */
	public static function on_uninstall_plugin( string $basename, string $slug, string $var ) : void {
		// DO NOT POPULATE. This is for extenders only.
	}

	// --- Hook Methods (`plugins_loaded`) ------------------------------------

	/**
	 * On `plugins_loaded` hook.
	 *
	 * @since 1.0.0
	 */
	final public function on_plugins_loaded_base() : void {
		if ( version_compare( get_option( $this->var . '__version' ) ?: '', $this->version, '<' ) ) {
			$this->on_plugin_activation_base( false );
			$this->on_plugin_activation( false );
		}
	}

	/**
	 * On `plugins_loaded` hook.
	 *
	 * @since 1.0.0
	 *
	 * @internal DO NOT POPULATE. This is for extenders only.
	 */
	public function on_plugins_loaded() : void {
		// DO NOT POPULATE. This is for extenders only.
	}

	// --- Hook Methods (`after_setup_theme`) ---------------------------------

	/**
	 * On `after_setup_theme` hook.
	 *
	 * @since 1.0.0
	 */
	final public function on_after_setup_theme_base() : void {
		// Nothing for now.
	}

	/**
	 * On `after_setup_theme` hook.
	 *
	 * @since 1.0.0
	 *
	 * @internal DO NOT POPULATE. This is for extenders only.
	 */
	public function on_after_setup_theme() : void {
		// DO NOT POPULATE. This is for extenders only.
	}

	// --- Hook Methods (`init`) ----------------------------------------------

	/**
	 * On `init` hook.
	 *
	 * @since 1.0.0
	 */
	final public function on_init_base() : void {
		if ( is_dir( $this->dir . '/languages' ) ) {
			load_plugin_textdomain( $this->slug, false, dirname( $this->basename ) . '/languages' );
		}
	}

	/**
	 * On `init` hook.
	 *
	 * @since 1.0.0
	 *
	 * @internal DO NOT POPULATE. This is for extenders only.
	 */
	public function on_init() : void {
		// DO NOT POPULATE. This is for extenders only.
	}
}
