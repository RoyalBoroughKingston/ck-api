<?php

namespace App\Docs;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Contact;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Info as BaseInfo;

class Info extends BaseInfo
{
    /**
     * @param string|null $objectId
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->title(sprintf('%s Core API Specification', config('app.name')))
            ->description(sprintf('For using the Core %s API', config('app.name')))
            ->version('v1')
            ->contact(
                Contact::create()
                    ->name('Ayup Digital')
                    ->url('https://ayup.agency')
            );
    }
}
