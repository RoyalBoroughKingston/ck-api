<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddBannerFieldsToCmsSettings extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::table('settings')
            ->where('key', '=', 'cms')
            ->update([
                'value' => DB::raw(
                    <<<'EOT'
JSON_INSERT(
    `settings`.`value`,
    '$.frontend.banner',
    JSON_OBJECT(
        "title", null,
        "content", null,
        "button_text", null,
        "button_url", null,
        "image_file_id", null
    )
)
EOT
                ),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::table('settings')
            ->where('key', '=', 'cms')
            ->update([
                'value' => DB::raw(
                    <<<'EOT'
JSON_REMOVE(
    `settings`.`value`,
    '$.frontend.banner'
)
EOT
                ),
            ]);
    }
}
