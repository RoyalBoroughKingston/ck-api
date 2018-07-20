<?php

namespace App\Console\Commands\Cwk;

use Illuminate\Console\Command;
use Symfony\Component\Yaml\Yaml;

class SetCfSecretsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cwk:set-cf-secrets 
                            {--show : Display the secrets instead of modifying files}
                            {--path=secrets.yml : The path of the secrets file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sets the secrets missing in the Cloud Foundry secrets file with environment variables';

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
        $path = $this->option('path');
        $show = $this->option('show');

        // Parse the YAML into an array.
        $secrets = Yaml::parseFile($path);

        // Loop through each key.
        foreach ($secrets as $key => &$secret) {
            $matches = [];

            // Check to see if the value should be replaced with the following syntax: `KEY: ${ENV_VAR}`.
            if (preg_match('/^\${(.+)}$/', $secret, $matches) >= 1) {
                $envName = $matches[1];
                $secret = env($envName, null);
            }
        }

        // Export the array back to a YAML string.
        $contents = Yaml::dump($secrets);

        // Either output the contents to the terminal or save to the file.
        if ($show) {
            $this->warn($contents);
        } else {
            file_put_contents($path, $contents);
            $this->info("$path has been updated with the environment variables inserted.");
        }

        return true;
    }
}
