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
namespace WP_Groove\Framework\Dev\CLI_Tools\Composer;

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
 * File-specific.
 *
 * @since 2021-12-15
 */
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

// </editor-fold>

/**
 * On `post-(install|update)-cmd` hook.
 *
 * @since 2021-12-15
 */
final class Compiler extends Operations {
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
	protected const NAME = 'Composer/Compiler';

	/**
	 * Constructor.
	 *
	 * @since 2021-12-15
	 *
	 * @param string|array|null $args_to_parse Optional custom args to parse instead of `$_SERVER['argv']`.
	 *                                         If not given, defaults internally to `$_SERVER['argv']`.
	 */
	public function __construct( /* string|array|null */ $args_to_parse = null ) {
		parent::__construct( $args_to_parse );

		$this->add_commands( [
			'symlink' => [
				'callback'    => [ $this, 'symlink' ],
				'synopsis'    => 'Updates project symlinks.',
				'description' => 'Updates project symlinks. See ' . __CLASS__ . '::symlink()',
				'options'     => [
					'project-dir' => [
						'required'     => true,
						'arg_required' => true,
						'description'  => 'Project directory path.',
						'validator'    => fn( $value ) => ( $abs_path = $this->v6e_abs_path( $value, 'dir' ) )
							&& is_file( U\Dir::join( $abs_path, '/composer.json' ) ),
					],
				],
			],
			'compile' => [
				'callback'    => [ $this, 'compile' ],
				'synopsis'    => 'Compiles project.',
				'description' => 'Compiles project. See ' . __CLASS__ . '::compile()',
				'options'     => [
					'project-dir' => [
						'required'     => true,
						'arg_required' => true,
						'description'  => 'Project directory path.',
						'validator'    => fn( $value ) => ( $abs_path = $this->v6e_abs_path( $value, 'dir' ) )
							&& is_file( U\Dir::join( $abs_path, '/composer.json' ) ),
					],
				],
			],
		] );
		$this->route_request();
	}

	/**
	 * Command: `symlink`.
	 *
	 * @since 2021-12-15
	 */
	protected function symlink() : void {
		try {
			U\CLI::heading( '[' . __METHOD__ . '()]: Symlinking ...' );

			$project_dir   = U\Fs::abs( $this->get_option( 'project-dir' ) );
			$this->project = new U\Dev\Project( $project_dir );

			$this->maybe_symlink_local_repos();
			$this->maybe_symlink_wp_app_locally();

			U\CLI::success( '[' . __METHOD__ . '()]: Symlinking complete ✔.' );
		} catch ( \Throwable $throwable ) {
			U\CLI::danger_hilite( $throwable->getMessage() );
			U\CLI::log( $throwable->getTraceAsString() );
			U\CLI::exit_status( 1 );
		}
	}

	/**
	 * Command: `compile`.
	 *
	 * @since 2021-12-15
	 */
	protected function compile() : void {
		try {
			U\CLI::heading( '[' . __METHOD__ . '()]: Compiling ...' );

			$project_dir   = U\Fs::abs( $this->get_option( 'project-dir' ) );
			$this->project = new U\Dev\Project( $project_dir );

			$this->maybe_setup_dotfiles();

			$this->maybe_run_npm_update();
			$this->maybe_run_npx_webpack();
			$this->maybe_run_wp_app_composer_update();

			$this->maybe_sync_wp_plugin_headers();
			$this->maybe_sync_wp_theme_headers();

			$this->maybe_compile_distro_lib_dir();
			$this->maybe_compile_distro_lib_tests_dir();

			$this->maybe_compile_distro_lib_zip();
			$this->maybe_s3_upload_distro_lib_zip();

			$this->maybe_compile_wp_app_svn_repo();
			$this->maybe_compile_wp_app_distro_tests();

			$this->maybe_compile_wp_app_zip();
			$this->maybe_s3_upload_wp_app_zip();

			U\CLI::success( '[' . __METHOD__ . '()]: Compilation complete ✔.' );
		} catch ( \Throwable $throwable ) {
			U\CLI::danger_hilite( $throwable->getMessage() );
			U\CLI::log( $throwable->getTraceAsString() );
			U\CLI::exit_status( 1 );
		}
	}
}
