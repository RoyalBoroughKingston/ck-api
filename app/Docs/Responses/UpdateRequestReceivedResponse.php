<?php

namespace App\Docs\Responses;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Illuminate\Http\Response as LaravelResponse;

class UpdateRequestReceivedResponse extends Response
{
    /**
     * @param string|null $objectId
     * @param \GoldSpecDigital\ObjectOrientedOAS\Objects\Schema|null $data
     * @return static
     */
    public static function create(string $objectId = null, Schema $data = null): BaseObject
    {
        return parent::create($objectId)
            ->statusCode(LaravelResponse::HTTP_OK)
            ->description('OK')
            ->content(
                MediaType::json()->schema(
                    Schema::object()->properties(
                        Schema::string('message'),
                        Schema::string('id')
                            ->format(Schema::FORMAT_UUID),
                        $data->objectId('data')
                    )
                )
            );
    }
}
