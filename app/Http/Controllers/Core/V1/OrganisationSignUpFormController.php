<?php

namespace App\Http\Controllers\Core\V1;

use App\Events\EndpointHit;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrganisationSignUpForm\StoreRequest;
use App\Http\Responses\UpdateRequestReceived;
use App\Models\UpdateRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class OrganisationSignUpFormController extends Controller
{
    /**
     * OrganisationSignUpFormController constructor.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * @param \App\Http\Requests\OrganisationSignUpForm\StoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        return DB::transaction(function () use ($request) {
            /** @var \App\Models\UpdateRequest $updateRequest */
            $updateRequest = UpdateRequest::create([
                'updateable_type' => UpdateRequest::NEW_TYPE_ORGANISATION_SIGN_UP_FORM,
                'data' => [
                    'user' => [
                        'first_name' => $request->input('user.first_name'),
                        'last_name' => $request->input('user.last_name'),
                        'email' => $request->input('user.email'),
                        'phone' => $request->input('user.phone'),
                    ],
                    'organisation' => [
                        'slug' => $request->input('organisation.slug'),
                        'name' => $request->input('organisation.name'),
                        'description' => sanitize_markdown(
                            $request->input('organisation.description')
                        ),
                        'url' => $request->input('organisation.url'),
                        'email' => $request->input('organisation.email'),
                        'phone' => $request->input('organisation.phone'),
                    ],
                    'service' => [
                        'slug' => $request->input('service.slug'),
                        'name' => $request->input('service.name'),
                        'type' => $request->input('service.type'),
                        'intro' => $request->input('service.intro'),
                        'description' => sanitize_markdown(
                            $request->input('service.description')
                        ),
                        'wait_time' => $request->input('service.wait_time'),
                        'is_free' => $request->input('service.is_free'),
                        'fees_text' => $request->input('service.fees_text'),
                        'fees_url' => $request->input('service.fees_url'),
                        'testimonial' => $request->input('service.testimonial'),
                        'video_embed' => $request->input('service.video_embed'),
                        'url' => $request->input('service.url'),
                        'contact_name' => $request->input('service.contact_name'),
                        'contact_phone' => $request->input('service.contact_phone'),
                        'contact_email' => $request->input('service.contact_email'),
                        'criteria' => [
                            'age_group' => $request->input('service.criteria.age_group'),
                            'disability' => $request->input('service.criteria.disability'),
                            'employment' => $request->input('service.criteria.employment'),
                            'gender' => $request->input('service.criteria.gender'),
                            'housing' => $request->input('service.criteria.housing'),
                            'income' => $request->input('service.criteria.income'),
                            'language' => $request->input('service.criteria.language'),
                            'other' => $request->input('service.criteria.other'),
                        ],
                        'useful_infos' => array_map(
                            function (array $usefulInfo) {
                                return Arr::only($usefulInfo, [
                                    'title',
                                    'description',
                                    'order',
                                ]);
                            },
                            $request->input('service.useful_infos')
                        ),
                        'offerings' => array_map(
                            function (array $offering) {
                                return Arr::only($offering, [
                                    'offering',
                                    'order',
                                ]);
                            },
                            $request->input('service.offerings')
                        ),
                        'social_medias' => array_map(
                            function (array $socialMedia) {
                                return Arr::only($socialMedia, [
                                    'type',
                                    'url',
                                ]);
                            },
                            $request->input('service.social_medias')
                        ),
                    ],
                ],
            ]);

            event(
                EndpointHit::onCreate(
                    $request,
                    "Submitted organisation sign up form [{$updateRequest->id}]",
                    $updateRequest
                )
            );

            return new UpdateRequestReceived($updateRequest, Response::HTTP_CREATED);
        });
    }
}
