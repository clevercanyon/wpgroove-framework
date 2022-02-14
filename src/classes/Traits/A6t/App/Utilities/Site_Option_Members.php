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
trait Site_Option_Members {
	/**
	 * Plugin|Theme: {@see get_site_option()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $key     {@see get_site_option()} for further details.
	 *                        This gets auto-prefixed using app’s `var_prefix`, which can be up to 67 bytes.
	 *                        A WordPress site option key can be up to a maximum of 255 chars in total length.
	 *                        Therefore, on multisite, maximum option `$key` size is `255 - 67` = `188` chars.
	 *                        If not multisite, maximum option `$key` size is `191 - 67` = `124` chars.
	 *
	 * @param mixed  $default Optional default return value, which by default is `null`.
	 *
	 *                        * Unlike {@see get_site_option()}, the default here is `null` instead of `false`.
	 *                          Made possible by {@see U\Str::serialize()}, {@see U\Str::maybe_unserialize()}.
	 *
	 * @return mixed {@see get_site_option()}, {@see U\Str::maybe_unserialize()}.
	 *
	 *               * Unlike {@see get_site_option()}, this returns value with its original data type.
	 *                 Made possible by {@see U\Str::serialize()}, {@see U\Str::maybe_unserialize()}.
	 *
	 *               * Unlike {@see get_site_option()}, this returns `null` on miss or failure.
	 *                 Made possible by {@see U\Str::serialize()}, {@see U\Str::maybe_unserialize()}.
	 */
	final public function get_site_option( string $key, /* mixed */ $default = null ) /* : mixed */ {
		$value = get_site_option( $this->var_prefix . $key );
		$value = false === $value ? null : U\Str::maybe_unserialize( $value );

		return $value ?? $default;
	}

	/**
	 * Plugin|Theme: {@see add_site_option()}.
	 *
	 * Site options are never autoloaded by WordPress core, though they are cached by core.
	 * So while they're not autoloaded, if there's an object cache plugin running, they sorta are.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $key   {@see add_site_option()} for further details.
	 *                      This gets auto-prefixed using app’s `var_prefix`, which can be up to 67 bytes.
	 *                      A WordPress site option key can be up to a maximum of 255 chars in total length.
	 *                      Therefore, on multisite, maximum option `$key` size is `255 - 67` = `188` chars.
	 *                      If not multisite, maximum option `$key` size is `191 - 67` = `124` chars.
	 *
	 * @param mixed  $value Option value to store. Goes into DB table.
	 *
	 *                      * Passing `null` explicitly will {@see delete_site_option()}.
	 *
	 *                      * Unlike {@see add_site_option()}, this stores values w/ their original data type.
	 *                        Made possible by {@see U\Str::serialize()}, {@see U\Str::maybe_unserialize()}.
	 *
	 *                      * PHP does not allow a {@see \Closure} to be serialized whatsoever.
	 *                        Do not attempt to store closures here; either directly or indirectly.
	 *                        You can, however, store a {@see U\Code_Stream_Closure}.
	 *
	 *                      * PHP serializes a resource as `0`, and therefore works, but it's a bad practice.
	 *                        Do not attempt to store resource values here; either directly or indirectly.
	 *                        Future versions of PHP will likely disallow altogether.
	 *
	 *                      * If you must store a resource, consider {@see U\A6t\Base::cls_cache()}.
	 *
	 * @return bool {@see add_site_option()}.
	 */
	final public function add_site_option( string $key, /* mixed */ $value ) : bool {
		assert( ! is_resource( $value ) );
		assert( ! $value instanceof \Closure );

		if ( null === $value ) {
			return $this->delete_site_option( $key );
		}
		$value = U\Str::serialize( $value );

		return add_site_option( $this->var_prefix . $key, $value );
	}

	/**
	 * Plugin|Theme: {@see update_site_option()}.
	 *
	 * Site options are never autoloaded by WordPress core, though they are cached by core.
	 * So while they're not autoloaded, if there's an object cache plugin running, they sorta are.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $key   {@see update_site_option()} for further details.
	 *                      This gets auto-prefixed using app’s `var_prefix`, which can be up to 67 bytes.
	 *                      A WordPress site option key can be up to a maximum of 255 chars in total length.
	 *                      Therefore, on multisite, maximum option `$key` size is `255 - 67` = `188` chars.
	 *                      If not multisite, maximum option `$key` size is `191 - 67` = `124` chars.
	 *
	 * @param mixed  $value Option value to store. Goes into DB table.
	 *
	 *                      * Passing `null` explicitly will {@see delete_site_option()}.
	 *
	 *                      * Unlike {@see update_site_option()}, this stores values w/ their original data type.
	 *                        Made possible by {@see U\Str::serialize()}, {@see U\Str::maybe_unserialize()}.
	 *
	 *                      * PHP does not allow a {@see \Closure} to be serialized whatsoever.
	 *                        Do not attempt to store closures here; either directly or indirectly.
	 *                        You can, however, store a {@see U\Code_Stream_Closure}.
	 *
	 *                      * PHP serializes a resource as `0`, and therefore works, but it's a bad practice.
	 *                        Do not attempt to store resource values here; either directly or indirectly.
	 *                        Future versions of PHP will likely disallow altogether.
	 *
	 *                      * If you must store a resource, consider {@see U\A6t\Base::cls_cache()}.
	 *
	 * @return bool {@see update_site_option()}.
	 */
	final public function update_site_option( string $key, /* mixed */ $value ) : bool {
		assert( ! is_resource( $value ) );
		assert( ! $value instanceof \Closure );

		if ( null === $value ) {
			return $this->delete_site_option( $key );
		}
		$value = U\Str::serialize( $value );

		return update_site_option( $this->var_prefix . $key, $value );
	}

	/**
	 * Plugin|Theme: {@see delete_site_option()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param string $key {@see delete_site_option()} for further details.
	 *                    This gets auto-prefixed using app’s `var_prefix`, which can be up to 67 bytes.
	 *                    A WordPress site option key can be up to a maximum of 255 chars in total length.
	 *                    Therefore, on multisite, maximum option `$key` size is `255 - 67` = `188` chars.
	 *                    If not multisite, maximum option `$key` size is `191 - 67` = `124` chars.
	 *
	 * @return bool {@see delete_site_option()}.
	 */
	final public function delete_site_option( string $key ) : bool {
		return delete_site_option( $this->var_prefix . $key );
	}
}
