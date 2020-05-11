<?php

namespace App\Docs\Operations\Settings;

use App\Docs\Parameters\MaxDimensionParameter;
use App\Docs\Responses\PngResponse;
use App\Docs\Tags\SettingsTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;

class BannerImageSettingOperation extends Operation
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
            ->tags(SettingsTag::create())
            ->summary('Get the banner image')
            ->description('**Permission:** `Open`')
            ->noSecurity()
            ->parameters(
                MaxDimensionParameter::create()
            )
            ->responses(PngResponse::create());
    }
}
