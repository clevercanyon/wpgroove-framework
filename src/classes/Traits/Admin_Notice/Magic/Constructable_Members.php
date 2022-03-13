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
namespace WP_Groove\Framework\Traits\Admin_Notice\Magic;

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
 * @see   WPG\Admin_Notice
 */
trait Constructable_Members {
	/**
	 * Constructor.
	 *
	 * @param WPG\A6t\App $app   App.
	 * @param array       $props Props.
	 *
	 * Props array:
	 *
	 * string  $idx            IDx. Default is a new UUIDv4.
	 *
	 * int     $blog_id        Blog ID. Default is `0` (all blogs).
	 * int     $user_id        User ID. Default is `0` (all users).
	 *
	 * string  $type           Type. Default is an `info` notice type.
	 *                         One of: `success`, `warning`, `error`, `info`.
	 *
	 * bool    $persistent     Default is `false`. If `true`, the notice will persist until dismissed by IDx.
	 *
	 *                           * A persistent notice should always get a custom IDx to avoid duplication;
	 *                             i.e., in case code attempts to enqueue the same notice multiple times.
	 *                             Also, so it's easier to dismiss the notice, when appropriate.
	 *
	 * bool    $dismissable    Default is `true`. A `$persistent` notice should typically be dismissable,
	 *                         but it doesn't have to be. For example, a `$persistent` notice that's
	 *                         not `$dismissable` will persist until an app dismisses it in code.
	 *
	 * string  $markup         Markup. Default is an empty string. Notice will not display.
	 *                         Markup is absolutely required, else nothing to display.
	 *
	 * array   $conditions     Conditions. An array of callables, or {@see U\Code_Stream_Closure} instances.
	 *                         There must be at least one condition. If empty, default is: `[ 'is_super_admin' ]`.
	 *
	 * int     $generated      Time generated. Set as a UTC timestamp.
	 *                         Default is current time (i.e., newly generated).
	 *
	 * int     $displayed      Time displayed. Set as a UTC timestamp.
	 *                         Default is `0` (not displayed yet).
	 *
	 * int     $dismissed      Time dismissed. Set as a UTC timestamp.
	 *                         Default is `0` (not dismissed yet).
	 *
	 * @noinspection PhpMultipleClassDeclarationsInspection
	 */
	public function __construct( WPG\A6t\App $app, array $props ) {
		parent::__construct();
		$this->app = $app;

		$this->idx = u\iff_string( $props[ 'idx' ] )
			?: U\Crypto::uuid_v4(); // Auto-generate.

		$this->blog_id = u\iff_int( $props[ 'blog_id' ], 0 );
		$this->user_id = u\iff_int( $props[ 'user_id' ], 0 );

		$this->type        = u\iff_string( $props[ 'type' ] ) ?: 'info';
		$this->persistent  = u\iff_bool( $props[ 'persistent' ], false );
		$this->dismissable = u\iff_bool( $props[ 'dismissable' ], true );

		$this->markup     = trim( u\iff_string( $props[ 'markup' ], '' ) );
		$this->conditions = u\iff_array( $props[ 'conditions' ] ) ?: [ 'is_super_admin' ];

		$this->generated = u\iff_int( $props[ 'generated' ] ) ?: U\Time::utc();
		$this->displayed = u\iff_int( $props[ 'displayed' ], 0 );
		$this->dismissed = u\iff_int( $props[ 'dismissed' ], 0 );
	}
}
