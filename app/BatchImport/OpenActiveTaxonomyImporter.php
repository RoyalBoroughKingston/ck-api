<?php

namespace App\BatchImport;

use App\Models\Taxonomy;
use Carbon\Exceptions\UnitException;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class OpenActiveTaxonomyImporter
{
    /**
     * The URL for the Open Active taxonomy in jsonld format.
     *
     * @var string
     */
    protected $openActiveDirectoryUrl = 'https://openactive.io/activity-list/activity-list.jsonld';

    /**
     * Dry run: no taxonomies will be created
     *
     * @var bool
     **/
    protected $dryRun = false;

    public function __construct($directoryUrl = null, $dryRun = false)
    {
        if ($directoryUrl) {
            $this->openActiveDirectoryUrl = $directoryUrl;
        }
        $this->dryRun = $dryRun;
    }

    /**
     * Fetch the Open Active taxonomy data and store it as a collection.
     *
     * @param mixed $openActiveDirectoryUrl
     * @return array
     */
    public function fetchTaxonomies($openActiveDirectoryUrl)
    {
        $client = new Client();
        try {
            $response = $client->get($openActiveDirectoryUrl);
            if (200 === $response->getStatusCode() && $response->getBody()->isReadable()) {
                $data = json_decode((string)$response->getBody(), true);

                return $data['concept'];
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            throw $e;
        }
    }

    /**
     * Get the root Open Active category.
     *
     * @return \App\Models\Taxonomy
     */
    public function getOpenActiveCategory()
    {
        return Taxonomy::category()
            ->children()
            ->where('name', 'OpenActive')
            ->firstOrFail();
    }

    /**
     * Runs the import process
     *
     * @return void
     * @throws ModelNotFoundException
     * @throws Exception
     * @throws GuzzleException
     * @throws UnitException
     * @throws RuntimeException
     * @throws MassAssignmentException
     * @return int
     */
    public function runImport():int
    {
        // Get the root Open Active category
        $openActiveCategory = $this->getOpenActiveCategory();

        // Fetch the remote Open Active data
        $openActiveTaxonomies = $this->fetchTaxonomies($this->openActiveDirectoryUrl);

        // Correctly format the remote data ready for import
        $taxonomyImports = $this->mapOpenActiveTaxonomyImport($openActiveCategory, $openActiveTaxonomies);

        return $this->importTaxonomies($openActiveCategory, $taxonomyImports);
    }

    /**
     * Import the formatted taxonomies into the database.
     *
     * @param \App\Models\Taxonomy $rootTaxonomy
     * @param array $taxonomyImports
     * @return int
     */
    public function importTaxonomies(Taxonomy $openActiveCategory, array $taxonomyImports): int
    {
        // Import the data
        if (App::environment() != 'testing') {
            DB::beginTransaction();
        }

        Schema::disableForeignKeyConstraints();

        DB::table((new Taxonomy())->getTable())->insertOrIgnore($taxonomyImports);

        if (!$this->dryRun && App::environment() != 'testing') {
            DB::commit();
        }

        Schema::enableForeignKeyConstraints();

        $openActiveCategory->refresh();

        $openActiveCategory->updateDepth();

        return count($taxonomyImports);
    }

    /**
     * Map the imported data into an import friendly format.
     *
     * @param \App\Models\Taxonomy $rootTaxonomy
     * @param array openActiveTaxonomyData
     * @param mixed $openActiveTaxonomyData
     * @return array
     */
    public function mapOpenActiveTaxonomyImport(Taxonomy $rootTaxonomy, $openActiveTaxonomyData)
    {
        $nowDateTimeString = Carbon::now()->toDateTimeString();

        return array_map(function ($taxonomy) use ($rootTaxonomy, $nowDateTimeString) {
            return array_merge(
                $this->mapOpenActiveTaxonomyToTaxonomyModelSchema($rootTaxonomy, $taxonomy),
                [
                    'created_at' => $nowDateTimeString,
                    'updated_at' => $nowDateTimeString,
                ]
            );
        }, $openActiveTaxonomyData);
    }

    /**
     * Return the uuid component from an Open Active directory url.
     *
     * @param string $identifierUrl
     * @return string
     */
    private function parseIdentifier(string $identifierUrl)
    {
        return mb_substr($identifierUrl, mb_strpos($identifierUrl, '#') + 1);
    }

    /**
     * Convert Open Active taxonomies into Taxonomy model data.
     *
     * @param \App\Models\Taxonomy $rootTaxonomy
     * @param array openActiveTaxonomyData
     * @return array
     */
    private function mapOpenActiveTaxonomyToTaxonomyModelSchema(Taxonomy $rootTaxonomy, array $taxonomyData)
    {
        return [
            'id' => $taxonomyData['identifier'],
            'name' => $taxonomyData['prefLabel'],
            'parent_id' => array_key_exists('broader', $taxonomyData) ? $this->parseIdentifier($taxonomyData['broader'][0]) : $rootTaxonomy->id,
            'order' => 0,
            'depth' => 2,
        ];
    }
}
