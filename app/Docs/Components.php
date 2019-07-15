<?php

namespace App\Docs;

use App\Docs\SecuritySchemes\OAuth2SecurityScheme;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Components as BaseComponents;

class Components extends BaseComponents
{
    /**
     * @param string|null $objectId
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->securitySchemes(OAuth2SecurityScheme::create());
    }
}
