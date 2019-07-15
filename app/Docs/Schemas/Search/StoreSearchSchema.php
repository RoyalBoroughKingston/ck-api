<?php

namespace App\Docs\Schemas\Search;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class StoreSearchSchema extends Schema
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->type(static::TYPE_OBJECT)
            ->properties(
                Schema::integer('page')
                    ->example(1),
                Schema::integer('per_page')
                    ->default(config('ck.pagination_results')),
                Schema::string('query')
                    ->example('Health and Social'),
                Schema::string('category')
                    ->example('Self Help'),
                Schema::string('persona')
                    ->example('Refugees'),
                Schema::string('wait_time')
                    ->enum('one_week', 'two_weeks', 'three_weeks', 'month', 'longer'),
                Schema::boolean('is_free'),
                Schema::string('order')
                    ->enum('relevance', 'distance')
                    ->default('relevance'),
                Schema::object('location')
                    ->required('lat', 'lon')
                    ->properties(
                        Schema::number('lat')
                            ->type(Schema::FORMAT_FLOAT)
                            ->example(5.78263),
                        Schema::number('lon')
                            ->type(Schema::FORMAT_FLOAT)
                            ->example(-52.12710)
                    ),
                Schema::integer('distance')
                    ->default(config('ck.search_distance'))
            );
    }
}
