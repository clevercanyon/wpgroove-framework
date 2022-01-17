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
namespace WP_Groove\Framework\I7e;

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
 * Plugin|Theme (i.e., app) interface.
 *
 * @since 2021-12-15
 */
interface App extends U\I7e\Base {
	/**
	 * Plugin|Theme: adds app instance hooks.
	 *
	 * @since 2021-12-15
	 *
	 * @param string ...$args Constructor args.
	 *
	 * @throws U\Fatal_Exception On failure to determine app type.
	 */
	public static function add_instance_hooks( string ...$args ) : void;

	/**
	 * Plugin|Theme: sets app instance, loads app.
	 *
	 * @since 2021-12-15
	 *
	 * @param bool $maybe_setup_hooks Control over hook setup, for use by constructor.
	 *                                Default is `true`. Set to `false` when uninstalling.
	 *
	 * @throws U\Fatal_Exception If missing instance args.
	 * @return WPG\I7e\App App instance for the called class.
	 */
	public static function load( bool $maybe_setup_hooks = true ) : WPG\I7e\App;

	/**
	 * Plugin|Theme: gets app instance.
	 *
	 * @since 2021-12-15
	 *
	 * @throws U\Fatal_Exception If missing instance.
	 * @return WPG\I7e\App App instance for the called class.
	 */
	public static function instance() : WPG\I7e\App;

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
	public function on_activation_base( bool $network_wide = false ) : void;

	/**
	 * Plugin: on `{$this->var_prefix}activation` hook.
	 *
	 * @since 2021-12-15
	 *
	 * @param bool $network_wide True if activated network-wide.
	 *
	 * @note  DO NOT POPULATE. This is for extenders only.
	 */
	public function on_plugin_activation( bool $network_wide ) : void;

	/**
	 * Theme: `after_switch_theme` hook.
	 *
	 * @since 2021-12-15
	 *
	 * @note  DO NOT POPULATE. This is for extenders only.
	 */
	public function on_theme_activation() : void;

	// --- Deactivation Hooks -------------------------------------------------

	/**
	 * Plugin: on `{$this->var_prefix}deactivation` hook.
	 *
	 * @since 2021-12-15
	 *
	 * @param bool $network_wide True if deativated network-wide.
	 */
	public function on_plugin_deactivation_base( bool $network_wide ) : void;

	/**
	 * Plugin: on `{$this->var_prefix}deactivation` hook.
	 *
	 * @since 2021-12-15
	 *
	 * @param bool $network_wide True if deativated network-wide.
	 *
	 * @note  DO NOT POPULATE. This is for extenders only.
	 */
	public function on_plugin_deactivation( bool $network_wide ) : void;

	/**
	 * Theme: on `switch_theme` hook.
	 *
	 * @since 2021-12-15
	 *
	 * @param string    $new_template New template name.
	 * @param \WP_Theme $new_theme    New theme instance.
	 * @param \WP_Theme $old_theme    Old theme instance.
	 */
	public function on_theme_deactivation_base( string $new_template, \WP_Theme $new_theme, \WP_Theme $old_theme ) : void;

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
	public function on_theme_deactivation( string $new_template, \WP_Theme $new_theme, \WP_Theme $old_theme ) : void;

	// --- Uninstall Hook -----------------------------------------------------

	/**
	 * Plugin|Theme: uninstall hooks.
	 *
	 * - Plugin: on `uninstall_{$this->subpath}` hook.
	 * - Theme: on `switch_theme` hook.
	 *
	 * @since 2021-12-15
	 */
	public static function on_uninstall_base() : void;

	/**
	 * Plugin: on `uninstall_{$this->subpath}` hook, via {@see WPG\I7e\App::on_uninstall_base()}.
	 *
	 * @since 2021-12-15
	 *
	 * @param WPG\I7e\Plugin $plugin Plugin instance.
	 *
	 * @note  DO NOT POPULATE. This is for extenders only.
	 */
	public static function on_uninstall_plugin( WPG\I7e\Plugin $plugin ) : void;

	/**
	 * Theme: on `switch_theme` hook, via {@see WPG\I7e\App::on_uninstall_base()}.
	 *
	 * @since 2021-12-15
	 *
	 * @param WPG\I7e\Theme $theme Theme instance.
	 *
	 * @note  DO NOT POPULATE. This is for extenders only.
	 */
	public static function on_uninstall_theme( WPG\I7e\Theme $theme ) : void;

	// --- Plugins Loaded Hook ------------------------------------------------
	// This particular hook is applicable to plugins only — themes load later.

	/**
	 * Plugin: on `plugins_loaded` hook.
	 *
	 * @since 2021-12-15
	 */
	public function on_plugins_loaded_base() : void;

	/**
	 * Plugin: on `plugins_loaded` hook.
	 *
	 * @since 2021-12-15
	 *
	 * @note  DO NOT POPULATE. This is for extenders only.
	 */
	public function on_plugins_loaded() : void;

	// --- After Setup Theme Hook ---------------------------------------------

	/**
	 * Plugin|Theme: on `after_setup_theme` hook.
	 *
	 * @since 2021-12-15
	 */
	public function on_after_setup_theme_base() : void;

	/**
	 * Plugin|Theme: on `after_setup_theme` hook.
	 *
	 * @since 2021-12-15
	 *
	 * @note  DO NOT POPULATE. This is for extenders only.
	 */
	public function on_after_setup_theme() : void;

	// --- Init Hook ----------------------------------------------------------

	/**
	 * Plugin|Theme: on `init` hook.
	 *
	 * @since 2021-12-15
	 */
	public function on_init_base() : void;

	/**
	 * Plugin|Theme: on `init` hook.
	 *
	 * @since 2021-12-15
	 *
	 * @note  DO NOT POPULATE. This is for extenders only.
	 */
	public function on_init() : void;

	// --- Action Utilities ---------------------------------------------------

	/**
	 * Plugin|Theme: {@see do_action()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $hook_name {@see do_action()}.
	 * @param mixed  ...$args   {@see do_action()}.
	 */
	public function do_action( string $hook_name, /* mixed */ ...$args ) : void;

	// --- Filter Utilities ---------------------------------------------------

	/**
	 * Plugin|Theme: {@see apply_filters()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $hook_name {@see apply_filters()}.
	 * @param mixed  ...$args   {@see apply_filters()}.
	 *
	 * @return mixed {@see apply_filters()}.
	 */
	public function apply_filters( string $hook_name, /* mixed */ ...$args ); /* : mixed */

	// --- Option Utilities ---------------------------------------------------

	/**
	 * Plugin|Theme: {@see get_option()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $option  {@see get_option()}.
	 * @param mixed  ...$args {@see get_option()}.
	 *
	 * @return mixed {@see get_option()}.
	 */
	public function get_option( string $option, /* mixed */ ...$args ); /* : mixed */

	/**
	 * Plugin|Theme: {@see add_option()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $option  {@see add_option()}.
	 * @param mixed  ...$args {@see add_option()}.
	 *
	 * @return bool {@see add_option()}.
	 */
	public function add_option( string $option, /* mixed */ ...$args ) : bool;

	/**
	 * Plugin|Theme: {@see update_option()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $option  {@see update_option()}.
	 * @param mixed  ...$args {@see update_option()}.
	 *
	 * @return bool {@see update_option()}.
	 */
	public function update_option( string $option, /* mixed */ ...$args ) : bool;

	/**
	 * Plugin|Theme: {@see delete_option()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $option {@see delete_option()}.
	 *
	 * @return bool {@see delete_option()}.
	 */
	public function delete_option( string $option ) : bool;

	// --- Site Option Utilities ----------------------------------------------

	/**
	 * Plugin|Theme: {@see get_site_option()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $option  {@see get_site_option()}.
	 * @param mixed  ...$args {@see get_site_option()}.
	 *
	 * @return mixed {@see get_site_option()}.
	 */
	public function get_site_option( string $option, /* mixed */ ...$args ); /* : mixed */

	/**
	 * Plugin|Theme: {@see add_site_option()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $option  {@see add_site_option()}.
	 * @param mixed  ...$args {@see add_site_option()}.
	 *
	 * @return bool {@see add_site_option()}.
	 */
	public function add_site_option( string $option, /* mixed */ ...$args ) : bool;

	/**
	 * Plugin|Theme: {@see update_site_option()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $option  {@see update_site_option()}.
	 * @param mixed  ...$args {@see update_site_option()}.
	 *
	 * @return bool {@see update_site_option()}.
	 */
	public function update_site_option( string $option, /* mixed */ ...$args ) : bool;

	/**
	 * Plugin|Theme: {@see delete_site_option()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $option {@see delete_site_option()}.
	 *
	 * @return bool {@see delete_site_option()}.
	 */
	public function delete_site_option( string $option ) : bool;

	// --- Transient Utilities ------------------------------------------------

	/**
	 * Plugin|Theme: {@see get_transient()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $transient {@see get_transient()}.
	 *
	 * @return mixed {@see get_transient()}.
	 */
	public function get_transient( string $transient ); /* : mixed */

	/**
	 * Plugin|Theme: {@see set_transient()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $transient {@see set_transient()}.
	 * @param mixed  ...$args   {@see set_transient()}.
	 *
	 * @return bool {@see set_transient()}.
	 */
	public function set_transient( string $transient, /* mixed */ ...$args ) : bool;

	/**
	 * Plugin|Theme: {@see delete_transient()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $transient {@see delete_transient()}.
	 *
	 * @return bool {@see delete_transient()}.
	 */
	public function delete_transient( string $transient ) : bool;

	// --- Site Transient Utilities -------------------------------------------

	/**
	 * Plugin|Theme: {@see get_site_transient()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $transient {@see get_site_transient()}.
	 *
	 * @return mixed {@see get_site_transient()}.
	 */
	public function get_site_transient( string $transient ); /* : mixed */

	/**
	 * Plugin|Theme: {@see set_site_transient()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $transient {@see set_site_transient()}.
	 * @param mixed  ...$args   {@see set_site_transient()}.
	 *
	 * @return bool {@see set_site_transient()}.
	 */
	public function set_site_transient( string $transient, /* mixed */ ...$args ) : bool;

	/**
	 * Plugin|Theme: {@see delete_site_transient()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $transient {@see delete_site_transient()}.
	 *
	 * @return bool {@see delete_site_transient()}.
	 */
	public function delete_site_transient( string $transient ) : bool;
}
