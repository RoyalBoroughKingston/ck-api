<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateSideboxSchemaInMetaColumnOfCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::table('collections')
            ->update([
                'meta' => DB::raw('JSON_SET(`meta`, "$.sideboxes", JSON_ARRAY())'),
            ]);

        DB::table('collections')
            ->whereRaw('JSON_TYPE(`meta`->"$.sidebox_title") != "NULL"')
            ->update([
                'meta' => DB::raw(
                    <<<'EOT'
JSON_ARRAY_APPEND(
    `meta`,
    "$.sideboxes",
    JSON_OBJECT(
        "title", `meta`->>"$.sidebox_title",
        "content", `meta`->>"$.sidebox_content"
    )
)
EOT
                ),
            ]);

        DB::table('collections')
            ->update([
                'meta' => DB::raw('JSON_REMOVE(`meta`, "$.sidebox_title", "$.sidebox_content")'),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::table('collections')
            ->update([
                'meta' => DB::raw('JSON_SET(`meta`, "$.sidebox_title", NULL, "$.sidebox_content", NULL)'),
            ]);

        DB::table('collections')
            ->where(DB::raw('JSON_LENGTH(`meta`->"$.sideboxes")'), '>', 0)
            ->update([
                'meta' => DB::raw('JSON_SET(`meta`, "$.sidebox_title", `meta`->>"$.sideboxes[0].title", "$.sidebox_content", `meta`->>"$.sideboxes[0].content")'),
            ]);

        DB::table('collections')
            ->update([
                'meta' => DB::raw('JSON_REMOVE(`meta`, "$.sideboxes")'),
            ]);
    }
}
