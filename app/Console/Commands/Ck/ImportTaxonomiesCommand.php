<?php

namespace App\Console\Commands\Ck;

use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Models\Taxonomy;
use Illuminate\Support\Str;
use App\Models\UpdateRequest;
use App\Models\ServiceTaxonomy;
use Illuminate\Console\Command;
use App\Models\CollectionTaxonomy;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ImportTaxonomiesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ck:import-taxonomies
                            {url : The url of the taxonomies .csv file}
                            {--refresh : Delete all current taxonomies}
                            {--dry-run : Parse the import but do not commit the changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports a .csv file of Taxonomies';

    /**
     * Rows which have failed to import.
     *
     * @var array
     */
    protected $failedRows = [];

    /**
    * The taxonomy which will be used as the root
    *
    * @var \App\Models\Taxonomy
    **/
    protected $rootTaxonomy;

    /**
    * The URL to fetch the csv import file from
    *
    * @var string
    **/
    protected $csvUrl;

    /**
    * Flag to indicate if the first row of the import should be ignored
    *
    * @var bool
    **/
    protected $firstRowLabels;

    /**
    * The column index for the parent relationship
    *
    * @var int
    **/
    protected $parentIdColumn;

    /**
    * The column index for the unique taxonomy identifier
    *
    * @var int
    **/
    protected $taxonomyIdColumn;

    /**
    * The column index for the taxonomy name
    *
    * @var int
    **/
    protected $taxonomyNameColumn;

    /**
     * Create a new command instance.
     */
    public function __construct($params = null)
    {
        parent::__construct();

        // Set the local properties, used in tests
        if (is_array($params)) {
            foreach ($params as $key => $param) {
                $this->{$key} = $param;
            }
        }
    }

    /**
     * Execute the console command.
     *
     * @throws \Exception
     * @return mixed
     */
    public function handle()
    {
        $refresh = $this->option('refresh');
        $dryrun = $this->option('dry-run');

        if (!$this->promptForParameters()) {
            return;
        }

        if ($dryrun) {
            $this->warn('Dry Run, no data will be committed, delete option ignored');
        }

        $records = $this->fetchTaxonomyRecords($this->csvUrl);

        if (is_array($records) && count($records)) {
            $this->info('Spreadsheet uploaded');

            if ($refresh) {
                $this->warn($dryrun ? 'Current Taxonomies will be preserved' : 'All current Taxonomies will be deleted');
            }

            $importCount = $this->importTaxonomyRecords($records, $refresh, $dryrun);

            if (count($this->failedRows)) {
                $this->warn('Unable to import all records. Failed records:');
                $this->table(['ID', 'Name', 'Parent ID'], $this->failedRows);
            } else {
                $this->info('All records imported. Total records imported: ' . $importCount);
            }
        } else {
            $this->info('Spreadsheet could not be uploaded');
        }

        if ($dryrun) {
            $this->warn('Dry Run complete');
        }
    }

    /**
     * Prompt and retrieve all the required params
     *
     * @return array | false
     **/
    public function promptForParameters()
    {
        $params = [];

        $params['root_taxonomy_name'] = $this->ask('Enter the exact name of the root taxonomy (optional: if empty new root categories will be created)');
        $params['csv_url'] = $this->ask('Enter the full URL where the .csv file can be obtained (e.g. http://...) (required)');
        $params['taxonomy_id_column'] = $this->ask('Enter the column (1 = first) which contains the taxonomy identifier (required)');
        $params['taxonomy_name_column'] = $this->ask('Enter the column (1 = first) which contains the taxonomy name (required)');
        $params['parent_id_column'] = $this->ask('Enter the column (1 = first) which contains the taxonomy\'s parent identifier (optional: if empty the parent will be root)');
        $params['first_row_labels'] = $this->confirm('Does the first row contain labels?');

        foreach ($params as $key => $param) {
            switch ($key) {
                case 'root_taxonomy_name':
                    if (empty($param)) {
                        $this->rootTaxonomy = Taxonomy::category();
                    } elseif (Taxonomy::query()->where('name', $param)->exists()) {
                        $this->rootTaxonomy = Taxonomy::query()->where('name', $param)->first();
                    } else {
                        $this->error('The root category does not exist. Exiting');
                        return false;
                    }
                    break;
                case 'csv_url':
                    if (!preg_match("/\b(?:(?:https?):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $param)) {
                        $this->error("The CSV file URL is invalid. Exiting");
                        return false;
                    }
                    $this->csvUrl = $param;
                break;
                case 'first_row_labels':
                    $this->firstRowLabels = $param;
                    break;
                case 'parent_id_column':
                    $this->parentIdColumn = $param -1;
                    break;
                case 'taxonomy_id_column':
                case 'taxonomy_name_column':
                    if (empty($param)) {
                        $paramTitle = Str::title(str_replace('_', ' ', $key));
                        $this->error("$paramTitle is required. Exiting");
                        return false;
                    }
                    $this->{lcfirst(Str::studly($key))} = $param -1;
                    break;
            }
        }

        return true;
    }

    /**
     * Delete all current taxonomies.
     */
    public function deleteAllTaxonomies()
    {
        DB::table((new ServiceTaxonomy())->getTable())->truncate();
        DB::table((new CollectionTaxonomy())->getTable())->truncate();
        DB::table((new UpdateRequest())->getTable())->whereIn(
            'updateable_type',
            [
                UpdateRequest::EXISTING_TYPE_SERVICE,
                UpdateRequest::NEW_TYPE_ORGANISATION_SIGN_UP_FORM,
            ]
        )->delete();

        $taxonomyIds = $this->getDescendantTaxonomyIds([Taxonomy::category()->id]);
        Schema::disableForeignKeyConstraints();
        DB::table((new Taxonomy())->getTable())->whereIn('id', $taxonomyIds)->delete();
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Get all the Category Taxonomy IDs.
     *
     * @param array $rootId
     * @param array $taxonomyIds
     * @param mixed $rootIds
     * @return array
     */
    public function getDescendantTaxonomyIds($rootIds, $taxonomyIds = []): array
    {
        $childIds = DB::table((new Taxonomy())->getTable())->whereIn('parent_id', $rootIds)->pluck('id');

        $taxonomyIds = array_merge($taxonomyIds, array_diff($childIds->all(), $taxonomyIds));

        if (count($childIds)) {
            $childTaxonomyIds = $this->getDescendantTaxonomyIds($childIds, $taxonomyIds);
            $taxonomyIds = array_merge($taxonomyIds, array_diff($childTaxonomyIds, $taxonomyIds));
        }

        return $taxonomyIds;
    }

    /**
     * Get the Taxonomy records to import.
     *
     * @param string $csvUrl
     * @return array || Null
     */
    public function fetchTaxonomyRecords(string $csvUrl)
    {
        $this->line('Fetching ' . $csvUrl);
        $client = new Client();
        try {
            $response = $client->get($csvUrl);
            if (200 === $response->getStatusCode() && $response->getBody()->isReadable()) {
                return csv_to_array($response->getBody()->getContents());
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            if ($this->output) {
                $this->error('Error Fetching Taxonomy Records:');
                $this->error($e->getMessage());
            }
        }
    }

    /**
     * Import the Taxonomy records into the database.
     *
     * @param array $taxonomyRecords
     * @param bool $refresh
     * @param bool $dryrun
     * @return bool | int
     */
    public function importTaxonomyRecords(array $taxonomyRecords, bool $refresh, bool $dryrun)
    {
        if (App::environment() != 'testing') {
            $this->info('Starting transaction');
            DB::beginTransaction();
        }

        if ($this->firstRowLabels) {
            array_shift($taxonomyRecords);
        }

        $taxonomyImports = $this->prepareImports($taxonomyRecords, $refresh);

        if (count($this->failedRows) && App::environment() != 'testing') {
            $this->info('Rolling back transaction');
            DB::rollBack();

            return false;
        }

        $taxonomyImports = $this->mapTaxonomyDepth($taxonomyImports);

        if ($refresh && !$dryrun) {
            $this->deleteAllTaxonomies();
        }

        DB::table((new Taxonomy())->getTable())->insert($taxonomyImports);

        if (!$dryrun && App::environment() != 'testing') {
            $this->info('Commiting transaction');
            DB::commit();
        }

        return count($taxonomyImports);
    }

    /**
     * Sanity check the records before converting them to format for import.
     *
     * @param array $records
     * @param bool $refresh
     * @return array
     */
    public function prepareImports(array $records, bool $refresh): array
    {
        $taxonomys = collect($records)->mapWithKeys(function ($record) {
            return [$record[$this->taxonomyIdColumn] => $record[$this->taxonomyNameColumn]];
        });

        /**
         * Incorrect relationships or existing taxonomies cannot be imported so the import will fail.
         */
        foreach ($records as $record) {
            $failedRow = null;
            $taxonomyName = trim($record[$this->taxonomyNameColumn]);
            /**
             * Does the parent ID exist as a taxonomy row?
             */
            if ($this->parentIdColumn && !empty($record[$this->parentIdColumn]) && !$taxonomys->keys()->contains($record[$this->parentIdColumn])) {
                $failedRow = $record;
                $failedRow[] = 'ID or parent ID invalid';
            }
            /**
             * Does a taxonomy with the same name and optionally, the same parent, exist?
             */
            $exists = DB::table((new Taxonomy())->getTable(), 'taxonomies')
            ->join((new Taxonomy())->getTable() . ' as parents', 'parents.id', '=', 'taxonomies.parent_id')
            ->where('taxonomies.name', $taxonomyName)
            ->when($this->parentIdColumn, function ($query) use ($taxonomys, $record) {
                return $query->where('parents.name', $taxonomys->get($record[$this->parentIdColumn]));
            })
            ->exists();
            if (!$refresh && $exists) {
                $failedRow = $failedRow ?? $record;
                $failedRow[] = 'Taxonomy exists';
            }
            if ($failedRow) {
                $this->failedRows[] = $failedRow;
            }
        }

        if (count($this->failedRows)) {
            return [];
        }

        $imports = $this->mapToUuidKeys($records);

        return $imports;
    }

    /**
     * Convert the flat array to a collection of associative array with UUID keys.
     *
     * @param array $records
     * @return array
     */
    public function mapToUuidKeys(array $records): array
    {
        $taxonomies = collect($records)->mapWithKeys(function ($record) {
            return [
                $record[$this->taxonomyIdColumn] => [
                    'id' => (string) Str::uuid(),
                    'name' => $record[$this->taxonomyNameColumn],
                    'parent_id' => $record[$this->parentIdColumn] ?: $this->rootTaxonomy->id,
                    'order' => 0,
                    'depth' => 1,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ],
            ];
        });

        return $taxonomies->mapWithKeys(function ($record, $key) use ($taxonomies) {
            if ($record['parent_id'] !== $this->rootTaxonomy->id) {
                $record['parent_id'] = $taxonomies->get($record['parent_id'])['id'];
            }
            return [$key => $record];
        })
        ->mapWithKeys(function ($record, $key) {
            return [$record['id'] => $record];
        })
        ->all();
    }

    /**
     * Calculate the depth of each Taxonomy record.
     *
     * @param array $records
     * @return array
     */
    public function mapTaxonomyDepth(array $records): array
    {
        $rootRecords = array_filter($records, function ($record) use ($records) {
            return $record['parent_id'] == $this->rootTaxonomy->id;
        });

        return $this->calculateTaxonomyDepth(array_keys($rootRecords), $records);
    }

    /**
     * Walk through the levels of child records and record the depth.
     *
     * @param array $parentIds
     * @param array $records
     * @param int $depth
     * @return array
     */
    public function calculateTaxonomyDepth($parentIds, &$records, $depth = 1): array
    {
        $newParentIds = [];
        $depth++;
        foreach ($records as $id => &$record) {
            // Is this a direct child node?
            if (in_array($record['parent_id'], $parentIds)) {
                // Set the depth
                $record['depth'] = $depth;
                // Add to the array of parent nodes to pass through to the next depth
                if (!in_array($id, $newParentIds)) {
                    $newParentIds[] = $id;
                }
            }
        }
        if (count($newParentIds)) {
            $this->calculateTaxonomyDepth($newParentIds, $records, $depth);
        }

        return $records;
    }
}
