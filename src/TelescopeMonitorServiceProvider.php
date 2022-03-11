<?php

namespace AdriaanZon\TelescopeMonitor;

use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\Telescope;

class TelescopeMonitorServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/telescope-monitor', 'telescope-monitor');

        if (! is_null($channel = config('telescope-monitor.log_channel'))) {
            Telescope::afterStoring(
                fn($entries) => $this->app->make(TelescopeExceptionLogger::class)->log($entries, $channel)
            );
        }
    }

    public function boot()
    {
        if (config('telescope-monitor.report_failed_jobs')) {
            Event::listen(JobFailed::class, ReportFailedJobListener::class);
        }
    }
}
