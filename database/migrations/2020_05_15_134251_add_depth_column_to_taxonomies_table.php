<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddDepthColumnToTaxonomiesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('taxonomies', function (Blueprint $table) {
            $table->unsignedInteger('depth')
                ->default(0)
                ->after('order')
                ->index();
        });

        // Remove the default value.
        Schema::table('taxonomies', function (Blueprint $table) {
            $table->unsignedInteger('depth')
                ->default(null)
                ->change();
        });

        // Calculate the depth for all.
        DB::table('taxonomies')->orderBy('id')->chunk(
            200,
            function (Collection $taxonomies) {
                $taxonomies->each(function (stdClass $taxonomy) {
                    DB::table('taxonomies')
                        ->where('id', '=', $taxonomy->id)
                        ->update([
                            'depth' => $this->getDepth($taxonomy),
                        ]);
                });
            }
        );
    }

    /**
     * @param \stdClass $taxonomy
     * @return int
     */
    protected function getDepth(stdClass $taxonomy): int
    {
        if ($taxonomy->parent_id === null) {
            return 0;
        }

        $parentTaxonomy = DB::table('taxonomies')
            ->where('id', '=', $taxonomy->parent_id)
            ->first();

        return 1 + $this->getDepth($parentTaxonomy);
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('taxonomies', function (Blueprint $table) {
            $table->dropIndex(['depth']);
            $table->dropColumn('depth');
        });
    }
}
