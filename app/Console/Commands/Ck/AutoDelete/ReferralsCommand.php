<?php

namespace App\Console\Commands\Ck\AutoDelete;

use App\Models\Referral;
use Illuminate\Console\Command;

class ReferralsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ck:auto-delete:referrals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes any referrals that are due for deletion';

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
        $months = Referral::AUTO_DELETE_MONTHS;

        $this->line("Deleting referrals completed {$months} months ago...");
        $count = Referral::dueForDeletion()->delete();
        $this->info("Deleted {$count} referrals.");
    }
}
