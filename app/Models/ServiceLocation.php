<?php

namespace App\Models;

use App\Http\Requests\ServiceLocation\UpdateRequest as Request;
use App\Models\Mutators\ServiceLocationMutators;
use App\Models\Relationships\ServiceLocationRelationships;
use App\Models\Scopes\ServiceLocationScopes;
use App\Support\Time;
use App\UpdateRequest\AppliesUpdateRequests;
use App\UpdateRequest\UpdateRequests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

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
        foreach ($this->regularOpeningHours as $regularOpeningHour) {
            $isOpenNow = Time::now()->between($regularOpeningHour->opens_at, $regularOpeningHour->closes_at);

            if (!$isOpenNow) {
                continue;
            }

            switch ($regularOpeningHour->frequency) {
                case RegularOpeningHour::FREQUENCY_WEEKLY:
                    if (today()->dayOfWeek === $regularOpeningHour->weekday) {
                        return true;
                    }
                    break;
                case RegularOpeningHour::FREQUENCY_MONTHLY:
                    if (today()->day === $regularOpeningHour->day_of_month) {
                        return true;
                    }
                    break;
            }
        }

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
        $rules = (new Request())->rules();

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

    /**
     * @return \App\Models\ServiceLocation
     */
    public function touchService(): ServiceLocation
    {
        $this->service->save();

        return $this;
    }
}
