<?php

namespace App\Http\Controllers\Core\V1;

use App\Events\EndpointHit;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrganisationSignUpForm\StoreRequest;
use App\Http\Responses\UpdateRequestReceived;
use App\Models\UpdateRequest;
use Illuminate\Http\Response;
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
                        // TODO
                    ],
                    'organisation' => [
                        // TODO
                    ],
                    'service' => [
                        // TODO
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
