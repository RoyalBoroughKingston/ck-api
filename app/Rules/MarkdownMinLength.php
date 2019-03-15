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
     * MarkdownMaxLength constructor.
     *
     * @param int $minLength
     */
    public function __construct(int $minLength)
    {
        $this->minLength = $minLength;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $html = (new Parsedown())->text($value);
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
        return "The :attribute must be at least {$this->minLength} characters long.";
    }
}
