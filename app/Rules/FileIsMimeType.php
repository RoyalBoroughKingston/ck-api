<?php

namespace App\Rules;

use App\Models\File;
use Illuminate\Contracts\Validation\Rule;

class FileIsMimeType implements Rule
{
    /**
     * @var string
     */
    protected $mimeType;

    /**
     * FileIsMimeType constructor.
     *
     * @param string $mimeType
     */
    public function __construct(string $mimeType)
    {
        $this->mimeType = $mimeType;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $fileId
     * @return bool
     */
    public function passes($attribute, $fileId)
    {
        return File::findOrFail($fileId)->mime_type === $this->mimeType;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "The :attribute must be of type $this->mimeType.";
    }
}
