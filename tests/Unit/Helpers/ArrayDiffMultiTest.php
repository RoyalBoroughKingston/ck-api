<?php

namespace Tests\Unit\Helpers;

use Tests\TestCase;

class ArrayDiffMultiTest extends TestCase
{
    public function test_added_elements_works()
    {
        $arrayA = [
            ['name' => 'John', 'age' => 21],
            ['name' => 'Bob', 'age' => 20],
        ];
        $arrayB = [
            ['name' => 'John', 'age' => 21],
        ];

        $difference = array_diff_multi($arrayA, $arrayB);

        $this->assertEquals([
            ['name' => 'Bob', 'age' => 20],
        ], $difference);
    }

    public function test_removed_elements_works()
    {
        $arrayA = [
            ['name' => 'John', 'age' => 21],
        ];
        $arrayB = [
            ['name' => 'John', 'age' => 21],
            ['name' => 'Bob', 'age' => 20],
        ];

        $difference = array_diff_multi($arrayB, $arrayA);

        $this->assertEquals([
            ['name' => 'Bob', 'age' => 20],
        ], $difference);
    }

    public function test_added_and_removed_elements_works()
    {
        $arrayA = [
            ['name' => 'John', 'age' => 21],
            ['name' => 'Mike', 'age' => 23],
        ];
        $arrayB = [
            ['name' => 'John', 'age' => 21],
            ['name' => 'Bob', 'age' => 20],
        ];

        $difference = array_diff_multi($arrayA, $arrayB);

        $this->assertEquals([
            ['name' => 'Mike', 'age' => 23],
        ], $difference);
    }
}
