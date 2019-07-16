<?php

namespace App\Docs\Schemas\Thesaurus;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class ThesaurusSchema extends Schema
{
    /**
     * @param string|null $objectId
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->type(static::TYPE_ARRAY)
            ->items(
                Schema::array('data')->items(
                    Schema::string()
                )
            );
    }
}
