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
namespace WP_Groove\Framework\Traits\A6t\App;

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

/**
 * Framework.
 *
 * @since 2021-12-15
 */

// </editor-fold>

/**
 * Class members.
 *
 * @since 2021-12-15
 *
 * @see   WPG\A6t\App
 */
trait Members {
	/**
	 * Core finals.
	 *
	 * @since 2021-12-28
	 */
	use U\Traits\A6t\Base\Magic\Finals\Destructable_Members;
	use U\Traits\A6t\Base\Magic\Finals\Cloneable_Members;

	use U\Traits\A6t\Base\Magic\Finals\Readable_Members;
	use U\Traits\A6t\Base\Magic\Finals\Unwritable_Members;

	use U\Traits\A6t\Base\Magic\Finals\Uncallable_Members;
	use U\Traits\A6t\Base\Magic\Finals\Uninvokable_Members;

	use U\Traits\A6t\Base\Magic\Finals\Debuggable_Members;
	use U\Traits\A6t\Base\Magic\Finals\Stringable_Members;

	use U\Traits\A6t\Base\Magic\Finals\Unserializable_Members;
	use U\Traits\A6t\Base\I7e\Finals\JsonSerializable_Members;

	use U\Traits\A6t\Stc_Base\Magic\Finals\Uncallable_Members;
	use U\Traits\A6t\Stc_Base\Magic\Finals\Unimportable_Members;

	/**
	 * App traits.
	 *
	 * @since 2021-12-28
	 */
	use WPG\Traits\A6t\App\Magic\Constructable_Members;
	use WPG\Traits\A6t\App\Utilities\Instance_Members;
	use WPG\Traits\A6t\App\Utilities\Property_Members;

	use WPG\Traits\A6t\App\Hooks\On_Activation_Members;
	use WPG\Traits\A6t\App\Hooks\On_Deactivation_Members;
	use WPG\Traits\A6t\App\Hooks\On_Uninstall_Members;

	use WPG\Traits\A6t\App\Hooks\On_Plugins_Loaded_Members;
	use WPG\Traits\A6t\App\Hooks\On_After_Setup_Theme_Members;

	use WPG\Traits\A6t\App\Hooks\On_Init_Members;
	use WPG\Traits\A6t\App\Hooks\On_REST_API_Init_Members;

	use WPG\Traits\A6t\App\Utilities\Multisite_Members;

	use WPG\Traits\A6t\App\Utilities\Option_Members;
	use WPG\Traits\A6t\App\Utilities\Site_Option_Members;

	use WPG\Traits\A6t\App\Utilities\Transient_Members;
	use WPG\Traits\A6t\App\Utilities\Site_Transient_Members;

	use WPG\Traits\A6t\App\Utilities\Action_Members;
	use WPG\Traits\A6t\App\Utilities\Filter_Members;

	use WPG\Traits\A6t\App\Hooks\On_Admin_Init_Members;
	use WPG\Traits\A6t\App\Hooks\On_Admin_Enqueue_Scripts_Members;

	use WPG\Traits\A6t\App\Utilities\Admin_Notice_Members;
	use WPG\Traits\A6t\App\Hooks\On_Admin_Notice_Members;

	use WPG\Traits\A6t\App\Hooks\On_Style_Script_Tag_Members;
}
