<?php

namespace App\Docs\Paths\Audits;

use App\Docs\Operations\Audits\IndexAuditOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class AuditsIndexPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/audits/index')
            ->operations(
                IndexAuditOperation::create()
                    ->action(IndexAuditOperation::ACTION_POST)
                    ->description(
                        <<<'EOT'
This is an alias of `GET /audits` which allows all the query string parameters to be passed as part 
of the request body.
EOT
                    )
            );
    }
}
