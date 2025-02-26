<?php

namespace Tests\Feature;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Telescope\Storage\EntryModel;
use Tests\TestCase;

class QueueTest extends TestCase
{
    use RefreshDatabase;

    public function testFailedJobWithMultipleTriesIsLoggedOnce(): void
    {
        $identifier = (string) Str::uuid();

        dispatch(new ThrowingJob($identifier));
        $this->artisan('queue:work --stop-when-empty');

        $log = file_get_contents(storage_path('logs/laravel.log'));

        $this->assertSame(3, substr_count($log, "Running {$identifier}"));
        $this->assertSame(1, substr_count($log, "Failed {$identifier}"));
    }

    public function testReportingJobIsLoggedOnce(): void
    {
        $identifier = (string) Str::uuid();

        dispatch(new ReportingJob($identifier));
        $this->artisan('queue:work --stop-when-empty');

        $log = file_get_contents(storage_path('logs/laravel.log'));

        $this->assertSame(1, substr_count($log, "Running {$identifier}"));
        $this->assertSame(1, substr_count($log, "Failed {$identifier}"));
    }

    public function testNonReportingJobDoesNotGetLogged(): void
    {
        $identifier = (string) Str::uuid();

        dispatch(new NonReportingJob($identifier));
        $this->artisan('queue:work --stop-when-empty');

        $log = file_get_contents(storage_path('logs/laravel.log'));

        $this->assertSame(1, substr_count($log, "Running {$identifier}"));
        $this->assertSame(0, substr_count($log, "Failed {$identifier}"));
    }
}

class ThrowingJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public function __construct(public string $identifier) {}

    public function handle(): void
    {
        Log::channel(config('telescope-monitor.log_channel'))->info("Running {$this->identifier}");
        throw new Exception("Failed {$this->identifier}");
    }
}

class ReportingJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public string $identifier) {}

    public function handle(): void
    {
        Log::channel(config('telescope-monitor.log_channel'))->info("Running {$this->identifier}");

        try {
            throw new Exception("Failed {$this->identifier}");
        } catch (Exception $exception) {
            report($exception);

            $this->fail($exception);
        }
    }
}

class NonReportingJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public string $identifier) {}

    public function handle(): void
    {
        Log::channel(config('telescope-monitor.log_channel'))->info("Running {$this->identifier}");

        try {
            throw new Exception("Failed {$this->identifier}");
        } catch (Exception $exception) {
            $this->fail($exception);
        }
    }
}
