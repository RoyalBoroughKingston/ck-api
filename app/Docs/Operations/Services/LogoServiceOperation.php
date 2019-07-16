<?php

namespace App\Docs\Operations\Services;

use App\Docs\Parameters\MaxDimensionParameter;
use App\Docs\Parameters\UpdateRequestIdParameter;
use App\Docs\Responses\PngResponse;
use App\Docs\Tags\ServicesTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class LogoServiceOperation extends Operation
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
            ->summary('Get a specific service\'s logo')
            ->description('**Permission:** `Open`')
            ->noSecurity()
            ->parameters(
                Parameter::path()
                    ->name('service')
                    ->description('The ID or slug of the service')
                    ->schema(Schema::string()),
                MaxDimensionParameter::create(),
                UpdateRequestIdParameter::create()
            )
            ->responses(PngResponse::create());
    }
}
