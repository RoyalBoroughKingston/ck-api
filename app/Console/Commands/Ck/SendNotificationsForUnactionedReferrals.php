<?php

namespace App\Console\Commands\Ck;

use App\Emails\ReferralUnactioned\NotifyServiceEmail;
use App\Models\Referral;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class SendNotificationsForUnactionedReferrals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ck:send-notifications-for-unactioned-referrals
                            {--working-days=8 : The number of working days to wait for}';

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
            // Send the email.
            $referral->service->sendEmailToContact(new NotifyServiceEmail($referral->service->contact_email, [
                'CONTACT_NAME' => $referral->service->contact_name,
                'SERVICE_NAME' => $referral->service->name,
                'REFERRAL_ID' => $referral->id,
                'DATE_CREATED' => $referral->created_at->format('jS F Y'),
                'DAYS_OLD' => $referral->created_at->diffInDays(now()),
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
