<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRefreshToken extends Model
{
    use Mutators\ServiceRefreshTokenMutators;
    use Relationships\ServiceRefreshTokenRelationships;
    use Scopes\ServiceRefreshTokenScopes;
}
