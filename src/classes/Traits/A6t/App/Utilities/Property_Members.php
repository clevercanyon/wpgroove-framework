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
 * Interface members.
 *
 * @since 2021-12-15
 *
 * @property-read $file
 * @property-read $dir
 * @property-read $url
 * @property-read $subpath
 *
 * @property-read $type
 * @property-read $version
 *
 * @property-read $namespace_scope
 * @property-read $namespace_crux
 *
 * @property-read $brand_n7m
 * @property-read $brand_name
 * @property-read $brand_namespace
 *
 * @property-read $brand_slug
 * @property-read $brand_var
 *
 * @property-read $brand_slug_prefix
 * @property-read $brand_var_prefix
 *
 * @property-read $name
 * @property-read $slug
 * @property-read $var
 *
 * @property-read $slug_prefix
 * @property-read $var_prefix
 *
 * @property-read $unbranded_slug
 * @property-read $unbranded_var
 *
 * @see   WPG\I7e\App
 */
trait Property_Members {
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
	 * Plugin|Theme: app type.
	 *
	 * @since 2021-12-15
	 */
	protected string $type;

	/**
	 * Plugin|Theme: version string.
	 *
	 * @since 2021-12-15
	 */
	protected string $version;

	/**
	 * Plugin|Theme: namespace scope; e.g., `Xae3c7c368fe2e3c`
	 *
	 * @since 2021-12-15
	 */
	protected string $namespace_scope;

	/**
	 * Plugin|Theme: namespace crux; e.g., `WP_Groove\My_App`
	 *
	 * @since 2021-12-15
	 */
	protected string $namespace_crux;

	/**
	 * Plugin|Theme: brand n7m; e.g., `w6e`.
	 *
	 * @since 2021-12-15
	 */
	protected string $brand_n7m;

	/**
	 * Plugin|Theme: brand name (i.e., WP Groove).
	 *
	 * @since 2021-12-15
	 */
	protected string $brand_name;

	/**
	 * Brand namespace; e.g., `WP_Groove`.
	 *
	 * @since 2021-12-15
	 */
	protected string $brand_namespace;

	/**
	 * Plugin|Theme: brand slug (i.e., wpgroove).
	 *
	 * @since 2021-12-15
	 */
	protected string $brand_slug;

	/**
	 * Plugin|Theme: brand var (i.e., wpgroove).
	 *
	 * @since 2021-12-15
	 */
	protected string $brand_var;

	/**
	 * Plugin|Theme: brand slug prefix (i.e., wpgroove-).
	 *
	 * @since 2021-12-15
	 */
	protected string $brand_slug_prefix;

	/**
	 * Plugin|Theme: brand var prefix (i.e., wpgroove_).
	 *
	 * @since 2021-12-15
	 */
	protected string $brand_var_prefix;

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
	 * Plugin|Theme: var (e.g., wpgroove_my_app).
	 *
	 * @since 2021-12-15
	 */
	protected string $var;

	/**
	 * Plugin|Theme: slug prefix (e.g., wpgroove-my-app-x-).
	 *
	 * @since 2021-12-15
	 */
	protected string $slug_prefix;

	/**
	 * Plugin|Theme: var prefix (e.g., wpgroove_my_app_x_).
	 *
	 * @since 2021-12-15
	 */
	protected string $var_prefix;

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
}
