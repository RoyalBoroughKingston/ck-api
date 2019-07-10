<?php

namespace App\Models;

use App\Models\Mutators\UpdateRequestMutators;
use App\Models\Relationships\UpdateRequestRelationships;
use App\Models\Scopes\UpdateRequestScopes;
use App\UpdateRequest\AppliesUpdateRequests;
use Exception;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\MessageBag;

class UpdateRequest extends Model
{
    use SoftDeletes;
    use UpdateRequestMutators;
    use UpdateRequestRelationships;
    use UpdateRequestScopes;

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
     * @throws \Exception
     * @return \Illuminate\Support\MessageBag
     */
    public function getValidationErrors(): MessageBag
    {
        if (!$this->updateable instanceof AppliesUpdateRequests) {
            throw new Exception(sprintf('[%s] must be an instance of %s', get_class($this->updateable), AppliesUpdateRequests::class));
        }

        return $this->updateable->validateUpdateRequest($this)->errors();
    }

    /**
     * @throws \Exception
     * @return bool
     */
    public function validate(): bool
    {
        if (!$this->updateable instanceof AppliesUpdateRequests) {
            throw new Exception(sprintf('[%s] must be an instance of %s', get_class($this->updateable), AppliesUpdateRequests::class));
        }

        return $this->updateable->validateUpdateRequest($this)->fails() === false;
    }

    /**
     * @throws \Exception
     * @return \App\Models\UpdateRequest
     */
    public function apply(): self
    {
        if (!$this->updateable instanceof AppliesUpdateRequests) {
            throw new Exception(sprintf('[%s] must be an instance of %s', get_class($this->updateable), AppliesUpdateRequests::class));
        }

        $this->updateable->applyUpdateRequest($this);
        $this->update(['approved_at' => now()]);

        return $this;
    }
}
