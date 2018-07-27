<?php

namespace App\Models;

use App\Models\Mutators\FileMutators;
use App\Models\Relationships\FileRelationships;
use App\Models\Scopes\FileScopes;

class File extends Model
{
    use FileMutators;
    use FileRelationships;
    use FileScopes;
}
