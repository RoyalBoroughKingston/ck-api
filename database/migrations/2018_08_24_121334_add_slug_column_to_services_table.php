<?php

use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSlugColumnToServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('slug')->after('logo_file_id');
        });

        Service::query()->chunk(200, function (Collection $services) {
            $services->each(function (Service $service) {
                $iteration = 0;
                do {
                    $slug = $iteration === 0
                        ? str_slug($service->name)
                        : str_slug($service->name).'-'.$iteration;
                    $iteration++;
                } while (Service::query()->where('slug', $slug)->exists());

                $service->update(['slug' => $slug]);
            });
        });

        Schema::table('services', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
}
