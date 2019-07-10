<?php

namespace App\Rules;

class NullableIf
{
    /**
     * The condition that validates the attribute.
     *
     * @var callable|bool
     */
    public $condition;

    /**
     * Create a new nullable validation rule based on a condition.
     *
     * @param callable|bool $condition
     */
    public function __construct($condition)
    {
        $this->condition = $condition;
    }

    /**
     * Convert the rule to a validation string.
     *
     * @return string
     */
    public function __toString()
    {
        if (is_callable($this->condition)) {
            return call_user_func($this->condition) ? 'nullable' : '';
        }

        return $this->condition ? 'nullable' : '';
    }
}
