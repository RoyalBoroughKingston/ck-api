<?php

namespace App\Models;

use App\Emails\Email;
use App\Models\Mutators\ReferralMutators;
use App\Models\Relationships\ReferralRelationships;
use App\Models\Scopes\ReferralScopes;
use App\Notifications\Notifiable;
use App\Notifications\Notifications;
use App\Sms\Sms;
use Exception;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Str;

class Referral extends Model implements Notifiable
{
    use DispatchesJobs;
    use Notifications;
    use ReferralMutators;
    use ReferralRelationships;
    use ReferralScopes;

    const AUTO_DELETE_MONTHS = 6;

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
        $reference = strtoupper(Str::random(10));

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
        Notification::sendEmail($email, $this);
    }

    /**
     * @param \App\Sms\Sms $sms
     */
    public function sendSmsToClient(Sms $sms)
    {
        Notification::sendSms($sms, $this);
    }

    /**
     * @param \App\Emails\Email $email
     */
    public function sendEmailToReferee(Email $email)
    {
        Notification::sendEmail($email, $this);
    }

    /**
     * @param \App\Sms\Sms $sms
     */
    public function sendSmsToReferee(Sms $sms)
    {
        Notification::sendSms($sms, $this);
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

    /**
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->status === static::STATUS_COMPLETED;
    }

    /**
     * @param \App\Models\User $user
     * @param string $to
     * @param string|null $comments
     * @return \App\Models\StatusUpdate
     */
    public function updateStatus(User $user, string $to, string $comments = null): StatusUpdate
    {
        /** @var \App\Models\StatusUpdate $statusUpdate */
        $statusUpdate = $this->statusUpdates()->create([
            'user_id' => $user->id,
            'from' => $this->status,
            'to' => $to,
            'comments' => $comments,
        ]);

        $this->update(['status' => $to]);

        return $statusUpdate;
    }

    /**
     * @return string
     */
    public function organisationName(): string
    {
        return $this->organisation ?? $this->organisationTaxonomy->name;
    }
}
