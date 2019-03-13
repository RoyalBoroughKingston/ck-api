<?php

namespace App\Console\Commands\Ck\Notify;

use App\Emails\ReferralStillUnactioned\NotifyGlobalAdminEmail;
use App\Models\Notification;
use App\Models\Referral;
use App\Models\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class StillUnactionedReferralsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ck:notify:still-unactioned-referrals
                            {--working-days=9 : The number of working days to wait for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications to the global admin team for unactioned referrals';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->getReferralQuery()->chunk(200, function (Collection $referrals) {
            $referrals->each(function (Referral $referral) {
                $this->sendEmail($referral);
            });
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function getReferralQuery(): Builder
    {
        return Referral::query()
            ->with('service.users')
            ->unactioned($this->option('working-days'));
    }

    /**
     * @param \App\Models\Referral $referral
     */
    protected function sendEmail(Referral $referral)
    {
        try {
            /** @var \Illuminate\Database\Eloquent\Collection $organisationAdmins */
            $organisationAdmins = $referral->service->users
                ->unique('id')
                ->filter(function (User $user) {
                    return $user->isGlobalAdmin();
                });

            /** @var \Illuminate\Database\Eloquent\Collection $serviceAdmins */
            $serviceAdmins = $referral->service->users
                ->unique('id')
                ->filter(function (User $user) use ($referral) {
                    return $user->isServiceAdmin($referral->service)
                        && !$user->isGlobalAdmin();
                });

            /** @var \Illuminate\Database\Eloquent\Collection $serviceWorkers */
            $serviceWorkers = $referral->service->users
                ->unique('id')
                ->filter(function (User $user) use ($referral) {
                    return $user->isServiceWorker($referral->service)
                        && !$user->isGlobalAdmin()
                        && !$user->isServiceAdmin($referral->service);
                });

            Notification::sendEmail(
                new NotifyGlobalAdminEmail(config('ck.global_admin.email'), [
                    'REFERRAL_SERVICE_NAME' => $referral->service->name,
                    'REFERRAL_CREATED_AT' => $referral->created_at->format('j/n/Y'),
                    'REFERRAL_TYPE' => $referral->isSelfReferral() ? 'Self referral' : 'Champion referral',
                    'REFERRAL_INITIALS' => $referral->initials(),
                    'REFERRAL_ID' => $referral->reference,
                    'SERVICE_REFERRAL_EMAIL' => $referral->service->referral_email ?? 'not provided',
                    'SERVICE_WORKERS' => $serviceWorkers->implode('full_name', PHP_EOL) ?: 'None',
                    'SERVICE_ADMINS' => $serviceAdmins->implode('full_name', PHP_EOL) ?: 'None',
                    'ORGANISATION_ADMINS' => $organisationAdmins->implode('full_name', PHP_EOL) ?: 'None',
                ])
            );

            // Output a success message.
            $this->info("Email successfully sent for referral [{$referral->id}]");
        } catch (Exception $exception) {
            // Log the exception.
            logger()->error($exception);

            // Output an error message.
            $this->error("Email failed sending for referral [{$referral->id}]");
        }
    }
}
