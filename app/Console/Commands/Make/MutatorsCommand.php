<?php

namespace App\Console\Commands\Make;

use Illuminate\Console\Command;

class MutatorsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:mutators {model : The name of the Model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Model mutators trait';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $fileContents = $this->getTemplate();
        $this->saveToFile($fileContents);

        $this->info('Model mutators trait created successfully.');
    }

    /**
     * @return string
     */
    protected function getTemplate(): string
    {
        $model = $this->argument('model');

        return <<<EOT
<?php

namespace App\Models\Mutators;

trait {$model}Mutators
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

        if (!is_dir(app_path('Models/Mutators'))) {
            mkdir(app_path('Models/Mutators'));
        }

        file_put_contents(app_path('Models/Mutators/' . $model . 'Mutators.php'), $contents);

        return true;
    }
}
