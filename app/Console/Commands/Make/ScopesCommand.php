<?php

namespace App\Console\Commands\Make;

use Illuminate\Console\Command;

class ScopesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:scopes {model : The name of the Model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Model scopes trait';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $fileContents = $this->getTemplate();
        $this->saveToFile($fileContents);

        $this->info('Model scopes trait created successfully.');
    }

    /**
     * @return string
     */
    protected function getTemplate(): string
    {
        $model = $this->argument('model');

        return <<<EOT
<?php

namespace App\Models\Scopes;

trait {$model}Scopes
{
    //
}

EOT;
    }

    /**
     * @param string $contents
     *
     * @return bool
     */
    protected function saveToFile(string $contents): bool
    {
        $model = $this->argument('model');

        if (!is_dir(app_path('Models/Scopes'))) {
            mkdir(app_path('Models/Scopes'));
        }

        file_put_contents(app_path('Models/Scopes/' . $model . 'Scopes.php'), $contents);

        return true;
    }
}
