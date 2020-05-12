<?php

namespace App\Models;

use App\Models\Mutators\UpdateRequestMutators;
use App\Models\Relationships\UpdateRequestRelationships;
use App\Models\Scopes\UpdateRequestScopes;
use App\UpdateRequest\AppliesUpdateRequests;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;

class UpdateRequest extends Model
{
    use SoftDeletes;
    use UpdateRequestMutators;
    use UpdateRequestRelationships;
    use UpdateRequestScopes;

    const EXISTING_TYPE_LOCATION = 'locations';
    const EXISTING_TYPE_REFERRAL = 'referrals';
    const EXISTING_TYPE_SERVICE = 'services';
    const EXISTING_TYPE_SERVICE_LOCATION = 'service_locations';
    const EXISTING_TYPE_ORGANISATION = 'organisations';
    const EXISTING_TYPE_USER = 'users';

    const NEW_TYPE_ORGANISATION_SIGN_UP_FORM = 'organisation_sign_up_form';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'approved_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->updateable_id === null;
    }

    /**
     * @return bool
     */
    public function isExisting(): bool
    {
        return !$this->isNew();
    }

    /**
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->approved_at !== null;
    }

    /**
     * @return bool
     */
    public function isDeclined(): bool
    {
        return $this->deleted_at !== null;
    }

    /**
     * @return \Illuminate\Support\MessageBag
     */
    public function getValidationErrors(): MessageBag
    {
        return $this->getUpdateable()->validateUpdateRequest($this)->errors();
    }

    /**
     * @return bool
     */
    public function validate(): bool
    {
        return $this->getUpdateable()->validateUpdateRequest($this)->fails() === false;
    }

    /**
     * @param \App\Models\User|null $user
     * @return \App\Models\UpdateRequest
     */
    public function apply(User $user = null): self
    {
        $this->getUpdateable()->applyUpdateRequest($this);
        $this->update([
            'actioning_user_id' => $user->id ?? null,
            'approved_at' => Date::now(),
        ]);

        return $this;
    }

    /**
     * @param \App\Models\User|null $user
     * @throws \Exception
     * @return bool|null
     */
    public function delete(User $user = null)
    {
        if ($user) {
            $this->update(['actioning_user_id' => $user->id]);
        }

        return parent::delete();
    }

    /**
     * @return \App\UpdateRequest\AppliesUpdateRequests
     */
    public function getUpdateable(): AppliesUpdateRequests
    {
        return $this->isExisting()
            ? $this->updateable
            : $this->createUpdateableInstance();
    }

    /**
     * @return \App\UpdateRequest\AppliesUpdateRequests
     */
    protected function createUpdateableInstance(): AppliesUpdateRequests
    {
        $className = '\\App\\UpdateRequest\\' . Str::studly($this->updateable_type);

        return resolve($className);
    }
}
