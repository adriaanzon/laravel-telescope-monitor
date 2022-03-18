<?php

namespace AdriaanZon\TelescopeMonitor;

use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Arr;
use Laravel\Telescope\ExceptionContext;
use Laravel\Telescope\IncomingExceptionEntry;
use Laravel\Telescope\Telescope;
use Throwable;

class ReportFailedJobListener
{
    public function handle(JobFailed $event): void
    {
        if (! config('telescope.enabled') || ! config('telescope-monitor.report_failed_jobs')) {
            return;
        }

        // In \Laravel\Telescope\ListensForStorageOpportunities::storeIfDoneProcessingJob,
        // Telescope already stops recording, so here it needs to be temporarily enabled again.
        $shouldRecord = Telescope::$shouldRecord;
        Telescope::$shouldRecord = true;

        $this->recordException($event->exception);

        Telescope::$shouldRecord = $shouldRecord;
    }

    /**
     * @see \Laravel\Telescope\Watchers\ExceptionWatcher::recordException()
     */
    public function recordException(Throwable $exception): void
    {
        $trace = collect($exception->getTrace())->map(function ($item) {
            return Arr::only($item, ['file', 'line']);
        })->toArray();

        Telescope::recordException(new IncomingExceptionEntry($exception, [
            'class' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'message' => $exception->getMessage(),
            'trace' => $trace,
            'line_preview' => ExceptionContext::get($exception),
        ]));

        app()->call(Telescope::store(...));
    }
}
