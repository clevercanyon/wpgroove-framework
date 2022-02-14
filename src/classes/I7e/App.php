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
	 * @return WPG\I7e\App App instance for the called class.
	 *
	 * @throws U\Fatal_Exception If missing instance args.
	 */
	public static function load( bool $maybe_setup_hooks = true ) : WPG\I7e\App;

	/**
	 * Plugin|Theme: gets app instance.
	 *
	 * @since 2021-12-15
	 *
	 * @return WPG\I7e\App App instance for the called class.
	 *
	 * @throws U\Fatal_Exception If missing instance.
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
	 * DO NOT POPULATE. This is for extenders only.
	 *
	 * @since 2021-12-15
	 *
	 * @param bool $network_wide True if activated network-wide.
	 */
	public function on_plugin_activation( bool $network_wide ) : void;

	/**
	 * Theme: `after_switch_theme` hook.
	 *
	 * DO NOT POPULATE. This is for extenders only.
	 *
	 * @since 2021-12-15
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
	 * DO NOT POPULATE. This is for extenders only.
	 *
	 * @since 2021-12-15
	 *
	 * @param bool $network_wide True if deativated network-wide.
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
	 * DO NOT POPULATE. This is for extenders only.
	 *
	 * @since 2021-12-15
	 *
	 * @param string    $new_template New template name.
	 * @param \WP_Theme $new_theme    New theme instance.
	 * @param \WP_Theme $old_theme    Old theme instance.
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
	 * DO NOT POPULATE. This is for extenders only.
	 *
	 * @since 2021-12-15
	 *
	 * @param WPG\I7e\Plugin $plugin Plugin instance.
	 */
	public static function on_uninstall_plugin( WPG\I7e\Plugin $plugin ) : void;

	/**
	 * Theme: on `switch_theme` hook, via {@see WPG\I7e\App::on_uninstall_base()}.
	 *
	 * DO NOT POPULATE. This is for extenders only.
	 *
	 * @since 2021-12-15
	 *
	 * @param WPG\I7e\Theme $theme Theme instance.
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
	 * DO NOT POPULATE. This is for extenders only.
	 *
	 * @since 2021-12-15
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
	 * DO NOT POPULATE. This is for extenders only.
	 *
	 * @since 2021-12-15
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
	 * DO NOT POPULATE. This is for extenders only.
	 *
	 * @since 2021-12-15
	 */
	public function on_init() : void;

	// --- Action Utilities ---------------------------------------------------

	/**
	 * Plugin|Theme: {@see do_action()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $hook_name {@see do_action()} for further details.
	 *                          This must be auto-prefixed using app's `var_prefix`.
	 *
	 * @param mixed  ...$args   {@see do_action()} for further details.
	 */
	public function do_action( string $hook_name, /* mixed */ ...$args ) : void;

	// --- Filter Utilities ---------------------------------------------------

	/**
	 * Plugin|Theme: {@see apply_filters()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $hook_name {@see apply_filters()} for further details.
	 *                          This must be auto-prefixed using app's `var_prefix`.
	 *
	 * @param mixed  ...$args   {@see apply_filters()} for further details.
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
	 * @param string $key     {@see get_option()} for further details.
	 *                        This must be auto-prefixed using app's `var_prefix`.
	 *
	 * @param mixed  $default Optional default return value, which by default must be `null`.
	 *
	 *                        * Unlike {@see get_option()}, the default here must be `null` instead of `false`.
	 *                          Made possible by {@see U\Str::serialize()}, {@see U\Str::maybe_unserialize()}.
	 *
	 * @return mixed {@see get_option()}, {@see U\Str::maybe_unserialize()}.
	 *
	 *               * Unlike {@see get_option()}, this must return value with its original data type.
	 *                 Made possible by {@see U\Str::serialize()}, {@see U\Str::maybe_unserialize()}.
	 *
	 *               * Unlike {@see get_option()}, this must return `null` on miss or failure.
	 *                 Made possible by {@see U\Str::serialize()}, {@see U\Str::maybe_unserialize()}.
	 */
	public function get_option( string $key, /* mixed */ $default = null ); /* : mixed */

	/**
	 * Plugin|Theme: {@see add_option()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $key      {@see add_option()} for further details.
	 *                         This must be auto-prefixed using app's `var_prefix`.
	 *
	 * @param mixed  $value    Option value to store. Goes into DB table.
	 *
	 *                         * Passing `null` explicitly must {@see delete_option()}.
	 *
	 *                         * Unlike {@see add_option()}, this must store values w/ their original data type.
	 *                           Made possible by {@see U\Str::serialize()}, {@see U\Str::maybe_unserialize()}.
	 *
	 * @param bool   $autoload Optional. Default must be `true` (autoload).
	 *
	 *                         * Unlike {@see add_option()}, this must be passed as third argument, not fourth.
	 *                           The reason is because the third argument in {@see add_option()} is deprecated anyway.
	 *
	 *                         * Unlike {@see add_option()}, this must be passed as a boolean value, not as `yes|no` string.
	 *                           The reason is because it's silly to pass `yes|no`; i.e., provides no added clarity.
	 *                           In PHP 8+ this will make more sense when it can be passed as a named argument.
	 *
	 * @return bool {@see add_option()}.
	 */
	public function add_option( string $key, /* mixed */ $value, bool $autoload = true ) : bool;

	/**
	 * Plugin|Theme: {@see update_option()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string    $key      {@see update_option()} for further details.
	 *                            This must be auto-prefixed using app's `var_prefix`.
	 *
	 * @param mixed     $value    Option value to store. Goes into DB table.
	 *
	 *                            * Passing `null` explicitly must {@see delete_option()}.
	 *
	 *                            * Unlike {@see update_option()}, this must store values w/ their original data type.
	 *                              Made possible by {@see U\Str::serialize()}, {@see U\Str::maybe_unserialize()}.
	 *
	 * @param bool|null $autoload Optional. Default must be `null`, just like {@see update_option()}.
	 *
	 *                            * Just like {@see update_option()}, if the option does not exist already,
	 *                              then `null` must imply the default behavior, which is `true` (autoload).
	 *
	 *                            * Just like {@see update_option()}, autoload must only be modified for existing options
	 *                              whenever the updated `$value` is actually changing from what it currently is in the DB.
	 *
	 *                            * Unlike {@see update_option()}, this must be passed as a boolean value, not as `yes|no` string.
	 *                              The reason is because it's silly to pass `yes|no`; i.e., provides no added clarity.
	 *                              In PHP 8+ this will make more sense when it can be passed as a named argument.
	 *
	 * @return bool {@see update_option()}.
	 */
	public function update_option( string $key, /* mixed */ $value, /* bool|null */ ?bool $autoload = null ) : bool;

	/**
	 * Plugin|Theme: {@see delete_option()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $key {@see delete_option()} for further details.
	 *                    This must be auto-prefixed using app's `var_prefix`.
	 *
	 * @return bool {@see delete_option()}.
	 */
	public function delete_option( string $key ) : bool;

	// --- Site Option Utilities ----------------------------------------------

	/**
	 * Plugin|Theme: {@see get_site_option()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $key     {@see get_site_option()} for further details.
	 *                        This must be auto-prefixed using app's `var_prefix`.
	 *
	 * @param mixed  $default Optional default return value, which by default must be `null`.
	 *
	 *                        * Unlike {@see get_site_option()}, the default here must be `null` instead of `false`.
	 *                          Made possible by {@see U\Str::serialize()}, {@see U\Str::maybe_unserialize()}.
	 *
	 * @return mixed {@see get_site_option()}, {@see U\Str::maybe_unserialize()}.
	 *
	 *               * Unlike {@see get_site_option()}, this must return value with its original data type.
	 *                 Made possible by {@see U\Str::serialize()}, {@see U\Str::maybe_unserialize()}.
	 *
	 *               * Unlike {@see get_site_option()}, this must return `null` on miss or failure.
	 *                 Made possible by {@see U\Str::serialize()}, {@see U\Str::maybe_unserialize()}.
	 */
	public function get_site_option( string $key, /* mixed */ $default = null ); /* : mixed */

	/**
	 * Plugin|Theme: {@see add_site_option()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $key   {@see add_site_option()} for further details.
	 *                      This must be auto-prefixed using app's `var_prefix`.
	 *
	 * @param mixed  $value Option value to store. Goes into DB table.
	 *
	 *                      * Passing `null` explicitly must {@see delete_site_option()}.
	 *
	 *                      * Unlike {@see add_site_option()}, this must store values w/ their original data type.
	 *                        Made possible by {@see U\Str::serialize()}, {@see U\Str::maybe_unserialize()}.
	 *
	 * @return bool {@see add_site_option()}.
	 */
	public function add_site_option( string $key, /* mixed */ $value ) : bool;

	/**
	 * Plugin|Theme: {@see update_site_option()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $key   {@see update_site_option()} for further details.
	 *                      This must be auto-prefixed using app's `var_prefix`.
	 *
	 * @param mixed  $value Option value to store. Goes into DB table.
	 *
	 *                      * Passing `null` explicitly must {@see delete_site_option()}.
	 *
	 *                      * Unlike {@see update_site_option()}, this must store values w/ their original data type.
	 *                        Made possible by {@see U\Str::serialize()}, {@see U\Str::maybe_unserialize()}.
	 *
	 * @return bool {@see update_site_option()}.
	 */
	public function update_site_option( string $key, /* mixed */ $value ) : bool;

	/**
	 * Plugin|Theme: {@see delete_site_option()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $key {@see delete_site_option()} for further details.
	 *                    This must be auto-prefixed using app's `var_prefix`.
	 *
	 * @return bool {@see delete_site_option()}.
	 */
	public function delete_site_option( string $key ) : bool;

	// --- Transient Utilities ------------------------------------------------

	/**
	 * Plugin|Theme: {@see get_transient()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param mixed $key_parts A single key part or a bundle containing multiple key parts.
	 *                         These are key parts used to formulate an actual transient key identifier.
	 *                         Whatever is passed, it must be serialized & hashed; i.e., converted to a string key.
	 *                         The final transient key identifier must be auto-prefixed using app's `var_prefix`.
	 *
	 * @return mixed {@see get_transient()}, {@see U\Str::maybe_unserialize()}.
	 *
	 *               * Unlike {@see get_transient()}, this must return values w/ their original data type.
	 *                 Made possible by {@see U\Str::serialize()}, {@see U\Str::maybe_unserialize()}.
	 *
	 *               * Unlike {@see get_transient()}, this must return `null` on miss or failure.
	 *                 Made possible by {@see U\Str::serialize()}, {@see U\Str::maybe_unserialize()}.
	 */
	public function get_transient( /* mixed */ $key_parts ); /* : mixed */

	/**
	 * Plugin|Theme: {@see set_transient()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param mixed $key_parts  A single key part or a bundle containing multiple key parts.
	 *                          These are key parts used to formulate an actual transient key identifier.
	 *                          Whatever is passed, it must be serialized & hashed; i.e., converted to a string key.
	 *                          The final transient key identifier must be auto-prefixed using app's `var_prefix`.
	 *
	 * @param mixed $value      Transient value to store. Goes into DB or potentially an in-memory cache.
	 *
	 *                          * Passing `null` explicitly must {@see delete_transient()}.
	 *
	 *                          * Unlike {@see set_transient()}, this must store values w/ their original data type.
	 *                            Made possible by {@see U\Str::serialize()}, {@see U\Str::maybe_unserialize()}.
	 *
	 * @param int   $expires_in Default should be {@see U\Time::HOUR_IN_SECONDS} (one hour).
	 *                          Should be `> 0`, else it should revert to default {@see U\Time::HOUR_IN_SECONDS}.
	 *
	 * @return bool {@see set_transient()}.
	 */
	public function set_transient( /* mixed */ $key_parts, /* mixed */ $value, int $expires_in = U\Time::HOUR_IN_SECONDS ) : bool;

	/**
	 * Plugin|Theme: {@see delete_transient()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param mixed $key_parts A single key part or a bundle containing multiple key parts.
	 *                         These are key parts used to formulate an actual transient key identifier.
	 *                         Whatever is passed, it must be serialized & hashed; i.e., converted to a string key.
	 *                         The final transient key identifier must be auto-prefixed using app's `var_prefix`.
	 *
	 * @return bool {@see delete_transient()}.
	 */
	public function delete_transient( /* mixed */ $key_parts ) : bool;

	// --- Site Transient Utilities -------------------------------------------

	/**
	 * Plugin|Theme: {@see get_site_transient()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param mixed $key_parts A single key part or a bundle containing multiple key parts.
	 *                         These are key parts used to formulate an actual transient key identifier.
	 *                         Whatever is passed, it must be serialized & hashed; i.e., converted to a string key.
	 *                         The final transient key identifier must be auto-prefixed using app's `var_prefix`.
	 *
	 * @return mixed {@see get_site_transient()}, {@see U\Str::maybe_unserialize()}.
	 *
	 *               * Unlike {@see get_site_transient()}, this must return values w/ their original data type.
	 *                 Made possible by {@see U\Str::serialize()}, {@see U\Str::maybe_unserialize()}.
	 *
	 *               * Unlike {@see get_site_transient()}, this must return `null` on miss or failure.
	 *                 Made possible by {@see U\Str::serialize()}, {@see U\Str::maybe_unserialize()}.
	 */
	public function get_site_transient( /* mixed */ $key_parts ); /* : mixed */

	/**
	 * Plugin|Theme: {@see set_site_transient()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param mixed $key_parts  A single key part or a bundle containing multiple key parts.
	 *                          These are key parts used to formulate an actual transient key identifier.
	 *                          Whatever is passed, it must be serialized & hashed; i.e., converted to a string key.
	 *                          The final transient key identifier must be auto-prefixed using app's `var_prefix`.
	 *
	 * @param mixed $value      Transient value to store. Goes into DB or potentially an in-memory cache.
	 *
	 *                          * Passing `null` explicitly must {@see delete_site_transient()}.
	 *
	 *                          * Unlike {@see set_site_transient()}, this must store values w/ their original data type.
	 *                            Made possible by {@see U\Str::serialize()}, {@see U\Str::maybe_unserialize()}.
	 *
	 * @param int   $expires_in Default should be {@see U\Time::HOUR_IN_SECONDS} (one hour).
	 *                          Should be `> 0`, else it should revert to default {@see U\Time::HOUR_IN_SECONDS}.
	 *
	 * @return bool {@see set_site_transient()}.
	 */
	public function set_site_transient( /* mixed */ $key_parts, /* mixed */ $value, int $expires_in = U\Time::HOUR_IN_SECONDS ) : bool;

	/**
	 * Plugin|Theme: {@see delete_site_transient()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param mixed $key_parts A single key part or a bundle containing multiple key parts.
	 *                         These are key parts used to formulate an actual transient key identifier.
	 *                         Whatever is passed, it must be serialized & hashed; i.e., converted to a string key.
	 *                         The final transient key identifier must be auto-prefixed using app's `var_prefix`.
	 *
	 * @return bool {@see delete_site_transient()}.
	 */
	public function delete_site_transient( /* mixed */ $key_parts ) : bool;

	// --- Notice Utilities ---------------------------------------------------

	/**
	 * Plugin|Theme: on `all_admin_notices` hook.
	 *
	 * @since 2021-12-30
	 */
	public function on_all_admin_notices_base() : void;

	/**
	 * Plugin|Theme: on `all_admin_notices` hook.
	 *
	 * DO NOT POPULATE. This is for extenders only.
	 *
	 * @since 2021-12-15
	 */
	public function on_all_admin_notices() : void;

	/**
	 * Plugin|Theme: gets admin notices.
	 *
	 * @since 2021-12-30
	 *
	 * @return WPG\Admin_Notice[] Admin notices.
	 */
	public function get_admin_notices() : array;

	/**
	 * Plugin|Theme: updates admin notices.
	 *
	 * @since 2021-12-30
	 *
	 * @param WPG\Admin_Notice[] $admin_notices New admin notices.
	 *
	 * @return bool True on success.
	 */
	public function update_admin_notices( array $admin_notices ) : bool;

	/**
	 * Plugin|Theme: gets admin notice.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $idx Admin notice IDx.
	 *
	 * @return WPG\Admin_Notice|null Notice; else `null` on failure.
	 */
	public function get_admin_notice( string $idx ) /* : WPG\Admin_Notice|null */ : ?WPG\Admin_Notice;

	/**
	 * Plugin|Theme: updates admin notice.
	 *
	 * @since 2021-12-30
	 *
	 * @param WPG\Admin_Notice $admin_notice New admin notice.
	 *
	 * @return bool True on success.
	 */
	public function update_admin_notice( WPG\Admin_Notice $admin_notice ) : bool;

	/**
	 * Plugin|Theme: enqueues admin notice.
	 *
	 * @since 2021-12-30
	 *
	 * @param WPG\Admin_Notice|array|string $admin_notice Admin notice; props; or markup.
	 *
	 * @return bool True on success.
	 */
	public function enqueue_admin_notice( /* WPG\Admin_Notice|array|string */ $admin_notice ) : bool;

	/**
	 * Plugin|Theme: dequeues admin notice.
	 *
	 * @since 2021-12-30
	 *
	 * @param WPG\Admin_Notice|string $admin_notice Admin notice; or IDx.
	 *
	 * @return bool True on success.
	 */
	public function dequeue_admin_notice( /* WPG\Admin_Notice|string */ $admin_notice ) : bool;
}
