<?php

namespace App\Docs;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\ExternalDocs as BaseExternalDocs;

class ExternalDocs extends BaseExternalDocs
{
    /**
     * @param string|null $objectId
     * @return \GoldSpecDigital\ObjectOrientedOAS\Objects\ExternalDocs
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->description('GitHub Wiki')
            ->url('https://github.com/RoyalBoroughKingston/cwk-api/wiki');
    }
}
