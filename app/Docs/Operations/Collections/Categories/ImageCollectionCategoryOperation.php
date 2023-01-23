<?php

namespace App\Docs\Operations\Collections\Categories;

use App\Docs\Responses\SvgResponse;
use App\Docs\Tags\CollectionCategoriesTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;

class ImageCollectionCategoryOperation extends Operation
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->action(static::ACTION_GET)
            ->tags(CollectionCategoriesTag::create())
            ->summary("Get a specific category collection's image")
            ->description('**Permission:** `Open`')
            ->noSecurity()
            ->responses(SvgResponse::create());
    }
}
