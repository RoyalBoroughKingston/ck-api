<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Parsedown;

class MarkdownMaxLength implements Rule
{
    /**
     * @var int
     */
    protected $maxLength;

    /**
     * @var string|null
     */
    protected $message;

    /**
     * MarkdownMaxLength constructor.
     *
     * @param int $maxLength
     * @param string|null $message
     */
    public function __construct(int $maxLength, ?string $message = null)
    {
        $this->maxLength = $maxLength;
        $this->message = $message;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $html = (new Parsedown())->text(sanitize_markdown($value));
        $text = strip_tags($html);

        return mb_strlen($text) <= $this->maxLength;
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message()
    {
        return $this->message ?? "The :attribute must be not more than {$this->maxLength} characters long.";
    }
}
