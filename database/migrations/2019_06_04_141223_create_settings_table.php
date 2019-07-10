<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key');
            $table->json('value');
        });

        DB::table('settings')->insert([
            'key' => 'cms',
            'value' => json_encode([
                'frontend' => [
                    'global' => [
                        'footer_title' => 'Footer title',
                        'footer_content' => 'Footer content',
                        'contact_phone' => 'Contact phone',
                        'contact_email' => 'Contact email',
                        'facebook_handle' => 'Facebook handle',
                        'twitter_handle' => 'Twitter handle',
                    ],
                    'home' => [
                        'search_title' => 'Search title',
                        'categories_title' => 'Categories title',
                        'personas_title' => 'Personas title',
                        'personas_content' => 'Personas content',
                    ],
                    'terms_and_conditions' => [
                        'title' => 'Title',
                        'content' => 'Content',
                    ],
                    'privacy_policy' => [
                        'title' => 'Title',
                        'content' => 'Content',
                    ],
                    'about' => [
                        'title' => 'Title',
                        'content' => 'Content',
                        'video_url' => 'Video URL',
                    ],
                    'contact' => [
                        'title' => 'Title',
                        'content' => 'Content',
                    ],
                    'get_involved' => [
                        'title' => 'Title',
                        'content' => 'Content',
                    ],
                    'favourites' => [
                        'title' => 'Title',
                        'content' => 'Content',
                    ],
                ],
            ]),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
