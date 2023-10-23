=== AB Truncate Debug Log ===
Contributors: arnaudbroes
Tags: debug, debug log, wp debug, wp debug log, maintenance
Requires at least: 4.9
Tested up to: 6.3.1
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Simple plugin to prevent the WP debug log file from growing too large.

== Description ==

The simple plugin schedules a cron job that will truncate the debug log file to a certain number of lines (default: 5,000 lines) after the file reaches a certain size (default: 10MB). No user interaction is required.
This is to prevent the debug log file from growing too large and taking up too much space on the server.

Two filters can be used to change the default values:

* `ab_truncate_debug_log_max_size` to change the maximum size of the debug log file (in MB)
* `ab_truncate_debug_log_lines` to change the number of lines the debug log file is truncated to.

== Changelog ==

**New in Version 1.0.0**

Initial release.