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
namespace WP_Groove\Framework\Traits\A6t\App\Properties;

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
 * @property-read $org
 * @property-read $brand
 *
 * @property-read $type
 * @property-read $version
 *
 * @property-read $file
 * @property-read $dir
 *
 * @property-read $file_subpath
 * @property-read $dir_basename
 * @property-read $dir_url
 *
 * @property-read $vendor_dir
 * @property-read $framework_dir
 *
 * @property-read $namespace_scope
 * @property-read $namespace_crux
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
 * @property-read $needs
 * @property-read $hook_priorities
 *
 * @see   WPG\A6t\App
 */
trait Property_Members {
	/**
	 * Plugin|Theme: org data.
	 *
	 * @since 2021-12-15
	 */
	protected object $org;

	/**
	 * Plugin|Theme: brand data.
	 *
	 * @since 2021-12-15
	 */
	protected object $brand;

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
	 * Plugin|Theme: file path.
	 *
	 * @since 2021-12-15
	 */
	protected string $file;

	/**
	 * Plugin|Theme: directory path.
	 *
	 * @since 2021-12-15
	 */
	protected string $dir;

	/**
	 * Plugin|Theme: file subpath.
	 *
	 * @since 2021-12-15
	 */
	protected string $file_subpath;

	/**
	 * Plugin|Theme: directory basename.
	 *
	 * @since 2021-12-15
	 */
	protected string $dir_basename;

	/**
	 * Plugin|Theme: directory URL.
	 *
	 * @since 2021-12-15
	 */
	protected string $dir_url;

	/**
	 * Plugin|Theme: vendor directory path.
	 *
	 * @since 2021-12-15
	 */
	protected string $vendor_dir;

	/**
	 * Plugin|Theme: framework directory path.
	 *
	 * @since 2021-12-15
	 */
	protected string $framework_dir;

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
	 * Plugin|Theme: needs; e.g., `admin_webpack`.
	 *
	 * @since 2021-12-15
	 */
	protected array $needs; // Features.

	/**
	 * Plugin|Theme: hook priorities; e.g., `init`.
	 *
	 * @since 2021-12-15
	 */
	protected array $hook_priorities;

	/**
	 * Plugin|Theme: static app instances.
	 *
	 * @since 2021-12-15
	 */
	protected static array $instances;
}
