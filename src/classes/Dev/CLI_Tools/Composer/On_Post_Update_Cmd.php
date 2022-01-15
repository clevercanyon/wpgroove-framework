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
 * On `post-update-cmd` hook.
 *
 * @since 2021-12-15
 */
class On_Post_Update_Cmd extends U\A6t\CLI_Tool {
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
	protected const NAME = 'Composer/Hook/On_Post_Update_Cmd';

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
			'update' => [
				'callback'    => [ $this, 'update' ],
				'synopsis'    => 'Updates project dependencies, symlinks, headers, SVN repos, and zip files.',
				'description' => 'Updates project dependencies, symlinks, headers, SVN repos, and zip files. See ' . __CLASS__ . '::update()',
				'options'     => [
					'project-dir' => [
						'optional'    => true,
						'description' => 'Project directory path.',
						'validator'   => fn( $value ) => $value && is_string( $value ) && is_dir( $value )
							&& is_file( U\Dir::join( $value, '/composer.json' ) ),
						'default'     => getcwd(),
					],
				],
			],
		] );
		$this->route_request();
	}

	/**
	 * Command: `update`.
	 *
	 * @since 2021-12-15
	 */
	protected function update() : void {
		try {
			$this->project = new U\Dev\Project(
				$this->get_option( 'project-dir' )
			);
			$this->maybe_run_wp_app_composer_updates();
			$this->maybe_symlink_wp_app_locally();

			$this->maybe_sync_wp_plugin_headers();
			$this->maybe_sync_wp_theme_headers();

			$this->maybe_compile_wp_app_svn_repo();
			$this->maybe_compile_wp_app_zip();
			$this->maybe_s3_upload_wp_app_zip();

		} catch ( \Throwable $throwable ) {
			U\CLI::error( $throwable->getMessage() );
			U\CLI::error( $throwable->getTraceAsString() );
			U\CLI::exit_status( 1 );
		}
	}

	/**
	 * Maybe run WordPress app’s composer updates.
	 *
	 * @since        2021-12-15
	 *
	 * @throws U\Exception On any failure.
	 * @noinspection PhpDocRedundantThrowsInspection
	 */
	protected function maybe_run_wp_app_composer_updates() : void {
		if ( ! $this->project->is_wp_project() ) {
			return; // Not applicable.
		}
		if ( $this->project->has_file( 'trunk/composer.json' ) ) {
			U\CLI::run( [ 'composer', 'update' ], U\Dir::join( $this->project->dir, '/trunk' ) );
		}
	}

	/**
	 * Maybe symlink WordPress app locally.
	 *
	 * @since 2021-12-15
	 *
	 * @throws U\Exception On any failure.
	 */
	protected function maybe_symlink_wp_app_locally() : void {
		if ( ! $this->project->is_wp_project() ) {
			return; // Not applicable.
		}
		if ( $this->project->is_wp_plugin() ) {
			$app = $this->project->wp_plugin_data();
		} elseif ( $this->project->is_wp_theme() ) {
			$app = $this->project->wp_theme_data();
		} else {
			throw new U\Exception( 'Unknown WordPress app type.' );
		}
		if ( ! $local_wp_public_html_dir = $this->project->local_wp_public_html_dir() ) {
			return; // Not possible.
		}
		if ( $this->project->is_wp_plugin() ) {
			$local_dir = U\Dir::join( $local_wp_public_html_dir, '/wp-content/plugins/' . $this->project->slug );
		} elseif ( $this->project->is_wp_theme() ) {
			$local_dir = U\Dir::join( $local_wp_public_html_dir, '/wp-content/themes/' . $this->project->slug );
		} else {
			throw new U\Exception( 'Unknown WordPress app type.' );
		}
		if ( U\Fs::path_exists( $local_dir ) ) {
			return; // Do not overwrite.
		}
		if ( ! is_writable( U\Dir::name( $local_dir ) ) ) {
			throw new U\Exception( 'Local WordPress symlink failure. Directory not writable: `' . U\Dir::name( $local_dir ) . '`.' );
		}
		if ( ! symlink( $app->dir, $local_dir ) ) {
			throw new U\Exception( 'Unexpected local WordPress symlink failure: `' . $local_dir . '`.' );
		}
	}

	/**
	 * Maybe sync WordPress plugin headers.
	 *
	 * @since 2021-12-15
	 *
	 * @throws U\Exception On any failure.
	 */
	protected function maybe_sync_wp_plugin_headers() : void {
		if ( ! $this->project->is_wp_plugin() ) {
			return; // Not applicable.
		}
		$plugin           = $this->project->wp_plugin_data();
		$local_wp_version = $this->project->local_wp_version();

		if ( $local_wp_version && version_compare( $local_wp_version, $plugin->headers->tested_up_to_wp_version, '>' ) ) {
			$plugin->headers->tested_up_to_wp_version = $local_wp_version;
		}
		// The existence of these files already confimred by {@see Project::wp_plugin_data()}.

		$plugin_file_contents        = file_get_contents( $plugin->file );
		$plugin_readme_file_contents = file_get_contents( $plugin->readme_file );

		foreach ( $plugin->headers->_map as $_prop => $_header ) {
			if ( in_array( $_prop, [ 'version', 'stable_tag', 'name' ], true ) ) {
				$plugin->headers->{$_prop} = $this->project->{$_prop}; // Sync these w/ project data.
			}
			$plugin_file_contents        = preg_replace( '/^(\h*\*\h*)?' . U\Str::esc_reg( $_header ) . '\:\h*.*$/umi', '${1}' . $_header . ': ' . $plugin->headers->{$_prop}, $plugin_file_contents );
			$plugin_readme_file_contents = preg_replace( '/^(\h*)?' . U\Str::esc_reg( $_header ) . '\:\h*.*$/umi', '${1}' . $_header . ': ' . $plugin->headers->{$_prop}, $plugin_readme_file_contents );
		}
		$plugin_file_contents = preg_replace( '/^(\h*)["\'][^"\']*["\']\h*(,)?\h*\/\/\h*@slug\h*$/uim', '${1}' . "'" . U\Str::esc_sq( $this->project->slug ) . "'" . '${2} // @slug', $plugin_file_contents );
		$plugin_file_contents = preg_replace( '/^(\h*)["\'][^"\']*["\']\h*(,)?\h*\/\/\h*@name\h*$/uim', '${1}' . "'" . U\Str::esc_sq( $this->project->name ) . "'" . '${2} // @name', $plugin_file_contents );
		$plugin_file_contents = preg_replace( '/^(\h*)["\'][^"\']*["\']\h*(,)?\h*\/\/\h*@version\h*$/uim', '${1}' . "'" . U\Str::esc_sq( $this->project->version ) . "'" . '${2} // @version', $plugin_file_contents );

		if ( false === file_put_contents( $plugin->file, $plugin_file_contents ) ) {
			throw new U\Exception( 'Unable to update plugin file when syncing versions: ' . $plugin->file );
		}
		if ( false === file_put_contents( $plugin->readme_file, $plugin_readme_file_contents ) ) {
			throw new U\Exception( 'Unable to update plugin readme file when syncing versions: ' . $plugin->readme_file );
		}
	}

	/**
	 * Maybe sync WordPress theme headers.
	 *
	 * @since 2021-12-15
	 *
	 * @throws U\Exception On any failure.
	 */
	protected function maybe_sync_wp_theme_headers() : void {
		if ( ! $this->project->is_wp_theme() ) {
			return; // Not applicable.
		}
		$theme            = $this->project->wp_theme_data();
		$local_wp_version = $this->project->local_wp_version();

		if ( $local_wp_version && version_compare( $local_wp_version, $theme->headers->tested_up_to_wp_version, '>' ) ) {
			$theme->headers->tested_up_to_wp_version = $local_wp_version;
		}
		// The existence of these files already confimred by {@see Project::wp_theme_data()}.

		$theme_file_contents           = file_get_contents( $theme->file );
		$theme_functions_file_contents = file_get_contents( $theme->functions_file );
		$theme_style_file_contents     = file_get_contents( $theme->style_file );
		$theme_readme_file_contents    = file_get_contents( $theme->readme_file );

		foreach ( $theme->headers->_map as $_prop => $_header ) {
			if ( in_array( $_prop, [ 'version', 'stable_tag', 'name' ], true ) ) {
				$theme->headers->{$_prop} = $this->project->{$_prop}; // Sync these w/ project data.
			}
			$theme_file_contents           = preg_replace( '/^(\h*\*\h*)?' . U\Str::esc_reg( $_header ) . '\:\h*.*$/umi', '${1}' . $_header . ': ' . $theme->headers->{$_prop}, $theme_file_contents );
			$theme_functions_file_contents = preg_replace( '/^(\h*\*\h*)?' . U\Str::esc_reg( $_header ) . '\:\h*.*$/umi', '${1}' . $_header . ': ' . $theme->headers->{$_prop}, $theme_functions_file_contents );
			$theme_style_file_contents     = preg_replace( '/^(\h*\*\h*)?' . U\Str::esc_reg( $_header ) . '\:\h*.*$/umi', '${1}' . $_header . ': ' . $theme->headers->{$_prop}, $theme_style_file_contents );
			$theme_readme_file_contents    = preg_replace( '/^(\h*)?' . U\Str::esc_reg( $_header ) . '\:\h*.*$/umi', '${1}' . $_header . ': ' . $theme->headers->{$_prop}, $theme_readme_file_contents );
		}
		$theme_file_contents = preg_replace( '/^(\h*)["\'][^"\']*["\']\h*(,)?\h*\/\/\h*@slug\h*$/uim', '${1}' . "'" . U\Str::esc_sq( $this->project->slug ) . "'" . '${2} // @slug', $theme_file_contents );
		$theme_file_contents = preg_replace( '/^(\h*)["\'][^"\']*["\']\h*(,)?\h*\/\/\h*@name\h*$/uim', '${1}' . "'" . U\Str::esc_sq( $this->project->name ) . "'" . '${2} // @name', $theme_file_contents );
		$theme_file_contents = preg_replace( '/^(\h*)["\'][^"\']*["\']\h*(,)?\h*\/\/\h*@version\h*$/uim', '${1}' . "'" . U\Str::esc_sq( $this->project->version ) . "'" . '${2} // @version', $theme_file_contents );

		$theme_functions_file_contents = preg_replace( '/^(\h*)["\'][^"\']*["\']\h*(,)?\h*\/\/\h*@slug\h*$/uim', '${1}' . "'" . U\Str::esc_sq( $this->project->slug ) . "'" . '${2} // @slug', $theme_functions_file_contents );
		$theme_functions_file_contents = preg_replace( '/^(\h*)["\'][^"\']*["\']\h*(,)?\h*\/\/\h*@name\h*$/uim', '${1}' . "'" . U\Str::esc_sq( $this->project->name ) . "'" . '${2} // @name', $theme_functions_file_contents );
		$theme_functions_file_contents = preg_replace( '/^(\h*)["\'][^"\']*["\']\h*(,)?\h*\/\/\h*@version\h*$/uim', '${1}' . "'" . U\Str::esc_sq( $this->project->version ) . "'" . '${2} // @version', $theme_functions_file_contents );

		if ( false === file_put_contents( $theme->file, $theme_file_contents ) ) {
			throw new U\Exception( 'Unable to update theme file when syncing versions: ' . $theme->file );
		}
		if ( false === file_put_contents( $theme->functions_file, $theme_functions_file_contents ) ) {
			throw new U\Exception( 'Unable to update theme functions file when syncing versions: ' . $theme->functions_file );
		}
		if ( false === file_put_contents( $theme->style_file, $theme_style_file_contents ) ) {
			throw new U\Exception( 'Unable to update theme style file when syncing versions: ' . $theme->style_file );
		}
		if ( false === file_put_contents( $theme->readme_file, $theme_readme_file_contents ) ) {
			throw new U\Exception( 'Unable to update theme readme file when syncing versions: ' . $theme->readme_file );
		}
	}

	/**
	 * Maybe compile WordPress app’s SVN repo.
	 *
	 * @since 2021-12-15
	 *
	 * @throws U\Exception On any failure.
	 *
	 * @note  Regarding use of `--no-plugins` in Composer calls below.
	 *       {@see https://github.com/humbug/php-scoper#composer-plugins}.
	 */
	protected function maybe_compile_wp_app_svn_repo() : void {
		if ( ! $this->project->is_wp_project() ) {
			return; // Not applicable.
		}
		if ( $this->project->is_wp_plugin() ) {
			$app = $this->project->wp_plugin_data();
		} elseif ( $this->project->is_wp_theme() ) {
			$app = $this->project->wp_theme_data();
		} else {
			throw new U\Exception( 'Unknown WordPress app type.' );
		}
		$comp_dir_copy_config  = $this->project->comp_dir_copy_config();
		$comp_dir_prune_config = $this->project->comp_dir_prune_config();

		$svn_comp_dir   = U\Dir::join( $this->project->dir, '/._x/svn-comp' );
		$svn_distro_dir = U\Dir::join( $this->project->dir, '/._x/svn-distro' );
		$svn_repo_dir   = U\Dir::join( $this->project->dir, '/._x/svn-repo' );

		// Copies project directory into `._x/svn-comp`.
		// This copy ignores everything in `.gitignore`, and nothing else.
		// For further details {@see Project::comp_dir_copy_config()}.

		if ( ! U\Fs::copy(
			$this->project->dir,
			$svn_comp_dir,
			$comp_dir_copy_config[ 'ignore' ],
			$comp_dir_copy_config[ 'exceptions' ]
		) ) {
			throw new U\Exception( 'Failed to create `./._x/svn-comp`.' );
		}
		// Installs composer dependencies in `._x/svn-comp/trunk`.
		// We didn't ignore `composer.json` when copying, so it's available.
		// The autoloader is optimized here, as we are compiling for production.

		U\CLI::run( [
			[ 'composer', 'install' ],
			[ '--no-dev', '--no-scripts', '--no-plugins' ],
			[ '--optimize-autoloader', '--classmap-authoritative' ],
		], U\Dir::join( $svn_comp_dir, '/trunk' ) );

		// Prunes the `./._x/svn-comp` directory, which speeds up remaining tasks.
		// This prunes everything in `.gitignore`, except: `vendor`, `composer.json`.
		// It also prunes a bunch of other things; {@see Project::comp_dir_prune_config()}.

		if ( ! U\Dir::prune(
			$svn_comp_dir,
			$comp_dir_prune_config[ 'prune' ],
			array_merge( $comp_dir_prune_config[ 'exceptions' ], [
				'/(?:^|.+?\/)composer\.json$/ui',
			] ),
		) ) {
			throw new U\Exception( 'Failed to prune `./._x/svn-comp`.' );
		}
		// Adds text domain to everything in `._x/svn-comp/trunk`.
		// This tool ignores everything in `.gitignore`, except `vendor/clevercanyon/*`.
		// Therefore, we are adding the text domain to other clevercanyon packages, including the WP Groove framework.

		U\CLI::run( [
			[ U\Dir::join( $this->project->dir, '/vendor/clevercanyon/wpgroove-framework/dev/cli-tools/i18n/text-domain' ), 'add' ],
			[ '--text-domain', $app->headers->text_domain ],
			[ '--dir', U\Dir::join( $svn_comp_dir, '/trunk' ) ],
		] );
		// Runs PHP Scoper on full `._x/svn-comp` directory; outputting to `._x/svn-distro`.
		// PHP Scoper ignores files based on Finders in the `.scoper.cfg.php` file.
		// We're not using that functionality, though, as we have already pruned the directory.

		U\CLI::run( [
			[ U\Dir::join( $this->project->dir, '/vendor/clevercanyon/php-js-utilities/dev/cli-tools/php-scoper/scoper' ), 'scope' ],
			[ '--project-dir', $this->project->dir ],
			[ '--prefix', ucfirst( $this->project->pkg_name_hash ) ],
			[ '--dir', $svn_comp_dir ],
			[ '--output-dir', $svn_distro_dir ],
			[ '--output-project-dir', U\Dir::join( $svn_distro_dir, '/trunk' ) ],
			[ '--output-project-dir-entry-file', U\Dir::join( $svn_distro_dir, '/trunk/' . basename( $app->file ) ) ],
		] );
		// Prunes the `./._x/svn-distro` directory now.
		// This prunes everything in `.gitignore`, except `vendor`. This time, including `composer.json` files.
		// It also prunes a bunch of other things; {@see Project::comp_dir_prune_config()}.

		if ( ! U\Dir::prune(
			$svn_distro_dir,
			$comp_dir_prune_config[ 'prune' ],
			$comp_dir_prune_config[ 'exceptions' ]
		) ) {
			throw new U\Exception( 'Failed to prune `./._x/svn-distro`.' );
		}
		// Copies contents of `./._x/svn-distro/*` into `./._x/svn-repo/` directory.
		// This copy ignores nothing. Everything is copied without exception.

		if ( ! U\Fs::copy(
			U\Dir::join( $svn_distro_dir, '/*' ),
			$svn_repo_dir
		) ) {
			throw new U\Exception(
				'Failed to copy contents of `./._x/svn-distro/*`' .
				' into `./._x/svn-repo`.'
			);
		}
		// Copies `./._x/svn-distro/trunk` into `./._x/svn-distro/tags/[version]` directory.
		// This copy ignores nothing. Everything is copied without exception.

		if ( ! U\Fs::copy(
			U\Dir::join( $svn_distro_dir, '/trunk' ),
			U\Dir::join( $svn_distro_dir, '/tags/' . $this->project->version )
		) ) {
			throw new U\Exception(
				'Failed to copy `./._x/svn-distro/trunk`' .
				' into `./._x/svn-distro/tags/' . $this->project->version . '`.'
			);
		}
		// Copies `./._x/svn-distro/tags[version]` into `./._x/svn-repo/tags/[version]`.
		// This copy ignores nothing. Everything is copied without exception.

		if ( ! U\Fs::copy(
			U\Dir::join( $svn_distro_dir, '/tags/' . $this->project->version ),
			U\Dir::join( $svn_repo_dir, '/tags/' . $this->project->version )
		) ) {
			throw new U\Exception(
				'Failed to copy `./._x/svn-distro/tags/' . $this->project->version . '`' .
				' into `./._x/svn-repo/tags/' . $this->project->version . '`.'
			);
		}
	}

	/**
	 * Maybe compile WordPress app’s zip file.
	 *
	 * @since 2021-12-15
	 *
	 * @throws U\Exception On any failure.
	 */
	protected function maybe_compile_wp_app_zip() : void {
		if ( ! $this->project->is_wp_project() ) {
			return; // Not applicable.
		}
		$svn_repo_tag_dir = U\Dir::join( $this->project->dir, '/._x/svn-repo/tags/' . $this->project->version );

		if ( ! is_dir( $svn_repo_tag_dir ) ) {
			throw new U\Exception(
				'Failed to zip `./._x/svn-repo/tags/' . $this->project->version . '` directory.' .
				' Directory is missing: `' . $svn_repo_tag_dir . '`.'
			);
		}
		$zip_basename = $this->project->slug . '-v' . $this->project->version . '.zip';
		$zip_path     = U\Dir::join( $this->project->dir, '/._x/svn-distro-zips/' . $zip_basename );

		if ( ! U\Fs::zip( $svn_repo_tag_dir . '->' . $this->project->slug, $zip_path ) ) {
			throw new U\Exception(
				'Failed to zip `./._x/svn-repo/tags/' . $this->project->version . '` directory.' .
				' From: `' . $svn_repo_tag_dir . '->' . $this->project->slug . '`, to: `' . $zip_path . '`.'
			);
		}
	}

	/**
	 * Maybe upload a WordPress app’s zip file to AWS S3.
	 *
	 * @since 2021-12-15
	 *
	 * @throws U\Exception On any failure.
	 * @throws \Throwable On some failures.
	 */
	protected function maybe_s3_upload_wp_app_zip() : void {
		if ( ! $this->project->is_wp_project() ) {
			return; // Not applicable.
		}
		$zip_basename = $this->project->slug . '-v' . $this->project->version . '.zip';
		$zip_path     = U\Dir::join( $this->project->dir, '/._x/svn-distro-zips/' . $zip_basename );

		if ( ! is_file( $zip_path ) ) {
			throw new U\Exception( 'Missing zip file: `' . $zip_path . '`.' );
		}
		$s3_zip_hash           = $this->project->s3_hash_hmac_sha256( $this->project->unbranded_slug . $this->project->version );
		$s3_zip_file_subpath   = 'cdn/product/' . $this->project->unbranded_slug . '/zips/' . $s3_zip_hash . '/' . $zip_basename;
		$s3_index_file_subpath = 'cdn/product/' . $this->project->unbranded_slug . '/data/index.json';

		$s3 = new S3Client( $this->project->s3_bucket_config() );

		// Get index w/ tagged versions.

		try {
			$_s3r     = $s3->getObject( [
				'Bucket' => $this->project->s3_bucket(),
				'Key'    => $s3_index_file_subpath,
			] );
			$s3_index = U\Str::json_decode( (string) $_s3r->get( 'Body' ) );

			if ( ! is_object( $s3_index )
				|| ! isset( $s3_index->versions->tags )
				|| ! isset( $s3_index->versions->stable_tag )
				|| ! is_object( $s3_index->versions->tags )
				|| ! is_string( $s3_index->versions->stable_tag )
			) {
				throw new U\Exception(
					'Unable to retrieve valid JSON data from: ' .
					' `' . U\Dir::join( 's3://' . $this->project->s3_bucket(), '/' . $s3_index_file_subpath ) . '`.'
				);
			}
		} catch ( \Throwable $throwable ) {
			if ( ! $throwable instanceof AwsException ) {
				throw $throwable; // Problem.
			}
			if ( 'NoSuchKey' !== $throwable->getAwsErrorCode() ) {
				throw $throwable; // Problem.
			}
			$s3_index = (object) [
				'versions' => (object) [
					'tags'       => (object) [],
					'stable_tag' => '',
				],
			]; // No index file yet, we'll create below.
		}
		// Upload zip file. Throws exception on failure, which we intentionally do not catch.

		$s3->putObject( [
			'SourceFile' => $zip_path,
			'Bucket'     => $this->project->s3_bucket(),
			'Key'        => $s3_zip_file_subpath,
		] );
		// Update index w/ tagged versions.
		// Throws exception on failure, which we intentionally do not catch.

		$s3_index->versions->tags = (array) $s3_index->versions->tags;
		$s3_index->versions->tags = array_merge( $s3_index->versions->tags, [ $this->project->version => time() ] );

		uksort( $s3_index->versions->tags, 'version_compare' ); // Example: <https://3v4l.org/QitGb>.
		$s3_index->versions->tags = array_reverse( $s3_index->versions->tags );

		$s3_index->versions->stable_tag = $this->project->stable_tag;

		$s3->putObject( [
			'Body'   => U\Str::json_encode( $s3_index ),
			'Bucket' => $this->project->s3_bucket(),
			'Key'    => $s3_index_file_subpath,
		] );
	}
}
