<?php

namespace App\Docs\Operations\Taxonomies\Categories;

use App\Docs\Responses\ResourceDeletedResponse;
use App\Docs\Tags\TaxonomyCategoriesTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;

class DestroyTaxonomyCategoryOperation extends Operation
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
            ->tags(TaxonomyCategoriesTag::create())
            ->summary('Delete a specific category taxonomy')
            ->description('**Permission:** `Super Admin`')
            ->responses(
                ResourceDeletedResponse::create(null, 'taxonomy category')
            );
    }
}
