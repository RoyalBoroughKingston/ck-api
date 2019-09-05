<?php

namespace App\Emails;

use App\Contracts\EmailSender;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Date;

abstract class Email implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var string
     */
    public $to;

    /**
     * @var array
     */
    public $values;

    /**
     * @var string
     */
    public $templateId;

    /**
     * @var string|null
     */
    public $reference;

    /**
     * @var string|null
     */
    public $replyTo;

    /**
     * @var \App\Models\Notification|null
     */
    public $notification;

    /**
     * Email constructor.
     *
     * @param string $to
     * @param array $values
     */
    public function __construct(string $to, array $values = [])
    {
        $this->queue = 'notifications';

        $this->to = $to;
        $this->values = $values;
        $this->templateId = $this->getTemplateId();
        $this->reference = $this->getReference();
        $this->replyTo = $this->getReplyTo();
    }

    /**
     * @return string
     */
    abstract protected function getTemplateId(): string;

    /**
     * @return string|null
     */
    abstract protected function getReference(): ?string;

    /**
     * @return string|null
     */
    abstract protected function getReplyTo(): ?string;

    /**
     * @return string
     */
    abstract public function getContent(): string;

    /**
     * Send the email.
     */
    public function send()
    {
        $this->handle(resolve(EmailSender::class));
    }

    /**
     * Execute the job.
     *
     * @param \App\Contracts\EmailSender $emailSender
     */
    public function handle(EmailSender $emailSender)
    {
        try {
            // Send the email.
            $emailSender->send($this);

            // Update the notification.
            if ($this->notification) {
                $this->notification->update(['sent_at' => Date::now()]);
            }
        } catch (Exception $exception) {
            // Log the error.
            logger()->error($exception);

            // Update the notification.
            if ($this->notification) {
                $this->notification->update(['failed_at' => Date::now()]);
            }
        }
    }
}
