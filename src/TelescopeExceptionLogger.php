<?php

namespace AdriaanZon\TelescopeMonitor;

use Illuminate\Support\Facades\Log;
use Laravel\Telescope\IncomingExceptionEntry;

class TelescopeExceptionLogger
{
    protected TelescopeEntryRepository $entryRepository;

    public function __construct(TelescopeEntryRepository $entryRepository)
    {
        $this->entryRepository = $entryRepository;
    }

    /**
     * Log new exceptions to a given channel, with a URL to the Telescope entry. By checking
     * whether the latest entry with the same "family hash" wasn't marked as resolved, it
     * prevents flooding the channel with subsequent occurrences of the same exception.
     */
    public function log(array $entries, ?string $channel = null): void
    {
        foreach ($entries as $entry) {
            if ($entry instanceof IncomingExceptionEntry && $this->entryRepository->isUniqueException($entry)) {
                Log::channel($channel)->error($entry->content['message'], [
                    'type' => $entry->content['class'],
                    'location' => $entry->content['file'] . ':' . $entry->content['line'],
                    'details' => url(config('telescope.path') . '/exceptions/' . $entry->uuid),
                ]);
            }
        }
    }
}
