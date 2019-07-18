<?php

namespace App\Docs\Paths\Settings;

use App\Docs\Operations\Settings\IndexSettingOperation;
use App\Docs\Operations\Settings\UpdateSettingOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class SettingsRootPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/settings')
            ->operations(
                IndexSettingOperation::create(),
                UpdateSettingOperation::create()
            );
    }
}
