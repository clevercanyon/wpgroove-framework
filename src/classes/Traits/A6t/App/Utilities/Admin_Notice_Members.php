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
 * @see   WPG\I7e\App
 */
trait Admin_Notice_Members {
	/**
	 * Plugin|Theme: on `all_admin_notices` hook.
	 *
	 * @since 2021-12-30
	 */
	final public function on_all_admin_notices_base() : void {
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
	 * Plugin|Theme: gets admin notices.
	 *
	 * @since 2021-12-30
	 *
	 * @return WPG\Admin_Notice[] Admin notices.
	 */
	final public function get_admin_notices() : array {
		$admin_notices = &$this->ins_cache( __FUNCTION__ );

		if ( null !== $admin_notices ) {
			return $admin_notices;
		}
		$admin_notices           = []; // Initialize.
		$transient_admin_notices = u\if_array( $this->get_site_transient( 'admin_notices' ), [] );

		foreach ( $transient_admin_notices as $_idx => $_admin_notice ) {
			$admin_notices[ $_idx ] = new WPG\Admin_Notice( $this, $_admin_notice );
		}
		return $admin_notices;
	}

	/**
	 * Plugin|Theme: updates admin notices.
	 *
	 * @since 2021-12-30
	 *
	 * @param WPG\Admin_Notice[] $admin_notices New admin notices.
	 *
	 * @return bool True on success.
	 */
	final public function update_admin_notices( array $admin_notices ) : bool {
		$transient_admin_notices = []; // Initialize.

		foreach ( $admin_notices as $_idx => $_admin_notice ) {
			$transient_admin_notices[ $_idx ] = $_admin_notice->props( 'own:public...protected', true );
		}
		$this->ins_cache( 'get_admin_notices', null );

		return $this->set_site_transient( 'admin_notices', $transient_admin_notices, U\Time::DAY_IN_SECONDS * 90 );
	}

	/**
	 * Plugin|Theme: gets admin notice.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $idx Admin notice IDx.
	 *
	 * @return WPG\Admin_Notice|null Notice; else `null` on failure.
	 */
	final public function get_admin_notice( string $idx ) /* : WPG\Admin_Notice|null */ : ?WPG\Admin_Notice {
		return $this->get_admin_notices()[ $idx ] ?? null;
	}

	/**
	 * Plugin|Theme: updates admin notice.
	 *
	 * @since 2021-12-30
	 *
	 * @param WPG\Admin_Notice $admin_notice New admin notice.
	 *
	 * @return bool True on success.
	 */
	final public function update_admin_notice( WPG\Admin_Notice $admin_notice ) : bool {
		$admin_notices                       = $this->get_admin_notices();
		$admin_notices[ $admin_notice->idx ] = $admin_notice;

		return $this->update_admin_notices( $admin_notices );
	}

	/**
	 * Plugin|Theme: enqueues admin notice.
	 *
	 * @since 2021-12-30
	 *
	 * @param WPG\Admin_Notice|array|string $admin_notice Admin notice; props; or markup.
	 *
	 * @return bool True on success.
	 */
	final public function enqueue_admin_notice( /* WPG\Admin_Notice|array|string */ $admin_notice ) : bool {
		assert( $admin_notice instanceof WPG\Admin_Notice || is_array( $admin_notice ) || is_string( $admin_notice ) );

		if ( ! $admin_notice instanceof WPG\Admin_Notice ) {
			if ( is_string( $admin_notice ) ) {
				$admin_notice = [ 'markup' => $admin_notice ];
			}
			$admin_notice = new WPG\Admin_Notice( $this, $admin_notice );
		}
		$admin_notices                       = $this->get_admin_notices();
		$admin_notices[ $admin_notice->idx ] = $admin_notice;

		return $this->update_admin_notices( $admin_notices );
	}

	/**
	 * Plugin|Theme: dequeues admin notice.
	 *
	 * @since 2021-12-30
	 *
	 * @param WPG\Admin_Notice|string $admin_notice Admin notice; or IDx.
	 *
	 * @return bool True on success.
	 */
	final public function dequeue_admin_notice( /* WPG\Admin_Notice|string */ $admin_notice ) : bool {
		assert( $admin_notice instanceof WPG\Admin_Notice || is_string( $admin_notice ) );

		if ( ! $admin_notice instanceof WPG\Admin_Notice ) {
			if ( ! $admin_notice = $this->get_admin_notice( $admin_notice ) ) {
				return false; // Missing.
			}
		}
		$admin_notices = $this->get_admin_notices();
		unset( $admin_notices[ $admin_notice->idx ] );

		return $this->update_admin_notices( $admin_notices );
	}
}
