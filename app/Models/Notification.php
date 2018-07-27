<?php

namespace App\Models;

use App\Models\Mutators\NotificationMutators;
use App\Models\Relationships\NotificationRelationships;
use App\Models\Scopes\NotificationScopes;

class Notification extends Model
{
    use NotificationMutators;
    use NotificationRelationships;
    use NotificationScopes;
}
