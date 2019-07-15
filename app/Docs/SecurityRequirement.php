<?php

namespace App\Docs;

use App\Docs\SecuritySchemes\OAuth2SecurityScheme;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\SecurityRequirement as BaseSecurityRequirement;

class SecurityRequirement extends BaseSecurityRequirement
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->securityScheme(OAuth2SecurityScheme::create());
    }
}
