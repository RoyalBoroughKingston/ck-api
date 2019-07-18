<?php

namespace App\Docs\Paths\Services;

use App\Docs\Operations\Services\DestroyServiceOperation;
use App\Docs\Operations\Services\ShowServiceOperation;
use App\Docs\Operations\Services\UpdateServiceOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class ServicesNestedPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/services/{service}')
            ->parameters(
                Parameter::path()
                    ->name('service')
                    ->description('The ID or slug of the service')
                    ->required()
                    ->schema(Schema::string())
            )
            ->operations(
                ShowServiceOperation::create(),
                UpdateServiceOperation::create(),
                DestroyServiceOperation::create()
            );
    }
}
