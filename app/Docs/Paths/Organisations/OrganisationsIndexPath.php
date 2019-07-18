<?php

namespace App\Docs\Paths\Organisations;

use App\Docs\Operations\Organisations\IndexOrganisationOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class OrganisationsIndexPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/organisations/index')
            ->operations(
                IndexOrganisationOperation::create()
                    ->action(IndexOrganisationOperation::ACTION_POST)
                    ->description(
                        <<<'EOT'
This is an alias of `GET /organisations` which allows all the query string parameters to be passed 
as part of the request body.
EOT
                    )
            );
    }
}
