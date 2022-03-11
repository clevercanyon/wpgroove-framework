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
namespace WP_Groove\Framework\Traits\A6t\App\Magic;

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
	 *
	 * @noinspection PhpMultipleClassDeclarationsInspection
	 */
	final protected function __construct( string $file, string $name, string $slug, string $version, bool $maybe_setup_hooks ) {
		parent::__construct();

		$this->brand = U\Brand::get( 'w6e' );
		$this->org   = U\Brand::get( $this->brand->org_n7m );

		/**
		 * PHP assertions run in debug mode only.
		 * https://www.php.net/manual/en/function.assert.php
		 *
		 * {@see assert()} is a language construct in PHP 7.
		 * Assertions take effect in development and testing environments,
		 * but are optimized completely away to have zero cost in production.
		 */
		assert( $file && is_file( $file ) );
		assert( $name && U\Str::is_name( $name ) );
		assert( $slug && U\Str::is_lede_slug( $slug, $this->brand->slug_prefix ) );
		assert( $version && U\Str::is_version( $version ) );

		$this->file = U\Fs::normalize( $file );
		$this->dir  = U\Dir::name( $this->file );

		if ( $this instanceof WPG\A6t\Plugin ) {
			$this->url     = rtrim( plugins_url( '', $this->file ), '/' );
			$this->subpath = U\Fs::normalize( plugin_basename( $this->file ) );

		} elseif ( $this instanceof WPG\A6t\Theme ) {
			$this->url     = rtrim( get_template_directory_uri(), '/' );
			$this->subpath = ''; // Not applicable.
		} else {
			throw new U\Fatal_Exception(
				'Unable to determine app type for class: `' . static::class . '`.'
			);
		}
		$this->vendor_dir    = U\Env::static_var( 'W6E_VENDOR_DIR' ) ?: U\Dir::join( $this->dir, '/vendor' );
		$this->framework_dir = U\Dir::join( $this->vendor_dir, '/' . $this->org->slug . '/' . $this->brand->slug_prefix . 'framework' );

		$this->type    = static::app_type();
		$this->version = $version; // e.g., `1.0.0`.

		$this->namespace_scope = U\Pkg::namespace_scope( static::class );
		$this->namespace_crux  = U\Pkg::namespace_crux( static::class );

		$this->name = $name;                             // e.g., `My Plugin`.
		$this->slug = $slug;                             // e.g., `wpgroove-my-plugin`.
		$this->var  = U\Str::to_lede_var( $this->slug ); // e.g., `wpgroove_my_plugin`.

		$this->slug_prefix = U\Str::to_lede_slug_prefix( $this->slug ); // e.g., `wpgroove-my-plugin-x-`.
		$this->var_prefix  = U\Str::to_lede_var_prefix( $this->var );   // e.g., `wpgroove_my_plugin_x_`.

		$this->unbranded_slug = mb_substr( $this->slug, mb_strlen( $this->brand->slug_prefix ) ); // e.g., `my-plugin`.
		$this->unbranded_var  = mb_substr( $this->var, mb_strlen( $this->brand->var_prefix ) );   // e.g., `my_plugin`.

		$this->needs                         ??= [];
		$this->needs[ 'admin_base_webpack' ] ??= false;

		$this->hook_priorities                        ??= [];
		$this->hook_priorities[ 'plugins_loaded' ]    ??= 10;
		$this->hook_priorities[ 'after_setup_theme' ] ??= 10;
		$this->hook_priorities[ 'init' ]              ??= 10;
		$this->hook_priorities[ 'rest_api_init' ]     ??= 10;
		$this->hook_priorities[ 'admin_init' ]        ??= 10;

		if ( $this instanceof WPG\A6t\Plugin ) { // Not lower that plugin instance loader.
			$this->hook_priorities[ 'plugins_loaded' ] = max(
				$this->hook_priorities[ 'plugins_loaded' ], -( PHP_INT_MAX - 10001 )
			);
		} elseif ( $this instanceof WPG\A6t\Theme ) { // Not lower that theme instance loader.
			$this->hook_priorities[ 'after_setup_theme' ] = max(
				$this->hook_priorities[ 'after_setup_theme' ], -( PHP_INT_MAX - 10001 )
			);
		}
		/**
		 * PHP assertions run in debug mode only.
		 * https://www.php.net/manual/en/function.assert.php
		 *
		 * {@see assert()} is a language construct in PHP 7.
		 * Assertions take effect in development and testing environments,
		 * but are optimized completely away to have zero cost in production.
		 */
		assert( ! $this->namespace_scope || U\Str::is_namespace_scope( $this->namespace_scope ) );
		assert( U\Str::is_namespace_crux( $this->namespace_crux, $this->brand->n7m ) );

		assert( U\Str::is_brand_slug( $this->brand->slug, $this->brand->slug ) );
		assert( U\Str::is_brand_var( $this->brand->var, $this->brand->var ) );

		assert( U\Str::is_brand_slug_prefix( $this->brand->slug_prefix, $this->brand->slug_prefix ) );
		assert( U\Str::is_brand_var_prefix( $this->brand->var_prefix, $this->brand->var_prefix ) );

		assert( U\Str::is_lede_slug( $this->slug, $this->brand->slug_prefix ) );
		assert( U\Str::is_lede_var( $this->var, $this->brand->var_prefix ) );

		assert( U\Str::to_lede_slug( $this->var ) === $this->slug );
		assert( U\Str::to_lede_var( $this->slug ) === $this->var );

		assert( U\Str::is_lede_slug_prefix( $this->slug_prefix, $this->brand->slug_prefix ) );
		assert( U\Str::is_lede_var_prefix( $this->var_prefix, $this->brand->var_prefix ) );

		assert( U\Str::to_lede_slug_prefix( $this->var_prefix ) === $this->slug_prefix );
		assert( U\Str::to_lede_var_prefix( $this->slug_prefix ) === $this->var_prefix );

		assert( U\Str::is_lede_slug( $this->unbranded_slug ) );
		assert( U\Str::is_lede_var( $this->unbranded_var ) );

		assert( U\Str::to_lede_slug( $this->unbranded_var ) === $this->unbranded_slug );
		assert( U\Str::to_lede_var( $this->unbranded_slug ) === $this->unbranded_var );

		assert( U\Str::is_namespace_crux( $this->namespace_crux, $this->brand->n7m, $this->unbranded_slug ) );

		/**
		 * Let's get our WP Groove on.
		 */
		if ( $maybe_setup_hooks && $this->should_setup_hooks_base() ) {
			$this->setup_hooks(); // Let’s make some waves.
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
	 * DO NOT POPULATE. This is for extenders only.
	 *
	 * @since 2021-12-15
	 *
	 * @return bool Should run setup?
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
		if ( $this instanceof WPG\A6t\Plugin ) {
			$this->add_action( 'activation', [ $this, 'on_activation_base' ] );
			$this->add_action( 'activation', [ $this, 'on_plugin_activation' ] );

			$this->add_action( 'deactivation', [ $this, 'on_plugin_deactivation' ] );
			$this->add_action( 'deactivation', [ $this, 'on_plugin_deactivation_base' ] );

			add_action( 'plugins_loaded', [ $this, 'on_plugins_loaded_base' ], $this->hook_priorities[ 'plugins_loaded' ] );
			add_action( 'plugins_loaded', [ $this, 'on_plugins_loaded' ], $this->hook_priorities[ 'plugins_loaded' ] );

		} elseif ( $this instanceof WPG\A6t\Theme ) {
			add_action( 'after_switch_theme', [ $this, 'on_activation_base' ] );
			add_action( 'after_switch_theme', [ $this, 'on_theme_activation' ] );

			add_action( 'switch_theme', [ $this, 'on_theme_deactivation' ] );
			add_action( 'switch_theme', [ $this, 'on_theme_deactivation_base' ] );
		}
		// The following apply to both app types.

		add_action( 'after_setup_theme', [ $this, 'on_after_setup_theme_base' ], $this->hook_priorities[ 'after_setup_theme' ] );
		add_action( 'after_setup_theme', [ $this, 'on_after_setup_theme' ], $this->hook_priorities[ 'after_setup_theme' ] );

		add_action( 'init', [ $this, 'on_init_base' ], $this->hook_priorities[ 'init' ] );
		add_action( 'init', [ $this, 'on_init' ], $this->hook_priorities[ 'init' ] );

		add_action( 'rest_api_init', [ $this, 'on_rest_api_init_base' ], $this->hook_priorities[ 'rest_api_init' ] );
		add_action( 'rest_api_init', [ $this, 'on_rest_api_init' ], $this->hook_priorities[ 'rest_api_init' ] );

		if ( is_admin() ) { // Admin-only hooks.
			add_action( 'admin_init', [ $this, 'on_admin_init_base' ], $this->hook_priorities[ 'admin_init' ] );
			add_action( 'admin_init', [ $this, 'on_admin_init' ], $this->hook_priorities[ 'admin_init' ] );

			add_action( 'wp_ajax_' . $this->var_prefix . 'admin_notice_dismiss', [ $this, 'on_wp_ajax_admin_notice_dismiss_base' ] );
			add_action( 'wp_ajax_' . $this->var_prefix . 'admin_notice_dismiss', [ $this, 'on_wp_ajax_admin_notice_dismiss' ] );

			add_action( 'admin_enqueue_scripts', [ $this, 'on_admin_enqueue_scripts_base' ] );
			add_action( 'admin_enqueue_scripts', [ $this, 'on_admin_enqueue_scripts' ] );

			add_action( 'all_admin_notices', [ $this, 'on_all_admin_notices_base' ] );
			add_action( 'all_admin_notices', [ $this, 'on_all_admin_notices' ] );
		}
	}
}
