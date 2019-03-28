<?php

namespace Tests\Unit\Console\Commands\Ck;

use App\Console\Commands\Ck\AutoDelete\PendingAssignmentFilesCommand;
use App\Models\File;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class PendingAssignmentFilesTest extends TestCase
{
    public function test_auto_delete_works()
    {
        $newPendingAssignmentFile = factory(File::class)
            ->states('pending-assignment')
            ->create([
                'created_at' => today(),
                'updated_at' => today(),
            ]);

        $dueForDeletionFile = factory(File::class)
            ->states('pending-assignment')
            ->create([
                'created_at' => today()->subDays(File::PEDNING_ASSIGNMENT_AUTO_DELETE_DAYS),
                'updated_at' => today()->subDays(File::PEDNING_ASSIGNMENT_AUTO_DELETE_DAYS),
            ]);

        Artisan::call(PendingAssignmentFilesCommand::class);

        $this->assertDatabaseHas($newPendingAssignmentFile->getTable(), ['id' => $newPendingAssignmentFile->id]);
        $this->assertDatabaseMissing($dueForDeletionFile->getTable(), ['id' => $dueForDeletionFile->id]);
    }
}
