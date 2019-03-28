<?php

namespace App\Console\Commands\Ck\AutoDelete;

use App\Models\PageFeedback;
use Illuminate\Console\Command;

class PageFeedbacksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ck:auto-delete:page-feedbacks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes all page feedback that are due for deletion';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $months = PageFeedback::AUTO_DELETE_MONTHS;

        $this->line("Deleting page feedback created {$months} month(s) ago...");
        $count = PageFeedback::dueForDeletion()->delete();
        $this->info("Deleted {$count} page feedback(s).");
    }
}
