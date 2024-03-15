# Laravel Telescope Monitor

**Exception monitoring via Laravel Telescope**

Laravel already allows you to get notified when something goes wrong in your application. You can, for example, log exceptions to the log file and to Slack by configuring a [log stack][] and setting it as your default log channel. However, one disadvantage is that subsequent occurrences of the same exception will also be logged, causing a flood of notifications when there is an exception that occurs frequently.

This package aims to solve that problem by logging the exceptions recorded by Telescope to a log channel. When an exception has already occurred before (according to the data stored by Telescope), it will not be logged again.

> [!TIP]
> Laravel 10 added support for [throttling reported exceptions][]. You should use that instead, if you're only interested in preventing floods of notifications.

Exceptions causing a queued job to fail will also be recorded as a Telescope exception entry, so they will also be logged to the configured channel.

## Installation

```shell
composer require adriaanzon/laravel-telescope-monitor
```

## Configuration

The log channel can be configured by adding `TELESCOPE_MONITOR_LOG_CHANNEL` to your dotenv file, for example:

```dotenv
TELESCOPE_MONITOR_LOG_CHANNEL=slack
```

In your local development environment, you'd typically disable Laravel Telescope Monitor by excluding `TELESCOPE_MONITOR_LOG_CHANNEL` from your .env file or setting it to null:

```dotenv
TELESCOPE_MONITOR_LOG_CHANNEL=null
```

To configure other options, you can publish the [configuration file][]:

```shell
php artisan vendor:publish --provider="AdriaanZon\TelescopeMonitor\TelescopeMonitorServiceProvider"
```

[log stack]: https://laravel.com/docs/11.x/logging#building-log-stacks
[configuration file]: config/telescope-monitor.php
[throttling reported exceptions]: https://laravel.com/docs/11.x/errors#throttling-reported-exceptions
