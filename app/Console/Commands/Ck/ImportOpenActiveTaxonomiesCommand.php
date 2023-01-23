<?php

namespace App\Console\Commands\Ck;

use App\BatchImport\OpenActiveTaxonomyImporter;
use Illuminate\Console\Command;

class ImportOpenActiveTaxonomiesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ck:import-openactive-taxonomies
                            {--url : The url of the Open Active taxonomies .jsonld file (e.g. http://...)}
                            {--dry-run : Parse the import but do not commit the changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports a .jsonld file of Taxonomies under the OpenActive category';

    /**
     * Execute the console command.
     *
     * @throws \Exception
     * @return mixed
     */
    public function handle()
    {
        $jsonUrl = null;
        if ($this->option('url')) {
            if (!preg_match("/\b(?:(?:https?):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $this->option('url'))) {
                $this->error('The .jsonld file URL is invalid. Exiting');

                return false;
            }
            $jsonUrl = $this->option('url');
        }

        $dryRun = $this->option('dry-run');

        $openActiveImporter = new OpenActiveTaxonomyImporter($jsonUrl, $dryRun);

        if ($dryRun) {
            $this->warn('Dry Run, no data will be committed');
        }

        try {
            $importCount = $openActiveImporter->runImport();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }

        $this->info('All records imported. Total records imported: ' . $importCount);
    }
}
