<?php

namespace App\Emails;

use App\Contracts\EmailSender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

abstract class Email implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

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
        $this->replyTo = $this->getReplyto();
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
     * @return void
     */
    public function send()
    {
        $this->handle(resolve(EmailSender::class));
    }

    /**
     * Execute the job.
     *
     * @param \App\Contracts\EmailSender $emailSender
     * @return void
     */
    public function handle(EmailSender $emailSender)
    {
        $emailSender->send($this);
    }
}
