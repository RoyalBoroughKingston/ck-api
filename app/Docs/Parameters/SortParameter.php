<?php

namespace App\Docs\Parameters;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class SortParameter extends Parameter
{
    /**
     * @param string|null $objectId
     * @param string[] $fields
     * @param string|null $default
     * @return static
     */
    public static function create(
        string $objectId = null,
        array $fields = ['N/A'],
        string $default = null
    ): BaseObject {
        $fieldsMarkdown = sprintf('`%s`', implode('`, `', $fields));

        return parent::create($objectId)
            ->in(static::IN_QUERY)
            ->name('sort')
            ->description(
                <<<EOT
Comma separated list of fields to sort by.
The results are sorted in the order of which the fields have been provided.
Prefix a field with `-` to indicate a descending sort.

Supported fields: [{$fieldsMarkdown}]
EOT
            )
            ->schema(
                Schema::array()->items(
                    Schema::string()->default($default)
                )
            )
            ->style(FilterParameter::STYLE_SIMPLE);
    }
}
