<?php

/**
 * This class renders the HTML for our dashboard panel by retrieving the
 * activity list from the log object and passing that along to a template.
 */
class SmashingDashboardWidget
{
	/**
	 * The number of seconds in a day.  Used for timestamp manipulation.  Defined
	 * here to avoid "magic numbers" elsewhere in the code.
	 *
	 * @const
	 */
	const DAY_IN_SECONDS = 86400;

	/**
	 * The log object that will be used to retrive the activity list.
	 *
	 * @var SmashingLog
	 */
	protected $log;

	/**
	 * Because we get the log instance via the constructor like this, we have
	 * the freedom to replace it with an alternate implementation during testing.
	 * This strategy is known as "dependency injection" and it is a critical
	 * tool for creating a "seam" for testing and generally making your code
	 * more flexible.
	 *
	 * @param SmashingLog $log
	 */
	public function __construct( SmashingLog $log )
	{
		$this->log = $log;
	}

	/**
	 * Render the HTML for the dashboard panel.  The WordPress API will complicate
	 * things a bit for us during testing here.  WordPress expects that we'll directly
	 * print/echo the HTML for our panel, but during tests it would be more convenient
	 * to have this method and other rendering methods return a string that we could
	 * examine.  To work around this WP quirk, we'll using PHP's output buffering
	 * API.
	 */
	public function render()
	{
		$users = $this->log->fetch_dashboard_listing();

		require dirname( __FILE__ ) . '/templates/dashboard-widget.php';
	}

	/**
	 * Format the last activity time provided to the method.  This function has
	 * no other dependencies, so it's quite easy to test.  We could easily add
	 * additional special cases like the "Today" or "Yesterday" cases already
	 * present and our tests would help us make sure all of them were working
	 * as expected without having to manually manipulate the data to trigger
	 * each possible case.
	 *
	 * @param string $last_activity
	 * @return string
	 */
	public function format_time( $last_activity )
	{
		$unix_timestamp = strtotime( $last_activity );

		if ( date( 'Y-m-d', $unix_timestamp ) === date( 'Y-m-d', time() ) ) {
			$date = 'Today';
		} elseif ( date( 'Y-m-d', $unix_timestamp ) === date( 'Y-m-d', time() - self::DAY_IN_SECONDS ) ) {
			$date = 'Yesterday';
		} else {
			$date = date( 'M j Y', $unix_timestamp );
		}

		$time = date( 'g:iA', $unix_timestamp );

		return $date . ' at ' . $time;
	}
}
