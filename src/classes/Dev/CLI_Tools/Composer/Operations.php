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
 * Base operations class.
 *
 * @since 2021-12-15
 */
abstract class Operations extends U\Dev\CLI_Tools\Composer\Operations {
	/**
	 * Maybe symlink WordPress app locally.
	 *
	 * @since 2021-12-15
	 *
	 * @throws U\Fatal_Exception On any failure.
	 */
	protected function maybe_symlink_wp_app_locally() : void {
		U\CLI::output( '[' . __FUNCTION__ . '()]: Maybe; looking ...' );

		if ( ! $this->project->is_wp_project() ) {
			return; // Not applicable.
		}
		if ( $this->project->is_wp_plugin() ) {
			$app = $this->project->wp_plugin_data();
		} elseif ( $this->project->is_wp_theme() ) {
			$app = $this->project->wp_theme_data();
		} else {
			throw new U\Fatal_Exception( 'Unknown WordPress app type.' );
		}
		if ( ! $local_wp_public_html_dir = $this->project->local_wp_public_html_dir() ) {
			U\CLI::log( '[' . __FUNCTION__ . '()]: No local WP public HTML directory in `~/.dev.json`.' );
			return; // Not possible.
		}
		if ( $this->project->is_wp_plugin() ) {
			$local_dir = U\Dir::join( $local_wp_public_html_dir, '/wp-content/plugins/' . $this->project->slug );
		} elseif ( $this->project->is_wp_theme() ) {
			$local_dir = U\Dir::join( $local_wp_public_html_dir, '/wp-content/themes/' . $this->project->slug );
		} else {
			throw new U\Fatal_Exception( 'Unknown WordPress app type.' );
		}
		if ( U\Fs::exists( $local_dir ) ) {
			U\CLI::log( '[' . __FUNCTION__ . '()]: Path exists: `' . $local_dir . '` — leaving as-is.' );
			return; // Do not overwrite existing path.
		}
		if ( ! is_writable( U\Dir::name( $local_dir ) ) ) {
			throw new U\Fatal_Exception( 'Local WordPress symlink failure. Directory not writable: `' . U\Dir::name( $local_dir ) . '`.' );
		}
		if ( ! U\Fs::make_link( $app->dir, $local_dir, [], true, false ) ) {
			throw new U\Fatal_Exception( 'Unexpected local WordPress symlink failure: `' . $local_dir . '`.' );
		}
		U\CLI::log( '[' . __FUNCTION__ . '()]: Symlinked: `' . $local_dir . '`' . "\n" . ' →  `' . $app->dir . '`.' );
	}

	/**
	 * Maybe run WordPress app’s `$ composer install`.
	 *
	 * @since 2021-12-15
	 */
	protected function maybe_run_wp_app_composer_install() : void {
		U\CLI::output( '[' . __FUNCTION__ . '()]: Maybe; looking ...' );

		if ( ! $this->project->is_wp_project()
			// Special exception for WP Groove framework.
			// It's not a WP project, strictly speaking, but it
			// has a `trunk/plugin.php` file for testing purposes.
			&& 'clevercanyon/wpgroove-framework' !== $this->project->pkg_name
			&& 'clevercanyon/wpgroove-framework-pro' !== $this->project->pkg_name
		) {
			return; // Not applicable.
		}
		if ( $this->project->has_file( 'trunk/composer.json' ) ) {
			U\CLI::run( [ 'composer', 'install', '--no-interaction' ], U\Dir::join( $this->project->dir, '/trunk' ) );
		}
		U\CLI::log( '[' . __FUNCTION__ . '()]: Ran `composer install` in `' . U\Dir::join( $this->project->dir, '/trunk' ) . '`.' );
	}

	/**
	 * Maybe run WordPress app’s `$ composer update`.
	 *
	 * @since 2021-12-15
	 */
	protected function maybe_run_wp_app_composer_update() : void {
		U\CLI::output( '[' . __FUNCTION__ . '()]: Maybe; looking ...' );

		if ( ! $this->project->is_wp_project()
			// Special exception for WP Groove framework.
			// It's not a WP project, strictly speaking, but it
			// has a `trunk/plugin.php` file for testing purposes.
			&& 'clevercanyon/wpgroove-framework' !== $this->project->pkg_name
			&& 'clevercanyon/wpgroove-framework-pro' !== $this->project->pkg_name
		) {
			return; // Not applicable.
		}
		if ( $this->project->has_file( 'trunk/composer.json' ) ) {
			U\CLI::run( [ 'composer', 'update', '--no-interaction' ], U\Dir::join( $this->project->dir, '/trunk' ) );
		}
		U\CLI::log( '[' . __FUNCTION__ . '()]: Ran `composer update` in `' . U\Dir::join( $this->project->dir, '/trunk' ) . '`.' );
	}

	/**
	 * Maybe sync WordPress plugin headers.
	 *
	 * @since 2021-12-15
	 *
	 * @throws U\Fatal_Exception On any failure.
	 */
	protected function maybe_sync_wp_plugin_headers() : void {
		U\CLI::output( '[' . __FUNCTION__ . '()]: Maybe; looking ...' );

		if ( ! $this->project->is_wp_plugin() ) {
			return; // Not applicable.
		}
		$plugin           = $this->project->wp_plugin_data();
		$local_wp_version = $this->project->local_wp_version();

		if ( $local_wp_version && version_compare( $local_wp_version, $plugin->headers->tested_up_to_wp_version, '>' ) ) {
			$plugin->headers->tested_up_to_wp_version = $local_wp_version;
		}
		// The existence of these files already confimred by {@see Project::wp_plugin_data()}.

		$plugin_file_contents        = U\File::read( $plugin->file );
		$plugin_readme_file_contents = U\File::read( $plugin->readme_file );

		foreach ( $plugin->headers->_map as $_prop => $_header ) {
			if ( in_array( $_prop, [ 'version', 'stable_tag', 'name' ], true ) ) {
				$plugin->headers->{$_prop} = $this->project->{$_prop}; // Sync these w/ project data.
			}
			$plugin_file_contents        = preg_replace( '/^(\h*\*\h*)?' . U\Str::esc_regexp( $_header ) . '\:\h*.*$/umi', '${1}' . $_header . ': ' . $plugin->headers->{$_prop}, $plugin_file_contents );
			$plugin_readme_file_contents = preg_replace( '/^(\h*)?' . U\Str::esc_regexp( $_header ) . '\:\h*.*$/umi', '${1}' . $_header . ': ' . $plugin->headers->{$_prop}, $plugin_readme_file_contents );

			if ( 'name' === $_prop ) { // Updates plugin name in `readme.txt` file.
				$plugin_readme_file_contents = preg_replace( '/^\={3}[^=\v]*\={3}/ui', '=== ' . $plugin->headers->{$_prop} . ' ===', $plugin_readme_file_contents, 1 );
			}
		}
		$plugin_file_contents = preg_replace( '/^(\h*)["\'][^"\']*["\']\h*(,)?\h*\/\/\h*@slug\h*$/uim', '${1}' . "'" . U\Str::esc_sq( $this->project->slug ) . "'" . '${2} // @slug', $plugin_file_contents );
		$plugin_file_contents = preg_replace( '/^(\h*)["\'][^"\']*["\']\h*(,)?\h*\/\/\h*@name\h*$/uim', '${1}' . "'" . U\Str::esc_sq( $this->project->name ) . "'" . '${2} // @name', $plugin_file_contents );
		$plugin_file_contents = preg_replace( '/^(\h*)["\'][^"\']*["\']\h*(,)?\h*\/\/\h*@version\h*$/uim', '${1}' . "'" . U\Str::esc_sq( $this->project->version ) . "'" . '${2} // @version', $plugin_file_contents );

		if ( ! U\File::write( $plugin->file, $plugin_file_contents, false ) ) {
			throw new U\Fatal_Exception( 'Unable to update plugin file when syncing versions: ' . $plugin->file );
		}
		if ( ! U\File::write( $plugin->readme_file, $plugin_readme_file_contents, false ) ) {
			throw new U\Fatal_Exception( 'Unable to update plugin readme file when syncing versions: ' . $plugin->readme_file );
		}
		U\CLI::log(
			'[' . __FUNCTION__ . '()]: The following files are now in sync:' . "\n" .
			'`' . $plugin->file . '`' . "\n" .
			'`' . $plugin->readme_file . '`'
		);
	}

	/**
	 * Maybe sync WordPress theme headers.
	 *
	 * @since 2021-12-15
	 *
	 * @throws U\Fatal_Exception On any failure.
	 */
	protected function maybe_sync_wp_theme_headers() : void {
		U\CLI::output( '[' . __FUNCTION__ . '()]: Maybe; looking ...' );

		if ( ! $this->project->is_wp_theme() ) {
			return; // Not applicable.
		}
		$theme            = $this->project->wp_theme_data();
		$local_wp_version = $this->project->local_wp_version();

		if ( $local_wp_version && version_compare( $local_wp_version, $theme->headers->tested_up_to_wp_version, '>' ) ) {
			$theme->headers->tested_up_to_wp_version = $local_wp_version;
		}
		// The existence of these files already confimred by {@see Project::wp_theme_data()}.

		$theme_file_contents           = U\File::read( $theme->file );
		$theme_functions_file_contents = U\File::read( $theme->functions_file );
		$theme_style_file_contents     = U\File::read( $theme->style_file );
		$theme_readme_file_contents    = U\File::read( $theme->readme_file );

		foreach ( $theme->headers->_map as $_prop => $_header ) {
			if ( in_array( $_prop, [ 'version', 'stable_tag', 'name' ], true ) ) {
				$theme->headers->{$_prop} = $this->project->{$_prop}; // Sync these w/ project data.
			}
			$theme_file_contents           = preg_replace( '/^(\h*\*\h*)?' . U\Str::esc_regexp( $_header ) . '\:\h*.*$/umi', '${1}' . $_header . ': ' . $theme->headers->{$_prop}, $theme_file_contents );
			$theme_functions_file_contents = preg_replace( '/^(\h*\*\h*)?' . U\Str::esc_regexp( $_header ) . '\:\h*.*$/umi', '${1}' . $_header . ': ' . $theme->headers->{$_prop}, $theme_functions_file_contents );
			$theme_style_file_contents     = preg_replace( '/^(\h*\*\h*)?' . U\Str::esc_regexp( $_header ) . '\:\h*.*$/umi', '${1}' . $_header . ': ' . $theme->headers->{$_prop}, $theme_style_file_contents );
			$theme_readme_file_contents    = preg_replace( '/^(\h*)?' . U\Str::esc_regexp( $_header ) . '\:\h*.*$/umi', '${1}' . $_header . ': ' . $theme->headers->{$_prop}, $theme_readme_file_contents );

			if ( 'name' === $_prop ) { // Updates theme name in `readme.txt` file.
				$theme_readme_file_contents = preg_replace( '/^\={3}[^=\v]*\={3}/ui', '=== ' . $theme->headers->{$_prop} . ' ===', $theme_readme_file_contents, 1 );
			}
		}
		$theme_file_contents = preg_replace( '/^(\h*)["\'][^"\']*["\']\h*(,)?\h*\/\/\h*@slug\h*$/uim', '${1}' . "'" . U\Str::esc_sq( $this->project->slug ) . "'" . '${2} // @slug', $theme_file_contents );
		$theme_file_contents = preg_replace( '/^(\h*)["\'][^"\']*["\']\h*(,)?\h*\/\/\h*@name\h*$/uim', '${1}' . "'" . U\Str::esc_sq( $this->project->name ) . "'" . '${2} // @name', $theme_file_contents );
		$theme_file_contents = preg_replace( '/^(\h*)["\'][^"\']*["\']\h*(,)?\h*\/\/\h*@version\h*$/uim', '${1}' . "'" . U\Str::esc_sq( $this->project->version ) . "'" . '${2} // @version', $theme_file_contents );

		$theme_functions_file_contents = preg_replace( '/^(\h*)["\'][^"\']*["\']\h*(,)?\h*\/\/\h*@slug\h*$/uim', '${1}' . "'" . U\Str::esc_sq( $this->project->slug ) . "'" . '${2} // @slug', $theme_functions_file_contents );
		$theme_functions_file_contents = preg_replace( '/^(\h*)["\'][^"\']*["\']\h*(,)?\h*\/\/\h*@name\h*$/uim', '${1}' . "'" . U\Str::esc_sq( $this->project->name ) . "'" . '${2} // @name', $theme_functions_file_contents );
		$theme_functions_file_contents = preg_replace( '/^(\h*)["\'][^"\']*["\']\h*(,)?\h*\/\/\h*@version\h*$/uim', '${1}' . "'" . U\Str::esc_sq( $this->project->version ) . "'" . '${2} // @version', $theme_functions_file_contents );

		if ( ! U\File::write( $theme->file, $theme_file_contents, false ) ) {
			throw new U\Fatal_Exception( 'Unable to update theme file when syncing versions: ' . $theme->file );
		}
		if ( ! U\File::write( $theme->functions_file, $theme_functions_file_contents, false ) ) {
			throw new U\Fatal_Exception( 'Unable to update theme functions file when syncing versions: ' . $theme->functions_file );
		}
		if ( ! U\File::write( $theme->style_file, $theme_style_file_contents, false ) ) {
			throw new U\Fatal_Exception( 'Unable to update theme style file when syncing versions: ' . $theme->style_file );
		}
		if ( ! U\File::write( $theme->readme_file, $theme_readme_file_contents, false ) ) {
			throw new U\Fatal_Exception( 'Unable to update theme readme file when syncing versions: ' . $theme->readme_file );
		}
		U\CLI::log(
			'[' . __FUNCTION__ . '()]: The following files are now in sync:' . "\n" .
			'`' . $theme->file . '`' . "\n" .
			'`' . $theme->functions_file . '`' . "\n" .
			'`' . $theme->style_file . '`' . "\n" .
			'`' . $theme->readme_file . '`'
		);
	}

	/**
	 * Maybe compile WordPress app’s SVN repo.
	 *
	 * Regarding use of `--no-plugins` in Composer calls below.
	 * {@see https://github.com/humbug/php-scoper#composer-plugins}.
	 *
	 * @since 2021-12-15
	 *
	 * @throws U\Fatal_Exception On any failure.
	 */
	protected function maybe_compile_wp_app_svn_repo() : void {
		U\CLI::output( '[' . __FUNCTION__ . '()]: Maybe; looking ...' );

		if ( ! $this->project->is_wp_project() ) {
			return; // Not applicable.
		}
		if ( $this->project->is_wp_plugin() ) {
			$app = $this->project->wp_plugin_data();

		} elseif ( $this->project->is_wp_theme() ) {
			$app = $this->project->wp_theme_data();
		} else {
			throw new U\Fatal_Exception( 'Unknown WordPress app type.' );
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
			throw new U\Fatal_Exception( 'Failed to create `./._x/svn-comp`.' );
		}
		U\CLI::log( '[' . __FUNCTION__ . '()]: Copied: `' . $this->project->dir . '`' . "\n" . ' →  `' . $svn_comp_dir . '`.' );

		// Installs composer dependencies in `._x/svn-comp/trunk`.
		// Good that we didn't ignore `composer.json|lock` when copying.
		// The autoloader is optimized here, as we are compiling for production.

		U\CLI::run( [
			[ 'composer', 'install', '--no-interaction' ],
			[ '--profile', '--no-dev', '--no-scripts', '--no-plugins' ],
			[ '--optimize-autoloader', '--classmap-authoritative' ],
		], U\Dir::join( $svn_comp_dir, '/trunk' ) );
		U\CLI::log( '[' . __FUNCTION__ . '()]: Ran `composer install` in: `' . U\Dir::join( $svn_comp_dir, '/trunk' ) . '`.' );

		// Prunes the `./._x/svn-comp` directory, which speeds up remaining tasks.
		// This prunes everything in `.gitignore`, except: `vendor` (off by default) and `composer.json|lock`.
		// It also prunes a bunch of other things; {@see Project::comp_dir_prune_config()}.

		if ( ! U\Dir::prune(
			$svn_comp_dir,
			$comp_dir_prune_config[ 'prune' ],
			array_merge( $comp_dir_prune_config[ 'exceptions' ], [
				'/(?:^|.+\/)composer\.(?:json|lock)$/ui',
			] ),
		) ) {
			throw new U\Fatal_Exception( 'Failed to prune `./._x/svn-comp`.' );
		}
		U\CLI::log( '[' . __FUNCTION__ . '()]: Pruned: `' . $svn_comp_dir . '`.' );

		// Adds text domain to everything in `._x/svn-comp/trunk`.
		// This tool ignores everything in `.gitignore`, except `vendor/clevercanyon/*`.
		// i.e., We're adding text domain to all clevercanyon packages, including the WP Groove framework.

		if ( 'clevercanyon/wpgroove-framework' === $this->project->pkg_name ) {
			$text_domain_bin_script = U\Dir::join( $this->project->dir, '/dev/cli-tools/i18n/text-domain' );
		} else {
			$text_domain_bin_script = U\Dir::join( $this->project->dir, '/vendor/clevercanyon/wpgroove-framework/dev/cli-tools/i18n/text-domain' );
		}
		U\CLI::run( [
			[ $text_domain_bin_script, 'add' ],
			[ '--project-dir', $this->project->dir ],
			[ '--work-dir', U\Dir::join( $svn_comp_dir, '/trunk' ) ],
			[ '--text-domain', $app->headers->text_domain ],
		] );
		U\CLI::log( '[' . __FUNCTION__ . '()]: Added text domain to: `' . U\Dir::join( $svn_comp_dir, '/trunk' ) . '`.' );

		// Runs PHP Scoper on full `._x/svn-comp` directory; outputting to `._x/svn-distro`.
		// PHP Scoper can expose, exclude, and apply patchers. To learn more, see `.scoper.cfg.php` file.
		// We're not excluding anything extra special here, though, as we have already pruned the directory.

		$scoper_bin_script = U\Dir::join( $this->project->dir, '/vendor/clevercanyon/utilities/dev/cli-tools/php-scoper/scoper' );

		U\CLI::run( [
			[ $scoper_bin_script, 'scope' ],
			[ '--project-dir', $this->project->dir ],
			[ '--prefix', $this->project->namespace_scope ],

			[ '--work-dir', $svn_comp_dir ],
			[ '--output-dir', $svn_distro_dir ],

			[ '--output-project-dir', U\Dir::join( $svn_distro_dir, '/trunk' ) ],
			[ '--output-project-dir-entry-file', U\Dir::join( $svn_distro_dir, '/trunk/' . basename( $app->file ) ) ],
		] );
		U\CLI::log( '[' . __FUNCTION__ . '()]: Scoped: `' . $svn_comp_dir . '`' . "\n" . ' →  `' . $svn_distro_dir . '`.' );

		// Prunes the `./._x/svn-distro` directory now.
		// This prunes everything in `.gitignore`, except `vendor` (off by default).
		// It also prunes a bunch of other things; {@see Project::comp_dir_prune_config()}.

		if ( ! U\Dir::prune(
			$svn_distro_dir,
			$comp_dir_prune_config[ 'prune' ],
			$comp_dir_prune_config[ 'exceptions' ]
		) ) {
			throw new U\Fatal_Exception( 'Failed to prune `./._x/svn-distro`.' );
		}
		U\CLI::log( '[' . __FUNCTION__ . '()]: Pruned: `' . $svn_distro_dir . '`.' );

		// Copies contents of `./._x/svn-distro/*` into `./._x/svn-repo/` directory.
		// This copy ignores nothing. Everything is copied without exception.

		if ( ! U\Fs::copy(
			U\Dir::join( $svn_distro_dir, '/*' ),
			$svn_repo_dir
		) ) {
			throw new U\Fatal_Exception(
				'Failed to copy contents of `./._x/svn-distro/*`' .
				' into `./._x/svn-repo`.'
			);
		}
		U\CLI::log(
			'[' . __FUNCTION__ . '()]: Copied: `' . U\Dir::join( $svn_distro_dir, '/*' ) . '`' . "\n"
			. ' →  `' . $svn_repo_dir . '`.'
		);
		// Copies `./._x/svn-distro/trunk` into `./._x/svn-distro/tags/[version]` directory.
		// This copy ignores nothing. Everything is copied without exception.

		if ( ! U\Fs::copy(
			U\Dir::join( $svn_distro_dir, '/trunk' ),
			U\Dir::join( $svn_distro_dir, '/tags/' . $this->project->version )
		) ) {
			throw new U\Fatal_Exception(
				'Failed to copy `./._x/svn-distro/trunk`' .
				' into `./._x/svn-distro/tags/' . $this->project->version . '`.'
			);
		}
		U\CLI::log(
			'[' . __FUNCTION__ . '()]: Copied: `' . U\Dir::join( $svn_distro_dir, '/trunk' ) . '`' . "\n" .
			' →  `' . U\Dir::join( $svn_distro_dir, '/tags/' . $this->project->version ) . '`.'
		);
		// Copies `./._x/svn-distro/tags[version]` into `./._x/svn-repo/tags/[version]`.
		// This copy ignores nothing. Everything is copied without exception.

		if ( ! U\Fs::copy(
			U\Dir::join( $svn_distro_dir, '/tags/' . $this->project->version ),
			U\Dir::join( $svn_repo_dir, '/tags/' . $this->project->version )
		) ) {
			throw new U\Fatal_Exception(
				'Failed to copy `./._x/svn-distro/tags/' . $this->project->version . '`' .
				' into `./._x/svn-repo/tags/' . $this->project->version . '`.'
			);
		}
		U\CLI::log(
			'[' . __FUNCTION__ . '()]: Copied: `' . U\Dir::join( $svn_distro_dir, '/tags/' . $this->project->version ) . '`' . "\n" .
			' →  `' . U\Dir::join( $svn_repo_dir, '/tags/' . $this->project->version ) . '`.'
		);
	}

	/**
	 * Maybe compile WordPress app’s distro tests.
	 *
	 * Regarding use of `--no-plugins` in Composer calls below.
	 * {@see https://github.com/humbug/php-scoper#composer-plugins}.
	 *
	 * @since 2021-12-15
	 *
	 * @throws U\Fatal_Exception On any failure.
	 */
	protected function maybe_compile_wp_app_distro_tests() : void {
		U\CLI::output( '[' . __FUNCTION__ . '()]: Maybe; looking ...' );

		if ( ! $this->project->is_wp_project() ) {
			return; // Not applicable.
		}
		if ( $this->project->is_wp_plugin() ) {
			$app = $this->project->wp_plugin_data();

		} elseif ( $this->project->is_wp_theme() ) {
			$app = $this->project->wp_theme_data();
		} else {
			throw new U\Fatal_Exception( 'Unknown WordPress app type.' );
		}
		$comp_dir_copy_config  = $this->project->comp_dir_copy_config();
		$comp_dir_prune_config = $this->project->comp_dir_prune_config();

		$comp_tests_dir   = U\Dir::join( $this->project->dir, '/._x/comp-tests' );
		$distro_tests_dir = U\Dir::join( $this->project->dir, '/._x/distro-tests' );

		// Copies project directory into `._x/comp-tests`.
		// This copy ignores everything in `.gitignore`, and nothing else.
		// For further details {@see Project::comp_dir_copy_config()}.

		if ( ! U\Fs::copy(
			$this->project->dir,
			$comp_tests_dir,
			$comp_dir_copy_config[ 'ignore' ],
			$comp_dir_copy_config[ 'exceptions' ]
		) ) {
			throw new U\Fatal_Exception( 'Failed to create `./._x/comp-tests`.' );
		}
		U\CLI::log( '[' . __FUNCTION__ . '()]: Copied: `' . $this->project->dir . '`' . "\n" . ' →  `' . $comp_tests_dir . '`.' );

		// Installs composer dependencies in `._x/comp-tests` and `._x/comp-tests/trunk`.
		// Good that we didn't ignore `composer.json|lock` when copying all of these directories.
		// This locates a PSR-4 `\Tests\$` prefix in `autoload-dev` and copies it to the `autoload` prop.
		// This way it's possible to run `composer install --no-dev`, but still get autoloading for tests.
		// The autoloader is optimized here, as we are compiling for pseudo-production tests.

		$comp_tests_composer_json_file = U\Dir::join( $comp_tests_dir, '/composer.json' );
		$comp_tests_composer_json      = U\File::read_json_obj( $comp_tests_composer_json_file );

		if ( isset( $comp_tests_composer_json->{'autoload-dev'}->{'psr-4'} ) ) {
			foreach ( $comp_tests_composer_json->{'autoload-dev'}->{'psr-4'} as $_prefix => $_dir ) {
				if ( U\Str::ends_with( $_prefix, '\\Tests\\' ) ) {
					$comp_tests_composer_json->{'autoload'}                        ??= (object) [];
					$comp_tests_composer_json->{'autoload'}->{'psr-4'}             ??= (object) [];
					$comp_tests_composer_json->{'autoload'}->{'psr-4'}->{$_prefix} = $_dir;
				}
			}
			U\File::write(
				$comp_tests_composer_json_file,
				U\Str::json_encode( $comp_tests_composer_json )
			);
		}
		U\CLI::run( [
			[ 'composer', 'install', '--no-interaction' ],
			[ '--profile', '--no-dev', '--no-scripts', '--no-plugins' ],
			[ '--optimize-autoloader', '--classmap-authoritative' ],
		], $comp_tests_dir );
		U\CLI::log( '[' . __FUNCTION__ . '()]: Ran `composer install` in: `' . $comp_tests_dir . '`.' );

		U\CLI::run( [
			[ 'composer', 'install', '--no-interaction' ],
			[ '--profile', '--no-dev', '--no-scripts', '--no-plugins' ],
			[ '--optimize-autoloader', '--classmap-authoritative' ],
		], U\Dir::join( $comp_tests_dir, '/trunk' ) );
		U\CLI::log( '[' . __FUNCTION__ . '()]: Ran `composer install` in: `' . U\Dir::join( $comp_tests_dir, '/trunk' ) . '`.' );

		// Prunes the `./._x/comp-tests` directory, which speeds up remaining tasks.
		// This prunes everything in `.gitignore`, except: `vendor` (including root), `tests`, and `composer.json|lock`.
		// It also prunes a bunch of other things; {@see Project::comp_dir_prune_config()}.

		if ( ! U\Dir::prune(
			$comp_tests_dir,
			$comp_dir_prune_config[ 'prune' ],
			array_merge( $comp_dir_prune_config[ 'exceptions' ], [
				'/^vendor(?:$|\/)/ui',
				'/^tests(?:$|\/)/ui',
				'/(?:^|.+\/)composer\.(?:json|lock)$/ui',
			] ),
		) ) {
			throw new U\Fatal_Exception( 'Failed to prune `./._x/comp-tests`.' );
		}
		U\CLI::log( '[' . __FUNCTION__ . '()]: Pruned: `' . $comp_tests_dir . '`.' );

		// Adds text domain to everything in `._x/comp-tests/trunk`.
		// This tool ignores everything in `.gitignore`, except `vendor/clevercanyon/*`.
		// i.e., We're adding text domain to all clevercanyon packages, including the WP Groove framework.

		if ( 'clevercanyon/wpgroove-framework' === $this->project->pkg_name ) {
			$text_domain_bin_script = U\Dir::join( $this->project->dir, '/dev/cli-tools/i18n/text-domain' );
		} else {
			$text_domain_bin_script = U\Dir::join( $this->project->dir, '/vendor/clevercanyon/wpgroove-framework/dev/cli-tools/i18n/text-domain' );
		}
		U\CLI::run( [
			[ $text_domain_bin_script, 'add' ],
			[ '--project-dir', $this->project->dir ],
			[ '--work-dir', U\Dir::join( $comp_tests_dir, '/trunk' ) ],
			[ '--text-domain', $app->headers->text_domain ],
		] );
		U\CLI::log( '[' . __FUNCTION__ . '()]: Added text domain to: `' . U\Dir::join( $comp_tests_dir, '/trunk' ) . '`.' );

		// Runs PHP Scoper on full `._x/comp-tests` directory; outputting to `._x/distro-tests`.
		// PHP Scoper can expose, exclude, and apply patchers. To learn more, see `.scoper.cfg.php` file.
		// We're not excluding anything extra special here, though, as we have already pruned the directory.

		$scoper_bin_script = U\Dir::join( $this->project->dir, '/vendor/clevercanyon/utilities/dev/cli-tools/php-scoper/scoper' );

		U\CLI::run( [
			[ $scoper_bin_script, 'scope' ],
			[ '--project-dir', $this->project->dir ],
			[ '--prefix', $this->project->namespace_scope ],

			[ '--work-dir', $comp_tests_dir ],
			[ '--output-dir', $distro_tests_dir ],

			[ '--output-project-dir', $distro_tests_dir ],
			[ '--output-project-dir', U\Dir::join( $distro_tests_dir, '/trunk' ) ],
			[ '--output-project-dir-entry-file', U\Dir::join( $distro_tests_dir, '/trunk/' . basename( $app->file ) ) ],
		] );
		U\CLI::log( '[' . __FUNCTION__ . '()]: Scoped: `' . $comp_tests_dir . '`' . "\n" . ' →  `' . $distro_tests_dir . '`.' );

		// Prunes the `./._x/distro-tests` directory now.
		// This prunes everything in `.gitignore`, except `vendor` (including root), and `tests`.
		// It also prunes a bunch of other things; {@see Project::comp_dir_prune_config()}.

		if ( ! U\Dir::prune(
			$distro_tests_dir,
			$comp_dir_prune_config[ 'prune' ],
			array_merge( $comp_dir_prune_config[ 'exceptions' ], [
				'/^vendor(?:$|\/)/ui',
				'/^tests(?:$|\/)/ui',
			] ),
		) ) {
			throw new U\Fatal_Exception( 'Failed to prune `./._x/distro-tests`.' );
		}
		U\CLI::log( '[' . __FUNCTION__ . '()]: Pruned: `' . $distro_tests_dir . '`.' );
	}

	/**
	 * Maybe compile WordPress app’s zip file.
	 *
	 * @since 2021-12-15
	 *
	 * @throws U\Fatal_Exception On any failure.
	 */
	protected function maybe_compile_wp_app_zip() : void {
		U\CLI::output( '[' . __FUNCTION__ . '()]: Maybe; looking ...' );

		if ( ! $this->project->is_wp_project() ) {
			return; // Not applicable.
		}
		$svn_repo_tag_dir = U\Dir::join( $this->project->dir, '/._x/svn-repo/tags/' . $this->project->version );

		if ( ! is_dir( $svn_repo_tag_dir ) ) {
			throw new U\Fatal_Exception(
				'Failed to zip `./._x/svn-repo/tags/' . $this->project->version . '` directory.' .
				' Directory is missing: `' . $svn_repo_tag_dir . '`.'
			);
		}
		$file_name = $this->project->slug . '-v' . $this->project->version . '.zip';
		$file_path = U\Dir::join( $this->project->dir, '/._x/svn-distro-zips/' . $file_name );

		if ( ! U\Fs::zip_er( $svn_repo_tag_dir . '->' . $this->project->slug, $file_path ) ) {
			throw new U\Fatal_Exception(
				'Failed to zip `./._x/svn-repo/tags/' . $this->project->version . '` directory.' .
				' From: `' . $svn_repo_tag_dir . '->' . $this->project->slug . '`, to: `' . $file_path . '`.'
			);
		}
		U\CLI::log( '[' . __FUNCTION__ . '()]: Zipped: `' . $svn_repo_tag_dir . '`' . "\n" . ' →  `' . $file_path . '`.' );
	}

	/**
	 * Maybe upload a WordPress app’s zip file to AWS S3.
	 *
	 * @since 2021-12-15
	 *
	 * @throws U\Fatal_Exception On any failure.
	 * @throws \Throwable On some failures.
	 */
	protected function maybe_s3_upload_wp_app_zip() : void {
		U\CLI::output( '[' . __FUNCTION__ . '()]: Maybe; looking ...' );

		if ( ! $this->project->is_wp_project() ) {
			return; // Not applicable.
		}
		if ( $this->project->is_wp_plugin() ) {
			$app = $this->project->wp_plugin_data();

		} elseif ( $this->project->is_wp_theme() ) {
			$app = $this->project->wp_theme_data();
		} else {
			throw new U\Fatal_Exception( 'Unknown WordPress app type.' );
		}
		$zip_file_name = $this->project->slug . '-v' . $this->project->version . '.zip';
		$zip_file_path = U\Dir::join( $this->project->dir, '/._x/svn-distro-zips/' . $zip_file_name );

		if ( ! is_file( $zip_file_path ) ) {
			throw new U\Fatal_Exception( 'Missing zip file: `' . $zip_file_path . '`.' );
		}
		$s3_zip_file_hash                 = $this->project->s3_hash_hmac_sha256( $this->project->unbranded_slug . $zip_file_name . $this->project->version );
		$s3_zip_file_subpath              = 'cdn/product/' . $this->project->unbranded_slug . '/files/' . $s3_zip_file_hash . '/' . $zip_file_name;
		$s3_distro_zips_json_file_subpath = 'cdn/product/' . $this->project->unbranded_slug . '/data/distro-zips.json';

		$s3 = new S3Client( $this->project->s3_bucket_config() );

		// Get `distro-zips.json` w/ tagged versions.

		try {
			$_s3r                = $s3->getObject( [
				'Bucket' => $this->project->s3_bucket(),
				'Key'    => $s3_distro_zips_json_file_subpath,
			] );
			$s3_distro_zips_json = U\Str::json_decode( (string) $_s3r->get( 'Body' ) );

			if ( ! is_object( $s3_distro_zips_json )

				|| ! isset( $s3_distro_zips_json->headers )
				|| ! is_object( $s3_distro_zips_json->headers )

				|| ! isset( $s3_distro_zips_json->versions->tags )
				|| ! is_object( $s3_distro_zips_json->versions->tags )

				|| ! isset( $s3_distro_zips_json->versions->stable_tag )
				|| ! is_string( $s3_distro_zips_json->versions->stable_tag )
			) {
				throw new U\Fatal_Exception(
					'Unable to retrieve valid JSON data from: ' .
					' `' . U\Dir::join( 's3://' . $this->project->s3_bucket(), '/' . $s3_distro_zips_json_file_subpath ) . '`.'
				);
			}
		} catch ( \Throwable $throwable ) {
			if ( ! $throwable instanceof AwsException ) {
				throw $throwable; // Problem.
			}
			if ( 'NoSuchKey' !== $throwable->getAwsErrorCode() ) {
				throw $throwable; // Problem.
			}
			$s3_distro_zips_json = (object) [
				'headers'  => (object) [],
				'versions' => (object) [
					'tags'       => (object) [],
					'stable_tag' => '',
				],
			]; // No `distro-zips.json` file yet. We'll create below.
		}
		// Upload zip file. Throws exception on failure, which we intentionally do not catch.

		$s3->putObject( [
			'SourceFile' => $zip_file_path,
			'Bucket'     => $this->project->s3_bucket(),
			'Key'        => $s3_zip_file_subpath,
		] );
		U\CLI::log(
			'[' . __FUNCTION__ . '()]: Uploaded: `' . $zip_file_path . '`' . "\n" .
			' →  `' . U\Dir::join( 's3://' . $this->project->s3_bucket(), '/' . $s3_zip_file_subpath ) . '`.'
		);
		// Update headers and tags in `distro-zips.json`.
		// Throws exception on failure, which we intentionally do not catch.

		$s3_distro_zips_json->headers                                   = $app->headers;
		$s3_distro_zips_json->versions->tags                            = (array) $s3_distro_zips_json->versions->tags;
		$s3_distro_zips_json->versions->tags[ $this->project->version ] = (object) [
			'time'                    => U\Time::utc(),
			'version'                 => $this->project->version,
			'requires_php_version'    => $app->headers->requires_php_version,
			'requires_wp_version'     => $app->headers->requires_wp_version,
			'tested_up_to_wp_version' => $app->headers->tested_up_to_wp_version,
		];
		uksort( $s3_distro_zips_json->versions->tags, 'version_compare' ); // {@see https://3v4l.org/QitGb}.
		$s3_distro_zips_json->versions->tags       = (object) array_reverse( $s3_distro_zips_json->versions->tags );
		$s3_distro_zips_json->versions->stable_tag = $this->project->stable_tag;

		$s3->putObject( [
			'Body'   => U\Str::json_encode( $s3_distro_zips_json ),
			'Bucket' => $this->project->s3_bucket(),
			'Key'    => $s3_distro_zips_json_file_subpath,
		] );
		U\CLI::log(
			'[' . __FUNCTION__ . '()]: Updated: `' .
			U\Dir::join( 's3://' . $this->project->s3_bucket(), '/' . $s3_distro_zips_json_file_subpath ) .
			'`.'
		);
	}
}
