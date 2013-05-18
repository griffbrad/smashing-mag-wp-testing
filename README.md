Smashing Mag WP Automated Testing Example Plugin
================================================

This WordPress plugin is an example to go along with the PHPUnit article for
Smashing Magazine.  The plugin provides a simple WordPress admin dashboard
panel that lists the time of the latest activity for each WordPress user.

The plugin demonstrates key automated testing strategies for WordPress
development including:

* Using wrappers around stock WP functions to allow simulating varying
  return values during testing.
* Using PHPUnit's mock object API to make it easy to isolate a single
  class or function for testing.
* Using the DBUnit extension to test interaction with the MySQL database.
* Using PHP's output buffering functions to capture and analyze output.
* Using dependency injection to avoid tightly coupling components of your
  plugin or theme code.

The code is heavily documented so that the reader can understand
why certain strategies are needed in specific situations.

