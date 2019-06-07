<?php

namespace App\Console\Commands\Ck\AutoDelete;

use App\Models\ServiceRefreshToken;
use Illuminate\Console\Command;

class ServiceRefreshTokensCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ck:auto-delete:service-refresh-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes any service refresh tokens that are due for deletion';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $months = ServiceRefreshToken::AUTO_DELETE_MONTHS;

        $this->line("Deleting service refresh tokens created {$months} month(s) ago...");
        $count = ServiceRefreshToken::dueForDeletion()->delete();
        $this->info("Deleted {$count} service refresh token(s).");
    }
}
