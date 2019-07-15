<?php


namespace App\Docs\Paths\Audits;

use App\Docs\Operations\Audits\ShowAuditOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class AuditsNestedPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/audits/{audit}')
            ->operations(
                ShowAuditOperation::create()
            );
    }
}
