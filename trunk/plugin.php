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
 * Plugin Name: Framework Plugin
 * Plugin URI: https://wpgroove.com/product/framework-plugin
 *
 * Description: Exists only for testing the WP Groove WordPress framework.
 * Tags: developers, framework plugin
 *
 * Version: 1.0.0
 * Stable tag: 1.0.0
 *
 * Requires PHP: 7.4
 * Requires at least: 5.8.2
 * Tested up to: 5.8.2
 *
 * Author: WP Groove
 * Author URI: https://wpgroove.com
 * Donate link: https://wpgroove.com/donate
 * Contributors: clevercanyon
 *
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * Text Domain: wpgroove-framework-plugin
 * Domain Path: /languages
 *
 * [x] Update URI: https://wpgroove.com/wp-json/c24s/v1/product/framework-plugin/wp-update
 */

/**
 * Declarations & namespace.
 *
 * @since 2021-12-25
 */
declare( strict_types = 1 );
namespace WP_Groove\Framework_Plugin;

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
 * Plugin.
 *
 * @since 2021-12-15
 */
use WP_Groove\{Framework_Plugin as WP};

// </editor-fold>

/**
 * No direct access.
 *
 * @since 2021-12-15
 */
if ( ! defined( 'WPINC' ) ) {
	return; // No direct access.
}

/**
 * Requires autoloader.
 *
 * @since 2021-12-15
 */
require_once __DIR__ . '/vendor/autoload.php';

/**
 * Adds instance hooks.
 *
 * @since 2021-12-15
 */
WP\Plugin::add_instance_hooks(
	__FILE__,
	'Framework Plugin',          // @name
	'wpgroove-framework-plugin', // @slug
	'1.0.0'                      // @version
);
