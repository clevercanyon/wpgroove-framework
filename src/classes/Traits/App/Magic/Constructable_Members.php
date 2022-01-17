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
namespace WP_Groove\Framework\Traits\App\Magic;

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
trait Constructable_Members {
	/**
	 * Plugin|Theme: class constructor.
	 *
	 * @since        2021-12-15
	 *
	 * @param string $file              Absolute file path.
	 * @param string $name              App name; e.g., `My App`.
	 * @param string $slug              App slug; e.g., `wpgroove-my-app`.
	 * @param string $version           Current version string; e.g., `1.0.0`.
	 * @param bool   $maybe_setup_hooks Maybe setup hooks? Set to `false` when uninstalling.
	 *
	 * @throws U\Fatal_Exception On failure to determine app type.
	 * @noinspection PhpMultipleClassDeclarationsInspection
	 */
	final protected function __construct( string $file, string $name, string $slug, string $version, bool $maybe_setup_hooks ) {
		parent::__construct();

		assert( $file && is_file( $file ) );
		assert( $name && U\Str::is_name( $name ) );
		assert( $slug && U\Str::is_slug( $slug, $this::BRAND[ 'slug_prefix' ] ) );
		assert( $version && U\Str::is_version( $version ) );

		$this->file = U\Fs::normalize( $file );
		$this->dir  = U\Dir::name( $this->file );

		if ( $this instanceof WPG\I7e\Plugin ) {
			$this->url     = rtrim( plugins_url( '', $this->file ), '/' );
			$this->subpath = U\Fs::normalize( plugin_basename( $this->file ) );

		} elseif ( $this instanceof WPG\I7e\Theme ) {
			$this->url     = rtrim( get_template_directory_uri(), '/' );
			$this->subpath = ''; // Not applicable.
		} else {
			throw new U\Fatal_Exception( 'Unable to determine app type for class: `' . get_class( $this ) . '`.' );
		}
		$this->version = $version; // e.g., `1.0.0`.

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
		if ( $this instanceof WPG\I7e\Plugin ) {
			add_action( $this->var_prefix . 'activation', [ $this, 'on_activation_base' ] );
			add_action( $this->var_prefix . 'activation', [ $this, 'on_plugin_activation' ] );

			add_action( $this->var_prefix . 'deactivation', [ $this, 'on_plugin_deactivation' ] );
			add_action( $this->var_prefix . 'deactivation', [ $this, 'on_plugin_deactivation_base' ] );

			add_action( 'plugins_loaded', [ $this, 'on_plugins_loaded_base' ], $this->plugins_loaded_hook_priority );
			add_action( 'plugins_loaded', [ $this, 'on_plugins_loaded' ], $this->plugins_loaded_hook_priority );

		} elseif ( $this instanceof WPG\I7e\Theme ) {
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
}
