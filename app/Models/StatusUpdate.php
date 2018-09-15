<?php

namespace App\Models;

use App\Models\Mutators\StatusUpdateMutators;
use App\Models\Relationships\StatusUpdateRelationships;
use App\Models\Scopes\StatusUpdateScopes;

class StatusUpdate extends Model
{
    use StatusUpdateMutators;
    use StatusUpdateRelationships;
    use StatusUpdateScopes;

    const FROM_NEW = 'new';
    const FROM_IN_PROGRESS = 'in_progress';
    const FROM_COMPLETED = 'completed';
    const FROM_INCOMPLETED = 'incompleted';

    const TO_NEW = 'new';
    const TO_IN_PROGRESS = 'in_progress';
    const TO_COMPLETED = 'completed';
    const TO_INCOMPLETED = 'incompleted';

    /**
     * @return bool
     */
    public function statusHasChanged(): bool
    {
        return $this->from !== $this->to;
    }
}
