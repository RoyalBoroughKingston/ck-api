<?php

namespace App\Docs\Paths\Services;

use App\Docs\Operations\Services\IndexServiceOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class ServicesIndexPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/services/index')
            ->operations(
                IndexServiceOperation::create()
                    ->action(IndexServiceOperation::ACTION_POST)
                    ->description(
                        <<<'EOT'
This is an alias of `GET /services` which allows all the query string parameters to be passed as 
part of the request body.
EOT
                    )
            );
    }
}
