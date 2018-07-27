<?php

namespace App\Console\Commands\Make;

use Illuminate\Console\Command;

class RelationshipsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:relationships {model : The name of the Model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Model relationships trait';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $fileContents = $this->getTemplate();
        $this->saveToFile($fileContents);

        $this->info('Model relationships trait created successfully.');
    }

    /**
     * @return string
     */
    protected function getTemplate(): string
    {
        $model = $this->argument('model');

        return <<<EOT
<?php

namespace App\Models\Relationships;

trait {$model}Relationships
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

        if (!is_dir(app_path('Models/Relationships'))) {
            mkdir(app_path('Models/Relationships'));
        }

        file_put_contents(app_path('Models/Relationships/' . $model . 'Relationships.php'), $contents);

        return true;
    }
}
