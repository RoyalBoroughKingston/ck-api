<?php

namespace App\Docs\SecuritySchemes;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\OAuthFlow;
use GoldSpecDigital\ObjectOrientedOAS\Objects\SecurityScheme;

class OAuth2SecurityScheme extends SecurityScheme
{
    /**
     * @param string|null $objectId
     * @return \GoldSpecDigital\ObjectOrientedOAS\Objects\SecurityScheme
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create('OAuth2')
            ->type(static::TYPE_OAUTH2)
            ->flows(
                OAuthFlow::create()
                    ->flow(OAuthFlow::FLOW_IMPLICIT)
                    ->authorizationUrl(url('/oauth/authorize'))
            );
    }
}
