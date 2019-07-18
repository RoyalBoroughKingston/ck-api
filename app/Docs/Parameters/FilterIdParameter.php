<?php

namespace App\Docs\Parameters;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class FilterIdParameter extends FilterParameter
{
    /**
     * @param string|null $objectId
     * @param string $field
     * @return static
     */
    public static function create(string $objectId = null, string $field = 'id'): BaseObject
    {
        return parent::create($objectId, $field)
            ->description('Comma separated list of IDs to filter by')
            ->schema(
                Schema::array()->items(
                    Schema::string()->format(Schema::FORMAT_UUID)
                )
            )
            ->style(static::STYLE_SIMPLE);
    }
}
