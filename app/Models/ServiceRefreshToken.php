<?php

namespace App\Models;

class ServiceRefreshToken extends Model
{
    use Mutators\ServiceRefreshTokenMutators;
    use Relationships\ServiceRefreshTokenRelationships;
    use Scopes\ServiceRefreshTokenScopes;

    const AUTO_DELETE_MONTHS = 1;
}
