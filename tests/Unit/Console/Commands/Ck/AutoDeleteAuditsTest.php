<?php

namespace Tests\Unit\Console\Commands\Ck;

use App\Console\Commands\Ck\AutoDeleteAuditsCommand;
use App\Models\Audit;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class AutoDeleteAuditsTest extends TestCase
{
    public function test_auto_delete_works()
    {
        $newAudit = factory(Audit::class)->create([
            'created_at' => today(),
            'updated_at' => today(),
        ]);

        $dueForDeletionAudit = factory(Audit::class)->create([
            'created_at' => today()->subMonths(Audit::AUTO_DELETE_MONTHS),
            'updated_at' => today()->subMonths(Audit::AUTO_DELETE_MONTHS),
        ]);

        Artisan::call(AutoDeleteAuditsCommand::class);

        $this->assertDatabaseHas($newAudit->getTable(), ['id' => $newAudit->id]);
        $this->assertDatabaseMissing($dueForDeletionAudit->getTable(), ['id' => $dueForDeletionAudit->id]);
    }
}
