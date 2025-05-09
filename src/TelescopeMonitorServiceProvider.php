<?php

namespace AdriaanZon\TelescopeMonitor;

use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\Telescope;

class TelescopeMonitorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->publishes([__DIR__ . '/../config/telescope-monitor.php' => config_path('telescope-monitor.php')]);

        $this->mergeConfigFrom(__DIR__ . '/../config/telescope-monitor.php', 'telescope-monitor');

        Telescope::afterStoring(
            fn($entries) => $this->app->make(TelescopeExceptionLogger::class)->logToConfiguredChannel($entries),
        );
    }

    public function boot(): void
    {
        Event::listen(JobFailed::class, ReportFailedJobListener::class);
    }
}
