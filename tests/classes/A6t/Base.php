<?php
/**
 * Clever Canyon™ {@see https://clevercanyon.com}
 *
 *  CCCCC  LL      EEEEEEE VV     VV EEEEEEE RRRRRR      CCCCC    AAA   NN   NN YY   YY  OOOOO  NN   NN ™
 * CC      LL      EE      VV     VV EE      RR   RR    CC       AAAAA  NNN  NN YY   YY OO   OO NNN  NN
 * CC      LL      EEEEE    VV   VV  EEEEE   RRRRRR     CC      AA   AA NN N NN  YYYYY  OO   OO NN N NN
 * CC      LL      EE        VV VV   EE      RR  RR     CC      AAAAAAA NN  NNN   YYY   OO   OO NN  NNN
 *  CCCCC  LLLLLLL EEEEEEE    VVV    EEEEEEE RR   RR     CCCCC  AA   AA NN   NN   YYY    OOOO0  NN   NN
 */
// <editor-fold desc="Strict types, namespace, use statements, and other headers.">

/**
 * Lint configuration.
 *
 * @since        2021-12-15
 *
 * @noinspection PhpComposerExtensionStubsInspection
 * @noinspection PhpStaticAsDynamicMethodCallInspection
 */

/**
 * Declarations & namespace.
 *
 * @since 2021-12-25
 */
declare( strict_types = 1 );
namespace WP_Groove\Framework\Tests\A6t;

/**
 * Utilities.
 *
 * @since 2021-12-15
 */
use Clever_Canyon\{Utilities as U};
use Clever_Canyon\Utilities\{Tests as U_Tests};

/**
 * Framework.
 *
 * @since 2021-12-15
 */
use WP_Groove\{Framework as WPG};
use WP_Groove\Framework\{Tests as WPG_Tests};

/**
 * Plugin.
 *
 * @since 2021-12-15
 */
use WP_Groove\{Framework_Plugin as WP};

// </editor-fold>

/**
 * Base class for tests.
 *
 * @since 2021-12-15
 */
abstract class Base extends U_Tests\A6t\Base {
	/**
	 * Fires before the first method is run.
	 *
	 * @since 2021-12-15
	 */
	public static function setUpBeforeClass() : void {
		if ( ! U\Env::is_wp_docker() ) {
			echo 'Must run tests in WP Docker.';
			exit( 1 ); // Error status.
		}
		parent::setUpBeforeClass();
	}

	/**
	 * Fires before each method is run.
	 *
	 * @since 2021-12-15
	 */
	protected function setUp() : void {
		parent::setUp();
	}

	/**
	 * Fires after each method is run.
	 *
	 * @since 2021-12-15
	 */
	protected function tearDown() : void {
		parent::tearDown();
	}

	/**
	 * Fires after the last method is run.
	 *
	 * @since 2021-12-15
	 */
	public static function tearDownAfterClass() : void {
		parent::tearDownAfterClass();
	}
}
