<?php

namespace App\Console\Commands\Ck;

use App\Models\PageFeedback;
use Illuminate\Console\Command;

class AutoDeletePageFeedbacksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ck:auto-delete-page-feedbacks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes all page feedback that are due for deletion';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $months = PageFeedback::AUTO_DELETE_MONTHS;

        $this->line("Deleting page feedback created {$months} months ago...");
        $count = PageFeedback::dueForDeletion()->delete();
        $this->info("Deleted {$count} page feedbacks.");
    }
}
