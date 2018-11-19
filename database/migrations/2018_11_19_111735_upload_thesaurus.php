<?php

use App\Console\Commands\Ck\ReindexElasticsearch;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class UploadThesaurus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function up()
    {
        $thesaurus = Storage::disk('local')->get('elasticsearch/thesaurus.csv');
        Storage::cloud()->put('elasticsearch/thesaurus.csv', $thesaurus);

        Artisan::call(ReindexElasticsearch::class);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Storage::cloud()->delete('elasticsearch/thesaurus.csv');

        Artisan::call(ReindexElasticsearch::class);
    }
}
