<?php

namespace AdriaanZon\TelescopeMonitor;

use Laravel\Telescope\IncomingExceptionEntry;
use Laravel\Telescope\Storage\EntryModel;

class TelescopeEntryRepository
{
    /**
     * Check if the exception didn't occur earlier, or if the previous occurrence was marked as resolved.
     */
    public function isUniqueException(IncomingExceptionEntry $entry): bool
    {
        $previousEntry = EntryModel::query()
            ->where('uuid', '!=', $entry->uuid)
            ->where('batch_id', '!=', $entry->batchId)
            ->where('family_hash', $entry->familyHash())
            ->latest('sequence')
            ->first(['content']);

        return $previousEntry === null || array_key_exists('resolved_at', $previousEntry->content);
    }

    /**
     * Get the number of unique exception entries that haven't been marked as resolved.
     */
    public function countUnresolvedExceptions(): int
    {
        return EntryModel::query()
            ->where('type', 'exception')
            ->where('should_display_on_index', true)
            ->where('content', 'not like', '%"resolved_at":%')
            ->count();
    }
}
