<?php

namespace App\Docs\Paths\Organisations;

use App\Docs\Operations\Organisations\DestroyOrganisationOperation;
use App\Docs\Operations\Organisations\ShowOrganisationOperation;
use App\Docs\Operations\Organisations\UpdateOrganisationOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class OrganisationsNestedPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/organisations/{organisation}')
            ->operations(
                ShowOrganisationOperation::create(),
                UpdateOrganisationOperation::create(),
                DestroyOrganisationOperation::create()
            );
    }
}
