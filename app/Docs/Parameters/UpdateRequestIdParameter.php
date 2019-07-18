<?php

namespace App\Docs\Parameters;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class UpdateRequestIdParameter extends Parameter
{
    /**
     * @param string|null $objectId
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->in(static::IN_QUERY)
            ->name('update_request_id')
            ->description('File to use from update request')
            ->schema(
                Schema::string()->format(Schema::FORMAT_UUID)
            );
    }
}
