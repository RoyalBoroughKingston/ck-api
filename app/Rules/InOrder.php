<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class InOrder implements Rule
{
    /**
     * @var array
     */
    protected $orders;

    /**
     * Create a new rule instance.
     *
     * @param array $orders
     */
    public function __construct(array $orders)
    {
        $this->orders = $orders;
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
        // Immediately fail if the value is not a integer.
        if (!is_int($value)) {
            return false;
        }

        // Initialise count to 0.
        $count = 0;

        // Loop through each order and increment count if the current value is in there.
        foreach ($this->orders as $order) {
            if ($order === $value) {
                $count++;
            }
        }

        // Loop through each order and check if in order.
        foreach (range(1, count($this->orders)) as $index) {
            if (!in_array($index, $this->orders)) {
                return false;
            }
        }

        // Pass if the count is not more than 1.
        return $count <= 1;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute is not in a valid order.';
    }
}
