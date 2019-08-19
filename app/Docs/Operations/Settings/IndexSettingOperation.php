<?php

namespace App\Docs\Operations\Settings;

use App\Docs\Schemas\ResourceSchema;
use App\Docs\Schemas\Setting\SettingSchema;
use App\Docs\Tags\SettingsTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;

class IndexSettingOperation extends Operation
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->action(static::ACTION_GET)
            ->tags(SettingsTag::create())
            ->summary('List all of the settings')
            ->description(
                <<<'EOT'
**Permission:** `Open`

---

Settings are all returned at once instead of being paginated.
EOT
            )
            ->responses(
                Response::ok()->content(
                    MediaType::json()->schema(
                        ResourceSchema::create(null, SettingSchema::create())
                    )
                )
            );
    }
}
