<?php

namespace App\Docs\Paths\Services;

use App\Docs\Operations\Services\DestroyServiceOperation;
use App\Docs\Operations\Services\ShowServiceOperation;
use App\Docs\Operations\Services\UpdateServiceOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

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
            ->operations(
                ShowServiceOperation::create(),
                UpdateServiceOperation::create(),
                DestroyServiceOperation::create()
            );
    }
}
