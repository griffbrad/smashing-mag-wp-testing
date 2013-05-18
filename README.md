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


ToDo
----

We still need an activation hook that adds the MySQL table
used throughout the plugin.  Until that hook is in place, you'll need
to add the table to MySQL manually.  To add the table, run the following
command:

    CREATE TABLE wp_smashing_user_log ( user_id INTEGER PRIMARY KEY, last_activity DATETIME NOT NULL);

Be sure to replace `wp_` with whatever database table prefix is used by
your WordPress install.

