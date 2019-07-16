<?php

namespace App\Docs\Operations\Services\GalleryItems;

use App\Docs\Parameters\MaxDimensionParameter;
use App\Docs\Parameters\UpdateRequestIdParameter;
use App\Docs\Responses\PngResponse;
use App\Docs\Tags\ServicesTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class ShowServiceGalleryItemOperation extends Operation
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
            ->tags(ServicesTag::create())
            ->summary("Get a specific service's gallery item")
            ->description('**Permission:** `Open`')
            ->noSecurity()
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
                    ->schema(Schema::string()->format(Schema::FORMAT_UUID)),
                MaxDimensionParameter::create(),
                UpdateRequestIdParameter::create()
            )
            ->responses(PngResponse::create());
    }
}
