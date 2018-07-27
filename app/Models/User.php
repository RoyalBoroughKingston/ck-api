<?php

namespace App\Models;

use App\Models\Mutators\UserMutators;
use App\Models\Relationships\UserRelationships;
use App\Models\Scopes\UserScopes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    use UserMutators;
    use UserRelationships;
    use UserScopes;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
