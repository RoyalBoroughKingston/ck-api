<?php

namespace App\BatchUpload;

use App\Models\Collection;
use App\Models\CollectionTaxonomy;
use App\Models\Location;
use App\Models\Organisation;
use Exception;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class BatchUploader
{
    /**
     * @var \PhpOffice\PhpSpreadsheet\Reader\Xlsx
     */
    protected $reader;

    /**
     * BatchUploader constructor.
     */
    public function __construct()
    {
        $this->reader = new XlsxReader();
        $this->reader->setReadDataOnly(true);
    }

    /**
     * Validates and then uploads the file.
     *
     * @param string $filePath
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws \Exception
     */
    public function upload(string $filePath)
    {
        // Load the spreadsheet.
        $spreadsheet = $this->reader->load($filePath);

        // Load each worksheet.
        $organisationsSheet = $spreadsheet->getSheetByName('Organisation');
        $servicesSheet = $spreadsheet->getSheetByName('Service');
        $locationsSheet = $spreadsheet->getSheetByName('Location');
        $serviceLocationsSheet = $spreadsheet->getSheetByName('Service Location');
        $collectionCategoriesSheet = $spreadsheet->getSheetByName('Collection - Category');
        $taxonomyServicesSheet = $spreadsheet->getSheetByName('Taxonomies - Service');
        $taxonomyCategoriesSheet = $spreadsheet->getSheetByName('Taxonomies - Category');

        // Convert the worksheets to associative arrays.
        $organisations = $this->toArray($organisationsSheet);
        $services = $this->toArray($servicesSheet);
        $locations = $this->toArray($locationsSheet);
        $serviceLocations = $this->toArray($serviceLocationsSheet);
        $collections = $this->toArray($collectionCategoriesSheet); // Categories only - not persona.
        $serviceTaxonomies = $this->toArray($taxonomyServicesSheet);
        $collectionTaxonomies = $this->toArray($taxonomyCategoriesSheet);

        // Process.
        try {
            DB::beginTransaction();

            $collections = $this->processCollections($collections);
            $collectionTaxonomies = $this->processCollectionTaxonomies($collectionTaxonomies, $collections);
            $locations = $this->processLocations($locations);
            $organisations = $this->processOrganisations($organisations);

            dump($organisations);

            // DB::commit();
            DB::rollBack(); // TODO: Remove this
        } catch (Exception $exception) {
            DB::rollBack();

            throw $exception;
        }
    }

    /**
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @return array
     */
    protected function toArray(Worksheet $sheet): array
    {
        $array = $sheet->toArray();
        $headings = array_shift($array);

        $array = array_map(function ($row) use ($headings) {
            $resource = [];

            foreach ($headings as $column => $heading) {
                $resource[$heading] = $row[$column];
            }

            return $resource;
        }, $array);

        return $array;
    }

    /**
     * @param array $collections
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function processCollections(array $collections): EloquentCollection
    {
        $order = Collection::categories()->orderByDesc('order')->first()->order;

        $collections = new EloquentCollection($collections);
        $collections = $collections->map(function (array $collectionArray) use (&$order): Collection {
            // Increment order.
            $order++;

            // Create a collection instance.
            $collection = Collection::create([
                'type' => Collection::TYPE_CATEGORY,
                'name' => $collectionArray['Category Name'],
                'meta' => [
                    'icon' => 'coffee',
                    'intro' => 'Lorem ipsum',
                ],
                'order' => $order,
            ]);

            // Assign the ID provided by the spreadsheet.
            $collection->_id = $collectionArray['Category ID'];

            return $collection;
        });

        return $collections;
    }

    /**
     * @param array $collectionTaxonomies
     * @param \Illuminate\Database\Eloquent\Collection $collections
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function processCollectionTaxonomies(array $collectionTaxonomies, EloquentCollection $collections): EloquentCollection
    {
        $collectionTaxonomies = new EloquentCollection($collectionTaxonomies);
        $collectionTaxonomies = $collectionTaxonomies->map(function (array $collectionTaxonomyArray) use ($collections): CollectionTaxonomy {
            // Get the collection ID.
            $collectionId = $collections->first(function (Collection $collection) use ($collectionTaxonomyArray): bool {
                return $collection->_id == $collectionTaxonomyArray['Collection ID'];
            })->id;

            // Fail if it doesn't exist.
            if ($collectionId === null) {
                throw new Exception("Collection ID [{$collectionTaxonomyArray['Collection ID']}] does not exist");
            }

            // Create a collection taxonomy instance.
            return CollectionTaxonomy::create([
                'collection_id' => $collectionId,
                'taxonomy_id' => $collectionTaxonomyArray['Taxonomy ID'],
            ]);
        });

        return $collectionTaxonomies;
    }

    /**
     * @param array $locations
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function processLocations(array $locations): EloquentCollection
    {
        $locations = new EloquentCollection($locations);
        $locations = $locations->map(function (array $locationArray): Location {
            $location = new Location(array_filter([
                'address_line_1' => $locationArray['Address Line 1*'],
                'address_line_2' => $locationArray['Address Line 2'],
                'address_line_3' => $locationArray['Address Line 3'],
                'city' => $locationArray['City*'],
                'county' => $locationArray['County*'],
                'postcode' => $locationArray['Postcode*'],
                'country' => $locationArray['Country*'],
            ]));

            $location->has_wheelchair_access = false;
            $location->has_induction_loop = false;

            // Save the location.
            $location->updateCoordinate()->save();

            // Assign the ID provided by the spreadsheet.
            $location->_id = $locationArray['ID*'];

            return $location;
        });

        return $locations;
    }

    /**
     * @param array $organisations
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function processOrganisations(array $organisations): EloquentCollection
    {
        $organisations = new EloquentCollection($organisations);
        $organisations = $organisations->map(function (array $organisationArray): Organisation {
            $slug = str_slug($organisationArray['Name*']);
            $iteration = 0;
            do {
                $slug = $iteration > 0 ? $slug.'-'.$iteration : $slug;
                $duplicate = Organisation::query()->where('slug', $slug)->exists();
                $iteration++;
            } while ($duplicate);

            $organisation = Organisation::create([
                'slug' => $slug,
                'name' => $organisationArray['Name*'],
                'description' => $organisationArray['Description*'],
                'url' => $organisationArray['URL*'],
                'email' => $organisationArray['Email*'],
                'phone' => $organisationArray['Phone*'],
            ]);

            $organisation->_id = $organisationArray['ID*'];

            return $organisation;
        });

        return $organisations;
    }
}
