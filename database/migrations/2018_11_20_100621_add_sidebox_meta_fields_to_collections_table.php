<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddSideboxMetaFieldsToCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::table('collections')->update([
            'meta' => DB::raw('JSON_SET(`meta`, "$.sidebox_title", NULL, "$.sidebox_content", NULL)'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::table('collections')->update([
            'meta' => DB::raw('JSON_REMOVE(`meta`, "$.sidebox_title", "$.sidebox_content")'),
        ]);
    }
}
