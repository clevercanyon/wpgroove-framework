<?php
/** WP Groove™ <https://wpgroove.com>
 *  _       _  ___       ___
 * ( )  _  ( )(  _`\    (  _`\
 * | | ( ) | || |_) )   | ( (_) _ __   _      _    _   _    __  ™
 * | | | | | || ,__/'   | |___ ( '__)/'_`\  /'_`\ ( ) ( ) /'__`\
 * | (_/ \_) || |       | (_, )| |  ( (_) )( (_) )| \_/ |(  ___/
 * `\___x___/'(_)       (____/'(_)  `\___/'`\___/'`\___/'`\____)
 */
namespace WP_Groove\Framework\Theme\Version_1_0_0;

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
	 * Slug (e.g., wpgroove-my-theme).
	 *
	 * @since 1.0.0
	 */
	protected string $slug;

	/**
	 * Var (e.g., wpgroove_my_theme).
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
	 * Slug (e.g., my-theme).
	 *
	 * @since 1.0.0
	 */
	protected string $unbranded_slug;

	/**
	 * Var (e.g., my_theme).
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
	 * Theme instance.
	 *
	 * @since 1.0.0
	 */
	protected static array $instances;

	// --- Instance Methods ---------------------------------------------------

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
			static::$instances[ $class ] = new static( $file, $version );
		}
		return static::$instances[ $class ];
	}

	/**
	 * Gets instance.
	 *
	 * @since 1.0.0
	 *
	 * @throws \Exception If missing `$instance`.
	 */
	final public static function instance() : self {
		$class = get_called_class();

		if ( ! isset( static::$instances[ $class ] ) ) {
			throw new \Exception( 'Missing theme instance for: `' . $class . '`.' );
		}
		return static::$instances[ $class ];
	}

	// --- Instantiation ------------------------------------------------------

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $file    Theme's absolute file path.
	 * @param  string $version Theme's current version string.
	 *
	 * @throws \Exception      If theme has a highly unexpected slug or var.
	 */
	final protected function __construct( string $file, string $version ) {
		parent::__construct();

		$this->file = U\Fs::normalize( $file );
		$this->dir  = dirname( $this->file );
		$this->url  = rtrim( get_template_directory_uri(), '/' );

		$this->slug = get_template();
		$this->var  = str_replace( '-', '_', $this->slug );

		$this->brand_slug = 'wpgroove'; // WP Groove™
		$this->brand_var  = str_replace( '-', '_', $this->brand_slug );

		$this->unbranded_slug = preg_replace( '/^' . preg_quote( $this->brand_slug . '-', '/' ) . '/ui', '', $this->slug );
		$this->unbranded_var  = str_replace( '-', '_', $this->unbranded_slug );

		$this->version = $version; // e.g., `1.0.0`.

		$this->after_setup_theme_hook_priority ??= 10;
		$this->init_hook_priority              ??= 10;

		if ( 0 !== mb_stripos( $this->slug, $this->brand_slug . '-' ) || 0 !== mb_stripos( $this->var, $this->brand_var . '_' ) ) {
			throw new \Exception( 'Unexpected (unbranded) theme slug: `' . $this->slug . '`, or var: `' . $this->var . '`.' );
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
		add_action( 'after_switch_theme', [ $this, 'on_theme_activation_base' ] );
		add_action( 'after_switch_theme', [ $this, 'on_theme_activation' ] );

		add_action( 'switch_theme', [ $this, 'on_theme_deactivation' ] );
		add_action( 'switch_theme', [ $this, 'on_theme_deactivation_base' ] );

		add_action( 'after_setup_theme', [ $this, 'on_after_setup_theme_base' ], $this->after_setup_theme_hook_priority );
		add_action( 'after_setup_theme', [ $this, 'on_after_setup_theme' ], $this->after_setup_theme_hook_priority );

		add_action( 'init', [ $this, 'on_init_base' ], $this->init_hook_priority );
		add_action( 'init', [ $this, 'on_init' ], $this->init_hook_priority );
	}

	// --- Hook Methods (`after_switch_theme`) --------------------------------

	/**
	 * On `after_switch_theme` hook.
	 *
	 * @since 1.0.0
	 */
	final public function on_theme_activation_base() : void {
		update_option( $this->var . '__previous_version', (string) get_option( $this->var . '__version' ), false );
		update_option( $this->var . '__version', $this->version, true );
	}

	/**
	 * On `after_switch_theme` hook.
	 *
	 * @since 1.0.0
	 *
	 * @internal DO NOT POPULATE. This is for extenders only.
	 */
	public function on_theme_activation() : void {
		// DO NOT POPULATE. This is for extenders only.
	}

	// --- Hook Methods (`switch_theme`) --------------------------------------

	/**
	 * On `switch_theme` hook.
	 *
	 * @since 1.0.0
	 *
	 * @param string    $new_template New template name.
	 * @param \WP_Theme $new_theme    New theme instance.
	 * @param \WP_Theme $old_theme    Old theme instance.
	 */
	final public function on_theme_deactivation_base( string $new_template, \WP_Theme $new_theme, \WP_Theme $old_theme ) : void {
		global $wpdb;

		// Should uninstall on deactivation?

		if ( ! get_option( $this->var . '__uninstall_on_deactivation' ) ) {
			return; // Not applicable.
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
				error_log( 'Missing `$wpdb->' . $_meta_key_table . '` while uninstalling theme slug: `' . $slug . '`.' );
				continue; // Not possible.
			}
			foreach ( $meta_keys_like as $_meta_key_like ) {
				if ( false === $wpdb->query( $wpdb->prepare( 'DELETE FROM `' . esc_sql( $wpdb->{$_meta_key_table} ) . '` WHERE `' . esc_sql( $_meta_key_column ) . '` LIKE %s', $_meta_key_like ) ) ) { // phpcs:ignore -- query ok.
					error_log( '`$wpdb->' . $_meta_key_table . '` query failure on column `' . $_meta_key_column . '` while uninstalling theme slug: `' . $slug . '`.' );
				}
			}
		}
	}

	/**
	 * On `switch_theme` hook.
	 *
	 * @since 1.0.0
	 *
	 * @param string    $new_template New template name.
	 * @param \WP_Theme $new_theme    New theme instance.
	 * @param \WP_Theme $old_theme    Old theme instance.
	 *
	 * @internal                      DO NOT POPULATE. This is for extenders only.
	 */
	public function on_theme_deactivation( string $new_template, \WP_Theme $new_theme, \WP_Theme $old_theme ) : void {
		// DO NOT POPULATE. This is for extenders only.
	}

	// --- Hook Methods (`after_setup_theme`) ---------------------------------

	/**
	 * On `after_setup_theme` hook.
	 *
	 * @since 1.0.0
	 */
	final public function on_after_setup_theme_base() : void {
		if ( version_compare( get_option( $this->var . '__version' ) ?: '', $this->version, '<' ) ) {
			$this->on_theme_activation_base();
			$this->on_theme_activation();
		}
		if ( is_dir( $this->dir . '/languages' ) ) {
			load_theme_textdomain( $this->slug, $this->dir . '/languages' );
		}
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
		// Nothing for now.
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
