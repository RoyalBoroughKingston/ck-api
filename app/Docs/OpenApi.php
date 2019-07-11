<?php

namespace App\Docs;

use App\Docs\Tags\AuditsTag;
use App\Docs\Tags\CollectionCategoriesTag;
use App\Docs\Tags\CollectionPersonasTag;
use App\Docs\Tags\FilesTag;
use App\Docs\Tags\LocationsTag;
use App\Docs\Tags\NotificationsTag;
use App\Docs\Tags\OrganisationsTag;
use App\Docs\Tags\PageFeedbacksTag;
use App\Docs\Tags\ReferralsTag;
use App\Docs\Tags\ReportSchedulesTag;
use App\Docs\Tags\ReportsTag;
use App\Docs\Tags\SearchEngineTag;
use App\Docs\Tags\SearchTag;
use App\Docs\Tags\ServiceLocationsTag;
use App\Docs\Tags\ServicesTag;
use App\Docs\Tags\SettingsTag;
use App\Docs\Tags\StatusUpdatesTag;
use App\Docs\Tags\TaxonomyCategoriesTag;
use App\Docs\Tags\TaxonomyOrganisationsTag;
use App\Docs\Tags\UpdateRequestsTag;
use App\Docs\Tags\UsersTag;
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
     * @return \GoldSpecDigital\ObjectOrientedOAS\OpenApi
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->openapi(static::OPENAPI_3_0_2)
            ->info(Info::create())
            ->servers(Server::create())
            ->paths()
            ->components(Components::create())
            ->security(SecurityRequirement::create())
            ->tags(
                AuditsTag::create(),
                CollectionCategoriesTag::create(),
                CollectionPersonasTag::create(),
                FilesTag::create(),
                LocationsTag::create(),
                NotificationsTag::create(),
                OrganisationsTag::create(),
                PageFeedbacksTag::create(),
                ReferralsTag::create(),
                ReportSchedulesTag::create(),
                ReportsTag::create(),
                SearchTag::create(),
                SearchEngineTag::create(),
                ServiceLocationsTag::create(),
                ServicesTag::create(),
                SettingsTag::create(),
                StatusUpdatesTag::create(),
                TaxonomyCategoriesTag::create(),
                TaxonomyOrganisationsTag::create(),
                UpdateRequestsTag::create(),
                UsersTag::create()
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
