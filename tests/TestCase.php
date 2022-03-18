<?php

namespace Tests;

use AdriaanZon\TelescopeMonitor\TelescopeMonitorServiceProvider;
use Laravel\Telescope\TelescopeServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            TelescopeServiceProvider::class,
            TelescopeMonitorServiceProvider::class,
        ];
    }
}
