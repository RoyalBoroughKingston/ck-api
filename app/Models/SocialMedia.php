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

    const TYPE_TWITTER = 'twitter';
    const TYPE_FACEBOOK = 'facebook';
    const TYPE_INSTAGRAM = 'instagram';
    const TYPE_YOUTUBE = 'youtube';
    const TYPE_OTHER = 'other';
}
