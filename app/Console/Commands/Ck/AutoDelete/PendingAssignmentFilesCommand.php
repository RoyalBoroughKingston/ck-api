<?php

namespace App\Console\Commands\Ck\AutoDelete;

use App\Models\File;
use Illuminate\Console\Command;

class PendingAssignmentFilesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ck:auto-delete:pending-assignment-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes all files that are still pending assignment and due for deletion';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $days = File::PEDNING_ASSIGNMENT_AUTO_DELETE_DAYS;

        $this->line("Deleting file created {$days} day(s) ago...");
        $count = File::pendingAssignmentDueForDeletion()->delete();
        $this->info("Deleted {$count} file(s).");
    }
}
