<?php

namespace App\Docs\Responses;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Illuminate\Http\Response as LaravelResponse;

class ResourceDeletedResponse extends Response
{
    /**
     * @param string|null $objectId
     * @param string|null $resource
     * @return static
     */
    public static function create(string $objectId = null, string $resource = null): BaseObject
    {
        return parent::create($objectId)
            ->statusCode(LaravelResponse::HTTP_OK)
            ->description('OK')
            ->content(
                MediaType::json()->schema(
                    Schema::object()->properties(
                        Schema::string('message')
                            ->example("The {$resource} has been deleted.")
                    )
                )
            );
    }
}
