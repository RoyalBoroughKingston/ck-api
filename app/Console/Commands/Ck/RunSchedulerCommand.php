<?php

namespace App\Console\Commands\Ck;

use Illuminate\Console\Command;

class RunSchedulerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ck:run-scheduler {--sleep=60 : The number of seconds to sleep between each run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Starts an infinite loop for running the scheduler';

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
        while (true) {
            $this->call('schedule:run');

            sleep($this->option('sleep'));
        }
    }
}
