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
namespace WP_Groove\Framework\Theme\Version_1_0_0;

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
 * Base class for a WordPress theme.
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
	 * Name (e.g., My Theme).
	 *
	 * @since 2021-12-15
	 */
	protected string $name;

	/**
	 * Slug (e.g., wpgroove-my-theme).
	 *
	 * @since 2021-12-15
	 */
	protected string $slug;

	/**
	 * Slug prefix (e.g., wpgroove-my-theme--).
	 *
	 * @since 2021-12-15
	 */
	protected string $slug_prefix;

	/**
	 * Var (e.g., wpgroove_my_theme).
	 *
	 * @since 2021-12-15
	 */
	protected string $var;

	/**
	 * Var prefix (e.g., wpgroove_my_theme__).
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
	 * Unbranded slug (e.g., my-theme).
	 *
	 * @since 2021-12-15
	 */
	protected string $unbranded_slug;

	/**
	 * Unbranded var (e.g., my_theme).
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
	 * Theme instance.
	 *
	 * @since 2021-12-15
	 */
	protected static array $instances;

	// --- Instance -----------------------------------------------------------

	/**
	 * Adds theme instance hooks.
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

		$class = get_called_class();

		if ( ! isset( static::$instances[ $class ] ) ) {
			static::$instances                     ??= [];
			static::$instances[ $class ]           ??= [];
			static::$instances[ $class ][ 'args' ] = $args;
		}
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
	 * Sets theme instance, loads theme.
	 *
	 * @since 2021-12-15
	 *
	 * @param bool $maybe_setup_hooks Control over hook setup. {@see __construct()}.
	 *                                Default is `true`. Set to `false` when uninstalling.
	 *
	 * @throws Exception If missing instance args.
	 * @return self Theme instance for the called class.
	 */
	final public static function load( bool $maybe_setup_hooks = true ) : self {
		$class = get_called_class();

		if ( ! isset( static::$instances[ $class ][ '&' ] ) ) {
			if ( ! isset( static::$instances[ $class ][ 'args' ] ) ) {
				throw new Exception( 'Missing theme instance args for: `' . $class . '`.' );
			}
			$args                               = array_slice( static::$instances[ $class ][ 'args' ], 0, 4 );
			$args                               = array_merge( $args, [ $maybe_setup_hooks ] );
			static::$instances[ $class ][ '&' ] = new static( ...$args );
		}
		return static::$instances[ $class ][ '&' ];
	}

	/**
	 * Gets theme instance.
	 *
	 * @since 2021-12-15
	 *
	 * @throws Exception If missing instance.
	 * @return self Theme instance for the called class.
	 */
	final public static function instance() : self {
		$class = get_called_class();

		if ( ! isset( static::$instances[ $class ][ '&' ] ) ) {
			throw new Exception( 'Missing theme instance for: `' . $class . '`.' );
		}
		return static::$instances[ $class ][ '&' ];
	}

	// --- Instantiation ------------------------------------------------------

	/**
	 * Theme class constructor.
	 *
	 * @since 2021-12-15
	 *
	 * @param string $file              Absolute file path.
	 * @param string $name              Theme name; e.g., `My Theme`.
	 * @param string $slug              Theme slug; e.g., `wpgroove-my-theme`.
	 * @param string $version           Current version string; e.g., `1.0.0`.
	 * @param bool   $maybe_setup_hooks Maybe setup hooks? Set to `false` when uninstalling.
	 */
	final protected function __construct( string $file, string $name, string $slug, string $version, bool $maybe_setup_hooks ) {
		parent::__construct();

		assert( $file && is_file( $file ) );
		assert( $name && U\Str::is_name( $name ) );
		assert( $slug && U\Str::is_slug( $slug, $this::BRAND[ 'slug_prefix' ] ) );
		assert( $version && U\Str::is_version( $version ) );

		$this->file = U\Fs::normalize( $file );
		$this->dir  = U\Dir::name( $this->file );
		$this->url  = rtrim( get_template_directory_uri(), '/' );

		$this->name = $name; // e.g., `My Theme`.
		$this->slug = $slug; // e.g., `wpgroove-my-theme`.
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
		add_action( 'after_switch_theme', [ $this, 'on_theme_activation_base' ] );
		add_action( 'after_switch_theme', [ $this, 'on_theme_activation' ] );

		add_action( 'switch_theme', [ $this, 'on_theme_deactivation' ] );
		add_action( 'switch_theme', [ $this, 'on_theme_deactivation_base' ] );

		add_action( 'after_setup_theme', [ $this, 'on_after_setup_theme_base' ], $this->after_setup_theme_hook_priority );
		add_action( 'after_setup_theme', [ $this, 'on_after_setup_theme' ], $this->after_setup_theme_hook_priority );

		add_action( 'init', [ $this, 'on_init_base' ], $this->init_hook_priority );
		add_action( 'init', [ $this, 'on_init' ], $this->init_hook_priority );
	}

	// --- Activation Hook ----------------------------------------------------

	/**
	 * On `after_switch_theme` hook.
	 *
	 * @since 2021-12-15
	 */
	final public function on_theme_activation_base() : void {
		update_option( $this->var_prefix . 'previous_version', (string) get_option( $this->var_prefix . 'version' ), false );
		update_option( $this->var_prefix . 'version', $this->version, true );
	}

	/**
	 * On `after_switch_theme` hook.
	 *
	 * @since 2021-12-15
	 *
	 * @note  DO NOT POPULATE. This is for extenders only.
	 */
	public function on_theme_activation() : void {
		// DO NOT POPULATE. This is for extenders only.
	}

	// --- Deactivation Hook --------------------------------------------------

	/**
	 * On `switch_theme` hook.
	 *
	 * @since 2021-12-15
	 *
	 * @param string    $new_template New template name.
	 * @param \WP_Theme $new_theme    New theme instance.
	 * @param \WP_Theme $old_theme    Old theme instance.
	 */
	final public function on_theme_deactivation_base( string $new_template, \WP_Theme $new_theme, \WP_Theme $old_theme ) : void {
		// Should uninstall on deactivation?

		if ( ! get_option( $this->var_prefix . 'uninstall_on_deactivation' ) ) {
			return; // Not applicable.
		}
		// Uninstalls theme.

		static::on_uninstall_theme_base();
	}

	/**
	 * On `switch_theme` hook.
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
	 * On `switch_theme` hook, via {@see on_theme_deactivation_base()}.
	 *
	 * @since 2021-12-15
	 */
	final public static function on_uninstall_theme_base() : void {
		global $wpdb;

		// Load w/o hook setup.

		try { // Fail softly.
			$theme = static::load( false );
		} catch ( Exception $exception ) {
			error_log( 'Failed to load theme on: `' . current_action() . '`.' );
			return; // Fail software.
		}
		// Theme-specific uninstall routines.

		static::on_uninstall_theme( $theme );

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
			$wpdb->esc_like( $theme->var_prefix ) . '%',
			$wpdb->esc_like( '_' . $theme->var_prefix ) . '%',

			$wpdb->esc_like( '_transient_' . $theme->var_prefix ) . '%',
			$wpdb->esc_like( '_transient__' . $theme->var_prefix ) . '%',

			$wpdb->esc_like( '_transient_timeout_' . $theme->var_prefix ) . '%',
			$wpdb->esc_like( '_transient_timeout__' . $theme->var_prefix ) . '%',

			$wpdb->esc_like( '_site_transient_' . $theme->var_prefix ) . '%',
			$wpdb->esc_like( '_site_transient__' . $theme->var_prefix ) . '%',

			$wpdb->esc_like( '_site_transient_timeout_' . $theme->var_prefix ) . '%',
			$wpdb->esc_like( '_site_transient_timeout__' . $theme->var_prefix ) . '%',
		];
		foreach ( $meta_key_tables_columns as $_meta_key_table => $_meta_key_column ) {
			if ( in_array( $_meta_key_table, [ 'sitemeta' ], true ) && ! is_multisite() ) {
				continue; // ^ These keys are multisite-only.
			}
			if ( ! isset( $wpdb->{$_meta_key_table} ) ) {
				error_log( 'Missing `$wpdb->' . $_meta_key_table . '` while uninstalling theme: `' . $theme->slug . '`.' );
				continue; // Not possible.
			}
			foreach ( $meta_keys_like as $_meta_key_like ) {
				if ( false === $wpdb->query( $wpdb->prepare( 'DELETE FROM `' . esc_sql( $wpdb->{$_meta_key_table} ) . '` WHERE `' . esc_sql( $_meta_key_column ) . '` LIKE %s', $_meta_key_like ) ) ) { // phpcs:ignore -- query ok.
					error_log( '`$wpdb->' . $_meta_key_table . '` query failure on column `' . $_meta_key_column . '` while uninstalling theme: `' . $theme->slug . '`.' );
				}
			}
		}
	}

	/**
	 * On `switch_theme` hook, via {@see on_uninstall_theme_base()}.
	 *
	 * @since 2021-12-15
	 *
	 * @param self $theme Theme instance.
	 *
	 * @note  DO NOT POPULATE. This is for extenders only.
	 */
	public static function on_uninstall_theme( self $theme ) : void {
		// DO NOT POPULATE. This is for extenders only.
	}

	// --- After Setup Theme Hook ---------------------------------------------

	/**
	 * On `after_setup_theme` hook.
	 *
	 * @since 2021-12-15
	 */
	final public function on_after_setup_theme_base() : void {
		if ( version_compare( get_option( $this->var_prefix . 'version' ) ?: '', $this->version, '<' ) ) {
			$this->on_theme_activation_base();
			$this->on_theme_activation();
		}
		if ( is_dir( U\Dir::join( $this->dir, '/languages' ) ) ) {
			load_theme_textdomain( $this->slug, U\Dir::join( $this->dir, '/languages' ) );
		}
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
		// Nothing for now.
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
