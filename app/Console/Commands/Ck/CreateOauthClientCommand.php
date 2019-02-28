<?php

namespace App\Console\Commands\Ck;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;

class CreateOauthClientCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ck:create-oauth-client
                            {name : The name of the client}
                            {redirect-uri : The URI to redirect to}
                            {--first-party : Flag that indicates this is a first party client}';

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
        $client = Passport::client()->forceFill([
            'user_id' => null,
            'name' => $this->argument('name'),
            'secret' => Str::random(40),
            'redirect' => $this->argument('redirect-uri'),
            'personal_access_client' => false,
            'password_client' => false,
            'revoked' => false,
            'first_party' => $this->option('first-party'),
        ]);

        $client->save();

        $this->info('New client created successfully.');
        $this->line('<comment>Client ID:</comment> ' . $client->id);
        $this->line('<comment>Client secret:</comment> ' . $client->secret);
    }
}
