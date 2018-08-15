<?php

namespace Tests\Unit\Models;

use App\Models\RegularOpeningHour;
use App\Models\ServiceLocation;
use Illuminate\Support\Carbon;
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
            'weekday' => today()->dayOfWeek,
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
            'weekday' => today()->addDay()->dayOfWeek,
            'opens_at' => '00:00:00',
            'closes_at' => '24:00:00',
        ]);

        $this->assertFalse($serviceLocation->isOpenNow());
    }

    public function test_is_open_now_with_weekly_frequency_returns_false_if_weekday_is_today_but_out_of_hours()
    {
        Carbon::setTestNow(now()->setTime(9, 0));

        $serviceLocation = factory(ServiceLocation::class)->create();
        $serviceLocation->regularOpeningHours()->create([
            'frequency' => RegularOpeningHour::FREQUENCY_WEEKLY,
            'weekday' => today()->dayOfWeek,
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
            'day_of_month' => today()->day,
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
            'day_of_month' => today()->addDay()->day,
            'opens_at' => '00:00:00',
            'closes_at' => '24:00:00',
        ]);

        $this->assertFalse($serviceLocation->isOpenNow());
    }

    public function test_is_open_now_with_monthly_frequency_returns_false_if_day_of_month_is_today_but_out_of_hours()
    {
        Carbon::setTestNow(now()->setTime(9, 0));

        $serviceLocation = factory(ServiceLocation::class)->create();
        $serviceLocation->regularOpeningHours()->create([
            'frequency' => RegularOpeningHour::FREQUENCY_MONTHLY,
            'day_of_month' => today()->day,
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
            'starts_at' => today(),
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
            'starts_at' => today()->subWeeks(2),
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
            'starts_at' => today()->addWeeks(2),
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
            'starts_at' => today()->addWeek(),
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
            'starts_at' => today()->addDays(3),
            'opens_at' => '00:00:00',
            'closes_at' => '24:00:00',
        ]);

        $this->assertFalse($serviceLocation->isOpenNow());
    }

    public function test_is_open_now_with_fortnightly_frequency_returns_false_if_lands_on_today_but_out_of_hours()
    {
        Carbon::setTestNow(now()->setTime(9, 0));

        $serviceLocation = factory(ServiceLocation::class)->create();
        $serviceLocation->regularOpeningHours()->create([
            'frequency' => RegularOpeningHour::FREQUENCY_FORTNIGHTLY,
            'starts_at' => today(),
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
        Carbon::setTestNow(Carbon::createFromTimestamp(strtotime('second tuesday of august 2018')));

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
        Carbon::setTestNow(Carbon::createFromTimestamp(strtotime('second tuesday of august 2018')));

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

    /*
     * Holiday Opening Hours.
     */

    public function test_is_open_now_returns_true_if_holiday_opening_hours_include_today()
    {
        $serviceLocation = factory(ServiceLocation::class)->create();
        $serviceLocation->holidayOpeningHours()->create([
            'is_closed' => false,
            'starts_at' => today()->subDay(),
            'ends_at' => today()->addDay(),
            'opens_at' => '00:00:00',
            'closes_at' => '24:00:00',
        ]);

        $this->assertTrue($serviceLocation->isOpenNow());
    }

    public function test_is_open_now_returns_false_if_holiday_opening_hours_include_today_but_out_of_hours()
    {
        Carbon::setTestNow(now()->setTime(9, 0));

        $serviceLocation = factory(ServiceLocation::class)->create();
        $serviceLocation->holidayOpeningHours()->create([
            'is_closed' => false,
            'starts_at' => today()->subDay(),
            'ends_at' => today()->addDay(),
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
            'starts_at' => today()->subDay(),
            'ends_at' => today()->addDay(),
            'opens_at' => '00:00:00',
            'closes_at' => '24:00:00',
        ]);
        $serviceLocation->regularOpeningHours()->create([
            'frequency' => RegularOpeningHour::FREQUENCY_WEEKLY,
            'weekday' => today()->dayOfWeek,
            'opens_at' => '00:00:00',
            'closes_at' => '24:00:00',
        ]);

        $this->assertFalse($serviceLocation->isOpenNow());
    }
}
