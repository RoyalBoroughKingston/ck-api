<?php

namespace App\Models;

use App\Models\Mutators\ReferralMutators;
use App\Models\Relationships\ReferralRelationships;
use App\Models\Scopes\ReferralScopes;

class Referral extends Model
{
    use ReferralMutators;
    use ReferralRelationships;
    use ReferralScopes;
}
