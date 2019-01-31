<?php

namespace App\Http\Controllers\Core\V1;

use App\Console\Commands\Ck\ReindexElasticsearchCommand;
use App\Events\EndpointHit;
use App\Http\Controllers\Controller;
use App\Http\Requests\StopWords\IndexRequest;
use App\Http\Requests\StopWords\UpdateRequest;
use App\Http\Responses\StopWords;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class StopWordsController extends Controller
{
    const CACHE_KEY = 'stop_words';

    /**
     * StopWordsController constructor.
     */
    public function __construct()
    {
        $this->middleware('throttle:60,1');
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Http\Requests\StopWords\IndexRequest $request
     * @return \App\Http\Responses\StopWords
     * @throws \Exception
     */
    public function index(IndexRequest $request)
    {
        $stopWords = cache()->rememberForever(static::CACHE_KEY, function (): array {
            $content = Storage::cloud()->get('elasticsearch/stop-words.csv');
            $stopWords = csv_to_array($content);

            $stopWords = collect($stopWords)->map(function (array $stopWord) {
                return mb_strtolower($stopWord[0]);
            });

            return $stopWords->toArray();
        });

        event(EndpointHit::onRead($request, 'Viewed stop words'));

        return new StopWords($stopWords);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\StopWords\UpdateRequest $request
     * @return \App\Http\Responses\StopWords
     * @throws \Exception
     */
    public function update(UpdateRequest $request)
    {
        $stopWords = array_map(function (string $stopWord) {
            return mb_strtolower($stopWord);
        }, $request->stop_words);

        // Convert the array to a string.
        $stopWordsCsv = array_to_csv(
            array_map(function (string $stopWord) {
                return array_wrap($stopWord);
            }, $stopWords)
        );

        // Save the string to the stop words.
        Storage::cloud()->put('elasticsearch/stop-words.csv', $stopWordsCsv);

        // Clear the cache.
        cache()->forget(static::CACHE_KEY);

        // Reindex elasticsearch.
        Artisan::call(ReindexElasticsearchCommand::class);

        event(EndpointHit::onUpdate($request, 'Updated stop words'));

        // Return the stop words.
        return new StopWords($stopWords);
    }
}
