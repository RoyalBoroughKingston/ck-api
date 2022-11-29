<?php

use App\Models\ServiceTaxonomy;
use App\Models\Taxonomy;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddLgaAndOpenActiveCategoriesToTaxonomiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('taxonomies', function (Blueprint $table) {
            $categoryId = Taxonomy::category()->id;
            $nowDateTimeString = Carbon::now()->toDateTimeString();

            // Create LGA Standards Taxonomy as child of Category
            $lgaStandards = Taxonomy::firstOrCreate(
                [
                    'parent_id' => $categoryId,
                    'name' => 'LGA Standards',
                ],
                [
                    'order' => 0,
                    'depth' => 1,
                    'created_at' => $nowDateTimeString,
                    'updated_at' => $nowDateTimeString,
                ]
            );

            // Create the direct children of LGA Standards
            Taxonomy::firstOrCreate(
                [
                    'parent_id' => $lgaStandards->id,
                    'name' => 'Functions',
                ],
                [
                    'order' => 0,
                    'depth' => 1,
                ]
            )->save();
            Taxonomy::firstOrCreate(
                [
                    'parent_id' => $lgaStandards->id,
                    'name' => 'Services',
                ],
                [
                    'order' => 0,
                    'depth' => 1,
                ]
            )->save();

            // Create OpenActive Taxonomy as child of Category
            Taxonomy::firstOrCreate(
                [
                    'parent_id' => $categoryId,
                    'name' => 'OpenActive',
                ],
                [
                    'order' => 0,
                    'depth' => 1,
                    'created_at' => $nowDateTimeString,
                    'updated_at' => $nowDateTimeString,
                ]
            );

            Taxonomy::category()->updateDepth();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('taxonomies', function (Blueprint $table) {
            $categoryId = Taxonomy::category()->id;

            if (DB::table((new Taxonomy())->getTable())
            ->where('parent_id', $categoryId)
            ->where('name', 'LGA Standards')->exists()) {
                $lgaStandardsId = DB::table((new Taxonomy())->getTable())
                ->where('parent_id', $categoryId)
                ->where('name', 'LGA Standards')
                ->value('id');

                $functionsId = DB::table((new Taxonomy())->getTable())
                ->where('parent_id', $lgaStandardsId)
                ->where('name', 'Functions')
                ->value('id');

                $servicesId = DB::table((new Taxonomy())->getTable())
                ->where('parent_id', $lgaStandardsId)
                ->where('name', 'Services')
                ->value('id');

                DB::table((new ServiceTaxonomy())->getTable())
                ->whereIn('taxonomy_id', [$lgaStandardsId, $functionsId, $servicesId])
                ->delete();

                DB::table((new Taxonomy())->getTable())
                ->whereIn('parent_id', [$lgaStandardsId, $functionsId, $servicesId])
                ->update(['parent_id' => $categoryId]);

                Taxonomy::category()->updateDepth();

                DB::table((new Taxonomy())->getTable())
                ->whereIn('id', [$lgaStandardsId, $functionsId, $servicesId])
                ->delete();
            }
        });
    }
}
