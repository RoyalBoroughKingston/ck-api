<?php

namespace App\Http\Controllers\Core\V1;

use App\Events\EndpointHit;
use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\IndexRequest;
use App\Http\Requests\Setting\UpdateRequest;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    /**
     * SettingController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api')->except('index');
    }

    /**
     * @param \App\Http\Requests\Setting\IndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexRequest $request)
    {
        event(EndpointHit::onRead($request, 'Viewed all settings'));

        return Setting::toResponse();
    }

    /**
     * @param \App\Http\Requests\Setting\UpdateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request)
    {
        return DB::transaction(function () use ($request) {
            Setting::cms()
                ->update([
                    'value' => [
                        'frontend' => [
                            'global' => [
                                'footer_title' => $request->input('cms.frontend.global.footer_title'),
                                'footer_content' => sanitize_markdown($request->input('cms.frontend.global.footer_content')),
                                'contact_phone' => $request->input('cms.frontend.global.contact_phone'),
                                'contact_email' => $request->input('cms.frontend.global.contact_email'),
                                'facebook_handle' => $request->input('cms.frontend.global.facebook_handle'),
                                'twitter_handle' => $request->input('cms.frontend.global.twitter_handle'),
                            ],
                            'home' => [
                                'search_title' => $request->input('cms.frontend.home.search_title'),
                                'categories_title' => $request->input('cms.frontend.home.categories_title'),
                                'personas_title' => $request->input('cms.frontend.home.personas_title'),
                                'personas_content' => sanitize_markdown($request->input('cms.frontend.home.personas_content')),
                            ],
                            'terms_and_conditions' => [
                                'title' => $request->input('cms.frontend.terms_and_conditions.title'),
                                'content' => sanitize_markdown($request->input('cms.frontend.terms_and_conditions.content')),
                            ],
                            'privacy_policy' => [
                                'title' => $request->input('cms.frontend.privacy_policy.title'),
                                'content' => sanitize_markdown($request->input('cms.frontend.privacy_policy.content')),
                            ],
                            'about' => [
                                'title' => $request->input('cms.frontend.about.title'),
                                'content' => sanitize_markdown($request->input('cms.frontend.about.content')),
                                'video_url' => $request->input('cms.frontend.about.video_url'),
                            ],
                            'contact' => [
                                'title' => $request->input('cms.frontend.contact.title'),
                                'content' => sanitize_markdown($request->input('cms.frontend.contact.content')),
                            ],
                            'get_involved' => [
                                'title' => $request->input('cms.frontend.get_involved.title'),
                                'content' => sanitize_markdown($request->input('cms.frontend.get_involved.content')),
                            ],
                            'favourites' => [
                                'title' => $request->input('cms.frontend.favourites.title'),
                                'content' => sanitize_markdown($request->input('cms.frontend.favourites.content')),
                            ],
                        ],
                    ],
                ]);

            event(EndpointHit::onUpdate($request, 'Updated settings'));

            return Setting::toResponse();
        });
    }
}
