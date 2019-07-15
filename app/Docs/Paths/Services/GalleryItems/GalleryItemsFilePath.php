<?php

namespace App\Docs\Paths\Services\GalleryItems;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class GalleryItemsFilePath extends PathItem
{
    /**
     * @param string|null $objectId
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/services/{service}/gallery-items/{file}')
            ->operations(
                //
            );
    }
}
