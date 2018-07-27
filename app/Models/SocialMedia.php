<?php

namespace App\Models;

use App\Models\Mutators\SocialMediaMutators;
use App\Models\Relationships\SocialMediaRelationships;
use App\Models\Scopes\SocialMediaScopes;

class SocialMedia extends Model
{
    use SocialMediaMutators;
    use SocialMediaRelationships;
    use SocialMediaScopes;
}
