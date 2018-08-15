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

    public function test_is_open_now_with_monthly_frequency_returns_true_if_day_of_month_id_today()
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
}
