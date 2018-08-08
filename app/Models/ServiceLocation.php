<?php

namespace App\Models;

use App\Models\Mutators\ServiceLocationMutators;
use App\Models\Relationships\ServiceLocationRelationships;
use App\Models\Scopes\ServiceLocationScopes;
use App\UpdateRequest\AppliesUpdateRequests;
use App\UpdateRequest\UpdateRequests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\Rule;

class ServiceLocation extends Model implements AppliesUpdateRequests
{
    use ServiceLocationMutators;
    use ServiceLocationRelationships;
    use ServiceLocationScopes;
    use UpdateRequests;

    /**
     * Determine if the service location is open at this point in time.
     *
     * @return bool
     */
    public function isOpenNow(): bool
    {
        // TODO: Work out if the service location is currently open.

        return false;
    }

    /**
     * Check if the update request is valid.
     *
     * @param \App\Models\UpdateRequest $updateRequest
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validateUpdateRequest(UpdateRequest $updateRequest): Validator
    {
        $rules = [
            'name' => ['present', 'nullable', 'string', 'min:1', 'max:255'],

            'regular_opening_hours' => ['present', 'array'],
            'regular_opening_hours.*' => ['array'],
            'regular_opening_hours.*.frequency' => ['required_with:regular_opening_hours.*', Rule::in([
                RegularOpeningHour::FREQUENCY_WEEKLY,
                RegularOpeningHour::FREQUENCY_MONTHLY,
                RegularOpeningHour::FREQUENCY_FORTNIGHTLY,
                RegularOpeningHour::FREQUENCY_NTH_OCCURRENCE_OF_MONTH,
            ])],
            'regular_opening_hours.*.weekday' => ['required_if:regular_opening_hours.*.frequency,'.RegularOpeningHour::FREQUENCY_WEEKLY, 'integer', 'min:1', 'max:7'],
            'regular_opening_hours.*.day_of_month' => ['required_if:regular_opening_hours.*.frequency,'.RegularOpeningHour::FREQUENCY_MONTHLY, 'integer', 'min:1', 'max:31'],
            'regular_opening_hours.*.occurrence_of_month' => ['required_if:regular_opening_hours.*.frequency,'.RegularOpeningHour::FREQUENCY_NTH_OCCURRENCE_OF_MONTH, 'integer', 'min:1', 'max: 5'],
            'regular_opening_hours.*.starts_at' => ['required_if:regular_opening_hours.*.frequency,'.RegularOpeningHour::FREQUENCY_FORTNIGHTLY],
            'regular_opening_hours.*.opens_at' => ['required_with:regular_opening_hours.*', 'date_format:H:i:s'],
            'regular_opening_hours.*.closes_at' => ['required_with:regular_opening_hours.*', 'date_format:H:i:s'],

            'holiday_opening_hours' => ['present', 'array'],
            'holiday_opening_hours.*' => ['array'],
            'holiday_opening_hours.*.is_closed' => ['required_with:holiday_opening_hours.*', 'boolean'],
            'holiday_opening_hours.*.starts_at' => ['required_with:holiday_opening_hours.*', 'date_format:Y-m-d'],
            'holiday_opening_hours.*.ends_at' => ['required_with:holiday_opening_hours.*', 'date_format:Y-m-d'],
            'holiday_opening_hours.*.opens_at' => ['required_with:holiday_opening_hours.*', 'date_format:H:i:s'],
            'holiday_opening_hours.*.closes_at' => ['required_with:holiday_opening_hours.*', 'date_format:H:i:s'],
        ];

        return ValidatorFacade::make($updateRequest->data, $rules);
    }

    /**
     * Apply the update request.
     *
     * @param \App\Models\UpdateRequest $updateRequest
     * @return \App\Models\UpdateRequest
     */
    public function applyUpdateRequest(UpdateRequest $updateRequest): UpdateRequest
    {
        // Update the service location.
        $this->update(['name' => $updateRequest->data['name']]);

        // Attach the regular opening hours.
        $this->regularOpeningHours()->delete();
        foreach ($updateRequest->data['regular_opening_hours'] as $regularOpeningHour) {
            $this->regularOpeningHours()->create([
                'frequency' => $regularOpeningHour['frequency'],
                'weekday' => ($regularOpeningHour['frequency'] === RegularOpeningHour::FREQUENCY_WEEKLY)
                    ? $regularOpeningHour['weekday']
                    : null,
                'day_of_month' => ($regularOpeningHour['frequency'] === RegularOpeningHour::FREQUENCY_MONTHLY)
                    ? $regularOpeningHour['day_of_month']
                    : null,
                'occurrence_of_month' => ($regularOpeningHour['frequency'] === RegularOpeningHour::FREQUENCY_NTH_OCCURRENCE_OF_MONTH)
                    ? $regularOpeningHour['occurrence_of_month']
                    : null,
                'starts_at' => ($regularOpeningHour['frequency'] === RegularOpeningHour::FREQUENCY_FORTNIGHTLY)
                    ? $regularOpeningHour['starts_at']
                    : null,
                'opens_at' => $regularOpeningHour['opens_at'],
                'closes_at' => $regularOpeningHour['closes_at'],
            ]);
        }

        // Attach the holiday opening hours.
        $this->holidayOpeningHours()->delete();
        foreach ($updateRequest->data['holiday_opening_hours'] as $holidayOpeningHour) {
            $this->holidayOpeningHours()->create([
                'is_closed' => $holidayOpeningHour['is_closed'],
                'starts_at' => $holidayOpeningHour['starts_at'],
                'ends_at' => $holidayOpeningHour['ends_at'],
                'opens_at' => $holidayOpeningHour['opens_at'],
                'closes_at' => $holidayOpeningHour['closes_at'],
            ]);
        }

        return $updateRequest;
    }
}
