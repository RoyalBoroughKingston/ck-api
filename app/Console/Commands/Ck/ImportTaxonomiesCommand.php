<?php

namespace App\Console\Commands\Ck;

use App\Models\CollectionTaxonomy;
use App\Models\ServiceTaxonomy;
use App\Models\Taxonomy;
use App\Models\UpdateRequest;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ImportTaxonomiesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ck:import-taxonomies
                            {url : The url of the taxonomies .csv file (e.g. http://...)}
                            {id-column : The column (1 = first) which contains the taxonomy identifier}
                            {name-column : The column (1 = first) which contains the taxonomy name}
                            {--root-id= : The UUID of the root taxonomy from which imported taxonomies will descend (if empty new root categories will be created)}
                            {--parent-column= : The column (1 = first) which contains the taxonomy\'s parent identifier (if empty the parent will be the taxonomy set as root)}
                            {--skip-first : Does the first row contain labels?}
                            {--ignore-duplicates : No error on duplicate taxonomies, but duplicates not imported. Do not use with --refresh}
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
     * Headers from the imported CSV if applicable.
     *
     * @var array
     */
    protected $csvHeaders;

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
     * Ignore duplicate imports, no duplicate warning
     *
     * @var bool
     **/
    protected $ignoreDuplicates = false;

    /**
     * Should the current taxonomies be deleted first?
     *
     * @var bool
     **/
    protected $refresh = false;

    /**
     * Dry run: no taxonomies will be created
     *
     * @var bool
     **/
    protected $dryRun = false;

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
        if (!$this->manageParameters()) {
            return;
        }

        if ($this->dryRun) {
            $this->warn('Dry Run, no data will be committed, delete option ignored');
        }

        $records = $this->fetchTaxonomyRecords($this->csvUrl);

        if (is_array($records) && count($records)) {
            $this->info('Spreadsheet uploaded');

            if ($this->refresh) {
                $this->warn($this->dryRun ? 'Current Taxonomies will be preserved' : 'All current Taxonomies will be deleted');
            }

            $importCount = $this->importTaxonomyRecords($records);

            if (count($this->failedRows)) {
                $this->showfailedRows($records);
            } else {
                $this->info('All records imported. Total records imported: ' . $importCount);
            }
        } else {
            $this->warn('Spreadsheet could not be uploaded');
        }

        if ($this->dryRun) {
            $this->warn('Dry Run complete');
        }
    }

    /**
     * Prompt and retrieve all the required params
     *
     * @return array | false
     **/
    public function manageParameters()
    {
        if (!preg_match("/\b(?:(?:https?):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $this->argument('url'))) {
            $this->error('The CSV file URL is invalid. Exiting');

            return false;
        }
        $this->csvUrl = $this->argument('url');

        if (!is_numeric($this->argument('id-column')) || !is_numeric($this->argument('name-column')) || !(is_null($this->option('parent-column')) || is_numeric($this->option('parent-column')))) {
            $this->error('Column number references should be numeric');

            return false;
        }
        $this->taxonomyIdColumn = $this->argument('id-column') - 1;
        $this->taxonomyNameColumn = $this->argument('name-column') - 1;
        $this->parentIdColumn = $this->option('parent-column') ? $this->option('parent-column') - 1 : null;

        if (empty($this->option('root-id'))) {
            $this->rootTaxonomy = Taxonomy::category();
        } elseif (Taxonomy::query()->where('id', $this->option('root-id'))->exists()) {
            $this->rootTaxonomy = Taxonomy::find($this->option('root-id'));
        } else {
            $this->error('The root category does not exist. Exiting');

            return false;
        }

        if ($this->ignoreDuplicates && $this->refresh) {
            $this->error('Do not use the options --ignore-duplicates and --refresh together as some taxonomies may not be imported');

            return false;
        }

        $this->firstRowLabels = $this->option('skip-first');
        $this->ignoreDuplicates = $this->option('ignore-duplicates');
        $this->refresh = $this->option('refresh');
        $this->dryRun = $this->option('dry-run');

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
                return $this->parse_csv($response->getBody()->getContents());
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
     * Parse the csv into an array
     * Unlike php built in csv parsing functions, this will work with fields containing quotes and new lines
     *
     * @param mixed $csv_string
     * @param string $delimiter
     * @param bool $skip_empty_lines
     * @param bool $trim_fields
     * @return string[]|false
     * @author https://www.php.net/manual/en/function.str-getcsv.php#111665
     */
    protected function parse_csv($csv_string, $delimiter = ',', $skip_empty_lines = true, $trim_fields = true)
    {
        $enc = preg_replace('/(?<!")""/', '!!Q!!', $csv_string);
        $enc = preg_replace_callback(
            '/"(.*?)"/s',
            function ($field) {
                return urlencode(utf8_encode($field[1]));
            },
            $enc
        );
        $lines = preg_split($skip_empty_lines ? ($trim_fields ? '/( *\R)+/s' : '/\R+/s') : '/\R/s', $enc);

        return array_map(
            function ($line) use ($delimiter, $trim_fields) {
                $fields = $trim_fields ? array_map('trim', explode($delimiter, $line)) : explode($delimiter, $line);

                return array_map(
                    function ($field) {
                        return str_replace('!!Q!!', '"', utf8_decode(urldecode($field)));
                    },
                    $fields
                );
            },
            $lines
        );
    }

    /**
     * Import the Taxonomy records into the database.
     *
     * @param array $taxonomyRecords
     * @return bool | int
     */
    public function importTaxonomyRecords(array $taxonomyRecords)
    {
        if (App::environment() != 'testing') {
            $this->info('Starting transaction');
            DB::beginTransaction();
        }

        if ($this->firstRowLabels) {
            $this->csvHeaders = $taxonomyRecords[0];
            array_shift($taxonomyRecords);
        }

        $taxonomyImports = $this->prepareImports($taxonomyRecords);

        if (count($this->failedRows) && App::environment() != 'testing') {
            $this->info('Rolling back transaction');
            DB::rollBack();

            return false;
        }

        $taxonomyImports = $this->mapTaxonomyDepth($taxonomyImports);

        if ($this->refresh && !$this->dryRun) {
            $this->deleteAllTaxonomies();
        }

        DB::table((new Taxonomy())->getTable())->insert($taxonomyImports);

        if (!$this->dryRun && App::environment() != 'testing') {
            $this->info('Commiting transaction');
            DB::commit();
        }

        $this->rootTaxonomy->refresh();

        $this->rootTaxonomy->updateDepth();

        return count($taxonomyImports);
    }

    /**
     * Sanity check the records before converting them to format for import.
     *
     * @param array $records
     * @return array
     */
    public function prepareImports(array $records): array
    {
        $taxonomys = collect($records)->mapWithKeys(function ($record) {
            return [$record[$this->taxonomyIdColumn] => $record[$this->taxonomyNameColumn]];
        });

        /**
         * Incorrect relationships or existing taxonomies cannot be imported so the import will fail.
         */
        foreach ($records as $key => $record) {
            $failedRow = null;

            /**
             * Does the parent ID exist as a taxonomy row?
             */
            if ($this->parentIdColumn && !empty($record[$this->parentIdColumn]) && !$taxonomys->keys()->contains($record[$this->parentIdColumn])) {
                $failedRow = $record;
                $failedRow[] = 'ID or parent ID invalid';
            }
            if ($this->taxonomyExists($record, $taxonomys)) {
                if ($this->ignoreDuplicates) {
                    unset($records[$key]);
                } else {
                    $failedRow = $failedRow ?? $record;
                    $failedRow[] = 'Taxonomy exists';
                }
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
     * Does the taxonomy exist in the database
     *
     * @param array $record
     * @param \illuminate\Support\Collection $taxonomyNames
     * @return bool
     **/
    public function taxonomyExists(array $record, Collection $taxonomyNames): bool
    {
        /**
         * Does a taxonomy with the same name and optionally, the same parent, exist?
         */
        $existingTaxonomyIds = DB::table((new Taxonomy())->getTable(), 'taxonomies')
            ->join((new Taxonomy())->getTable() . ' as parents', 'parents.id', '=', 'taxonomies.parent_id')
            ->where('taxonomies.name', trim($record[$this->taxonomyNameColumn]))
            ->where('parents.name', $this->parentIdColumn ? $taxonomyNames->get($record[$this->parentIdColumn]) : $this->rootTaxonomy->name)
            ->pluck('taxonomies.id');
        if (!$this->refresh && count($existingTaxonomyIds)) {
            foreach ($existingTaxonomyIds as $taxonomyId) {
                if (in_array($this->rootTaxonomy->id, $this->taxonomyAncestors($taxonomyId))) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get the ancestors of a taxonomy
     *
     * @param string $taxonomyId
     * @return array
     **/
    public function taxonomyAncestors(string $taxonomyId): array
    {
        $taxonomyTable = (new Taxonomy)->getTable();

        return collect(DB::select(
            'SELECT T2.id
            FROM (
                SELECT
                    @r AS _id,
                    (SELECT @r := parent_id FROM ' . $taxonomyTable . ' WHERE id = _id) AS parent,
                    @l := @l + 1 AS lvl
                FROM
                    (SELECT @r := "' . $taxonomyId . '", @l := 0) vars,
                    ' . $taxonomyTable . ' t
                WHERE @r <> "' . Taxonomy::category()->id . '"
                ) T1
            JOIN ' . $taxonomyTable . ' T2
            ON T1._id = T2.id
            WHERE T2.id <> "' . $taxonomyId . '"
            ORDER BY T1.lvl'
        ))
            ->pluck('id')
            ->all();
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
                    'parent_id' => ($this->parentIdColumn && !empty($record[$this->parentIdColumn])) ? $record[$this->parentIdColumn] : $this->rootTaxonomy->id,
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
        return $this->calculateTaxonomyDepth([$this->rootTaxonomy->id], $records, $this->rootTaxonomy->depth);
    }

    /**
     * Walk through the levels of child records and record the depth.
     * Caution: recursive
     *
     * @param array $parentIds
     * @param array $records
     * @param int $depth
     * @return array
     */
    public function calculateTaxonomyDepth($parentIds, &$records, $depth): array
    {
        $newParentIds = [];
        $depth++;

        foreach ($records as &$record) {
            // Is this a direct child node?
            if (in_array($record['parent_id'], $parentIds)) {
                // Set the depth
                $record['depth'] = $depth;
                // Add to the array of parent nodes to pass through to the next depth
                if (!in_array($record['id'], $newParentIds)) {
                    $newParentIds[] = $record['id'];
                }
            }
        }
        if (count($newParentIds)) {
            $this->calculateTaxonomyDepth($newParentIds, $records, $depth);
        }

        return $records;
    }

    /**
     * Show the failed rows
     *
     * @param array $records
     * @return null
     **/
    public function showfailedRows(array $records)
    {
        $this->warn('Unable to import all records. Failed records:');
        if ($this->firstRowLabels) {
            $headers = $this->csvHeaders;
        } else {
            $headers = range(1, count($records[0]));
            $headers[$this->taxonomyIdColumn] = 'ID';
            $headers[$this->taxonomyNameColumn] = 'Name';
            if ($this->parentIdColumn) {
                $headers[$this->parentIdColumn] = 'Parent';
            }
        }
        $headers[] = 'Error';
        $this->table($headers, $this->failedRows);
    }
}
