<?php

namespace App\Docs\Responses;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Illuminate\Http\Response as LaravelResponse;

class PngResponse extends Response
{
    /**
     * @param string|null $objectId
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->statusCode(LaravelResponse::HTTP_OK)
            ->description('OK')
            ->content(
                MediaType::png()->schema(
                    Schema::string()->format(Schema::FORMAT_BINARY)
                )
            );
    }
}
