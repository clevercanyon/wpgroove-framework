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
namespace WP_Groove\Framework\Dev\CLI_Tools\I18n;

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
 * Text domain tool.
 *
 * @since 2021-12-15
 */
final class Text_Domain extends U\A6t\CLI_Tool {
	/**
	 * Project.
	 *
	 * @since 2021-12-15
	 */
	protected U\Dev\Project $project;

	/**
	 * Version.
	 *
	 * @since 2021-12-15
	 */
	protected const VERSION = '1.0.0';

	/**
	 * Tool name.
	 *
	 * @since 2021-12-15
	 */
	protected const NAME = 'I18n/Text_Domain';

	/**
	 * I18n functions.
	 *
	 * @since 2021-12-15
	 *
	 * @see   https://o5p.me/0IHrSW
	 */
	protected const I18N_FUNCTIONS = [
		// Basic functions.
		'__',
		'_e',
		'_x',
		'_ex',
		'_n',
		'_nx',
		'_n_noop',
		'_nx_noop',
		'translate_nooped_plural',

		// Translate + escape functions.
		'esc_html__',
		'esc_html_e',
		'esc_html_x',
		'esc_attr__',
		'esc_attr_e',
		'esc_attr_x',

		// Deprecated functions.
		'_c',
		'_nc',
		'__ngettext',
		'__ngettext_noop',
	];

	/**
	 * Constructor.
	 *
	 * @param string|array|null $args_to_parse Optional custom args to parse instead of `$_SERVER['argv']`.
	 *                                         If not given, defaults internally to `$_SERVER['argv']`.
	 */
	public function __construct( /* string|array|null */ $args_to_parse = null ) {
		parent::__construct( $args_to_parse );

		$this->add_commands( [
			'add' => [
				'callback'    => [ $this, 'add' ],
				'synopsis'    => 'Adds text domain to PHP files in a given directory.',
				'description' => 'Adds text domain to PHP files in a given directory. See ' . __CLASS__ . '::add()',
				'options'     => [
					'project-dir' => [
						'required'     => true,
						'arg_required' => true,
						'description'  => 'Project directory path.',
						'validator'    => fn( $value ) => ( $abs_path = $this->v6e_abs_path( $value, 'dir' ) )
							&& is_file( U\Dir::join( $abs_path, '/composer.json' ) ),
					],
					'work-dir'    => [
						'required'     => true,
						'arg_required' => true,
						'description'  => 'Work directory path.',
						'validator'    => fn( $value ) => ( $abs_path = $this->v6e_abs_path( $value, 'dir' ) )
							&& preg_match( '/\/\._[^\/]*\//u', $abs_path ),
					],
					'text-domain' => [
						'required'     => true,
						'arg_required' => true,
						'description'  => 'Text domain; e.g., `wpgroove-my-plugin`.',
						'validator'    => fn( $value ) => $value && is_string( $value ) && U\Str::is_lede_slug( $value ),
					],
				],
			],
		] );
		$this->route_request();
	}

	/**
	 * Command: `add`.
	 *
	 * @since 2021-12-15
	 */
	protected function add() : void {
		try {
			U\CLI::heading( '[' . __METHOD__ . '()]: Adding ...' );

			$project_dir   = U\Fs::abs( $this->get_option( 'project-dir' ) );
			$this->project = new U\Dev\Project( $project_dir );

			$work_dir    = U\Fs::abs( $this->get_option( 'work-dir' ) );
			$text_domain = $this->get_option( 'text-domain' );

			$regexp             = U\Fs::gitignore_regexp_lookahead( 'negative', '.+\.php$', [ 'except:vendor/' => 'clevercanyon' ] );
			$php_files_iterator = U\Dir::iterator( $work_dir, $regexp );

			foreach ( $php_files_iterator as $_php_file ) {
				$this->process_file( $text_domain, $_php_file->getPathname() );
				U\CLI::log( '[' . __FUNCTION__ . '()]: Processed: `' . $_php_file->getPathname() . '`.' );
			}
			U\CLI::done( '[' . __METHOD__ . '()]: Complete ✔.' );
		} catch ( \Throwable $throwable ) {
			U\CLI::error( $throwable->getMessage() );
			U\CLI::error( $throwable->getTraceAsString() );
			U\CLI::exit_status( 1 );
		}
	}

	/**
	 * Adds text domain to a single file.
	 *
	 * @since 2021-12-15
	 *
	 * @param string $text_domain Text domain.
	 * @param string $file        File path.
	 *
	 * @throws U\Fatal_Exception On any failure.
	 */
	protected function process_file( string $text_domain, string $file ) : void {
		if ( ! $text_domain ) {
			throw new U\Fatal_Exception( 'Missing text domain.' );
		}
		if ( ! $file || ! is_readable( $file ) || ! is_writable( $file ) ) {
			throw new U\Fatal_Exception( 'Unable to process file: `' . $file . '`. Is it readable and writable?' );
		}
		if ( ! U\File::write( $file, $this->process_string( $text_domain, U\File::read( $file ) ), false ) ) {
			throw new U\Fatal_Exception( 'Failed processing file: `' . $file . '`. Is the file readable and writable?' );
		}
	}

	/**
	 * Adds text domain to a string of PHP.
	 *
	 * @since 2021-12-15
	 *
	 * @param string $text_domain Text domain.
	 * @param string $str         PHP file contents.
	 *
	 * @return string Modified PHP file contents.
	 */
	protected function process_string( string $text_domain, string $str ) : string {
		return $this->process_tokens( $text_domain, token_get_all( U\Str::normalize_eols( $str ) ) );
	}

	/**
	 * Adds text domain to a set of PHP tokens.
	 *
	 * @since 2021-12-15
	 *
	 * @param string $text_domain Text domain.
	 * @param array  $tokens      PHP file tokens.
	 *
	 * @return string Modified PHP file contents (tokens converted to string).
	 */
	protected function process_tokens( string $text_domain, array $tokens ) : string {
		$round_brackets_balance     = 0;
		$in_i18n_function           = false;
		$i18n_function_args_started = false;
		$found_i18n_text_domain     = false;

		$prev_token_type                = null;
		$last_non_whitespace_token_type = null;

		$modified_file_contents = ''; // Initialize.
		$text_domain            = addslashes( $text_domain );

		foreach ( $tokens as $_token ) {
			if ( is_array( $_token ) ) {
				[ $_token_type, $_token_value ] = $_token;
			} else {
				$_token_type = $_token_value = $_token;
			}
			if ( T_STRING === $_token_type // Possible `__`, `_x`, `_n`, etc.
				&& T_FUNCTION !== $last_non_whitespace_token_type // Skip `function __` definitions.
				&& in_array( mb_strtolower( $_token_value ), $this::I18N_FUNCTIONS, true )
			) {
				$round_brackets_balance     = 0;
				$in_i18n_function           = true;
				$i18n_function_args_started = false;
				$found_i18n_text_domain     = false;

			} elseif ( T_CONSTANT_ENCAPSED_STRING === $_token_type
				&& ( "'" . $text_domain . "'" === $_token_value || '"' . $text_domain . '"' === $_token_value )
			) {
				if ( $in_i18n_function && $i18n_function_args_started ) {
					$found_i18n_text_domain = true;
				}
			} elseif ( '(' === $_token_type ) {
				++$round_brackets_balance;
				$i18n_function_args_started = $in_i18n_function && 1 === $round_brackets_balance;

			} elseif ( ')' === $_token_type ) {
				--$round_brackets_balance;

				if ( $in_i18n_function && 0 === $round_brackets_balance ) {
					if ( ! $found_i18n_text_domain ) {
						$_token_value = ', ' . "'" . $text_domain . "'";

						if ( T_WHITESPACE === $prev_token_type ) {
							$modified_file_contents = trim( $modified_file_contents );
							$_token_value           .= ' '; // Mimic adherence to coding standards.
						}
						$_token_value .= ')'; // Close bracket now.
					}
					$in_i18n_function           = false;
					$i18n_function_args_started = false;
					$found_i18n_text_domain     = false;
				}
			}
			$prev_token_type = $_token_type;

			if ( T_WHITESPACE !== $_token_type ) {
				$last_non_whitespace_token_type = $_token_type;
			}
			$modified_file_contents .= $_token_value;
		}
		return $modified_file_contents;
	}
}
