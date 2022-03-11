<?php

return [
    /**
     * The log channel to write Telescope exception entries to.
     *
     * See config/logging.php
     */
    'log_channel' => env('TELESCOPE_MONITOR_LOG_CHANNEL'),

    /**
     * Whether failed jobs should be reported as a Telescope exception
     * entry, when an exception occurs or when manually failing a job.
     *
     * @link https://laravel.com/docs/9.x/queues#manually-failing-a-job
     */
    'report_failed_jobs' => true,
];
