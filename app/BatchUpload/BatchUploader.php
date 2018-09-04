<?php

namespace App\BatchUpload;

use App\Models\Collection;
use App\Models\CollectionTaxonomy;
use App\Models\Location;
use App\Models\Organisation;
use App\Models\Service;
use App\Models\ServiceCriterion;
use App\Models\ServiceLocation;
use App\Models\SocialMedia;
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
            $services = $this->processServices($services, $organisations);
            $serviceLocations = $this->processServiceLocations($serviceLocations, $services, $locations);

            dump($serviceLocations->first());

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
    protected function processCollectionTaxonomies(
        array $collectionTaxonomies,
        EloquentCollection $collections
    ): EloquentCollection {
        $collectionTaxonomies = new EloquentCollection($collectionTaxonomies);
        $collectionTaxonomies = $collectionTaxonomies->map(function (array $collectionTaxonomyArray) use ($collections
        ): CollectionTaxonomy {
            // Get the collection ID.
            $collectionId = $collections->first(function (Collection $collection) use ($collectionTaxonomyArray): bool {
                return $collection->_id == $collectionTaxonomyArray['Collection ID'];
            })->id;

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
                $slug = $iteration > 0 ? $slug . '-' . $iteration : $slug;
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

    /**
     * @param array $services
     * @param \Illuminate\Database\Eloquent\Collection $organisations
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function processServices(array $services, EloquentCollection $organisations): EloquentCollection
    {
        $services = new EloquentCollection($services);
        $services = $services->map(function (array $serviceArray) use ($organisations): Service {
            $organisationId = $organisations->first(function (Organisation $organisation) use ($serviceArray): bool {
                return $organisation->_id == $serviceArray['Organisation ID*'];
            })->id;

            $slug = str_slug($serviceArray['Name*']);
            $iteration = 0;
            do {
                $slug = $iteration > 0 ? $slug . '-' . $iteration : $slug;
                $duplicate = Service::query()->where('slug', $slug)->exists();
                $iteration++;
            } while ($duplicate);

            $isFree = $serviceArray['Is Free*'] == 'yes';

            $isInternal = $serviceArray['Referral Method*'] == 'internal';
            $isExternal = $serviceArray['Referral Method*'] == 'external';
            $isNone = $serviceArray['Referral Method*'] == 'none';

            $service = Service::create([
                'organisation_id' => $organisationId,
                'slug' => $slug,
                'name' => $serviceArray['Name*'],
                'status' => $serviceArray['Status*'],
                'intro' => str_limit($serviceArray['Intro*'], 250),
                'description' => $serviceArray['Description*'],
                'wait_time' => $this->parseWaitTime($serviceArray['Wait Time']),
                'is_free' => $isFree,
                'fees_text' => !$isFree ? $serviceArray['Fees Text'] : null,
                'fees_url' => !$isFree ? $serviceArray['Fees URL'] : null,
                'testimonial' => $serviceArray['Testimonial'],
                'video_embed' => $serviceArray['Video Embed'],
                'url' => $serviceArray['URL*'],
                'contact_name' => $serviceArray['Contact Name*'],
                'contact_phone' => $serviceArray['Contact Phone*'],
                'contact_email' => $serviceArray['Contact Email*'],
                'show_referral_disclaimer' => $serviceArray['Show Referral Disclaimer*'] == 'yes',
                'referral_method' => $serviceArray['Referral Method*'],
                'referral_button_text' => !$isNone ? 'Make referral' : null,
                'referral_email' => $isInternal ? $serviceArray['Referral Email'] : null,
                'referral_url' => $isExternal ? $serviceArray['Referral URL'] : null,
                'seo_title' => $serviceArray['SEO Title*'],
                'seo_description' => str_limit($serviceArray['SEO Description*'], 250),
            ]);

            $service->_id = $serviceArray['ID*'];

            $service->criteria = $this->processCriteria($serviceArray, $service);
            $service->social_medias = $this->processSocialMedia($serviceArray, $service);

            return $service;
        });

        return $services;
    }

    /**
     * @param null|string $waitTime
     * @return null|string
     */
    protected function parseWaitTime(?string $waitTime): ?string
    {
        switch ($waitTime) {
            case 'Within a week':
                return Service::WAIT_TIME_ONE_WEEK;
            case 'Up to two weeks':
                return Service::WAIT_TIME_TWO_WEEKS;
            case 'Up to three weeks':
                return Service::WAIT_TIME_THREE_WEEKS;
            case 'Up to a month':
                return Service::WAIT_TIME_MONTH;
            case 'Not applicable for this service':
            default:
                return null;
        }
    }

    /**
     * @param array $serviceArray
     * @param \App\Models\Service $service
     * @return \App\Models\ServiceCriterion
     */
    protected function processCriteria(array $serviceArray, Service $service): ServiceCriterion
    {
        return $service->serviceCriterion()->create([
            'age_group' => $serviceArray['Critera - Age Group'],
            'disability' => $serviceArray['Criteria - Disability'],
            'employment' => $serviceArray['Criteria Employment'],
            'gender' => $serviceArray['Criteria - Gender'],
            'housing' => $serviceArray['Criteria - Housing'],
            'income' => $serviceArray['Criteria - Income'],
            'language' => $serviceArray['Criteria - Language'],
            'other' => $serviceArray['Criteria - Other'],
        ]);
    }

    /**
     * @param array $serviceArray
     * @param \App\Models\Service $service
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function processSocialMedia(array $serviceArray, Service $service): EloquentCollection
    {
        $socialMedias = new EloquentCollection();

        if ($serviceArray['Social Medias - Twitter']) {
            $socialMedias->push($service->socialMedias()->create([
                'type' => SocialMedia::TYPE_TWITTER,
                'url' => $serviceArray['Social Medias - Twitter'],
            ]));
        }

        if ($serviceArray['Social Medias - Facebook']) {
            $socialMedias->push($service->socialMedias()->create([
                'type' => SocialMedia::TYPE_FACEBOOK,
                'url' => $serviceArray['Social Medias - Facebook'],
            ]));
        }

        if ($serviceArray['Social Medias - Instagram']) {
            $socialMedias->push($service->socialMedias()->create([
                'type' => SocialMedia::TYPE_INSTAGRAM,
                'url' => $serviceArray['Social Medias - Instagram'],
            ]));
        }

        if ($serviceArray['Social Medias - YouTube']) {
            $socialMedias->push($service->socialMedias()->create([
                'type' => SocialMedia::TYPE_YOUTUBE,
                'url' => $serviceArray['Social Medias - YouTube'],
            ]));
        }

        if ($serviceArray['Social Medias - Other']) {
            $socialMedias->push($service->socialMedias()->create([
                'type' => SocialMedia::TYPE_OTHER,
                'url' => $serviceArray['Social Medias - Other'],
            ]));
        }

        return $socialMedias;
    }

    /**
     * @param array $serviceLocations
     * @param \Illuminate\Database\Eloquent\Collection $services
     * @param \Illuminate\Database\Eloquent\Collection $locations
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function processServiceLocations(
        array $serviceLocations,
        EloquentCollection $services,
        EloquentCollection $locations
    ): EloquentCollection {
        $serviceLocations = new EloquentCollection($serviceLocations);

        $serviceLocations = $serviceLocations->map(function (array $serviceLocationArray) use ($services, $locations): ServiceLocation {
            $serviceId = $services->first(function (Service $service) use ($serviceLocationArray): bool {
                return $service->_id == $serviceLocationArray['Service ID*'];
            })->id;

            $locationId = $locations->first(function (Location $location) use ($serviceLocationArray): bool {
                return $location->_id == $serviceLocationArray['Location ID*'];
            })->id;

            return ServiceLocation::create([
                'service_id' => $serviceId,
                'location_id' => $locationId,
            ]);
        });

        return $serviceLocations;
    }
}
