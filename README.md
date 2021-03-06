# Laravel Telescope Monitor

**Exception monitoring via Laravel Telescope**

Laravel already allows you to get notified when something goes wrong in your application. You can, for example, log exceptions to the log file and to Slack by configuring a [log stack][] and setting it as your default log channel. However, one disadvantage is that subsequent occurrences of the same exception will also be logged, causing a flood of notifications when there is an exception that occurs frequently.

This package aims to solve that problem by logging the exceptions recorded by Telescope to a log channel. When an exception has already occurred before (according to the data stored by Telescope), it will not be logged again.

Exceptions causing a queued job to fail will also be recorded as a Telescope exception entry, so they will also be logged to the configured channel.

## Installation

```shell
composer require adriaanzon/laravel-telescope-monitor
```

## Configuration

The configuration channel can be configured by adding `TELESCOPE_MONITOR_LOG_CHANNEL` to your dotenv file, for example:

```dotenv
TELESCOPE_MONITOR_LOG_CHANNEL=slack
```

To configure other options, you can publish the [configuration file][]:

```shell
php artisan vendor:publish --provider="AdriaanZon\TelescopeMonitor\TelescopeMonitorServiceProvider"
```

[log stack]: https://laravel.com/docs/9.x/logging#building-log-stacks
[configuration file]: config/telescope-monitor.php
