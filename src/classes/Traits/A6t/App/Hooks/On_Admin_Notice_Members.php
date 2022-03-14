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
trait On_Admin_Notice_Members {
	/**
	 * Plugin|Theme: on `all_admin_notices` hook.
	 *
	 * @since 2021-12-30
	 */
	final public function fw_on_all_admin_notices() : void {
		$update_admin_notices = false; // Initialize.
		$admin_notices        = $this->get_admin_notices();

		foreach ( $admin_notices as $_idx => $_admin_notice ) {
			$_admin_notice->maybe_display();

			if ( $_admin_notice->should_dequeue() ) {
				unset( $admin_notices[ $_idx ] );
				$update_admin_notices = true;
			}
		}
		if ( $update_admin_notices ) {
			$this->update_admin_notices( $admin_notices );
		}
	}

	/**
	 * Plugin|Theme: on `all_admin_notices` hook.
	 *
	 * DO NOT POPULATE. This is for extenders only.
	 *
	 * @since 2021-12-15
	 */
	public function on_all_admin_notices() : void {
		// DO NOT POPULATE. This is for extenders only.
	}

	/**
	 * Plugin|Theme: on `wp_ajax_{$this->var_prefix}admin_notice_dismiss` hook.
	 *
	 * @since 2022-03-11
	 */
	final public function fw_on_wp_ajax_admin_notice_dismiss() : void {
		check_admin_referer( 'wp_ajax_' . $this->var_prefix . 'admin_notice_dismiss' );
		$idx = u\if_string( U\URL::current_post_var( $this->var_prefix . 'admin_notice_dismiss' ), '' );

		if ( $idx && current_user_can( 'exist' ) ) {
			$this->dequeue_admin_notice( $idx );
		}
	}

	/**
	 * Plugin|Theme: on `wp_ajax_{$this->var_prefix}admin_notice_dismiss` hook.
	 *
	 * DO NOT POPULATE. This is for extenders only.
	 *
	 * @since 2022-03-11
	 */
	public function on_wp_ajax_admin_notice_dismiss() : void {
		// DO NOT POPULATE. This is for extenders only.
	}
}
