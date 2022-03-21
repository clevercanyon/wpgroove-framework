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
namespace WP_Groove\Framework\Traits\Admin_Notice\Properties;

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
trait Property_Members {
	/**
	 * Props `own:public...protected` are saved in transient data.
	 * Please be very careful before updating properties.
	 *
	 * @see WPG\A6t\App::update_admin_notices()
	 */

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
}
