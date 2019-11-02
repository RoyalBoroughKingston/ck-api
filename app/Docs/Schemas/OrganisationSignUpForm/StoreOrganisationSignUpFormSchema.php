<?php

namespace App\Docs\Schemas\OrganisationSignUpForm;

use App\Docs\Schemas\Organisation\StoreOrganisationSchema;
use App\Docs\Schemas\Service\StoreServiceSchema;
use App\Docs\Schemas\User\StoreUserSchema;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class StoreOrganisationSignUpFormSchema extends Schema
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
            ->required(
                'user',
                'organisation',
                'service'
            )
            ->properties(
                StoreUserSchema::create('user')
                    ->required(
                        'password',
                        ...array_filter(
                            StoreUserSchema::create()->required,
                            function (string $required): bool {
                                return !in_array($required, ['roles']);
                            }
                        )
                    )
                    ->properties(
                        Schema::string('password')
                            ->format(Schema::FORMAT_PASSWORD),
                        ...array_filter(
                            StoreUserSchema::create()->properties,
                            function (Schema $property): bool {
                                return !in_array($property->objectId, ['roles']);
                            }
                        )
                    ),
                StoreOrganisationSchema::create('organisation')
                    ->properties(
                        ...array_filter(
                            StoreOrganisationSchema::create()->properties,
                            function (Schema $property): bool {
                                return !in_array($property->objectId, ['logo_file_id']);
                            }
                        )
                    ),
                StoreServiceSchema::create('service')
                    ->required(
                        ...array_filter(
                            StoreServiceSchema::create()->required,
                            function (string $required): bool {
                                return !in_array($required, [
                                    'organisation_id',
                                    'status',
                                    'show_referral_disclaimer',
                                    'referral_method',
                                    'referral_button_text',
                                    'referral_email',
                                    'referral_url',
                                    'gallery_items',
                                    'category_taxonomies',
                                ]);
                            }
                        )
                    )
                    ->properties(
                        ...array_filter(
                            StoreServiceSchema::create()->properties,
                            function (Schema $property): bool {
                                return !in_array($property->objectId, [
                                    'organisation_id',
                                    'status',
                                    'show_referral_disclaimer',
                                    'referral_method',
                                    'referral_button_text',
                                    'referral_email',
                                    'referral_url',
                                    'logo_file_id',
                                    'gallery_items',
                                    'category_taxonomies',
                                ]);
                            }
                        )
                    )
            );
    }
}
