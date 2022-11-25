<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AddSlugColumnToCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('collections', function (Blueprint $table) {
            $table->string('slug')->after('type')->nullable()->unique();
        });

        DB::table('collections')
            ->orderBy('id')
            ->chunk(200, function (Collection $collections): void {
                foreach ($collections as $collection) {
                    $index = 0;
                    do {
                        $slug = Str::slug($collection->name);
                        $slug .= $index === 0 ? '' : "-{$index}";

                        $slugAlreadyUsed = DB::table('collections')
                            ->where('slug', '=', $slug)
                            ->exists();

                        if ($slugAlreadyUsed) {
                            $index++;
                            continue;
                        }

                        DB::table('collections')
                            ->where('id', '=', $collection->id)
                            ->update(['slug' => $slug]);

                        continue 2;
                    } while (true);
                }
            });

        DB::statement('alter table `collections` modify column `slug` varchar(255) not null');
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('collections', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
}
