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
namespace WP_Groove\Framework\Traits\A6t\App\Hooks;

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
trait On_Uninstall_Members {
	/**
	 * Plugin|Theme: uninstall hooks.
	 *
	 * - Plugin: on `uninstall_{$this->subpath}` hook.
	 *
	 * - Theme:  on `{$this->var_prefix}deactivation` hook via `switch_theme`.
	 *           Only when option `uninstall_on_deactivation` is `true`.
	 *
	 * @since 2021-12-15
	 */
	final public static function fw_on_uninstall() : void {
		global $wpdb;

		try { // Load w/o hook setup.
			$app = static::load( false );

		} catch ( U\Fatal_Exception $exception ) {
			error_log( 'Failed to load ' . static::app_type() . ' on: `' . current_action() . '`.' );
			return; // Fail softly on uninstallation.
		}
		// App uninstall routines.

		static::on_uninstall( $app );

		// Framework uninstall routines.

		$meta_key_tables_columns = [
			'sitemeta'    => 'meta_key',
			'usermeta'    => 'meta_key',
			'postmeta'    => 'meta_key',
			'termmeta'    => 'meta_key',
			'commentmeta' => 'meta_key',
			'options'     => 'option_name',
		];
		$meta_keys_like          = [
			$wpdb->esc_like( $app->var_prefix ) . '%',
			$wpdb->esc_like( '_' . $app->var_prefix ) . '%',

			$wpdb->esc_like( '_transient_' . $app->var_prefix ) . '%',
			$wpdb->esc_like( '_transient__' . $app->var_prefix ) . '%',

			$wpdb->esc_like( '_transient_timeout_' . $app->var_prefix ) . '%',
			$wpdb->esc_like( '_transient_timeout__' . $app->var_prefix ) . '%',

			$wpdb->esc_like( '_site_transient_' . $app->var_prefix ) . '%',
			$wpdb->esc_like( '_site_transient__' . $app->var_prefix ) . '%',

			$wpdb->esc_like( '_site_transient_timeout_' . $app->var_prefix ) . '%',
			$wpdb->esc_like( '_site_transient_timeout__' . $app->var_prefix ) . '%',
		];
		foreach ( $meta_key_tables_columns as $_meta_key_table => $_meta_key_column ) {
			if ( in_array( $_meta_key_table, [ 'sitemeta' ], true ) && ! is_multisite() ) {
				continue; // ^ These keys are multisite-only.
			}
			if ( ! isset( $wpdb->{$_meta_key_table} ) ) {
				error_log( 'Missing `$wpdb->' . $_meta_key_table . '` while uninstalling ' . static::app_type() . ': `' . $app->slug . '`.' );
				continue; // Not possible.
			}
			foreach ( $meta_keys_like as $_meta_key_like ) {
				if ( false === $wpdb->query( $wpdb->prepare( 'DELETE FROM `' . esc_sql( $wpdb->{$_meta_key_table} ) . '` WHERE `' . esc_sql( $_meta_key_column ) . '` LIKE %s', $_meta_key_like ) ) ) { // phpcs:ignore -- query ok.
					error_log( '`$wpdb->' . $_meta_key_table . '` query failure on column `' . $_meta_key_column . '` while uninstalling ' . static::app_type() . ': `' . $app->slug . '`.' );
				}
			}
		}
	}

	/**
	 * Plugin|Theme: uninstall hooks.
	 *
	 * - Plugin: on `uninstall_{$this->subpath}` hook.
	 *
	 * - Theme:  on `{$this->var_prefix}deactivation` hook via `switch_theme`.
	 *           Only when option `uninstall_on_deactivation` is `true`.
	 *
	 * DO NOT POPULATE. This is for extenders only.
	 *
	 * @since 2021-12-15
	 *
	 * @param WPG\A6t\App $app App (i.e., plugin|theme) instance.
	 */
	public static function on_uninstall( WPG\A6t\App $app ) : void {
		// DO NOT POPULATE. This is for extenders only.
	}
}
