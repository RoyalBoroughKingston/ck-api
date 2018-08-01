<?php

namespace App\Models;

use App\Models\Mutators\UserMutators;
use App\Models\Relationships\UserRelationships;
use App\Models\Scopes\UserScopes;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use InvalidArgumentException;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use SoftDeletes;
    use UserMutators;
    use UserRelationships;
    use UserScopes;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = uuid();
            }
        });
    }

    /**
     * @param \App\Models\Role $role
     * @param \App\Models\Service|null $service
     * @param \App\Models\Organisation|null $organisation
     * @return bool
     */
    protected function hasRole(Role $role, Service $service = null, Organisation $organisation = null): bool
    {
        if ($service !== null && $organisation !== null) {
            throw new InvalidArgumentException('A role cannot be assigned to both a service and an organisation');
        }

        return $this->userRoles()
            ->where('user_roles.role_id', $role->id)
            ->when($service, function (Builder $query) use ($service) {
                return $query->where('user_roles.service_id', $service->id);
            })
            ->when($organisation, function (Builder $query) use ($organisation) {
                return $query->where('user_roles.organisation_id', $organisation->id);
            })
            ->exists();
    }

    /**
     * @param \App\Models\Role $role
     * @param \App\Models\Service|null $service
     * @param \App\Models\Organisation|null $organisation
     * @return \App\Models\User
     */
    protected function assignRole(Role $role, Service $service = null, Organisation $organisation = null): self
    {
        if ($service !== null && $organisation !== null) {
            throw new InvalidArgumentException('A role cannot be assigned to both a service and an organisation');
        }

        // Check if the user already has the role.
        if ($this->hasRole($role, $service, $organisation)) {
            return $this;
        }

        // Create the role.
        UserRole::create([
            'user_id' => $this->id,
            'role_id' => $role->id,
            'service_id' => $service->id ?? null,
            'organisation_id' => $organisation->id ?? null,
        ]);

        return $this;
    }

    /**
     * @param \App\Models\Role $role
     * @param \App\Models\Service|null $service
     * @param \App\Models\Organisation|null $organisation
     * @return \App\Models\User
     */
    protected function removeRoll(Role $role, Service $service = null, Organisation $organisation = null): self
    {
        if ($service !== null && $organisation !== null) {
            throw new InvalidArgumentException('A role cannot be assigned to both a service and an organisation');
        }

        // Check if the user doesn't already have the role.
        if (!$this->hasRole($role)) {
            return $this;
        }

        // Remove the role.
        $this->userRoles()
            ->where('user_roles.role_id', $role->id)
            ->where('user_roles.service_id', $service->id ?? null)
            ->where('user_roles.organisation_id', $organisation->id ?? null)
            ->delete();

        return $this;
    }

    /**
     * @param null|\App\Models\Service $service
     * @return bool
     */
    public function isServiceWorker(Service $service = null): bool
    {
        return $this->hasRole(Role::serviceWorker(), $service);
    }

    /**
     * @param null|\App\Models\Service $service
     * @return bool
     */
    public function isServiceAdmin(Service $service = null): bool
    {
        return $this->hasRole(Role::serviceAdmin(), $service);
    }

    /**
     * @param null|\App\Models\Organisation $organisation
     * @return bool
     */
    public function isOrganisationAdmin(Organisation $organisation = null): bool
    {
        return $this->hasRole(Role::organisationAdmin(), null, $organisation);
    }

    /**
     * @return bool
     */
    public function isGlobalAdmin(): bool
    {
        return $this->hasRole(Role::organisationAdmin());
    }

    /**
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole(Role::superAdmin());
    }

    /**
     * @param \App\Models\Service $service
     * @return \App\Models\User
     */
    public function makeServiceWorker(Service $service): self
    {
        $this->assignRole(Role::serviceWorker(), $service);

        return $this;
    }

    /**
     * @param \App\Models\Service $service
     * @return \App\Models\User
     */
    public function makeServiceAdmin(Service $service): self
    {
        $this->makeServiceWorker($service);
        $this->assignRole(Role::serviceAdmin(), $service);

        return $this;
    }

    /**
     * @param \App\Models\Organisation $organisation
     * @return \App\Models\User
     */
    public function makeOrganisationAdmin(Organisation $organisation): self
    {
        foreach ($organisation->services as $service) {
            $this->makeServiceWorker($service);
            $this->makeServiceAdmin($service);
        }

        $this->assignRole(Role::organisationAdmin(), null, $organisation);

        return $this;
    }

    /**
     * @return \App\Models\User
     */
    public function makeGlobalAdmin(): self
    {
        foreach (Organisation::all() as $organisation) {
            $this->makeOrganisationAdmin($organisation);
        }

        $this->assignRole(Role::organisationAdmin());

        return $this;
    }

    /**
     * @return \App\Models\User
     */
    public function makeSuperAdmin(): self
    {
        $this->makeGlobalAdmin();
        $this->assignRole(Role::superAdmin());

        return $this;
    }

    /**
     * @param \App\Models\Service $service
     * @return \App\Models\User
     * @throws \Exception
     */
    public function revokeServiceWorker(Service $service)
    {
        if ($this->hasRole(Role::serviceAdmin(), $service)) {
            throw new Exception('Cannot revoke service worker role when user is a service admin');
        }

        return $this->removeRoll(Role::serviceWorker(), $service);
    }

    /**
     * @param \App\Models\Service $service
     * @return \App\Models\User
     * @throws \Exception
     */
    public function revokeServiceAdmin(Service $service)
    {
        if ($this->hasRole(Role::organisationAdmin(), $service->organisation)) {
            throw new Exception('Cannot revoke service admin role when user is an organisation admin');
        }

        $this->revokeServiceWorker($service);
        $this->removeRoll(Role::serviceAdmin(), $service);

        return $this;
    }

    /**
     * @param \App\Models\Organisation $organisation
     * @return \App\Models\User
     * @throws \Exception
     */
    public function revokeOrganisationAdmin(Organisation $organisation)
    {
        if ($this->hasRole(Role::globalAdmin())) {
            throw new Exception('Cannot revoke organisation admin role when user is an global admin');
        }

        foreach ($organisation->services as $service) {
            $this->revokeServiceAdmin($service);
        }
        $this->removeRoll(Role::organisationAdmin(), $organisation);

        return $this;
    }

    /**
     * @return \App\Models\User
     * @throws \Exception
     */
    public function revokeGlobalAdmin()
    {
        if ($this->hasRole(Role::superAdmin())) {
            throw new Exception('Cannot revoke global admin role when user is an super admin');
        }

        foreach (Organisation::all() as $organisation) {
            $this->revokeOrganisationAdmin($organisation);
        }
        $this->removeRoll(Role::globalAdmin());

        return $this;
    }

    /**
     * @return \App\Models\User
     * @throws \Exception
     */
    public function revokeSuperAdmin()
    {
        $this->revokeGlobalAdmin();
        $this->removeRoll(Role::superAdmin());

        return $this;
    }
}
