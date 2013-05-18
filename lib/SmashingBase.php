<?php

require_once dirname( __FILE__ ) . '/SmashingLog.php';

/**
 * This class contains our primary action handlers for WP hooks "admin_init" and
 * "dashboard_setup".  Handling those hooks is its sole responsibility.  It doesn't
 * have to worry about rendering any HTML or interacting with the MySQL DB itself.
 * Because this class is the seam between the rest of our plugin code and WP, there
 * are several mocked functions to allow us to simulate different scenarios during
 * testing.
 */
class SmashingBase
{
	/**
	 * A SmashingLog object used both for writing to the database and populating
	 * the activity list on the WordPress dashboard.
	 *
	 * @var SmashingLog
	 */
	protected $log;

	/**
	 * Create a new SmashingBase object using the supplied log.  Typically,
	 * you won't need to pass a SmashingLog object in directly because a
	 * default will be made for you.  However, during testing we can inject
	 * a mock log object that doesn't actually write any changes to the
	 * database.  By eliminating the database access, we can keep tests
	 * for SmashingBase very fast and make it easier to diagnose any
	 * failing tests by eliminating the database as a possible source of
	 * the error.
	 *
	 * @param SmashingLog $log
	 */
	public function __construct( SmashingLog $log = null)
	{
		if ( null !== $log ) {
			$this->log = $log;
		} else {
			global $wpdb;

			$this->log = new SmashingLog( $wpdb );
		}
	}

	/**
	 * Our admin_init action handler.  If the user is logged in, we update the
	 * activity log.  Otherwise, we do nothing.
	 */
	public function admin_init()
	{
		if ( $this->is_user_logged_in() ) {
			$this->log->update( $this->get_current_user_id() );
		}
	}

	/**
	 * Set up the dashboard widget by instantiating a SmashingDashboardWidget
	 * object and registering it with wp_add_dashboard_widget().
	 *
	 * @return void
	 */
	public function dashboard_setup()
	{
		require_once dirname( __FILE__ ) . '/SmashingDashboardWidget.php';
		$widget = new SmashingDashboardWidget( $this->log );

		$this->add_dashboard_widget(
			'smashing_user_tracker_widget',
			'Smashing Mag Admin Login History',
			array( $widget, 'render' )
		);
	}

	/**
	 * When we're actually running this code in WP, we just defer directly
	 * to the stock is_user_logged_in() function.  During testing, though,
	 * this method gives us the ability to simulate both logged in and
	 * logged out scenarios using PHPUnit's mock objects API.
	 *
	 * @return boolean
	 */
	protected function is_user_logged_in()
	{
		return is_user_logged_in();
	}

	/**
	 * Like is_user_logged_in(), when this method is called during a normal
	 * request, we just use the built-in WP function.  During testing, though,
	 * we can use PHPUnit's mock objects API to simulate any logged-in user
	 * ID we'd like.
	 *
	 * @return integer
	 */
	protected function get_current_user_id()
	{
		return get_current_user_id();
	}

	/**
	 * wp_add_dashboard_widget will not exist during our tests because they
	 * are not an actual admin request.  We aren't really trying to test
	 * WP core anyway; we're trying to test our own code.  So, during the
	 * test, we'll just make sure that we're calling wp_add_dashboard_widget()
	 * correctly.
	 *
	 * In the case of testing for this method, we'll use the Constraint API
	 * from PHPUnit to make sure the method's arguments are all of the expected
	 * types.
	 *
	 * @param string $name
	 * @param string $title
	 * @param callable $callback
	 */
	protected function add_dashboard_widget( $name, $title, $callback )
	{
		wp_add_dashboard_widget( $name, $title, $callback );
	}
}
