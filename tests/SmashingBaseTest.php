<?php

require_once dirname( __FILE__ ) . '/../lib/SmashingBase.php';

require_once 'PHPUnit/Framework/Constraint/IsInstanceOf.php';
require_once 'PHPUnit/Framework/Constraint/IsType.php';

/**
 * These tests cover our SmashingBase class.  There are quite a few mock
 * objects involved here because the SmashingBase class is the seam between
 * our plugin and WordPress.  By using mock objects, we can isolate our
 * plugin code from the rest of WordPress and just test the specific units
 * we're concerned about.
 */
class SmashingBaseTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var wpdb
	 */
	protected $wpdb;

	/**
	 * We use our setUp() method in this case just to stash a reference to wpdb
	 * for use in the other test cases.  PHPUnit will run this method before
	 * each of the test methods in this class so that we always start off with
	 * a clean slate.
	 */
	public function setUp()
	{
		global $wpdb;

		$this->wpdb = $wpdb;
	}

	/**
	 * In this case we simulate our admin_init handler being called when no
	 * user is logged in.  In that situation, we don't want to the log to be
	 * updated, so we use PHPUnit's mock object API to ensure that it isn't.
	 *
	 * To create a mock object, you just call getMock() inside your test case,
	 * supplying it with three arguments:
	 *
	 * <ol>
	 *     <li>The class you want to create a mock for.</li>
	 *     <li>An array of methods you'd like to mock.</li>
	 *     <li>An array of parameters to supply to the object's constructor.</li>
	 * </ol>
	 */
	public function testLogIsNotUpdatedIfUserIsNotLoggedIn()
	{
		$log = $this->getMock(
			'SmashingLog',
			array( 'update' ),
			array( $this->wpdb )
		);

		$base = $this->getMock(
			'SmashingBase',
			array( 'is_user_logged_in' ),
			array( $log )
		);

		$log
			->expects( $this->never() )
			->method( 'update' );

		$base
			->expects( $this->once() )
			->method( 'is_user_logged_in' )
			->will( $this->returnValue( false ) );

		$base->admin_init();
	}

	/**
	 * In this case, we want to make sure the log's update method is called if
	 * admin_init is run while a user is logged in.  We don't want to actually
	 * run the log's true update method because we're testing SmashingBase here,
	 * not SmashingLog.  For this test case, we just want to know that the logic
	 * in SmashingBase accurately passes responsibilty onto the log when it
	 * should.
	 */
	public function testLogIsUpdatedIfUserIsLoggedIn()
	{
		$log = $this->getMock(
			'SmashingLog',
			array( 'update' ),
			array( $this->wpdb )
		);

		$base = $this->getMock(
			'SmashingBase',
			array( 'is_user_logged_in', 'get_current_user_id' ),
			array( $log )
		);

		$log
			->expects( $this->once() )
			->method( 'update' );

		$base
			->expects( $this->once() )
			->method( 'is_user_logged_in' )
			->will( $this->returnValue( true ) );

		$base
			->expects( $this->once() )
			->method( 'get_current_user_id' )
			->will( $this->returnValue( 1 ) );

		$base->admin_init();
	}

	/**
	 * In this case, we're testing to make sure our dashboard_setup handler
	 * correctly adds the dashboard widget.  Because these WP functions won't
	 * exist in the test environment, we just test that the function is called
	 * using arguments of the correct/expected types.
	 */
	public function testDashboardSetupAddsWidget()
	{
		$base = $this->getMock(
			'SmashingBase',
			array( 'add_dashboard_widget' ),
			array()
		);

		$base
			->expects( $this->once() )
			->method( 'add_dashboard_widget' )
			->with(
				new PHPUnit_Framework_Constraint_IsType( 'string' ),
				new PHPUnit_Framework_Constraint_IsType( 'string' ),
				new PHPUnit_Framework_Constraint_IsType( 'callable' )
			);

		$base->dashboard_setup();
	}
}
