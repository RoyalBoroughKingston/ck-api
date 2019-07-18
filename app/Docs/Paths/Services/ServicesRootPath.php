<?php

namespace App\Docs\Paths\Services;

use App\Docs\Operations\Services\IndexServiceOperation;
use App\Docs\Operations\Services\StoreServiceOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class ServicesRootPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/services')
            ->operations(
                IndexServiceOperation::create(),
                StoreServiceOperation::create()
            );
    }
}
