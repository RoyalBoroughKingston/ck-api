<?php

namespace App\Http\Controllers\Core\V1;

use App\Events\EndpointHit;
use App\Events\UserRolesUpdated;
use App\Exceptions\CannotRevokeRoleException;
use App\Http\Controllers\Controller;
use App\Http\Filters\User\AtOrganisationFilter;
use App\Http\Filters\User\AtServiceFilter;
use App\Http\Filters\User\HasPermissionFilter;
use App\Http\Filters\User\HighestRoleFilter;
use App\Http\Requests\User\DestroyRequest;
use App\Http\Requests\User\IndexRequest;
use App\Http\Requests\User\ShowRequest;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\ResourceDeleted;
use App\Models\Organisation;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UserController extends Controller
{
    /**
     * UserController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Http\Requests\User\IndexRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(IndexRequest $request)
    {
        // Check if the request has asked for user roles to be included.
        $userRolesIncluded = Str::contains($request->include, 'user-roles');

        $baseQuery = User::query()
            ->select('*')
            ->withHighestRoleOrder('highest_role')
            ->when($userRolesIncluded, function (Builder $query): Builder {
                // If user roles included, then make sure the role is also eager loaded.
                return $query->with('userRoles.role');
            });

        $users = QueryBuilder::for($baseQuery)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                'first_name',
                'last_name',
                'email',
                'phone',
                AllowedFilter::custom('highest_role', new HighestRoleFilter()),
                AllowedFilter::custom('has_permission', new HasPermissionFilter()),
                AllowedFilter::custom('at_organisation', new AtOrganisationFilter()),
                AllowedFilter::custom('at_service', new AtServiceFilter()),
            ])
            ->allowedIncludes([
                'user-roles.organisation',
                'user-roles.service',
            ])
            ->allowedSorts([
                'first_name',
                'last_name',
                'highest_role',
            ])
            ->defaultSorts([
                'first_name',
                'last_name',
            ])
            ->paginate(per_page($request->per_page));

        event(EndpointHit::onRead($request, 'Viewed all users'));

        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\User\StoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        return DB::transaction(function () use ($request) {
            /** @var \App\Models\User $user */
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => bcrypt($request->password),
            ]);

            foreach ($request->roles as $role) {
                $service = isset($role['service_id'])
                    ? Service::findOrFail($role['service_id'])
                    : null;
                $organisation = isset($role['organisation_id'])
                    ? Organisation::findOrFail($role['organisation_id'])
                    : null;

                switch ($role['role']) {
                    case Role::NAME_SERVICE_WORKER:
                        $user->makeServiceWorker($service);
                        break;
                    case Role::NAME_SERVICE_ADMIN:
                        $user->makeServiceAdmin($service);
                        break;
                    case Role::NAME_ORGANISATION_ADMIN:
                        $user->makeOrganisationAdmin($organisation);
                        break;
                    case Role::NAME_GLOBAL_ADMIN:
                        $user->makeGlobalAdmin();
                        break;
                    case Role::NAME_SUPER_ADMIN:
                        $user->makeSuperAdmin();
                        break;
                }
            }

            event(EndpointHit::onCreate($request, "Created user [{$user->id}]", $user));

            return new UserResource($user);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\User\ShowRequest $request
     * @param \App\Models\User $user
     * @return \App\Http\Resources\UserResource
     */
    public function show(ShowRequest $request, User $user)
    {
        // Check if the request has asked for user roles to be included.
        $userRolesIncluded = Str::contains($request->include, 'user-roles');

        $baseQuery = User::query()
            ->where('id', $user->id)
            ->when($userRolesIncluded, function (Builder $query): Builder {
                // If user roles included, then make sure the role is also eager loaded.
                return $query->with('userRoles.role');
            });

        $user = QueryBuilder::for($baseQuery)
            ->allowedIncludes([
                'user-roles.organisation',
                'user-roles.service',
            ])
            ->firstOrFail();

        event(EndpointHit::onRead($request, "Viewed user [{$user->id}]", $user));

        return new UserResource($user);
    }

    /**
     * Display the logged in user.
     *
     * @param \App\Http\Requests\User\ShowRequest $request
     * @return \App\Http\Resources\UserResource
     */
    public function user(ShowRequest $request)
    {
        return $this->show($request, $request->user());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\User\UpdateRequest $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, User $user)
    {
        return DB::transaction(function () use ($request, $user) {
            // Store the original user roles in case they have been updated in the request (used for notification).
            $originalRoles = $user->userRoles;

            // Update the user record.
            $user->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);

            // Update the users password if provided in the request.
            if ($request->has('password')) {
                $user->update(['password' => bcrypt($request->password)]);
            }

            // Revoke the deleted roles.
            $deletedRoles = $request->getDeletedRoles();
            $orderedDeletedRoles = $request->orderRoles($deletedRoles);
            foreach ($orderedDeletedRoles as $role) {
                try {
                    $service = isset($role['service_id']) ? Service::findOrFail($role['service_id']) : null;
                    $organisation = isset($role['organisation_id']) ? Organisation::findOrFail($role['organisation_id']) : null;

                    switch ($role['role']) {
                        case Role::NAME_SERVICE_WORKER:
                            $user->revokeServiceWorker($service);
                            break;
                        case Role::NAME_SERVICE_ADMIN:
                            $user->revokeServiceAdmin($service);
                            break;
                        case Role::NAME_ORGANISATION_ADMIN:
                            $user->revokeOrganisationAdmin($organisation);
                            break;
                        case Role::NAME_GLOBAL_ADMIN:
                            $user->revokeGlobalAdmin();
                            break;
                        case Role::NAME_SUPER_ADMIN:
                            $user->revokeSuperAdmin();
                            break;
                    }
                } catch (CannotRevokeRoleException $exception) {
                    continue;
                }
            }

            // Add the new roles.
            foreach ($request->getNewRoles() as $role) {
                $service = isset($role['service_id']) ? Service::findOrFail($role['service_id']) : null;
                $organisation = isset($role['organisation_id']) ? Organisation::findOrFail($role['organisation_id']) : null;

                switch ($role['role']) {
                    case Role::NAME_SERVICE_WORKER:
                        $user->makeServiceWorker($service);
                        break;
                    case Role::NAME_SERVICE_ADMIN:
                        $user->makeServiceAdmin($service);
                        break;
                    case Role::NAME_ORGANISATION_ADMIN:
                        $user->makeOrganisationAdmin($organisation);
                        break;
                    case Role::NAME_GLOBAL_ADMIN:
                        $user->makeGlobalAdmin();
                        break;
                    case Role::NAME_SUPER_ADMIN:
                        $user->makeSuperAdmin();
                        break;
                }
            }

            // Refresh the user roles.
            $user->load('userRoles');

            // Trigger the roles updated event if they have been modified in the request.
            if ($request->rolesHaveBeenUpdated()) {
                event(new UserRolesUpdated($user, $originalRoles, $user->userRoles));
            }

            event(EndpointHit::onUpdate($request, "Updated user [{$user->id}]", $user));

            return new UserResource($user);
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\User\DestroyRequest $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRequest $request, User $user)
    {
        return DB::transaction(function () use ($request, $user) {
            event(EndpointHit::onDelete($request, "Deleted use [{$user->id}]", $user));

            $user->delete();

            return new ResourceDeleted('user');
        });
    }
}
