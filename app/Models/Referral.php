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

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'referral_consented_at' => 'datetime',
        'feedback_consented_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    const STATUS_NEW = 'new';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_INCOMPLETED = 'incompleted';
}
