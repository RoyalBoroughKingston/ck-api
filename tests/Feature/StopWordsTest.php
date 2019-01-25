<?php

namespace Tests\Feature;

use Illuminate\Http\Response;
use Tests\TestCase;

class StopWordsTest extends TestCase
{
    /*
     * View the stop words.
     */

    public function test_guest_cannot_view_stop_words()
    {
        $response = $this->json('GET', '/core/v1/stop-words');
        
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
