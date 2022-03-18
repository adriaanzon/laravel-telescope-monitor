<?php

namespace Tests\Integration;

use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Laravel\Telescope\ExceptionContext;
use Laravel\Telescope\IncomingExceptionEntry;
use Laravel\Telescope\Telescope;
use Tests\TestCase;

class LogTest extends TestCase
{
    use RefreshDatabase;

    protected function defineEnvironment($app)
    {
        $app['config']->set('telescope-monitor.log_channel', 'null');
    }

    public function testExeptionsGetLoggedToLogConfiguredChannel()
    {
        $exception = new Exception('whoops');

        Telescope::recordException($entry = new IncomingExceptionEntry($exception, [
            'class' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'message' => $exception->getMessage(),
            'trace' => $exception->getTrace(),
            'line_preview' => ExceptionContext::get($exception),
        ]));

        Log::shouldReceive('channel->error')->once()->with('whoops', [
            'type' => Exception::class,
            'location' => __FILE__ . ':' . 24,
            'details' => 'http://localhost/telescope/exceptions/' . $entry->uuid,
        ]);

        $this->app->call(Telescope::store(...));
    }
}
