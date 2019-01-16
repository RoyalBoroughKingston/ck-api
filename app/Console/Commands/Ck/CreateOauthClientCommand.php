<?php

namespace App\Console\Commands\Ck;

use Illuminate\Console\Command;
use Laravel\Passport\ClientRepository;

class CreateOauthClientCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ck:create-oauth-client
                            {name : The name of the client}
                            {redirect-uri : The URI to redirect to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an OAuth 2.0 client';

    /**
     * @var \Laravel\Passport\ClientRepository
     */
    protected $clients;

    /**
     * Create a new command instance.
     *
     * @param \Laravel\Passport\ClientRepository $clients
     */
    public function __construct(ClientRepository $clients)
    {
        parent::__construct();

        $this->clients = $clients;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $client = $this->clients->create(
            null,
            $this->argument('name'),
            $this->argument('redirect-uri')
        );

        $this->info('New client created successfully.');
        $this->line('<comment>Client ID:</comment> ' . $client->id);
        $this->line('<comment>Client secret:</comment> ' . $client->secret);
    }
}
