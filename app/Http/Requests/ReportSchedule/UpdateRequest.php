<?php

namespace App\Http\Requests\ReportSchedule;

use App\Models\ReportSchedule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->user()->isGlobalAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'report_type' => ['required', 'exists:report_types,name'],
            'repeat_type' => ['required', Rule::in([ReportSchedule::REPEAT_TYPE_WEEKLY, ReportSchedule::REPEAT_TYPE_MONTHLY])],
        ];
    }
}
