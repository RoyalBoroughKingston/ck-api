<?php

namespace App\Console\Commands\Ck;

use App\Emails\ReferralUnactioned\NotifyServiceEmail;
use App\Models\Referral;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class SendNotificationsForUnactionedReferralsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ck:send-notifications-for-unactioned-referrals
                            {--working-days=6 : The number of working days to wait for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send ';

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
            ->with('service')
            ->unactioned($this->option('working-days'));
    }

    /**
     * @param \App\Models\Referral $referral
     */
    protected function sendEmail(Referral $referral)
    {
        try {
            $contactMethod = null;
            if ($referral->email !== null) {
                $contactMethod = $referral->email;
            } elseif ($referral->phone !== null) {
                $contactMethod = $referral->email;
            } elseif ($referral->other !== null) {
                $contactMethod = $referral->other_contact;
            } else {
                $contactMethod = 'N/A';
            }

            // Send the email.
            $referral->service->sendEmailToContact(new NotifyServiceEmail($referral->service->contact_email, [
                'REFERRAL_SERVICE_NAME' => $referral->service->name,
                'REFERRAL_INITIALS' => $referral->initials(),
                'REFERRAL_ID' => $referral->reference,
                'REFERRAL_DAYS_AGO' => $referral->created_at->diffInWeekdays(now()),
                'REFERRAL_TYPE' => $referral->isSelfReferral() ? 'self referral' : 'champion referral',
                'REFERRAL_CONTACT_METHOD' => $contactMethod,
                'REFERRAL_DAYS_LEFT' => now()->diffInWeekdays($referral->created_at->copy()->addWeekdays(config('ck.working_days_for_service_to_respond'))),
            ]));

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
