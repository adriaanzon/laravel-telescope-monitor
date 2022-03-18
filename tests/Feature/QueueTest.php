<?php

namespace Tests\Feature;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Laravel\Telescope\Storage\EntryModel;
use Tests\TestCase;

class QueueTest extends TestCase
{
    use RefreshDatabase;

    public function testFailedJobWithMultipleTriesIsLoggedOnce(): void
    {
        $this->markTestIncomplete('This bug has not yet been resolved.');

        dispatch(new DummyJob());
        $this->artisan('queue:work --stop-when-empty');

        $entries = EntryModel::query()->where('type', 'exception')->get();
        $this->assertCount(1, $entries);
    }
}

class DummyJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public function handle(): void
    {
        throw new Exception('oh no!');
    }
}
