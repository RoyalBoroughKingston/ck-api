<?php

namespace App\Models;

use App\Emails\Email;
use App\Models\Mutators\ReferralMutators;
use App\Models\Relationships\ReferralRelationships;
use App\Models\Scopes\ReferralScopes;
use App\Notifications\Notifications;
use App\Sms\Sms;
use Exception;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\DB;

class Referral extends Model
{
    use DispatchesJobs;
    use Notifications;
    use ReferralMutators;
    use ReferralRelationships;
    use ReferralScopes;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'referral_consented_at' => 'datetime',
        'feedback_consented_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    const STATUS_NEW = 'new';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_INCOMPLETED = 'incompleted';

    const REFERENCE_MAX_TRIES = 10;

    /**
     * @param int $tries
     * @return string
     * @throws \Exception
     */
    public function generateReference(int $tries = 0): string
    {
        // Check if the max tries has been reached to avoid infinite looping.
        if ($tries > static::REFERENCE_MAX_TRIES) {
            throw new Exception('Max tries reached for reference generation');
        }

        // Generate a random reference.
        $reference = strtoupper(str_random(10));

        // Check if the reference already exists.
        if (static::where('reference', $reference)->exists()) {
            return $this->generateReference($tries + 1);
        }

        return $reference;
    }

    /**
     * @param \App\Emails\Email $email
     */
    public function sendEmailToClient(Email $email)
    {
        DB::transaction(function () use ($email) {
            // Log a notification for the email in the database.
            $notification = $this->notifications()->create([
                'channel' => Notification::CHANNEL_EMAIL,
                'recipient' => $this->email,
                'message' => $email->getContent(),
            ]);

            // Add the email as a job on the queue to be sent.
            $email->notification = $notification;
            $this->dispatch($email);
        });
    }

    /**
     * @param \App\Sms\Sms $sms
     */
    public function sendSmsToClient(Sms $sms)
    {
        DB::transaction(function () use ($sms) {
            // Log a notification for the SMS in the database.
            $notification = $this->notifications()->create([
                'channel' => Notification::CHANNEL_SMS,
                'recipient' => $this->phone,
                'message' => $sms->getContent(),
            ]);

            // Add the SMS as a job on the queue to be sent.
            $sms->notification = $notification;
            $this->dispatch($sms);
        });
    }

    /**
     * @param \App\Emails\Email $email
     */
    public function sendEmailToReferee(Email $email)
    {
        DB::transaction(function () use ($email) {
            // Log a notification for the email in the database.
            $notification = $this->notifications()->create([
                'channel' => Notification::CHANNEL_EMAIL,
                'recipient' => $this->referee_email,
                'message' => $email->getContent(),
            ]);

            // Add the email as a job on the queue to be sent.
            $email->notification = $notification;
            $this->dispatch($email);
        });
    }

    /**
     * @param \App\Sms\Sms $sms
     */
    public function sendSmsToReferee(Sms $sms)
    {
        DB::transaction(function () use ($sms) {
            // Log a notification for the SMS in the database.
            $notification = $this->notifications()->create([
                'channel' => Notification::CHANNEL_SMS,
                'recipient' => $this->referee_phone,
                'message' => $sms->getContent(),
            ]);

            // Add the SMS as a job on the queue to be sent.
            $sms->notification = $notification;
            $this->dispatch($sms);
        });
    }

    /**
     * Get the initials of the client.
     *
     * @return string
     */
    public function initials(): string
    {
        $names = explode(' ', $this->name);
        $names = array_filter($names);

        $initials = '';
        foreach ($names as $name) {
            $initials .= $name[0];
        }

        return strtoupper($initials);
    }

    /**
     * Determines whether this is a self referral or not.
     *
     * @return bool
     */
    public function isSelfReferral(): bool
    {
        return $this->referee_name === null;
    }
}
