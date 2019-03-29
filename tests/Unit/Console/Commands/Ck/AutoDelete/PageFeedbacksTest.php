<?php

namespace Tests\Unit\Console\Commands\Ck;

use App\Console\Commands\Ck\AutoDelete\PageFeedbacksCommand;
use App\Models\PageFeedback;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class PageFeedbacksTest extends TestCase
{
    public function test_auto_delete_works()
    {
        $newPageFeedback= factory(PageFeedback::class)->create([
            'created_at' => today(),
            'updated_at' => today(),
        ]);

        $dueForDeletionPageFeedback = factory(PageFeedback::class)->create([
            'created_at' => today()->subMonths(PageFeedback::AUTO_DELETE_MONTHS),
            'updated_at' => today()->subMonths(PageFeedback::AUTO_DELETE_MONTHS),
        ]);

        Artisan::call(PageFeedbacksCommand::class);

        $this->assertDatabaseHas($newPageFeedback->getTable(), ['id' => $newPageFeedback->id]);
        $this->assertDatabaseMissing($dueForDeletionPageFeedback->getTable(), ['id' => $dueForDeletionPageFeedback->id]);
    }
}
