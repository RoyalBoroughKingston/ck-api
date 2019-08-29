<?php

namespace App\Docs\Paths\OrganisationSignUpForms;

use App\Docs\Operations\OrganisationSignUpForms\StoreOrganisationSignUpFormOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class OrganisationSignUpFormsRootPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/organisation-sign-up-forms')
            ->operations(
                StoreOrganisationSignUpFormOperation::create()
            );
    }
}
