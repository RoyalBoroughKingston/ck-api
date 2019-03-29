<?php

namespace App\Console\Commands\Ck\AutoDelete;

use App\Models\Audit;
use Illuminate\Console\Command;

class AuditsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ck:auto-delete:audits';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes any audits that are due for deletion';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $months = Audit::AUTO_DELETE_MONTHS;

        $this->line("Deleting audits created {$months} month(s) ago...");
        $count = Audit::dueForDeletion()->delete();
        $this->info("Deleted {$count} audit(s).");
    }
}
