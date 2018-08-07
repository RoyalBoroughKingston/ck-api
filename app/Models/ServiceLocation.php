<?php

namespace App\Models;

use App\Models\Mutators\ServiceLocationMutators;
use App\Models\Relationships\ServiceLocationRelationships;
use App\Models\Scopes\ServiceLocationScopes;

class ServiceLocation extends Model
{
    use ServiceLocationMutators;
    use ServiceLocationRelationships;
    use ServiceLocationScopes;
    use UpdateRequests;

    /**
     * Determine if the service location is open at this point in time.
     *
     * @return bool
     */
    public function isOpenNow(): bool
    {
        // TODO: Work out if the service location is currently open.

        return false;
    }
}
