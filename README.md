
== Description ==

The simple plugin schedules a cron job that will truncate the debug log file to a certain number of lines (default: 5,000 lines) after the file reaches a certain size (default: 10MB). No user interaction is required.
This is to prevent the debug log file from growing too big and taking up too much space on the server.

Two filters can be used to change the default values:

* `ab_truncate_debug_log_max_size` to change the maximum size of the debug log file (in MB)
* `ab_truncate_debug_log_lines` to change the number of lines the debug log file is truncated to.

== Development ==

To build this plugin in a local environment, just install the depencies using Composer in order to generate the autoload files.