<?php

namespace App\Models;

use Laravel\Passport\Client as BaseClient;

class Client extends BaseClient
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'grant_types' => 'array',
        'personal_access_client' => 'bool',
        'password_client' => 'bool',
        'revoked' => 'bool',
        'first_party' => 'bool',
    ];
}
