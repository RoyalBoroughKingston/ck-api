<?php

namespace Tests\Unit\Models;

use App\Models\RegularOpeningHour;
use App\Models\ServiceLocation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
use Tests\TestCase;

class ServiceLocationTest extends TestCase
{
    public function test_is_open_now_returns_false_if_no_opening_hours_associated()
    {
        $serviceLocation = factory(ServiceLocation::class)->create();

        $this->assertFalse($serviceLocation->isOpenNow());
    }

    /*
     * Weekly Frequency.
     */

    public function test_is_open_now_with_weekly_frequency_returns_true_if_weekday_is_today()
    {
        $serviceLocation = factory(ServiceLocation::class)->create();
        $serviceLocation->regularOpeningHours()->create([
            'frequency' => RegularOpeningHour::FREQUENCY_WEEKLY,
            'weekday' => Date::today()->dayOfWeek,
            'opens_at' => '00:00:00',
            'closes_at' => '24:00:00',
        ]);

        $this->assertTrue($serviceLocation->isOpenNow());
    }

    public function test_is_open_now_with_weekly_frequency_returns_false_if_weekday_is_not_today()
    {
        $serviceLocation = factory(ServiceLocation::class)->create();
        $serviceLocation->regularOpeningHours()->create([
            'frequency' => RegularOpeningHour::FREQUENCY_WEEKLY,
            'weekday' => Date::today()->addDay()->dayOfWeek,
            'opens_at' => '00:00:00',
            'closes_at' => '24:00:00',
        ]);

        $this->assertFalse($serviceLocation->isOpenNow());
    }

    public function test_is_open_now_with_weekly_frequency_returns_false_if_weekday_is_today_but_out_of_hours()
    {
        Carbon::setTestNow(Date::now()->setTime(9, 0));

        $serviceLocation = factory(ServiceLocation::class)->create();
        $serviceLocation->regularOpeningHours()->create([
            'frequency' => RegularOpeningHour::FREQUENCY_WEEKLY,
            'weekday' => Date::today()->dayOfWeek,
            'opens_at' => '10:00:00',
            'closes_at' => '24:00:00',
        ]);

        $this->assertFalse($serviceLocation->isOpenNow());
    }

    /*
     * Monthly Frequency.
     */

    public function test_is_open_now_with_monthly_frequency_returns_true_if_day_of_month_is_today()
    {
        $serviceLocation = factory(ServiceLocation::class)->create();
        $serviceLocation->regularOpeningHours()->create([
            'frequency' => RegularOpeningHour::FREQUENCY_MONTHLY,
            'day_of_month' => Date::today()->day,
            'opens_at' => '00:00:00',
            'closes_at' => '24:00:00',
        ]);

        $this->assertTrue($serviceLocation->isOpenNow());
    }

    public function test_is_open_now_with_monthly_frequency_returns_false_if_day_of_month_is_not_today()
    {
        $serviceLocation = factory(ServiceLocation::class)->create();
        $serviceLocation->regularOpeningHours()->create([
            'frequency' => RegularOpeningHour::FREQUENCY_MONTHLY,
            'day_of_month' => Date::today()->addDay()->day,
            'opens_at' => '00:00:00',
            'closes_at' => '24:00:00',
        ]);

        $this->assertFalse($serviceLocation->isOpenNow());
    }

    public function test_is_open_now_with_monthly_frequency_returns_false_if_day_of_month_is_today_but_out_of_hours()
    {
        Carbon::setTestNow(Date::now()->setTime(9, 0));

        $serviceLocation = factory(ServiceLocation::class)->create();
        $serviceLocation->regularOpeningHours()->create([
            'frequency' => RegularOpeningHour::FREQUENCY_MONTHLY,
            'day_of_month' => Date::today()->day,
            'opens_at' => '12:00:00',
            'closes_at' => '24:00:00',
        ]);

        $this->assertFalse($serviceLocation->isOpenNow());
    }

    /*
     * Fortnightly Frequency.
     */

    public function test_is_open_now_with_fortnightly_frequency_returns_true_if_lands_on_today()
    {
        $serviceLocation = factory(ServiceLocation::class)->create();
        $serviceLocation->regularOpeningHours()->create([
            'frequency' => RegularOpeningHour::FREQUENCY_FORTNIGHTLY,
            'starts_at' => Date::today(),
            'opens_at' => '00:00:00',
            'closes_at' => '24:00:00',
        ]);

        $this->assertTrue($serviceLocation->isOpenNow());
    }

    public function test_is_open_now_with_fortnightly_frequency_returns_true_if_lands_on_today_in_past()
    {
        $serviceLocation = factory(ServiceLocation::class)->create();
        $serviceLocation->regularOpeningHours()->create([
            'frequency' => RegularOpeningHour::FREQUENCY_FORTNIGHTLY,
            'starts_at' => Date::today()->subWeeks(2),
            'opens_at' => '00:00:00',
            'closes_at' => '24:00:00',
        ]);

        $this->assertTrue($serviceLocation->isOpenNow());
    }

    public function test_is_open_now_with_fortnightly_frequency_returns_true_if_lands_on_today_in_future()
    {
        $serviceLocation = factory(ServiceLocation::class)->create();
        $serviceLocation->regularOpeningHours()->create([
            'frequency' => RegularOpeningHour::FREQUENCY_FORTNIGHTLY,
            'starts_at' => Date::today()->addWeeks(2),
            'opens_at' => '00:00:00',
            'closes_at' => '24:00:00',
        ]);

        $this->assertTrue($serviceLocation->isOpenNow());
    }

    public function test_is_open_now_with_fortnightly_frequency_returns_false_if_lands_on_odd_week()
    {
        $serviceLocation = factory(ServiceLocation::class)->create();
        $serviceLocation->regularOpeningHours()->create([
            'frequency' => RegularOpeningHour::FREQUENCY_FORTNIGHTLY,
            'starts_at' => Date::today()->addWeek(),
            'opens_at' => '00:00:00',
            'closes_at' => '24:00:00',
        ]);

        $this->assertFalse($serviceLocation->isOpenNow());
    }

    public function test_is_open_now_with_fortnightly_frequency_returns_false_if_lands_off_schedule()
    {
        $serviceLocation = factory(ServiceLocation::class)->create();
        $serviceLocation->regularOpeningHours()->create([
            'frequency' => RegularOpeningHour::FREQUENCY_FORTNIGHTLY,
            'starts_at' => Date::today()->addDays(3),
            'opens_at' => '00:00:00',
            'closes_at' => '24:00:00',
        ]);

        $this->assertFalse($serviceLocation->isOpenNow());
    }

    public function test_is_open_now_with_fortnightly_frequency_returns_false_if_lands_on_today_but_out_of_hours()
    {
        Carbon::setTestNow(Date::now()->setTime(9, 0));

        $serviceLocation = factory(ServiceLocation::class)->create();
        $serviceLocation->regularOpeningHours()->create([
            'frequency' => RegularOpeningHour::FREQUENCY_FORTNIGHTLY,
            'starts_at' => Date::today(),
            'opens_at' => '10:00:00',
            'closes_at' => '24:00:00',
        ]);

        $this->assertFalse($serviceLocation->isOpenNow());
    }

    /*
     * Nth Occurrence of Month.
     */

    public function test_is_open_now_with_nth_occurrence_of_month_frequency_returns_true_if_lands_on_today()
    {
        $now = Carbon::createFromTimestamp(strtotime('second tuesday of august 2018'));
        Carbon::setTestNow($now->setTime(9, 0));

        $serviceLocation = factory(ServiceLocation::class)->create();
        $serviceLocation->regularOpeningHours()->create([
            'frequency' => RegularOpeningHour::FREQUENCY_NTH_OCCURRENCE_OF_MONTH,
            'weekday' => RegularOpeningHour::WEEKDAY_TUESDAY,
            'occurrence_of_month' => 2,
            'opens_at' => '00:00:00',
            'closes_at' => '24:00:00',
        ]);

        $this->assertTrue($serviceLocation->isOpenNow());
    }

    public function test_is_open_now_with_nth_occurrence_of_month_frequency_returns_false_if_lands_on_different_day()
    {
        $now = Carbon::createFromTimestamp(strtotime('second tuesday of august 2018'));
        Carbon::setTestNow($now->setTime(9, 0));

        $serviceLocation = factory(ServiceLocation::class)->create();
        $serviceLocation->regularOpeningHours()->create([
            'frequency' => RegularOpeningHour::FREQUENCY_NTH_OCCURRENCE_OF_MONTH,
            'weekday' => RegularOpeningHour::WEEKDAY_TUESDAY,
            'occurrence_of_month' => 3,
            'opens_at' => '00:00:00',
            'closes_at' => '24:00:00',
        ]);

        $this->assertFalse($serviceLocation->isOpenNow());
    }

    public function test_is_open_now_with_nth_occurrence_of_month_frequency_returns_true_if_lands_on_today_but_out_of_hours()
    {
        $now = Carbon::createFromTimestamp(strtotime('second tuesday of august 2018'));
        Carbon::setTestNow($now->setTime(9, 0));

        $serviceLocation = factory(ServiceLocation::class)->create();
        $serviceLocation->regularOpeningHours()->create([
            'frequency' => RegularOpeningHour::FREQUENCY_NTH_OCCURRENCE_OF_MONTH,
            'weekday' => RegularOpeningHour::WEEKDAY_TUESDAY,
            'occurrence_of_month' => 2,
            'opens_at' => '10:00:00',
            'closes_at' => '24:00:00',
        ]);

        $this->assertFalse($serviceLocation->isOpenNow());
    }

    public function test_is_open_now_with_nth_occurrence_of_month_frequency_returns_true_for_last_day_of_month()
    {
        $now = Carbon::createFromTimestamp(strtotime('last friday of september 2018'));
        Carbon::setTestNow($now->setTime(9, 0));

        $serviceLocation = factory(ServiceLocation::class)->create();
        $serviceLocation->regularOpeningHours()->create([
            'frequency' => RegularOpeningHour::FREQUENCY_NTH_OCCURRENCE_OF_MONTH,
            'weekday' => RegularOpeningHour::WEEKDAY_FRIDAY,
            'occurrence_of_month' => 5,
            'opens_at' => '09:00:00',
            'closes_at' => '17:00:00',
        ]);

        $this->assertTrue($serviceLocation->isOpenNow());
    }

    /*
     * Holiday Opening Hours.
     */

    public function test_is_open_now_returns_true_if_holiday_opening_hours_include_today()
    {
        $serviceLocation = factory(ServiceLocation::class)->create();
        $serviceLocation->holidayOpeningHours()->create([
            'is_closed' => false,
            'starts_at' => Date::today()->subDay(),
            'ends_at' => Date::today()->addDay(),
            'opens_at' => '00:00:00',
            'closes_at' => '24:00:00',
        ]);

        $this->assertTrue($serviceLocation->isOpenNow());
    }

    public function test_is_open_now_returns_false_if_holiday_opening_hours_include_today_but_out_of_hours()
    {
        Carbon::setTestNow(Date::now()->setTime(9, 0));

        $serviceLocation = factory(ServiceLocation::class)->create();
        $serviceLocation->holidayOpeningHours()->create([
            'is_closed' => false,
            'starts_at' => Date::today()->subDay(),
            'ends_at' => Date::today()->addDay(),
            'opens_at' => '10:00:00',
            'closes_at' => '24:00:00',
        ]);

        $this->assertFalse($serviceLocation->isOpenNow());
    }

    public function test_is_open_now_with_weekly_frequency_returns_false_if_weekday_is_today_but_closed_for_holiday()
    {
        $serviceLocation = factory(ServiceLocation::class)->create();
        $serviceLocation->holidayOpeningHours()->create([
            'is_closed' => true,
            'starts_at' => Date::today()->subDay(),
            'ends_at' => Date::today()->addDay(),
            'opens_at' => '00:00:00',
            'closes_at' => '24:00:00',
        ]);
        $serviceLocation->regularOpeningHours()->create([
            'frequency' => RegularOpeningHour::FREQUENCY_WEEKLY,
            'weekday' => Date::today()->dayOfWeek,
            'opens_at' => '00:00:00',
            'closes_at' => '24:00:00',
        ]);

        $this->assertFalse($serviceLocation->isOpenNow());
    }
}
