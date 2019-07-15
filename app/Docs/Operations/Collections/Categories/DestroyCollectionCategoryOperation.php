<?php

namespace App\Docs\Operations\Collections\Categories;

use App\Docs\Responses\ResourceDeletedResponse;
use App\Docs\Tags\CollectionCategoriesTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class DestroyCollectionCategoryOperation extends Operation
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->action(static::ACTION_DELETE)
            ->tags(CollectionCategoriesTag::create())
            ->summary('Delete a specific category collection')
            ->description('**Permission:** `Super Admin`')
            ->parameters(
                Parameter::path()
                    ->name('category')
                    ->description('The ID of the category collection')
                    ->required()
                    ->schema(Schema::string()->format(Schema::FORMAT_UUID))
            )
            ->responses(
                ResourceDeletedResponse::create(null, 'collection category')
            );
    }
}
