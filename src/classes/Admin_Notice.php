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
namespace WP_Groove\Framework;

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
 * Admin notice.
 *
 * @since 2021-12-15
 */
final class Admin_Notice extends U\A6t\Base {
	/**
	 * Traits.
	 *
	 * @since 2022-01-28
	 */
	use U\Traits\A6t\Base\Magic\Readable_Members;

	/**
	 * Plugin|Theme.
	 *
	 * @since 2022-01-28
	 */
	private WPG\I7e\App $app;

	/**
	 * Notice IDx.
	 *
	 * @since 2022-01-28
	 */
	protected string $idx;

	/**
	 * For blog ID.
	 *
	 * @since 2022-01-28
	 */
	protected int $blog_id;

	/**
	 * For user ID.
	 *
	 * @since 2022-01-28
	 */
	protected int $user_id;

	/**
	 * Type.
	 *
	 * @since 2022-01-28
	 */
	protected string $type;

	/**
	 * Persistent?
	 *
	 * @since 2022-01-28
	 */
	protected bool $persistent;

	/**
	 * Dismissable?
	 *
	 * @since 2022-01-28
	 */
	protected bool $dismissable;

	/**
	 * Markup.
	 *
	 * @since 2022-01-28
	 */
	protected string $markup;

	/**
	 * Conditions.
	 *
	 * @since 2022-01-28
	 */
	protected array $conditions;

	/**
	 * Time generated.
	 *
	 * @since 2022-01-28
	 */
	protected int $generated;

	/**
	 * Time displayed.
	 *
	 * @since 2022-01-28
	 */
	protected int $displayed;

	/**
	 * Time dismissed.
	 *
	 * @since 2022-01-28
	 */
	protected int $dismissed;

	/**
	 * Constructor.
	 *
	 * @param WPG\I7e\App $app   App.
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
	 */
	public function __construct( WPG\I7e\App $app, array $props ) {
		parent::__construct();
		$this->app = $app;

		$this->idx = u\iff_string( $props[ 'idx' ] )
			?: U\Crypto::uuid_v4();

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

	/**
	 * Maybe display.
	 *
	 * @since 2022-01-28
	 */
	public function maybe_display() : void {
		if ( $this->should_display() ) {
			$this->display();
		}
	}

	/**
	 * Should enqueue?
	 *
	 * @since 2022-01-28
	 *
	 * @return bool True or false.
	 */
	public function should_enqueue() : bool {
		if ( $this->should_dequeue() ) {
			return false; // Obviously.
		}
		$admin_notices = $this->app->get_admin_notices();
		return ! isset( $admin_notices[ $this->idx ] );
	}

	/**
	 * Should dequeue?
	 *
	 * @since 2022-01-28
	 *
	 * @return bool True or false.
	 */
	public function should_dequeue() : bool {
		if ( ! $this->idx || ! $this->type || ! $this->markup || ! $this->conditions || ! $this->generated ) {
			return true; // Data is missing.
		}
		if ( $this->displayed && ( ! $this->persistent || $this->dismissed ) ) {
			return true; // Displayed already, or dismissed already.
		}
		if ( $this->generated < U\Time::utc( '-90 days' ) ) {
			return true; // Don't hold forever.
		}
		foreach ( $this->conditions as $_condition ) {
			if ( $_condition instanceof U\Code_Stream_Closure ) {
				$_condition = $_condition->get_closure();
			}
			if ( ! is_callable( $_condition ) ) {
				return true; // Invalid condition.
			}
		}
		return false;
	}

	/**
	 * Displays notice.
	 *
	 * @since 2022-01-28
	 */
	protected function display() : void {
		$this->displayed = U\Time::utc();
		?>
		<div class="<?php echo esc_attr( $this->classes() ); ?>">
			<?php echo $this->markup(); // phpcs:ignore -- output ok. ?>
		</div>
		<?php
	}

	/**
	 * Should display?
	 *
	 * @since 2022-01-28
	 *
	 * @return bool True or false.
	 */
	protected function should_display() : bool {
		if ( ! $this->idx || ! $this->type || ! $this->markup || ! $this->conditions || ! $this->generated ) {
			return false; // Data is missing.
		}
		if ( $this->displayed && ( ! $this->persistent || $this->dismissed ) ) {
			return false; // Displayed already, or dismissed already.
		}
		if ( $this->blog_id && get_current_blog_id() !== $this->blog_id ) {
			return false; // Not applicable.
		}
		if ( $this->user_id && get_current_user_id() !== $this->user_id ) {
			return false; // Not applicable.
		}
		foreach ( $this->conditions as $_condition ) {
			if ( $_condition instanceof U\Code_Stream_Closure ) {
				if ( ! $_condition->call( $this ) ) {
					return false; // Not applicable.
				}
			} elseif ( ! is_callable( $_condition ) || ! $_condition() ) {
				return false; // Not applicable.
			}
		}
		return true;
	}

	/**
	 * Outputs classes.
	 *
	 * @since 2022-01-28
	 */
	protected function classes() : string {
		$classes = [ 'notice' ];

		$classes[] = $this->app->brand->slug_prefix . 'notice';
		$classes[] = $this->app->slug_prefix . 'notice';

		switch ( $this->type ) {
			case 'success':
				$classes[] = 'notice-success';
				break;

			case 'warning':
				$classes[] = 'notice-warning';
				break;

			case 'error':
				$classes[] = 'notice-error';
				break;

			case 'info':
			default: // Default type.
				$classes[] = 'notice-info';
		}
		if ( $this->dismissable ) {
			$classes[] = 'is-dismissible';
		}
		return implode( ' ', $classes );
	}

	/**
	 * Gets prepared markup.
	 *
	 * @since 2022-02-12
	 *
	 * @return string Prepared markup.
	 */
	protected function markup() : string {
		$markup = $this->markup;

		$app = // Identify app.
			'<div class="x-app">' .
			esc_html( $this->app->name ) .
			'</div>';

		return $app . "\n\n"
			. U\HTML::markup( $markup );
	}
}
