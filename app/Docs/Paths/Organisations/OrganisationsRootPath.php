<?php

namespace App\Docs\Paths\Organisations;

use App\Docs\Operations\Organisations\IndexOrganisationOperation;
use App\Docs\Operations\Organisations\StoreOrganisationOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class OrganisationsRootPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/organisations')
            ->operations(
                IndexOrganisationOperation::create(),
                StoreOrganisationOperation::create()
            );
    }
}
