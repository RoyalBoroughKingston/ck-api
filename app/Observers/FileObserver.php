<?php

namespace App\Observers;

use App\Models\File;

class FileObserver
{
    /**
     * Handle the file "deleted" event.
     *
     * @param  \App\Models\File  $file
     * @return void
     */
    public function deleted(File $file)
    {
        $file->deleteFromDisk();
    }
}
