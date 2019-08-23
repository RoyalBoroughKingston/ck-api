<?php

namespace App\Rules;

use App\Models\File;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Arr;

class FileIsPendingAssignment implements Rule
{
    /**
     * @var callable
     */
    protected $callback;

    /**
     * FileIsPendingAssignment constructor.
     *
     * @param callable|null $callback Called if the file is not pending assignment
     */
    public function __construct(callable $callback = null)
    {
        $this->callback = $callback;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $fileId
     * @return bool
     */
    public function passes($attribute, $fileId)
    {
        $file = File::findOrFail($fileId);

        $passed = Arr::get($file->meta, 'type') === File::META_TYPE_PENDING_ASSIGNMENT;

        if ($passed) {
            return true;
        }

        if ($this->callback !== null) {
            return call_user_func($this->callback, $file);
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be an unassigned file.';
    }
}
