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
        $this->publishes([__DIR__ . '/../config/telescope-monitor.php' => config_path('telescope-monitor.php')]);

        $this->mergeConfigFrom(__DIR__ . '/../config/telescope-monitor.php', 'telescope-monitor');

        if (filled($channel = config('telescope-monitor.log_channel'))) {
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
