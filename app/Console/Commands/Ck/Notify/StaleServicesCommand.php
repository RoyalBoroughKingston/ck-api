<?php

namespace App\Console\Commands\Ck\Notify;

use App\Emails\ServiceUpdatePrompt\NotifyGlobalAdminEmail;
use App\Emails\ServiceUpdatePrompt\NotifyServiceAdminEmail;
use App\Models\Notification;
use App\Models\Role;
use App\Models\Service;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StaleServicesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ck:notify:stale-services';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends notifications out for stale services';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->handleServices6To12MonthsStale();
        $this->handleServicesOver12MonthsStale();
    }

    protected function handleServices6To12MonthsStale(): void
    {
        Service::query()
            ->with([
                'users' => function (BelongsToMany $query): void {
                    $query->where('user_roles.role_id', '=', Role::serviceAdmin()->id);
                },
            ])
            ->where('status', '=', Service::STATUS_ACTIVE)
            ->whereBetween(
                'last_modified_at',
                [now()->subMonths(12), now()->subMonths(6)]
            )
            ->chunk(200, function (Collection $services): void {
                // Loop through each service in the current chunk.
                $services->each(function (Service $service): void {
                    $this->sendUpdatePromptEmailToServiceAdmins($service);
                });
            });
    }

    protected function handleServicesOver12MonthsStale(): void
    {
        Service::query()
            ->with([
                'users' => function (BelongsToMany $query): void {
                    $query->where('user_roles.role_id', '=', Role::serviceAdmin()->id);
                },
                'organisation',
            ])
            ->where('status', '=', Service::STATUS_ACTIVE)
            ->whereBetween(
                'last_modified_at',
                [now()->subMonths(13)->addDay(), now()->subMonths(12)]
            )
            ->chunk(200, function (Collection $services): void {
                // Loop through each service in the current chunk.
                $services->each(function (Service $service): void {
                    $this->sendUpdatePromptEmailToGlobalAdmin($service);
                });
            });
    }

    /**
     * @param \App\Models\Service $service
     */
    protected function sendUpdatePromptEmailToServiceAdmins(Service $service): void
    {
        // Create a refresh token for the service.
        $refreshToken = $service->serviceRefreshTokens()->create();

        /** @var \App\Models\User $user */
        foreach ($service->users as $user) {
            try {
                $user->sendEmail(new NotifyServiceAdminEmail($user->email, [
                    'SERVICE_NAME' => $service->name,
                    'SERVICE_URL' => backend_uri("/services/{$service->id}"),
                    'SERVICE_STILL_UP_TO_DATE_URL' => backend_uri("/services/{$service->id}/refresh?token={$refreshToken->id}"),
                ]));

                // Output a success message.
                $this->info("Emails successfully sent for service [{$service->id}] to user [{$user->id}]");
            } catch (Exception $exception) {
                // Log the exception.
                logger()->error($exception);

                // Output an error message.
                $this->error("Email failed sending for service [{$service->id}] to user [{$user->id}]");
            }
        }
    }

    /**
     * @param \App\Models\Service $service
     */
    protected function sendUpdatePromptEmailToGlobalAdmin(Service $service): void
    {
        // Create a refresh token for the service.
        $refreshToken = $service->serviceRefreshTokens()->create();

        try {
            Notification::sendEmail(
                new NotifyGlobalAdminEmail(config('ck.global_admin.email'), [
                    'SERVICE_NAME' => $service->name,
                    'SERVICE_URL' => backend_uri("/services/{$service->id}"),
                    'SERVICE_ADMIN_NAMES' => $service->users->implode(', ', 'full_name'),
                    'SERVICE_STILL_UP_TO_DATE_URL' => backend_uri("/services/{$service->id}/refresh?token={$refreshToken->id}"),
                ])
            );

            // Output a success message.
            $this->info("Emails successfully sent to global admin for service [{$service->id}]");
        } catch (Exception $exception) {
            // Log the exception.
            logger()->error($exception);

            // Output an error message.
            $this->error("Email failed sending to global admin for service [{$service->id}]");
        }
    }
}
