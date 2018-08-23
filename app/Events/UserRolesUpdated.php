<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserRolesUpdated
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var \App\Models\User
     */
    public $user;

    /**
     * @var \Illuminate\Database\Eloquent\Collection
     */
    public $oldRoles;

    /**
     * @var \Illuminate\Database\Eloquent\Collection
     */
    public $newRoles;

    /**
     * UserPermissionsUpdated constructor.
     *
     * @param \App\Models\User $user
     * @param \Illuminate\Database\Eloquent\Collection $oldRoles
     * @param \Illuminate\Database\Eloquent\Collection $newRoles
     */
    public function __construct(User $user, Collection $oldRoles, Collection $newRoles)
    {
        $this->user = $user;
        $this->oldRoles = $oldRoles;
        $this->newRoles = $newRoles;
    }
}
