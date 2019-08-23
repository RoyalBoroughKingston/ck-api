<?php

namespace App\Observers;

use App\Models\File;

class FileObserver
{
    /**
     * Handle the file "deleting" event.
     *
     * @param \App\Models\File $file
     */
    public function deleting(File $file)
    {
        File::query()
            ->whereRaw('`meta`->>"$.type" = ?', [File::META_TYPE_RESIZED_IMAGE])
            ->whereRaw('`meta`->>"$.data.file_id" = ?', [$file->id])
            ->delete();
    }

    /**
     * Handle the file "deleted" event.
     *
     * @param \App\Models\File $file
     */
    public function deleted(File $file)
    {
        $file->deleteFromDisk();
    }
}
