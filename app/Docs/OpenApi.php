<?php

namespace App\Docs;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\OpenApi as BaseOpenApi;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class OpenApi extends BaseOpenApi implements Responsable
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->openapi(static::OPENAPI_3_0_2)
            ->info(Info::create())
            ->servers(Server::create())
            ->paths(
                Paths\Audits\AuditsRootPath::create(),
                Paths\Audits\AuditsIndexPath::create(),
                Paths\Audits\AuditsNestedPath::create(),
                Paths\Collections\Categories\CollectionCategoriesRootPath::create(),
                Paths\Collections\Categories\CollectionCategoriesIndexPath::create(),
                Paths\Collections\Categories\CollectionCategoriesNestedPath::create(),
                Paths\Collections\Personas\CollectionPersonasRootPath::create(),
                Paths\Collections\Personas\CollectionPersonasIndexPath::create(),
                Paths\Collections\Personas\CollectionPersonasNestedPath::create(),
                Paths\Collections\Personas\CollectionPersonasImagePath::create(),
                Paths\Files\FilesRootPath::create(),
                Paths\Locations\LocationsRootPath::create(),
                Paths\Locations\LocationsIndexPath::create(),
                Paths\Locations\LocationsNestedPath::create(),
                Paths\Locations\LocationsImagePath::create(),
                Paths\Notifications\NotificationsRootPath::create(),
                Paths\Notifications\NotificationsIndexPath::create(),
                Paths\Notifications\NotificationsNestedPath::create(),
                Paths\Organisations\OrganisationsRootPath::create(),
                Paths\Organisations\OrganisationsIndexPath::create(),
                Paths\Organisations\OrganisationsNestedPath::create(),
                Paths\Organisations\OrganisationsLogoPath::create(),
                Paths\OrganisationSignUpForms\OrganisationSignUpFormsRootPath::create(),
                Paths\PageFeedbacks\PageFeedbacksRootPath::create(),
                Paths\PageFeedbacks\PageFeedbacksIndexPath::create(),
                Paths\PageFeedbacks\PageFeedbacksNestedPath::create(),
                Paths\Referrals\ReferralsRootPath::create(),
                Paths\Referrals\ReferralsIndexPath::create(),
                Paths\Referrals\ReferralsNestedPath::create(),
                Paths\ReportSchedules\ReportSchedulesRootPath::create(),
                Paths\ReportSchedules\ReportSchedulesIndexPath::create(),
                Paths\ReportSchedules\ReportSchedulesNestedPath::create(),
                Paths\Reports\ReportsRootPath::create(),
                Paths\Reports\ReportsIndexPath::create(),
                Paths\Reports\ReportsNestedPath::create(),
                Paths\Reports\ReportsDownloadPath::create(),
                Paths\Search\SearchRootPath::create(),
                Paths\ServiceLocations\ServiceLocationsRootPath::create(),
                Paths\ServiceLocations\ServiceLocationsIndexPath::create(),
                Paths\ServiceLocations\ServiceLocationsNestedPath::create(),
                Paths\ServiceLocations\ServiceLocationsImagePath::create(),
                Paths\Services\ServicesRootPath::create(),
                Paths\Services\ServicesIndexPath::create(),
                Paths\Services\ServicesNestedPath::create(),
                Paths\Services\ServicesRefreshPath::create(),
                Paths\Services\ServicesRelatedPath::create(),
                Paths\Services\ServicesLogoPath::create(),
                Paths\Services\GalleryItems\GalleryItemsFilePath::create(),
                Paths\Settings\SettingsRootPath::create(),
                Paths\Settings\SettingsBannerImagePath::create(),
                Paths\StatusUpdates\StatusUpdatesRootPath::create(),
                Paths\StatusUpdates\StatusUpdatesIndexPath::create(),
                Paths\StopWords\StopWordsRootPath::create(),
                Paths\Taxonomies\Categories\TaxonomyCategoriesRootPath::create(),
                Paths\Taxonomies\Categories\TaxonomyCategoriesIndexPath::create(),
                Paths\Taxonomies\Categories\TaxonomyCategoriesNestedPath::create(),
                Paths\Taxonomies\Organisations\TaxonomyOrganisationsRootPath::create(),
                Paths\Taxonomies\Organisations\TaxonomyOrganisationsIndexPath::create(),
                Paths\Taxonomies\Organisations\TaxonomyOrganisationsNestedPath::create(),
                Paths\Thesaurus\ThesaurusRootPath::create(),
                Paths\UpdateRequests\UpdateRequestsRootPath::create(),
                Paths\UpdateRequests\UpdateRequestsIndexPath::create(),
                Paths\UpdateRequests\UpdateRequestsNestedPath::create(),
                Paths\UpdateRequests\UpdateRequestsApprovePath::create(),
                Paths\Users\UsersRootPath::create(),
                Paths\Users\UsersIndexPath::create(),
                Paths\Users\UsersNestedPath::create(),
                Paths\Users\UsersUserPath::create(),
                Paths\Users\User\UserSessionsPath::create()
            )
            ->components(Components::create())
            ->security(SecurityRequirement::create())
            ->tags(
                Tags\AuditsTag::create(),
                Tags\CollectionCategoriesTag::create(),
                Tags\CollectionPersonasTag::create(),
                Tags\FilesTag::create(),
                Tags\LocationsTag::create(),
                Tags\NotificationsTag::create(),
                Tags\OrganisationSignUpFormsTag::create(),
                Tags\OrganisationsTag::create(),
                Tags\PageFeedbacksTag::create(),
                Tags\ReferralsTag::create(),
                Tags\ReportSchedulesTag::create(),
                Tags\ReportsTag::create(),
                Tags\SearchTag::create(),
                Tags\SearchEngineTag::create(),
                Tags\ServiceLocationsTag::create(),
                Tags\ServicesTag::create(),
                Tags\SettingsTag::create(),
                Tags\StatusUpdatesTag::create(),
                Tags\TaxonomyCategoriesTag::create(),
                Tags\TaxonomyOrganisationsTag::create(),
                Tags\UpdateRequestsTag::create(),
                Tags\UsersTag::create()
            )
            ->externalDocs(ExternalDocs::create());
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponse($request): JsonResponse
    {
        return response()->json($this->toArray(), Response::HTTP_OK, [
            'Content-Disposition' => 'inline; filename="openapi.json"',
            'Content-Type' => 'application/json; charset=utf-8',
        ]);
    }
}
