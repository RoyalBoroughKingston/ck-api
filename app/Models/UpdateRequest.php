<?php

namespace App\Models;

use App\Models\Mutators\UpdateRequestMutators;
use App\Models\Relationships\UpdateRequestRelationships;
use App\Models\Scopes\UpdateRequestScopes;
use App\UpdateRequest\AppliesUpdateRequests;
use Exception;
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
     * @throws \Exception
     * @return \Illuminate\Support\MessageBag
     */
    public function getValidationErrors(): MessageBag
    {
        if (!$this->updateable instanceof AppliesUpdateRequests) {
            throw new Exception(
                sprintf(
                    '[%s] must be an instance of %s',
                    get_class($this->updateable),
                    AppliesUpdateRequests::class
                )
            );
        }

        return $this->updateable->validateUpdateRequest($this)->errors();
    }

    /**
     * @throws \Exception
     * @return bool
     */
    public function validate(): bool
    {
        $updateable = $this->isExisting()
            ? $this->updateable
            : $this->createUpdateableInstance($this->updateable_type);

        if (!$updateable instanceof AppliesUpdateRequests) {
            throw new Exception(
                sprintf(
                    '[%s] must be an instance of %s',
                    get_class($updateable),
                    AppliesUpdateRequests::class
                )
            );
        }

        return $updateable->validateUpdateRequest($this)->fails() === false;
    }

    /**
     * @throws \Exception
     * @return \App\Models\UpdateRequest
     */
    public function apply(): self
    {
        $updateable = $this->isExisting()
            ? $this->updateable
            : $this->createUpdateableInstance($this->updateable_type);

        if (!$updateable instanceof AppliesUpdateRequests) {
            throw new Exception(
                sprintf(
                    '[%s] must be an instance of %s',
                    get_class($updateable),
                    AppliesUpdateRequests::class
                )
            );
        }

        $updateable->applyUpdateRequest($this);
        $this->update(['approved_at' => Date::now()]);

        return $this;
    }

    /**
     * @param string $updateableType
     * @return \App\UpdateRequest\AppliesUpdateRequests
     */
    protected function createUpdateableInstance(string $updateableType): AppliesUpdateRequests
    {
        $className = '\\App\\UpdateRequest\\' . Str::studly($updateableType);

        return resolve($className);
    }

    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function getFromData(string $key, $default = null)
    {
        return Arr::get($this->data, $key, $default);
    }
}
