<?php

namespace App\Models;

use App\Models\Mutators\SettingMutators;
use App\Models\Relationships\SettingRelationships;
use App\Models\Scopes\SettingScopes;

class Setting extends Model
{
    use SettingMutators;
    use SettingRelationships;
    use SettingScopes;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'key';

    /**
     * Determines if the primary key is a UUID.
     *
     * @var bool
     */
    protected $keyIsUuid = false;
}
