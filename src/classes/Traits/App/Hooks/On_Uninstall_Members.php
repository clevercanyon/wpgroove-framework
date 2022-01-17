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
namespace WP_Groove\Framework\Traits\App\Hooks;

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
trait On_Uninstall_Members {
	/**
	 * Plugin|Theme: uninstall hooks.
	 *
	 * - Plugin: on `uninstall_{$this->subpath}` hook.
	 * - Theme: on `switch_theme` hook.
	 *
	 * @since 2021-12-15
	 */
	final public static function on_uninstall_base() : void {
		global $wpdb;

		// Load w/o hook setup.

		try { // Fail softly.
			$app = static::load( false );

		} catch ( U\Fatal_Exception $exception ) {
			error_log( 'Failed to load ' . static::app_type() . ' on: `' . current_action() . '`.' );
			return; // Fail software.
		}
		// App-specific uninstall routines.

		if ( $app instanceof WPG\I7e\Theme ) {
			static::on_uninstall_theme( $app );

		} elseif ( $app instanceof WPG\I7e\Plugin ) {
			static::on_uninstall_plugin( $app );
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
	 * Plugin: on `uninstall_{$this->subpath}` hook, via
	 * {@see WPG\Traits\App\Hooks\On_Uninstall_Members::on_uninstall_base()}.
	 *
	 * @since 2021-12-15
	 *
	 * @param WPG\I7e\Plugin $plugin Plugin instance.
	 *
	 * @note  DO NOT POPULATE. This is for extenders only.
	 */
	public static function on_uninstall_plugin( WPG\I7e\Plugin $plugin ) : void {
		// DO NOT POPULATE. This is for extenders only.
	}

	/**
	 * Theme: on `switch_theme` hook, via
	 * {@see WPG\Traits\App\Hooks\On_Uninstall_Members::on_uninstall_base()}.
	 *
	 * @since 2021-12-15
	 *
	 * @param WPG\I7e\Theme $theme Theme instance.
	 *
	 * @note  DO NOT POPULATE. This is for extenders only.
	 */
	public static function on_uninstall_theme( WPG\I7e\Theme $theme ) : void {
		// DO NOT POPULATE. This is for extenders only.
	}
}
