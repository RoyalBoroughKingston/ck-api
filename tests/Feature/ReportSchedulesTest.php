<?php

namespace Tests\Feature;

use Illuminate\Http\Response;
use Tests\TestCase;

class ReportSchedulesTest extends TestCase
{
    /*
     * List all the report schedules.
     */

    public function test_guest_cannot_list_them()
    {
        $response = $this->json('GET', '/core/v1/report-schedules');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /*
     * Create a report schedule.
     */

    /*
     * Get a specific report schedule.
     */

    /*
     * Update a specific report schedule.
     */

    /*
     * Delete a specific report schdule.
     */
}
