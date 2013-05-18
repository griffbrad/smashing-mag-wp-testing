<?php

require_once dirname( __FILE__ ) . '/../lib/SmashingDashboardWidget.php';
require_once dirname( __FILE__ ) . '/../lib/SmashingLog.php';

/**
 * Testing the SmashingDashboardWidget class is fairly simple.  We just mock
 * the log object to return some hard-coded activity log entries and capture
 * the render method's output to allow us to check that it is rendering the
 * HTML correctly and securely.
 */
class SmashingDashboardWidgetTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var SmashingLog
	 */
	protected $log;

	/**
	 * @var SmashingDashboardWidget
	 */
	protected $widget;

	/**
	 * Our setUp() method will be called before each test by PHPUnit to ensure
	 * each test starts with a known state.  For all tests in this class, we
	 * use a mock SmashingLog object because we want to specifically test
	 * SmashingDashboardWidget here, not the log object and its MySQL calls.
	 */
	public function setUp()
	{
		global $wpdb;

		$this->log = $this->getMock(
			'SmashingLog',
			array('fetch_dashboard_listing'),
			array($wpdb)
		);

		$this->widget = new SmashingDashboardWidget( $this->log );
	}

	/**
	 * In this case, we call the render method and then test the resulting HTML
	 * output to see if the expecting number of table rows is rendered for the
	 * data we have.  Note that we have to use PHP's output buffering functions
	 * here to capture the render method's output.  This is needed because
	 * WordPress expects that the dashboard panel's output will be printed
	 * directly to the browser instead of being returned as a string.
	 */
	public function testRenderDisplaysRowsForEveryUserReturnedFromLog()
	{
		$this->log
			->expects( $this->once() )
			->method( 'fetch_dashboard_listing' )
			->will( $this->returnValue( $this->getMockListingData() ) );

		ob_start();
		$this->widget->render();
		$output = ob_get_clean();

		// Should have one <tr> for each user and one for the headers
		$this->assertEquals( 4, substr_count( $output, '<tr>' ) );
	}

	/**
	 * This test is covering the render() method again, but this time we're testing
	 * to make sure that malicious or malformed content is properly escaped to prevent
	 * cross-site scripting.
	 */
	public function testRenderEscapesUnsafeHtmlCharactersSuppliedFromLog()
	{
		$this->log
			->expects( $this->once() )
			->method( 'fetch_dashboard_listing' )
			->will(
				$this->returnValue(
					array(
						array(
							'user_nicename' => '<script>alert(1);</script>',
							'last_activity' => '2013-06-01 00:00:00'
						)
					)
				)
			);

		ob_start();
		$this->widget->render();
		$output = ob_get_clean();

		$this->assertNotContains('<script>', $output);
		$this->assertContains('&lt;script&gt;', $output);
	}

	/**
	 * Testing format_time is very easy because there are no dependencies.  While
	 * the code is likely covered by the call's to render in the previous tests,
	 * we make sure the "Today" and "Yesterday" cases are covered by writing
	 * test cases specifically for them.
	 */
	public function testWillFormatDayAsTodayWhenLastActivityIsToday()
	{
		$this->assertContains( 'Today', $this->widget->format_time( date( 'Y-m-d G:i:s' ) ) );
	}

	/**
	 * Like with the previous test, this is a very simple one because we're dealing
	 * with a simple "pure function" with no depedencies.
	 */
	public function testWillFormatDayAsYesterdayWhenLastActivityIsYesterday()
	{
		$this->assertContains(
			'Yesterday',
			$this->widget->format_time( date( 'Y-m-d G:i:s', time() - SmashingDashboardWidget::DAY_IN_SECONDS ) )
		);
	}

	/**
	 * We'll use this mock dashboard listing data to test that the dashboard
	 * widget renders correctly.  By using some hard-coded data, our tests
	 * are easier to understand and the SmashingDashboardWidget tests focus
	 * solely on that code, rather than also testing SmashingLog.
	 */
	protected function getMockListingData()
	{
		return array(
			array(
				'user_nicename' => 'Larry',
				'last_activity' => '2013-04-01 00:00:00'
			),
			array(
				'user_nicename' => 'Moe',
				'last_activity' => '2013-04-01 00:00:00'
			),
			array(
				'user_nicename' => 'Curly',
				'last_activity' => '2013-04-01 00:00:00'
			)
		);
	}
}
