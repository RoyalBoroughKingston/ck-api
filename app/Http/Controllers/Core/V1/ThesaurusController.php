<?php

namespace App\Http\Controllers\Core\V1;

use App\Events\EndpointHit;
use App\Http\Controllers\Controller;
use App\Http\Requests\Thesaurus\IndexRequest;
use App\Http\Responses\Thesaurus;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class ThesaurusController extends Controller
{
    const CACHE_KEY = 'thesaurus';

    /**
     * ThesaurusController constructor.
     */
    public function __construct()
    {
        $this->middleware('throttle:60,1');
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Http\Requests\Thesaurus\IndexRequest $request
     * @return \App\Http\Responses\Thesaurus
     * @throws \Exception
     */
    public function index(IndexRequest $request)
    {
        $thesaurus = cache()->rememberForever(static::CACHE_KEY, function (): array {
            $content = Storage::cloud()->get('elasticsearch/thesaurus.csv');
            $thesaurus = csv_to_array($content);

            $thesaurus = collect($thesaurus)->map(function (array $synonyms) {
                return collect($synonyms);
            });

            $thesaurus = $thesaurus
                ->map(function (Collection $synonyms) {
                    return $synonyms
                        ->reject(function (string $term) {
                            // Filter out any empty strings.
                            return $term === '';
                        })
                        ->map(function (string $term) {
                            // Convert each term to lower case.
                            return strtolower($term);
                        });
                })
                ->toArray();

            return $thesaurus;
        });

        event(EndpointHit::onRead($request, 'Viewed thesaurus'));

        return new Thesaurus($thesaurus);
    }
}
