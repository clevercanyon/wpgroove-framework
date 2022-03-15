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
trait On_Style_Script_Tag_Members {
	/**
	 * Plugin|Theme: on `style_loader_tag` hook.
	 *
	 * @since 2021-12-15
	 *
	 * @param string $tag   Current style tag.
	 * @param string $slug  Style slug (i.e., handle).
	 * @param string $href  Style source location.
	 * @param string $media Style media attribute.
	 *
	 * @return string Revised style tag.
	 */
	final public function fw_on_style_loader_tag( string $tag, string $slug, string $href, string $media ) : string {
		if ( $this->slug . '-css' === $slug || 0 === mb_strpos( $slug, $this->slug_prefix ) ) {
			$tag = str_replace( ' href=', " charset='utf-8' href=", $tag );
		}
		return $tag;
	}

	/**
	 * Plugin|Theme: on `script_loader_tag` hook.
	 *
	 * @since 2021-12-15
	 *
	 * @param string $tag  Current script tag.
	 * @param string $slug Script slug (i.e., handle).
	 * @param string $src  Script source location.
	 *
	 * @return string Revised script tag.
	 */
	final public function fw_on_script_loader_tag( string $tag, string $slug, string $src ) : string {
		if ( $this->slug . '-js' === $slug || 0 === mb_strpos( $slug, $this->slug_prefix ) ) {
			$tag = str_replace( ' src=', " charset='utf-8' src=", $tag );
		}
		return $tag;
	}
}
