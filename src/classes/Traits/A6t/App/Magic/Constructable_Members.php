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
	 *
	 * @param bool   $maybe_setup_hooks Control over hook setup when loading.
	 *                                  Default is `true`. Set to `false` when uninstalling.
	 *                                  {@see WPG\A6t\App::load()} for default value of `true`.
	 *
	 * @throws U\Fatal_Exception On failure to determine app type.
	 *
	 * @noinspection PhpMultipleClassDeclarationsInspection
	 */
	final protected function __construct( string $file, string $name, string $slug, string $version, bool $maybe_setup_hooks ) {
		parent::__construct();

		/**
		 * Org & brand properties.
		 */
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

		/**
		 * App type & version properties.
		 */
		$this->type    = static::app_type(); // e.g., `plugin`, `theme`.
		$this->version = $version;           // e.g., `1.0.0`, `1.0.1`, etc.

		/**
		 * Filesystem properties.
		 */
		$this->file         = U\Fs::normalize( $file );
		$this->dir          = U\Dir::name( $this->file );
		$this->dir_basename = basename( $this->dir );

		if ( $this instanceof WPG\A6t\Plugin ) {
			$this->dir_url      = rtrim( plugins_url( '', $this->file ), '/' );
			$this->file_subpath = U\Fs::normalize( plugin_basename( $this->file ) );

		} elseif ( $this instanceof WPG\A6t\Theme ) {
			$this->dir_url      = rtrim( get_template_directory_uri(), '/' );
			$this->file_subpath = U\Dir::subpath( get_theme_root( $this->dir_basename ), $this->file );
		} else {
			throw new U\Fatal_Exception( 'Unable to determine app type for class: `' . static::class . '`.' );
		}
		$this->vendor_dir    = U\Env::static_var( 'W6E_VENDOR_DIR' ) ?: U\Dir::join( $this->dir, '/vendor' );
		$this->framework_dir = U\Dir::join( $this->vendor_dir, '/' . $this->org->slug . '/' . $this->brand->slug_prefix . 'framework' );

		/**
		 * Namespace, slug, & var properties.
		 */
		$this->namespace_scope = U\Pkg::namespace_scope( static::class ); // e.g., `Xae3c7c368fe2e3c`.
		$this->namespace_crux  = U\Pkg::namespace_crux( static::class );  // e.g., `WP_Groove\My_Plugin`.

		$this->name = $name;                             // e.g., `My Plugin`.
		$this->slug = $slug;                             // e.g., `wpgroove-my-plugin`.
		$this->var  = U\Str::to_lede_var( $this->slug ); // e.g., `wpgroove_my_plugin`.

		$this->slug_prefix = U\Str::to_lede_slug_prefix( $this->slug ); // e.g., `wpgroove-my-plugin-x-`.
		$this->var_prefix  = U\Str::to_lede_var_prefix( $this->var );   // e.g., `wpgroove_my_plugin_x_`.

		$this->unbranded_slug = mb_substr( $this->slug, mb_strlen( $this->brand->slug_prefix ) ); // e.g., `my-plugin`.
		$this->unbranded_var  = mb_substr( $this->var, mb_strlen( $this->brand->var_prefix ) );   // e.g., `my_plugin`.

		/**
		 * App’s feature needs.
		 */
		$this->needs                    ??= [];
		$this->needs[ 'admin_webpack' ] ??= false;

		/**
		 * App’s hook priorities.
		 */
		$this->hook_priorities ??= [];

		$this->hook_priorities[ 'activation' ]   ??= -( PHP_INT_MAX - 10000 );
		$this->hook_priorities[ 'deactivation' ] ??= ( PHP_INT_MAX - 10000 );

		$this->hook_priorities[ 'plugins_loaded' ]    ??= -( PHP_INT_MAX - 10000 );
		$this->hook_priorities[ 'after_setup_theme' ] ??= -( PHP_INT_MAX - 10000 );

		$this->hook_priorities[ 'init' ]          ??= -( PHP_INT_MAX - 10000 );
		$this->hook_priorities[ 'rest_api_init' ] ??= -( PHP_INT_MAX - 10000 );

		$this->hook_priorities[ 'admin_init' ]            ??= -( PHP_INT_MAX - 10000 );
		$this->hook_priorities[ 'admin_enqueue_scripts' ] ??= -( PHP_INT_MAX - 10000 );
		$this->hook_priorities[ 'all_admin_notices' ]     ??= -( PHP_INT_MAX - 10000 );
		$this->hook_priorities[ 'wp_ajax' ]               ??= -( PHP_INT_MAX - 10000 );

		if ( $this instanceof WPG\A6t\Plugin ) { // Not lower than plugin instance loader.
			$this->hook_priorities[ 'plugins_loaded' ] = max(
				$this->hook_priorities[ 'plugins_loaded' ], -( PHP_INT_MAX - 10000 )
			);
		} elseif ( $this instanceof WPG\A6t\Theme ) { // Not lower than theme instance loader.
			$this->hook_priorities[ 'after_setup_theme' ] = max(
				$this->hook_priorities[ 'after_setup_theme' ], -( PHP_INT_MAX - 10000 )
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
		 * Maybe setup hooks.
		 */
		if ( $maybe_setup_hooks
			&& $this->fw_should_setup_hooks()
			&& $this->fwp_should_setup_hooks()
			&& $this->should_setup_hooks()
		) {
			$this->fw_setup_hooks();  // Sets up core framework hooks.
			$this->fwp_setup_hooks(); // Gives our separate pro framework an easy way in.
			$this->setup_hooks();     // Sets up any other app-specific hooks.
		}
	}

	/**
	 * Plugin|Theme: should setup hooks?
	 *
	 * @since 2021-12-15
	 *
	 * @return bool Should setup hooks?
	 */
	final protected function fw_should_setup_hooks() : bool {
		return true; // Nothing to check, for now.
	}

	/**
	 * Plugin|Theme: should setup hooks?
	 *
	 * DO NOT POPULATE. This is for extenders only.
	 * i.e., This is for our separate pro framework only.
	 *
	 * @since 2021-12-15
	 *
	 * @return bool Should setup hooks?
	 */
	protected function fwp_should_setup_hooks() : bool {
		return true; // DO NOT POPULATE. This is for our separate pro framework only.
	}

	/**
	 * Plugin|Theme: should setup hooks?
	 *
	 * DO NOT POPULATE. This is for extenders only.
	 *
	 * @since 2021-12-15
	 *
	 * @return bool Should setup hooks?
	 */
	protected function should_setup_hooks() : bool {
		return true; // DO NOT POPULATE. This is for extenders only.
	}

	/**
	 * Plugin|Theme: setup hooks on instantiation.
	 *
	 * @since 2021-12-15
	 */
	final protected function fw_setup_hooks() : void {
		/**
		 * Activation & deactivation hooks.
		 */
		if ( $this instanceof WPG\A6t\Theme ) {
			add_action(
				'after_switch_theme', // Pseudo-activation hook.
				fn() => $this->do_action( 'activation', $this->is_network_active() ),
				$this->hook_priorities[ 'activation' ], 0
			);
			add_action(
				'switch_theme',       // Pseudo-deactivation hook.
				fn() => $this->do_action( 'deactivation', $this->is_network_active() ),
				$this->hook_priorities[ 'deactivation' ], 0
			);
		}
		$this->add_action( 'activation', [ $this, 'fw_on_activation' ], $this->hook_priorities[ 'activation' ], 1 );
		$this->add_action( 'deactivation', [ $this, 'fw_on_deactivation' ], $this->hook_priorities[ 'deactivation' ], 1 );

		/**
		 * Initialization hooks.
		 */
		if ( $this instanceof WPG\A6t\Plugin ) {
			add_action( 'plugins_loaded', [ $this, 'fw_on_plugins_loaded' ], $this->hook_priorities[ 'plugins_loaded' ], 0 );
		}
		add_action( 'after_setup_theme', [ $this, 'fw_on_after_setup_theme' ], $this->hook_priorities[ 'after_setup_theme' ], 0 );

		add_action( 'init', [ $this, 'fw_on_init' ], $this->hook_priorities[ 'init' ], 0 );
		add_action( 'rest_api_init', [ $this, 'fw_on_rest_api_init' ], $this->hook_priorities[ 'rest_api_init' ], 0 );

		/**
		 * Admin-only initialization hooks; and more.
		 */
		if ( is_admin() ) {
			add_action( 'admin_init', [ $this, 'fw_on_admin_init' ], $this->hook_priorities[ 'admin_init' ], 0 );
			add_action( 'admin_enqueue_scripts', [ $this, 'fw_on_admin_enqueue_scripts' ], $this->hook_priorities[ 'admin_enqueue_scripts' ], 0 );
			add_action( 'all_admin_notices', [ $this, 'fw_on_all_admin_notices' ], $this->hook_priorities[ 'all_admin_notices' ], 0 );
			add_action(
				'wp_ajax_' . $this->var_prefix . 'admin_notice_dismiss',
				[ $this, 'fw_on_wp_ajax_admin_notice_dismiss' ], $this->hook_priorities[ 'wp_ajax' ], 0
			);
		}
	}

	/**
	 * Plugin|Theme: setup hooks on instantiation.
	 *
	 * DO NOT POPULATE. This is for extenders only.
	 * i.e., This is for our separate pro framework only.
	 *
	 * @since 2021-12-15
	 */
	protected function fwp_setup_hooks() : void {
		// DO NOT POPULATE. This is for our separate pro framework only.
	}

	/**
	 * Plugin|Theme: setup hooks on instantiation.
	 *
	 * DO NOT POPULATE. This is for extenders only.
	 *
	 * @since 2021-12-15
	 */
	protected function setup_hooks() : void {
		// DO NOT POPULATE. This is for extenders only.
	}
}
