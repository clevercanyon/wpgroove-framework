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
 * Class members.
 *
 * @since 2021-12-15
 *
 * @see   WPG\A6t\App
 */
trait Site_Transient_Members {
	/**
	 * Plugin|Theme: {@see get_site_transient()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param mixed $key_parts A single key part or a bundle containing multiple key parts.
	 *                         These are key parts used to formulate an actual transient key identifier.
	 *                         The final transient key identifier is auto-prefixed using app's `var_prefix`.
	 *
	 *                         Whatever you pass, it will be serialized & hashed; i.e., converted to a string key.
	 *                         Passing a `__METHOD__` name as one part is a highly recommended best practice.
	 *
	 *                         * The more parts you pass, the longer it will take to hash.
	 *                           When passing a bundle, try to keep it simple for best performance.
	 *
	 *                         * PHP does not allow a {@see \Closure} to be serialized whatsoever.
	 *                           Do not pass closures as a key part; either directly or indirectly.
	 *                           You can, however, pass a {@see U\Code_Stream_Closure}.
	 *
	 *                         * PHP serializes a resource as `0`, and therefore works, but it's a bad practice.
	 *                           Do not pass resource values as a key part; either directly or indirectly.
	 *                           Future versions of PHP will likely disallow altogether.
	 *
	 * @param mixed $default   Optional default return value, which by default is `null`.
	 *
	 *                         * This parameter does not exist in WordPress core {@see get_site_transient()}.
	 *
	 * @return mixed {@see get_site_transient()}, {@see U\Str::maybe_unserialize()}.
	 *
	 *               * Unlike {@see get_site_transient()}, this returns value with its original data type.
	 *                 Made possible by {@see U\Str::serialize()}, {@see U\Str::maybe_unserialize()}.
	 *
	 *               * Unlike {@see get_site_transient()}, this returns `null` (or `$default`) on miss or failure.
	 *                 Made possible by {@see U\Str::serialize()}, {@see U\Str::maybe_unserialize()}.
	 */
	final public function get_site_transient( /* mixed */ $key_parts, /* mixed */ $default = null ) /* : mixed */ {
		assert( ! is_resource( $key_parts ) );
		assert( ! $key_parts instanceof \Closure );

		$key   = U\Crypto::sha1_key( $key_parts );
		$value = get_site_transient( $this->var_prefix . $key );
		$value = false === $value ? null : U\Str::maybe_unserialize( $value );

		return $value ?? $default;
	}

	/**
	 * Plugin|Theme: {@see set_site_transient()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param mixed $key_parts  A single key part or a bundle containing multiple key parts.
	 *                          These are key parts used to formulate an actual transient key identifier.
	 *                          The final transient key identifier is auto-prefixed using app's `var_prefix`.
	 *
	 *                          Whatever you pass, it will be serialized & hashed; i.e., converted to a string key.
	 *                          Passing a `__METHOD__` name as one part is a highly recommended best practice.
	 *
	 *                          * The more parts you pass, the longer it will take to hash.
	 *                            When passing a bundle, try to keep it simple for best performance.
	 *
	 *                          * PHP does not allow a {@see \Closure} to be serialized whatsoever.
	 *                            Do not pass closures as a key part; either directly or indirectly.
	 *                            You can, however, pass a {@see U\Code_Stream_Closure}.
	 *
	 *                          * PHP serializes a resource as `0`, and therefore works, but it's a bad practice.
	 *                            Do not pass resource values as a key part; either directly or indirectly.
	 *                            Future versions of PHP will likely disallow altogether.
	 *
	 * @param mixed $value      Transient value to store. Goes into DB or potentially an in-memory cache.
	 *
	 *                          * Passing `null` explicitly will {@see delete_site_transient()}.
	 *
	 *                          * Unlike {@see set_site_transient()}, this stores values w/ their original data type.
	 *                            Made possible by {@see U\Str::serialize()}, {@see U\Str::maybe_unserialize()}.
	 *
	 *                          * PHP does not allow a {@see \Closure} to be serialized whatsoever.
	 *                            Do not attempt to store closures here; either directly or indirectly.
	 *                            You can, however, store a {@see U\Code_Stream_Closure}.
	 *
	 *                          * PHP serializes a resource as `0`, and therefore works, but it's a bad practice.
	 *                            Do not attempt to store resource values here; either directly or indirectly.
	 *                            Future versions of PHP will likely disallow altogether.
	 *
	 *                          * If you must store a resource, consider {@see U\A6t\Base::cls_cache()}.
	 *
	 * @param int   $expires_in Default is {@see U\Time::HOUR_IN_SECONDS} (one hour).
	 *                          Must be `> 0`, else it reverts to default {@see U\Time::HOUR_IN_SECONDS}.
	 *
	 * @return bool {@see set_site_transient()}.
	 */
	final public function set_site_transient( /* mixed */ $key_parts, /* mixed */ $value, int $expires_in = U\Time::HOUR_IN_SECONDS ) : bool {
		assert( ! is_resource( $key_parts ) );
		assert( ! $key_parts instanceof \Closure );

		assert( ! is_resource( $value ) );
		assert( ! $value instanceof \Closure );

		assert( $expires_in > 0 );

		if ( null === $value ) {
			return $this->delete_site_transient( $key_parts );
		}
		$key        = U\Crypto::sha1_key( $key_parts );
		$value      = U\Str::serialize( $value );
		$expires_in = $expires_in <= 0 ? U\Time::HOUR_IN_SECONDS : $expires_in;

		return set_site_transient( $this->var_prefix . $key, $value, $expires_in );
	}

	/**
	 * Plugin|Theme: {@see delete_site_transient()}.
	 *
	 * @since 2021-12-30
	 *
	 * @param mixed $key_parts A single key part or a bundle containing multiple key parts.
	 *                         These are key parts used to formulate an actual transient key identifier.
	 *                         The final transient key identifier is auto-prefixed using app's `var_prefix`.
	 *
	 *                         Whatever you pass, it will be serialized & hashed; i.e., converted to a string key.
	 *                         Passing a `__METHOD__` name as one part is a highly recommended best practice.
	 *
	 *                         * The more parts you pass, the longer it will take to hash.
	 *                           When passing a bundle, try to keep it simple for best performance.
	 *
	 *                         * PHP does not allow a {@see \Closure} to be serialized whatsoever.
	 *                           Do not pass closures as a key part; either directly or indirectly.
	 *                           You can, however, pass a {@see U\Code_Stream_Closure}.
	 *
	 *                         * PHP serializes a resource as `0`, and therefore works, but it's a bad practice.
	 *                           Do not pass resource values as a key part; either directly or indirectly.
	 *                           Future versions of PHP will likely disallow altogether.
	 *
	 * @return bool {@see delete_site_transient()}.
	 */
	final public function delete_site_transient( /* mixed */ $key_parts ) : bool {
		assert( ! is_resource( $key_parts ) );
		assert( ! $key_parts instanceof \Closure );

		$key = U\Crypto::sha1_key( $key_parts );
		return delete_site_transient( $this->var_prefix . $key );
	}
}
