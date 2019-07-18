<?php

namespace App\Docs\Paths\UpdateRequests;

use App\Docs\Operations\UpdateRequests\DestroyUpdateRequestOperation;
use App\Docs\Operations\UpdateRequests\ShowUpdateRequestOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class UpdateRequestsNestedPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/update-requests/{update_request}')
            ->parameters(
                Parameter::path()
                    ->name('update_request')
                    ->description('The ID of the update request')
                    ->required()
                    ->schema(Schema::string()->format(Schema::FORMAT_UUID))
            )
            ->operations(
                ShowUpdateRequestOperation::create(),
                DestroyUpdateRequestOperation::create()
            );
    }
}
