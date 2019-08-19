<?php

namespace App\Docs\Parameters;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class IncludeParameter extends Parameter
{
    /**
     * @param string|null $objectId
     * @param string[] $includes
     * @return static
     */
    public static function create(string $objectId = null, array $includes = ['N/A']): BaseObject
    {
        $includes = sprintf('`%s`', implode('`, `', $includes));

        return parent::create($objectId)
            ->in(static::IN_QUERY)
            ->name('include')
            ->description(
                <<<EOT
Comma separated list of relationships to include.

Supported relationships: [{$includes}]
EOT
            )
            ->schema(
                Schema::array()->items(
                    Schema::string()
                )
            )
            ->style(static::STYLE_SIMPLE);
    }
}
