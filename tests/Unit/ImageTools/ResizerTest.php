<?php

namespace Tests\Unit\ImageTools;

use App\ImageTools\Resizer;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResizerTest extends TestCase
{
    const WIDTH = 0;
    const HEIGHT = 1;

    public function test_resize_works()
    {
        $srcContent = Storage::disk('local')->get('test-data/image.png');

        $resizer = new Resizer();

        $dstContent = $resizer->resize($srcContent, 300);

        $this->assertTrue(is_string($dstContent));

        $imageInfo = getimagesizefromstring($dstContent);

        $this->assertEquals(300, $imageInfo[static::WIDTH]);
        $this->assertEquals(300, $imageInfo[static::HEIGHT]);
        $this->assertEquals('image/png', $imageInfo['mime']);
    }
}
