<?php

namespace App\Rules;

use App\Models\File;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Arr;

class FileIsPendingAssignment implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $fileId
     * @return bool
     */
    public function passes($attribute, $fileId)
    {
        $file = File::findOrFail($fileId);

        return Arr::get($file->meta, 'type') === File::META_TYPE_PENDING_ASSIGNMENT;
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
