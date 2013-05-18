<?php

/**
 * Our log object is the sole place where our plugin interacts with wpdb.
 * Having the MySQL interaction isolated here makes testing simpler.  We
 * can use PHPUnit's DBUnit extension to simulate various data sets and
 * make sure our SQL is working as we expect.
 */
class SmashingLog
{
	/**
	 * The wpdb object created by WP.
	 *
	 * @var wpdb
	 */
	protected $wpdb;

	/**
	 * Create a new log object using the provide wpdb instance.  Though we won't
	 * need to for the example plugin's tests, having the wpdb instance injected
	 * via the constructor like this would allow us to swap out the stock wpdb
	 * for testing or customization.  If we had accessed wpdb via the global
	 * variable created by WordPress, this would be trickier and more error
	 * prone.
	 *
	 * @param wpdb $wpdb
	 */
	public function __construct( wpdb $wpdb )
	{
		$this->wpdb = $wpdb;
	}

	/**
	 * Update the log for the supplied user ID.  We use wpdb's replace method
	 * so that there is only ever one entry per user ID in the log table.
	 * We can test that replace() is working as we expect using DBUnit to
	 * track the number of rows in the log table.
	 *
	 * @param integer $user_id
	 */
	public function update( $user_id )
	{
		$this->wpdb->replace(
			"{$this->wpdb->prefix}smashing_user_log",
			array( 'user_id' => $user_id, 'last_activity' => $this->get_timestamp() )
		);
	}

	/**
	 * Fetch the list of last activity times for each user in reverse
	 * chronological order.  We can test this listing in a couple different ways.
	 * First, when testing SmashingLog itself, we'll just DBUnit to pre-populate
	 * the log table with some rows.  Second, when testing other objects interacting
	 * with the log, we can return a hard-coded array from this method to
	 * eliminate the database as a potential source of errors altogether using
	 * PHPUnit's mock object API.
	 *
	 * @return array
	 */
	public function fetch_dashboard_listing()
	{
		$sql = "SELECT u.user_nicename, l.last_activity
				FROM {$this->wpdb->prefix}smashing_user_log l
				JOIN {$this->wpdb->prefix}users u ON u.ID = l.user_id
				ORDER BY last_activity DESC";

		return $this->wpdb->get_results( $sql, ARRAY_A );
	}

	/**
	 * Get an ISO formatted timestamp for use as a value in the last_activity
	 * column of the log table.  We separate this into its own method because
	 * interacting with any transient time value during testing can be tricky.
	 * We don't want our test to fail if the second hand clicks by a bit and
	 * changes the timestamp.  So, we can override this method during testing
	 * and supply a hard-coded value instead.
	 *
	 * @return string
	 */
	public function get_timestamp()
	{
		return date( 'Y-m-d G:i:s' );
	}
}
