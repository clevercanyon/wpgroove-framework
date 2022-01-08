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
declare( strict_types = 1 ); // ｡･:*:･ﾟ★.
namespace WP_Groove\Framework\Dev\Toolchain\Composer\Hooks;

/**
 * Utilities.
 *
 * @since 2021-12-15
 */
use Clever_Canyon\Utilities\{STC as U};
use Clever_Canyon\Utilities\OOP\{Offsets, Generic, Error, Exception, Fatal_Exception};
use Clever_Canyon\Utilities\OOP\Abstracts\{A6t_Base, A6t_Offsets, A6t_Generic, A6t_Error, A6t_Exception};
use Clever_Canyon\Utilities\OOP\Interfaces\{I7e_Base, I7e_Offsets, I7e_Generic, I7e_Error, I7e_Exception};

/**
 * WP Groove utilities.
 *
 * @since 2021-12-15
 */
use WP_Groove\Framework\Utilities\{STC as UU};
use WP_Groove\Framework\Plugin\Abstracts\{AA6t_Plugin};
use WP_Groove\Framework\Utilities\OOP\Abstracts\{AA6t_App};

/**
 * Toolchain.
 *
 * @since 2021-12-15
 */
use Clever_Canyon\Utilities\Dev\Toolchain\{Tools as T};
use Clever_Canyon\Utilities\Dev\Toolchain\Composer\{Project};

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
class On_Post_Update_Cmd extends \Clever_Canyon\Utilities\OOP\Abstracts\A6t_CLI_Tool {
	/**
	 * Project.
	 *
	 * @since 2021-12-15
	 */
	protected Project $project;

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
	protected const NAME = 'Hook/Post_Update_Cmd_Handler';

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
						'validator'   => fn( $value ) => is_dir( $value ),
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
			$this->project = new Project(
				$this->get_option( 'project-dir' )
			);
			$this->maybe_run_wp_project_sub_composer_updates();

			$this->maybe_symlink_wp_plugin_locally();
			$this->maybe_symlink_wp_theme_locally();

			$this->maybe_sync_wp_plugin_headers();
			$this->maybe_sync_wp_theme_headers();

			$this->maybe_compile_wp_plugin_svn_repo();
			$this->maybe_compile_wp_theme_svn_repo();

			$this->maybe_compile_wp_plugin_zip();
			$this->maybe_compile_wp_theme_zip();

			$this->maybe_s3_upload_wp_plugin_zip();
			$this->maybe_s3_upload_wp_theme_zip();

		} catch ( \Throwable $throwable ) {
			U\CLI::error( $throwable->getMessage() );
			U\CLI::error( $throwable->getTraceAsString() );
			U\CLI::exit_status( 1 );
		}
	}

	/**
	 * Maybe run WordPress project sub-Composer updates.
	 *
	 * @since 2021-12-15
	 */
	protected function maybe_run_wp_project_sub_composer_updates() : void {
		if ( ! $this->project->is_wp_project() ) {
			return; // Not applicable.
		}
		if ( $this->project->has_file( 'trunk/composer.json' ) ) {
			U\CLI::run( [ 'composer', 'update' ], U\Dir::join( $this->project->dir, '/trunk' ) );
		}
	}

	/**
	 * Maybe symlink WordPress plugin locally.
	 *
	 * @since 2021-12-15
	 *
	 * @throws Exception Whenever any failure occurs.
	 */
	protected function maybe_symlink_wp_plugin_locally() : void {
		if ( ! $this->project->is_wp_plugin() ) {
			return; // Not applicable.
		}
		if ( ! $local_wp_public_html_dir = $this->project->local_wp_public_html_dir() ) {
			return; // Not possible.
		}
		$plugin           = $this->project->wp_plugin_data();
		$local_plugin_dir = U\Dir::join( $local_wp_public_html_dir, '/wp-content/plugins/' . $plugin->slug );

		if ( U\Fs::path_exists( $local_plugin_dir ) ) {
			return; // Do not overwrite.
		}
		if ( ! is_writable( U\Dir::name( $local_plugin_dir ) ) ) {
			throw new Exception( 'Failed to symlink local WordPress plugin directory. Directory not writable: ' . U\Dir::name( $local_plugin_dir ) );
		}
		if ( ! symlink( $plugin->dir, $local_plugin_dir ) ) {
			throw new Exception( 'Failed to symlink local WordPress plugin directory: ' . $local_plugin_dir );
		}
	}

	/**
	 * Maybe symlink WordPress theme locally.
	 *
	 * @since 2021-12-15
	 *
	 * @throws Exception Whenever any failure occurs.
	 */
	protected function maybe_symlink_wp_theme_locally() : void {
		if ( ! $this->project->is_wp_theme() ) {
			return; // Not applicable.
		}
		if ( ! $local_wp_public_html_dir = $this->project->local_wp_public_html_dir() ) {
			return; // Not possible.
		}
		$theme           = $this->project->wp_theme_data();
		$local_theme_dir = U\Dir::join( $local_wp_public_html_dir, '/wp-content/themes/' . $theme->slug );

		if ( U\Fs::path_exists( $local_theme_dir ) ) {
			return; // Do not overwrite.
		}
		if ( ! is_writable( U\Dir::name( $local_theme_dir ) ) ) {
			throw new Exception( 'Failed to symlink local WordPress theme directory. Directory not writable: ' . U\Dir::name( $local_theme_dir ) );
		}
		if ( ! symlink( $theme->dir, $local_theme_dir ) ) {
			throw new Exception( 'Failed to symlink local WordPress theme directory: ' . $local_theme_dir );
		}
	}

	/**
	 * Maybe sync WordPress plugin headers.
	 *
	 * @since 2021-12-15
	 *
	 * @throws Exception Whenever any failure occurs.
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
			$plugin_file_contents        = preg_replace( '/^(\h*\*\h*)?' . U\Str::esc_reg( $_header ) . '\:\h*.*$/umi', '${1}' . $_header . ': ' . $plugin->headers->{$_prop}, $plugin_file_contents );
			$plugin_readme_file_contents = preg_replace( '/^(\h*)?' . U\Str::esc_reg( $_header ) . '\:\h*.*$/umi', '${1}' . $_header . ': ' . $plugin->headers->{$_prop}, $plugin_readme_file_contents );
		}
		$plugin_file_contents = preg_replace( '/^(\h*)["\'][^"\']*["\']\h*(,)?\h*\/\/\h*@slug\h*$/uim', '${1}' . "'" . U\Str::esc_sq( $plugin->slug ) . "'" . '${2} // @slug', $plugin_file_contents );
		$plugin_file_contents = preg_replace( '/^(\h*)["\'][^"\']*["\']\h*(,)?\h*\/\/\h*@name\h*$/uim', '${1}' . "'" . U\Str::esc_sq( $plugin->headers->name ) . "'" . '${2} // @name', $plugin_file_contents );
		$plugin_file_contents = preg_replace( '/^(\h*)["\'][^"\']*["\']\h*(,)?\h*\/\/\h*@version\h*$/uim', '${1}' . "'" . U\Str::esc_sq( $plugin->headers->version ) . "'" . '${2} // @version', $plugin_file_contents );

		if ( false === file_put_contents( $plugin->file, $plugin_file_contents ) ) {
			throw new Exception( 'Unable to update plugin file when syncing versions: ' . $plugin->file );
		}
		if ( false === file_put_contents( $plugin->readme_file, $plugin_readme_file_contents ) ) {
			throw new Exception( 'Unable to update plugin readme file when syncing versions: ' . $plugin->readme_file );
		}
	}

	/**
	 * Maybe sync WordPress theme headers.
	 *
	 * @since 2021-12-15
	 *
	 * @throws Exception Whenever any failure occurs.
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
			$theme_file_contents           = preg_replace( '/^(\h*\*\h*)?' . U\Str::esc_reg( $_header ) . '\:\h*.*$/umi', '${1}' . $_header . ': ' . $theme->headers->{$_prop}, $theme_file_contents );
			$theme_functions_file_contents = preg_replace( '/^(\h*\*\h*)?' . U\Str::esc_reg( $_header ) . '\:\h*.*$/umi', '${1}' . $_header . ': ' . $theme->headers->{$_prop}, $theme_functions_file_contents );
			$theme_style_file_contents     = preg_replace( '/^(\h*\*\h*)?' . U\Str::esc_reg( $_header ) . '\:\h*.*$/umi', '${1}' . $_header . ': ' . $theme->headers->{$_prop}, $theme_style_file_contents );
			$theme_readme_file_contents    = preg_replace( '/^(\h*)?' . U\Str::esc_reg( $_header ) . '\:\h*.*$/umi', '${1}' . $_header . ': ' . $theme->headers->{$_prop}, $theme_readme_file_contents );
		}
		$theme_file_contents = preg_replace( '/^(\h*)["\'][^"\']*["\']\h*(,)?\h*\/\/\h*@slug\h*$/uim', '${1}' . "'" . U\Str::esc_sq( $theme->slug ) . "'" . '${2} // @slug', $theme_file_contents );
		$theme_file_contents = preg_replace( '/^(\h*)["\'][^"\']*["\']\h*(,)?\h*\/\/\h*@name\h*$/uim', '${1}' . "'" . U\Str::esc_sq( $theme->headers->name ) . "'" . '${2} // @name', $theme_file_contents );
		$theme_file_contents = preg_replace( '/^(\h*)["\'][^"\']*["\']\h*(,)?\h*\/\/\h*@version\h*$/uim', '${1}' . "'" . U\Str::esc_sq( $theme->headers->version ) . "'" . '${2} // @version', $theme_file_contents );

		$theme_functions_file_contents = preg_replace( '/^(\h*)["\'][^"\']*["\']\h*(,)?\h*\/\/\h*@slug\h*$/uim', '${1}' . "'" . U\Str::esc_sq( $theme->slug ) . "'" . '${2} // @slug', $theme_functions_file_contents );
		$theme_functions_file_contents = preg_replace( '/^(\h*)["\'][^"\']*["\']\h*(,)?\h*\/\/\h*@name\h*$/uim', '${1}' . "'" . U\Str::esc_sq( $theme->headers->name ) . "'" . '${2} // @name', $theme_functions_file_contents );
		$theme_functions_file_contents = preg_replace( '/^(\h*)["\'][^"\']*["\']\h*(,)?\h*\/\/\h*@version\h*$/uim', '${1}' . "'" . U\Str::esc_sq( $theme->headers->version ) . "'" . '${2} // @version', $theme_functions_file_contents );

		if ( false === file_put_contents( $theme->file, $theme_file_contents ) ) {
			throw new Exception( 'Unable to update theme file when syncing versions: ' . $theme->file );
		}
		if ( false === file_put_contents( $theme->functions_file, $theme_functions_file_contents ) ) {
			throw new Exception( 'Unable to update theme functions file when syncing versions: ' . $theme->functions_file );
		}
		if ( false === file_put_contents( $theme->style_file, $theme_style_file_contents ) ) {
			throw new Exception( 'Unable to update theme style file when syncing versions: ' . $theme->style_file );
		}
		if ( false === file_put_contents( $theme->readme_file, $theme_readme_file_contents ) ) {
			throw new Exception( 'Unable to update theme readme file when syncing versions: ' . $theme->readme_file );
		}
	}

	/**
	 * Maybe compile WordPress plugin's SVN repo.
	 *
	 * @since 2021-12-15
	 *
	 * @throws Exception Whenever any failure occurs.
	 */
	protected function maybe_compile_wp_plugin_svn_repo() : void {
		if ( ! $this->project->is_wp_plugin() ) {
			return; // Not applicable.
		}
		$plugin = $this->project->wp_plugin_data();

		$comp_dir_copy_config    = $this->project->comp_dir_copy_config();
		$distro_dir_prune_config = $this->project->distro_dir_prune_config();

		$plugin_svn_comp_dir   = U\Dir::join( $this->project->dir, '/._x/svn-comp' );
		$plugin_svn_distro_dir = U\Dir::join( $this->project->dir, '/._x/svn-distro' );
		$plugin_svn_repo_dir   = U\Dir::join( $this->project->dir, '/._x/svn-repo' );

		// Regarding use of `--no-plugins` in Composer calls below.
		// {@see https://github.com/humbug/php-scoper#composer-plugins}.

		if ( ! U\Fs::copy( $this->project->dir, $plugin_svn_comp_dir, $comp_dir_copy_config[ 'ignore' ], $comp_dir_copy_config[ 'exceptions' ] ) ) {
			throw new Exception( 'Failed to create project /._x/svn-comp directory.' );
		}
		if ( 0 !== U\CLI::run( [ 'composer', 'install', '--no-dev', '--no-scripts', '--no-plugins', '--optimize-autoloader', '--classmap-authoritative' ], U\Dir::join( $plugin_svn_comp_dir, '/trunk' ), false ) ) {
			throw new Exception( 'Failed to run `composer install --no-dev --no-scripts --no-plugins --optimize-autoloader --classmap-authoritative` from ./._x/svn-comp/trunk directory.' );
		}

		if ( 0 !== U\CLI::run( [ 'composer', 'exec', '--', 'i18n-text-domain', 'add', '--text-domain', $plugin->headers->text_domain, '--dir', $plugin_svn_comp_dir ], $this->project->dir, false ) ) {
			throw new Exception( 'Failed to run `composer exec -- i18n-text-domain add --text-domain ' . $plugin->headers->text_domain . ' --dir ' . $plugin_svn_comp_dir . '` from project directory.' );
		}
		if ( 0 !== U\CLI::run( [ 'composer', 'exec', '--', 'i18n-text-domain', 'add', '--text-domain', $plugin->headers->text_domain, '--dir', U\Dir::join( $plugin_svn_comp_dir, '/trunk/vendor/clevercanyon/wpgroove-framework' ) ], $this->project->dir, false ) ) {
			throw new Exception( 'Failed to run `composer exec -- i18n-text-domain add --text-domain ' . $plugin->headers->text_domain . ' --dir ' . U\Dir::join( $plugin_svn_comp_dir, '/trunk/vendor/clevercanyon/wpgroove-framework' ) . ' from project directory.' );
		}
		if ( 0 !== U\CLI::run( [ 'composer', 'exec', '--', 'php-scoper', 'add-prefix', '--config', U\Dir::join( $plugin_svn_comp_dir, '/.scoper.cfg.php' ), '--prefix', ucfirst( $this->project->name_hash ), '--no-interaction', '--force', '--stop-on-failure', '--working-dir', $plugin_svn_comp_dir, '--output-dir', $plugin_svn_distro_dir, $plugin_svn_comp_dir ], $this->project->dir, false ) ) {
			throw new Exception( 'Failed to run `composer exec -- php-scoper add-prefix --config ' . U\Dir::join( $plugin_svn_comp_dir, '/.scoper.cfg.php' ) . ' --prefix ' . ucfirst( $this->project->name_hash ) . ' --no-interaction --force --stop-on-failure --working-dir ' . $plugin_svn_comp_dir . ' --output-dir ' . $plugin_svn_distro_dir . ' ' . $plugin_svn_comp_dir . '` from project directory.' );
		}

		if ( 0 !== U\CLI::run( [ 'composer', 'dump-autoload', '--no-dev', '--no-scripts', '--no-plugins', '--optimize', '--classmap-authoritative' ], U\Dir::join( $plugin_svn_distro_dir, '/trunk' ), false ) ) {
			throw new Exception( 'Failed to run `composer dump-autoload --no-dev --no-scripts --no-plugins --optimize --classmap-authoritative` from ./._x/svn-distro/trunk directory.' );
		}
		if ( false === ( $_svn_distro_dir_trunk_plugin_file_contents = file_get_contents( U\Dir::join( $plugin_svn_distro_dir, '/trunk/plugin.php' ) ) ) ) {
			throw new Exception( 'Failed to read contents of ./._x/svn-distro/trunk/plugin.php.' );
		}
		if ( false === file_put_contents( U\Dir::join( $plugin_svn_distro_dir, '/trunk/plugin.php' ), str_replace( '/vendor/autoload.php', '/vendor/scoper-autoload.php', $_svn_distro_dir_trunk_plugin_file_contents ) ) ) {
			throw new Exception( 'Failed to update `/vendor/autoload.php` to `/vendor/scoper-autoload.php` in ./._x/svn-distro/trunk/plugin.php.' );
		}

		if ( ! U\Dir::prune( $plugin_svn_distro_dir, $distro_dir_prune_config[ 'prune' ], $distro_dir_prune_config[ 'exceptions' ] ) ) {
			throw new Exception( 'Failed to prune project ./._x/svn-distro directory.' );
		}
		if ( ! U\Fs::copy( U\Dir::join( $plugin_svn_distro_dir, '/*' ), $plugin_svn_repo_dir ) ) {
			throw new Exception( 'Failed to copy contents of pruned ./._x/svn-distro directory into ./._x/svn-repo directory.' );
		}

		if ( ! U\Fs::copy( U\Dir::join( $plugin_svn_distro_dir, '/trunk' ), U\Dir::join( $plugin_svn_distro_dir, '/tags/' . $plugin->headers->version ) ) ) {
			throw new Exception( 'Failed to copy ./._x/svn-distro/trunk to ./._x/svn-distro/tags/' . $plugin->headers->version . ' directory.' );
		}
		if ( ! U\Fs::copy( U\Dir::join( $plugin_svn_distro_dir, '/tags/' . $plugin->headers->version ), U\Dir::join( $plugin_svn_repo_dir, '/tags/' . $plugin->headers->version ) ) ) {
			throw new Exception( 'Failed to copy ./._x/svn-distro/tags/' . $plugin->headers->version . ' to ./._x/svn-repo/tags/' . $plugin->headers->version . ' directory.' );
		}
	}

	/**
	 * Maybe compile WordPress theme's SVN repo.
	 *
	 * @since 2021-12-15
	 *
	 * @throws Exception Whenever any failure occurs.
	 */
	protected function maybe_compile_wp_theme_svn_repo() : void {
		if ( ! $this->project->is_wp_theme() ) {
			return; // Not applicable.
		}
		$theme = $this->project->wp_theme_data();

		$comp_dir_copy_config    = $this->project->comp_dir_copy_config();
		$distro_dir_prune_config = $this->project->distro_dir_prune_config();

		$theme_svn_comp_dir   = U\Dir::join( $this->project->dir, '/._x/svn-comp' );
		$theme_svn_distro_dir = U\Dir::join( $this->project->dir, '/._x/svn-distro' );
		$theme_svn_repo_dir   = U\Dir::join( $this->project->dir, '/._x/svn-repo' );

		// Regarding use of `--no-plugins` in Composer calls below.
		// {@see https://github.com/humbug/php-scoper#composer-plugins}.

		if ( ! U\Fs::copy( $this->project->dir, $theme_svn_comp_dir, $comp_dir_copy_config[ 'ignore' ], $comp_dir_copy_config[ 'exceptions' ] ) ) {
			throw new Exception( 'Failed to create project /._x/svn-comp directory.' );
		}
		if ( 0 !== U\CLI::run( [ 'composer', 'install', '--no-dev', '--no-scripts', '--no-plugins', '--optimize-autoloader', '--classmap-authoritative' ], U\Dir::join( $theme_svn_comp_dir, '/trunk' ), false ) ) {
			throw new Exception( 'Failed to run `composer install --no-dev --no-scripts --no-plugins --optimize-autoloader --classmap-authoritative` from ./._x/svn-comp/trunk directory.' );
		}

		if ( 0 !== U\CLI::run( [ 'composer', 'exec', '--', 'i18n-text-domain', 'add', '--text-domain', $theme->headers->text_domain, '--dir', $theme_svn_comp_dir ], $this->project->dir, false ) ) {
			throw new Exception( 'Failed to run `composer exec -- i18n-text-domain add --text-domain ' . $theme->headers->text_domain . ' --dir ' . $theme_svn_comp_dir . '` from project directory.' );
		}
		if ( 0 !== U\CLI::run( [ 'composer', 'exec', '--', 'i18n-text-domain', 'add', '--text-domain', $theme->headers->text_domain, '--dir', U\Dir::join( $theme_svn_comp_dir, '/trunk/vendor/clevercanyon/wpgroove-framework' ) ], $this->project->dir, false ) ) {
			throw new Exception( 'Failed to run `composer exec -- i18n-text-domain add --text-domain ' . $theme->headers->text_domain . ' --dir ' . U\Dir::join( $theme_svn_comp_dir, '/trunk/vendor/clevercanyon/wpgroove-framework' ) . ' from project directory.' );
		}
		if ( 0 !== U\CLI::run( [ 'composer', 'exec', '--', 'php-scoper', 'add-prefix', '--config', U\Dir::join( $theme_svn_comp_dir, '/.scoper.cfg.php' ), '--prefix', ucfirst( $this->project->name_hash ), '--no-interaction', '--force', '--stop-on-failure', '--working-dir', $theme_svn_comp_dir, '--output-dir', $theme_svn_distro_dir, $theme_svn_comp_dir ], $this->project->dir, false ) ) {
			throw new Exception( 'Failed to run `composer exec -- php-scoper add-prefix --config ' . U\Dir::join( $theme_svn_comp_dir, '/.scoper.cfg.php' ) . ' --prefix ' . ucfirst( $this->project->name_hash ) . ' --no-interaction --force --stop-on-failure --working-dir ' . $theme_svn_comp_dir . ' --output-dir ' . $theme_svn_distro_dir . ' ' . $theme_svn_comp_dir . '` from project directory.' );
		}

		if ( 0 !== U\CLI::run( [ 'composer', 'dump-autoload', '--no-dev', '--no-scripts', '--no-plugins', '--optimize', '--classmap-authoritative' ], U\Dir::join( $theme_svn_distro_dir, '/trunk' ), false ) ) {
			throw new Exception( 'Failed to run `composer dump-autoload --no-dev --no-scripts --no-plugins --optimize --classmap-authoritative` from ./._x/svn-distro/trunk directory.' );
		}
		if ( false === ( $_svn_distro_dir_trunk_theme_file_contents = file_get_contents( U\Dir::join( $theme_svn_distro_dir, '/trunk/theme.php' ) ) ) ) {
			throw new Exception( 'Failed to read contents of ./._x/svn-distro/trunk/theme.php.' );
		}
		if ( false === file_put_contents( U\Dir::join( $theme_svn_distro_dir, '/trunk/theme.php' ), str_replace( '/vendor/autoload.php', '/vendor/scoper-autoload.php', $_svn_distro_dir_trunk_theme_file_contents ) ) ) {
			throw new Exception( 'Failed to update `/vendor/autoload.php` to `/vendor/scoper-autoload.php` in ./._x/svn-distro/trunk/theme.php.' );
		}

		if ( ! U\Dir::prune( $theme_svn_distro_dir, $distro_dir_prune_config[ 'prune' ], $distro_dir_prune_config[ 'exceptions' ] ) ) {
			throw new Exception( 'Failed to prune project ./._x/svn-distro directory.' );
		}
		if ( ! U\Fs::copy( U\Dir::join( $theme_svn_distro_dir, '/*' ), $theme_svn_repo_dir ) ) {
			throw new Exception( 'Failed to copy contents of pruned ./._x/svn-distro directory into ./._x/svn-repo directory.' );
		}

		if ( ! U\Fs::copy( U\Dir::join( $theme_svn_distro_dir, '/trunk' ), U\Dir::join( $theme_svn_distro_dir, '/tags/' . $theme->headers->version ) ) ) {
			throw new Exception( 'Failed to copy ./._x/svn-distro/trunk to ./._x/svn-distro/tags/' . $theme->headers->version . ' directory.' );
		}
		if ( ! U\Fs::copy( U\Dir::join( $theme_svn_distro_dir, '/tags/' . $theme->headers->version ), U\Dir::join( $theme_svn_repo_dir, '/tags/' . $theme->headers->version ) ) ) {
			throw new Exception( 'Failed to copy ./._x/svn-distro/tags/' . $theme->headers->version . ' to ./._x/svn-repo/tags/' . $theme->headers->version . ' directory.' );
		}
	}

	/**
	 * Maybe compile WordPress plugin zip.
	 *
	 * @since 2021-12-15
	 *
	 * @throws Exception Whenever any failure occurs.
	 */
	protected function maybe_compile_wp_plugin_zip() : void {
		if ( ! $this->project->is_wp_plugin() ) {
			return; // Not applicable.
		}
		$plugin                  = $this->project->wp_plugin_data();
		$plugin_svn_repo_tag_dir = U\Dir::join( $this->project->dir, '/._x/svn-repo/tags/' . $plugin->headers->version );

		if ( ! is_dir( $plugin_svn_repo_tag_dir ) ) {
			throw new Exception(
				'Failed to zip ./._x/svn-repo/tags/' . $plugin->headers->version . ' directory.' .
				' Directory is missing: `' . $plugin_svn_repo_tag_dir . '`.'
			);
		}
		$plugin_zip_basename = $plugin->slug . '-v' . $plugin->headers->version . '.zip';
		$plugin_zip_path     = U\Dir::join( $this->project->dir, '/._x/svn-distro-zips/' . $plugin_zip_basename );

		if ( ! U\Fs::zip( $plugin_svn_repo_tag_dir . '->' . $plugin->slug, $plugin_zip_path ) ) {
			throw new Exception(
				'Failed to zip ./._x/svn-repo/tags/' . $plugin->headers->version . ' directory.' .
				' From: `' . $plugin_svn_repo_tag_dir . '->' . $plugin->slug . '`, to: `' . $plugin_zip_path . '`.'
			);
		}
	}

	/**
	 * Maybe compile WordPress theme zip.
	 *
	 * @since 2021-12-15
	 *
	 * @throws Exception Whenever any failure occurs.
	 */
	protected function maybe_compile_wp_theme_zip() : void {
		if ( ! $this->project->is_wp_theme() ) {
			return; // Not applicable.
		}
		$theme                  = $this->project->wp_theme_data();
		$theme_svn_repo_tag_dir = U\Dir::join( $this->project->dir, '/._x/svn-repo/tags/' . $theme->headers->version );

		if ( ! is_dir( $theme_svn_repo_tag_dir ) ) {
			throw new Exception(
				'Failed to zip ./._x/svn-repo/tags/' . $theme->headers->version . ' directory.' .
				' Directory is missing: `' . $theme_svn_repo_tag_dir . '`.'
			);
		}
		$theme_zip_basename = $theme->slug . '-v' . $theme->headers->version . '.zip';
		$theme_zip_path     = U\Dir::join( $this->project->dir, '/._x/svn-distro-zips/' . $theme_zip_basename );

		if ( ! U\Fs::zip( $theme_svn_repo_tag_dir . '->' . $theme->slug, $theme_zip_path ) ) {
			throw new Exception(
				'Failed to zip ./._x/svn-repo/tags/' . $theme->headers->version . ' directory.' .
				' From: `' . $theme_svn_repo_tag_dir . '->' . $theme->slug . '`, to: `' . $theme_zip_path . '`.'
			);
		}
	}

	/**
	 * Maybe upload a plugin zip to AWS S3.
	 *
	 * @since 2021-12-15
	 *
	 * @throws Exception On any failure.
	 * @throws \Throwable On some failures.
	 */
	protected function maybe_s3_upload_wp_plugin_zip() : void {
		if ( ! $this->project->is_wp_plugin() ) {
			return; // Not applicable.
		}
		$plugin              = $this->project->wp_plugin_data();
		$plugin_zip_basename = $plugin->slug . '-v' . $plugin->headers->version . '.zip';
		$plugin_zip_path     = U\Dir::join( $this->project->dir, '/._x/svn-distro-zips/' . $plugin_zip_basename );

		if ( ! is_file( $plugin_zip_path ) ) {
			throw new Exception( 'Missing zip file: `' . $plugin_zip_path . '`.' );
		}
		$plugin_s3_zip_hash           = $this->project->s3_hash_hmac_sha256( $plugin->unbranded_slug . $plugin->headers->version );
		$plugin_s3_zip_file_subpath   = 'cdn/product/' . $plugin->unbranded_slug . '/zips/' . $plugin_s3_zip_hash . '/' . $plugin_zip_basename;
		$plugin_s3_index_file_subpath = 'cdn/product/' . $plugin->unbranded_slug . '/data/index.json';

		$s3 = new S3Client( $this->project->s3_bucket_config() );

		// Get index w/ tagged versions.

		try {
			$_s3r            = $s3->getObject( [
				'Bucket' => $this->project->s3_bucket(),
				'Key'    => $plugin_s3_index_file_subpath,
			] );
			$plugin_s3_index = U\Str::json_decode( (string) $_s3r->get( 'Body' ) );

			if ( ! is_object( $plugin_s3_index ) || ! isset( $plugin_s3_index->versions->tags, $plugin_s3_index->versions->stable_tag ) ) {
				throw new Exception( 'Unable to retrieve valid JSON data from: `' . U\Dir::join( 's3://' . $this->project->s3_bucket(), '/' . $plugin_s3_index_file_subpath ) . '`.' );
			}
			if ( ! is_object( $plugin_s3_index->versions->tags ) || ! is_string( $plugin_s3_index->versions->stable_tag ) ) {
				throw new Exception( 'Unable to retrieve valid JSON data from: `' . U\Dir::join( 's3://' . $this->project->s3_bucket(), '/' . $plugin_s3_index_file_subpath ) . '`.' );
			}
		} catch ( \Throwable $throwable ) {
			if ( ! $throwable instanceof AwsException ) {
				throw $throwable; // Problem.
			}
			if ( 'NoSuchKey' !== $throwable->getAwsErrorCode() ) {
				throw $throwable; // Problem.
			}
			$plugin_s3_index = (object) [
				'versions' => (object) [
					'tags'       => (object) [],
					'stable_tag' => '',
				],
			]; // No index file yet, we'll create below.
		}

		// Upload zip file.
		// Throws exception on failure, which we intentionally do not catch.

		$s3->putObject( [
			'SourceFile' => $plugin_zip_path,
			'Bucket'     => $this->project->s3_bucket(),
			'Key'        => $plugin_s3_zip_file_subpath,
		] );

		// Update index w/ tagged versions.
		// Throws exception on failure, which we intentionally do not catch.

		$plugin_s3_index->versions->tags = (array) $plugin_s3_index->versions->tags;
		$plugin_s3_index->versions->tags = array_merge( $plugin_s3_index->versions->tags, [ $plugin->headers->version => time() ] );

		uksort( $plugin_s3_index->versions->tags, 'version_compare' ); // Example: <https://3v4l.org/QitGb>.
		$plugin_s3_index->versions->tags = array_reverse( $plugin_s3_index->versions->tags );

		$plugin_s3_index->versions->stable_tag = $plugin->headers->stable_tag;

		$s3->putObject( [
			'Body'   => U\Str::json_encode( $plugin_s3_index ),
			'Bucket' => $this->project->s3_bucket(),
			'Key'    => $plugin_s3_index_file_subpath,
		] );
	}

	/**
	 * Maybe upload a theme zip to AWS S3.
	 *
	 * @since 2021-12-15
	 *
	 * @throws Exception Whenever any failure occurs.
	 * @throws \Throwable On some failures.
	 */
	protected function maybe_s3_upload_wp_theme_zip() : void {
		if ( ! $this->project->is_wp_theme() ) {
			return; // Not applicable.
		}
		$theme              = $this->project->wp_theme_data();
		$theme_zip_basename = $theme->slug . '-v' . $theme->headers->version . '.zip';
		$theme_zip_path     = U\Dir::join( $this->project->dir, '/._x/svn-distro-zips/' . $theme_zip_basename );

		if ( ! is_file( $theme_zip_path ) ) {
			throw new Exception( 'Missing zip file: `' . $theme_zip_path . '`.' );
		}
		$theme_s3_zip_hash           = $this->project->s3_hash_hmac_sha256( $theme->unbranded_slug . $theme->headers->version );
		$theme_s3_zip_file_subpath   = 'cdn/product/' . $theme->unbranded_slug . '/zips/' . $theme_s3_zip_hash . '/' . $theme_zip_basename;
		$theme_s3_index_file_subpath = 'cdn/product/' . $theme->unbranded_slug . '/data/index.json';

		$s3 = new S3Client( $this->project->s3_bucket_config() );

		// Get index w/ tagged versions.

		try {
			$_s3r           = $s3->getObject( [
				'Bucket' => $this->project->s3_bucket(),
				'Key'    => $theme_s3_index_file_subpath,
			] );
			$theme_s3_index = U\Str::json_decode( (string) $_s3r->get( 'Body' ) );

			if ( ! is_object( $theme_s3_index ) || ! isset( $theme_s3_index->versions->tags, $theme_s3_index->versions->stable_tag ) ) {
				throw new Exception( 'Unable to retrieve valid JSON data from: `' . U\Dir::join( 's3://' . $this->project->s3_bucket(), '/' . $theme_s3_index_file_subpath ) . '`.' );
			}
			if ( ! is_object( $theme_s3_index->versions->tags ) || ! is_string( $theme_s3_index->versions->stable_tag ) ) {
				throw new Exception( 'Unable to retrieve valid JSON data from: `' . U\Dir::join( 's3://' . $this->project->s3_bucket(), '/' . $theme_s3_index_file_subpath ) . '`.' );
			}
		} catch ( \Throwable $throwable ) {
			if ( ! $throwable instanceof AwsException ) {
				throw $throwable; // Problem.
			}
			if ( 'NoSuchKey' !== $throwable->getAwsErrorCode() ) {
				throw $throwable; // Problem.
			}
			$theme_s3_index = (object) [
				'versions' => (object) [
					'tags'       => (object) [],
					'stable_tag' => '',
				],
			]; // No index file yet, we'll create below.
		}

		// Upload zip file.
		// Throws exception on failure, which we intentionally do not catch.

		$s3->putObject( [
			'SourceFile' => $theme_zip_path,
			'Bucket'     => $this->project->s3_bucket(),
			'Key'        => $theme_s3_zip_file_subpath,
		] );

		// Update index w/ tagged versions.
		// Throws exception on failure, which we intentionally do not catch.

		$theme_s3_index->versions->tags = (array) $theme_s3_index->versions->tags;
		$theme_s3_index->versions->tags = array_merge( $theme_s3_index->versions->tags, [ $theme->headers->version => time() ] );

		uksort( $theme_s3_index->versions->tags, 'version_compare' ); // Example: <https://3v4l.org/QitGb>.
		$theme_s3_index->versions->tags = array_reverse( $theme_s3_index->versions->tags );

		$theme_s3_index->versions->stable_tag = $theme->headers->stable_tag;

		$s3->putObject( [
			'Body'   => U\Str::json_encode( $theme_s3_index ),
			'Bucket' => $this->project->s3_bucket(),
			'Key'    => $theme_s3_index_file_subpath,
		] );
	}
}