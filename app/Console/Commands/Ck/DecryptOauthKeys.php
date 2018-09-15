<?php

namespace App\Console\Commands\Ck;

use Illuminate\Console\Command;

class DecryptOauthKeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ck:decrypt-oauth-keys 
                            {--key-path=app/oauth-keys : The path to the keys}
                            {--public-key-name=oauth-public.key : The name of the public key file}
                            {--private-key-name=oauth-private.key : The name of the private key file}
                            {--force : Force creation even if the keys already exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Decrypts the OAuth keys';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $keyPath = $this->option('key-path');
        $publicKeyName = $this->option('public-key-name');
        $privateKeyName = $this->option('private-key-name');

        $publicKey = decrypt(file_get_contents(storage_path("$keyPath/$publicKeyName")));
        $privateKey = decrypt(file_get_contents(storage_path("$keyPath/$privateKeyName")));

        if (!file_exists(storage_path('oauth-public.key')) || $this->option('force')) {
            file_put_contents(storage_path('oauth-public.key'), $publicKey);
            file_put_contents(storage_path('oauth-private.key'), $privateKey);

            $this->info('Public and private keys have both been decrypted.');
        } else {
            $this->error('Public and private key already exist.');
        }
    }
}
