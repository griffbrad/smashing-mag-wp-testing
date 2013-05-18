<?php

require_once dirname( __FILE__ ) . '/../lib/SmashingLog.php';

/**
 * To test our SmashingLog object, we using PHPUnit's DBUnit extension so that
 * we can start each test with a known dataset.
 */
class SmashingLogTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var SmashingLog
	 */
	protected $log;

	/**
	 * Notice that in this setUp() method we also call parent::setUp().  That's
	 * because DBUnit expects us to call its own setUp() method so it can prepare
	 * our dataset before each test.
	 */
	public function setUp()
	{
		parent::setUp();

		global $wpdb;

		$this->log = new SmashingLog( $wpdb );
	}

	/**
	 * No DB interaction here.  We're just making sure the get_timestamp method
	 * returns the ISO format like we expect.
	 */
	public function testGetTimestampMethodReturnIsoFormattedString()
	{
		$iso  = $this->log->get_timestamp();
		$unix = strtotime( $iso );

		$this->assertEquals( date( 'Y-m-d G:i:s', $unix ), $iso );
	}
}
