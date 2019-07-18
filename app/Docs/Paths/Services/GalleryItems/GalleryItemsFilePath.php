<?php

namespace App\Docs\Paths\Services\GalleryItems;

use App\Docs\Operations\Services\GalleryItems\ShowServiceGalleryItemOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class GalleryItemsFilePath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/services/{service}/gallery-items/{file}')
            ->parameters(
                Parameter::path()
                    ->name('service')
                    ->description('The ID or slug of the service')
                    ->required()
                    ->schema(Schema::string()),
                Parameter::path()
                    ->name('file')
                    ->description('The ID of the file')
                    ->required()
                    ->schema(Schema::string()->format(Schema::FORMAT_UUID))
            )
            ->operations(
                ShowServiceGalleryItemOperation::create()
            );
    }
}
