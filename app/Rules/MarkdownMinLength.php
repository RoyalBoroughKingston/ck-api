<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Parsedown;

class MarkdownMinLength implements Rule
{
    /**
     * @var int
     */
    protected $minLength;

    /**
     * @var string|null
     */
    protected $message;

    /**
     * MarkdownMaxLength constructor.
     *
     * @param int $minLength
     * @param string|null $message
     */
    public function __construct(int $minLength, ?string $message = null)
    {
        $this->minLength = $minLength;
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

        return mb_strlen($text) >= $this->minLength;
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message()
    {
        return $this->message ?? "The :attribute must be at least {$this->minLength} characters long.";
    }
}
