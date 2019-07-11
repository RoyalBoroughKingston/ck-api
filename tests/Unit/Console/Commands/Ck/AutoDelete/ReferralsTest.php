<?php

namespace Tests\Unit\Console\Commands\Ck;

use App\Console\Commands\Ck\AutoDelete\ReferralsCommand;
use App\Models\Referral;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Date;
use Tests\TestCase;

class ReferralsTest extends TestCase
{
    public function test_auto_delete_works()
    {
        $newReferral = factory(Referral::class)->create([
            'email' => 'test@example.com',
            'referee_email' => 'test@example.com',
            'status' => Referral::STATUS_COMPLETED,
            'created_at' => Date::today(),
            'updated_at' => Date::today(),
        ]);

        $dueForDeletionReferral = factory(Referral::class)->create([
            'email' => 'test@example.com',
            'referee_email' => 'test@example.com',
            'status' => Referral::STATUS_COMPLETED,
            'created_at' => Date::today()->subMonths(Referral::AUTO_DELETE_MONTHS),
            'updated_at' => Date::today()->subMonths(Referral::AUTO_DELETE_MONTHS),
        ]);

        Artisan::call(ReferralsCommand::class);

        $this->assertDatabaseHas($newReferral->getTable(), ['id' => $newReferral->id]);
        $this->assertDatabaseMissing($dueForDeletionReferral->getTable(), ['id' => $dueForDeletionReferral->id]);
    }

    public function test_old_incompleted_referrals_are_not_deleted()
    {
        $dueForDeletionReferral = factory(Referral::class)->create([
            'email' => 'test@example.com',
            'referee_email' => 'test@example.com',
            'status' => Referral::STATUS_INCOMPLETED,
            'created_at' => Date::today()->subMonths(Referral::AUTO_DELETE_MONTHS),
            'updated_at' => Date::today()->subMonths(Referral::AUTO_DELETE_MONTHS),
        ]);

        Artisan::call(ReferralsCommand::class);

        $this->assertDatabaseHas($dueForDeletionReferral->getTable(), ['id' => $dueForDeletionReferral->id]);
    }
}
