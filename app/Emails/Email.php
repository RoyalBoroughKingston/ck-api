<?php

namespace App\Emails;

/**
 * @property string $to
 * @property string $templateId
 * @property array $values
 * @property string $reference
 * @property string|null $replyTo
 */
abstract class Email
{
    /**
     * @var string
     */
    protected $to;

    /**
     * @var array
     */
    protected $values;

    /**
     * @var string
     */
    protected $templateId;

    /**
     * @var string
     */
    protected $reference;

    /**
     * @var string|null
     */
    protected $replyTo;

    /**
     * Email constructor.
     *
     * @param string $to
     * @param array $values
     */
    public function __construct(string $to, array $values = [])
    {
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
     * @param string $property
     * @return mixed
     */
    public function __get(string $property)
    {
        return $this->$property;
    }
}
