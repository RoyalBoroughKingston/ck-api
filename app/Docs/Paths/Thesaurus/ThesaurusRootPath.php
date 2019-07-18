<?php

namespace App\Docs\Paths\Thesaurus;

use App\Docs\Operations\Thesaurus\IndexThesaurusOperation;
use App\Docs\Operations\Thesaurus\UpdateThesaurusOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class ThesaurusRootPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/thesaurus')
            ->operations(
                IndexThesaurusOperation::create(),
                UpdateThesaurusOperation::create()
            );
    }
}
