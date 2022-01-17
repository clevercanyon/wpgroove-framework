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
namespace WP_Groove\Framework\Traits\App;

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
trait Members {
	/**
	 * Traits.
	 *
	 * @since 2021-12-28
	 */
	use WPG\Traits\App\Magic\Constructable_Members;
	use WPG\Traits\App\Utilities\Instance_Members;

	use WPG\Traits\App\Utilities\Property_Members;
	use U\Traits\Base\Magic\Finals\Readable_Members;

	use WPG\Traits\App\Hooks\On_Activation_Members;
	use WPG\Traits\App\Hooks\On_Deactivation_Members;

	use WPG\Traits\App\Hooks\On_Plugins_Loaded_Members;
	use WPG\Traits\App\Hooks\On_After_Setup_Theme_Members;
	use WPG\Traits\App\Hooks\On_Init_Members;

	use WPG\Traits\App\Hooks\On_Uninstall_Members;

	use WPG\Traits\App\Utilities\Option_Members;
	use WPG\Traits\App\Utilities\Site_Option_Members;

	use WPG\Traits\App\Utilities\Transient_Members;
	use WPG\Traits\App\Utilities\Site_Transient_Members;

	use WPG\Traits\App\Utilities\Action_Members;
	use WPG\Traits\App\Utilities\Filter_Members;
}
