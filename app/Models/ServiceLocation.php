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
use Illuminate\Support\Carbon;
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
        // First check if any holiday opening hours have been specified.
        $hasHolidayHoursOpenNow = $this->hasHolidayHoursOpenNow();

        // If holiday opening hours found, then return them.
        if ($hasHolidayHoursOpenNow !== null) {
            return $hasHolidayHoursOpenNow;
        }

        // If no holiday hours found, then resort to regular opening hours.
        return $this->hasRegularHoursOpenNow();
    }

    /**
     * Returns true if open, false if closed, or null if not specified.
     *
     * @return bool|null
     */
    protected function hasHolidayHoursOpenNow(): ?bool
    {
        // Get the holiday opening hours that today falls within.
        $holidayOpeningHour = $this->holidayOpeningHours()
            ->where('starts_at', '<=', today())
            ->where('ends_at', '>=', today())
            ->first();

        // If none found, return null.
        if ($holidayOpeningHour === null) {
            return null;
        }

        // If closed, opening and closing time are redundant, so just return false.
        if ($holidayOpeningHour->is_closed) {
            return false;
        }

        // Return if the current time falls within the opening and closing time.
        return Time::now()->between($holidayOpeningHour->opens_at, $holidayOpeningHour->closes_at);
    }

    /**
     * @return bool
     */
    protected function hasRegularHoursOpenNow(): bool
    {
        // Loop through each opening hour.
        foreach ($this->regularOpeningHours as $regularOpeningHour) {
            // Check if the current time falls within the opening hours.
            $isOpenNow = Time::now()->between($regularOpeningHour->opens_at, $regularOpeningHour->closes_at);

            // If not, then continue to the next opening hour.
            if (!$isOpenNow) {
                continue;
            }

            // Use a different algorithm for each frequency type.
            switch ($regularOpeningHour->frequency) {
                // If weekly then check that the weekday is the same as today.
                case RegularOpeningHour::FREQUENCY_WEEKLY:
                    if (today()->dayOfWeek === $regularOpeningHour->weekday) {
                        return true;
                    }
                    break;
                // If monthly then check that the day of the month is the same as today.
                case RegularOpeningHour::FREQUENCY_MONTHLY:
                    if (today()->day === $regularOpeningHour->day_of_month) {
                        return true;
                    }
                    break;
                // If fortnightly then check that today falls directly on a multiple of 2 weeks.
                case RegularOpeningHour::FREQUENCY_FORTNIGHTLY:
                    if (fmod(today()->diffInDays($regularOpeningHour->starts_at) / Carbon::DAYS_PER_WEEK, 2) === 0.0) {
                        return true;
                    }
                    break;
                // If nth occurrence of month then check today is the same occurrence.
                case RegularOpeningHour::FREQUENCY_NTH_OCCURRENCE_OF_MONTH:
                    $occurrence = occurrence($regularOpeningHour->occurrence_of_month);
                    $weekday = weekday($regularOpeningHour->weekday);
                    $month = month(today()->month);
                    $year = today()->year;
                    $dateString = "$occurrence $weekday of $month $year";
                    $date = Carbon::createFromTimestamp(strtotime($dateString));
                    if (today()->equalTo($date)) {
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
        $this->update([
            'name' => $updateRequest->data['name'] ?? $this->name,
        ]);

        // Attach the regular opening hours.
        if (array_key_exists('regular_opening_hours', $updateRequest->data)) {
            $this->regularOpeningHours()->delete();
            foreach ($updateRequest->data['regular_opening_hours'] as $regularOpeningHour) {
                $this->regularOpeningHours()->create([
                    'frequency' => $regularOpeningHour['frequency'],
                    'weekday' => in_array(
                        $regularOpeningHour['frequency'],
                        [RegularOpeningHour::FREQUENCY_WEEKLY, RegularOpeningHour::FREQUENCY_NTH_OCCURRENCE_OF_MONTH]
                    )
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
        }

        // Attach the holiday opening hours.
        if (array_key_exists('holiday_opening_hours', $updateRequest->data)) {
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
