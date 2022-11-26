<?php

namespace Tests\Unit\Console\Commands\Ck;

use Tests\TestCase;
use App\Models\Taxonomy;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use App\Console\Commands\Ck\ImportTaxonomiesCommand;

class ImportTaxonomiesCommandTest extends TestCase
{
    public $taxonomyRecords = [];

    public function setUp(): void
    {
        parent::setUp();

        $this->taxonomyRecords = [
            ['level-0-0', 'Level 0 0', Taxonomy::category()->id],
            ['level-0-1', 'Level 0 1', Taxonomy::category()->id],
            ['level-0-2', 'Level 0 2', Taxonomy::category()->id],
            ['level-1-0', 'Level 1 0', 'level-0-0'],
            ['level-1-1', 'Level 1 1', 'level-0-0'],
            ['level-1-2', 'Level 1 2', 'level-0-0'],
            ['level-1-3', 'Level 1 3', 'level-0-1'],
            ['level-1-4', 'Level 1 4', 'level-0-1'],
            ['level-1-5', 'Level 1 5', 'level-0-1'],
            ['level-1-6', 'Level 1 6', 'level-0-2'],
            ['level-1-7', 'Level 1 7', 'level-0-2'],
            ['level-1-8', 'Level 1 8', 'level-0-2'],
            ['level-2-0', 'Level 2 0', 'level-1-0'],
            ['level-2-1', 'Level 2 1', 'level-1-0'],
            ['level-2-2', 'Level 2 2', 'level-1-0'],
            ['level-2-3', 'Level 2 3', 'level-1-1'],
            ['level-2-4', 'Level 2 4', 'level-1-1'],
            ['level-2-5', 'Level 2 5', 'level-1-3'],
            ['level-2-6', 'Level 2 6', 'level-1-3'],
            ['level-2-7', 'Level 2 7', 'level-1-4'],
            ['level-2-8', 'Level 2 8', 'level-1-4'],
            ['level-2-9', 'Level 2 9', 'level-1-6'],
            ['level-3-0', 'Level 3 0', 'level-2-0'],
            ['level-3-1', 'Level 3 1', 'level-2-1'],
            ['level-3-2', 'Level 3 2', 'level-2-2'],
            ['level-3-3', 'Level 3 3', 'level-2-3'],
            ['level-3-4', 'Level 3 4', 'level-2-4'],
            ['level-3-5', 'Level 3 5', 'level-2-5'],
            ['level-3-6', 'Level 3 6', 'level-2-6'],
            ['level-3-7', 'Level 3 7', 'level-2-7'],
            ['level-3-8', 'Level 3 8', 'level-2-8'],
            ['level-3-9', 'Level 3 9', 'level-2-9'],
        ];
    }

    /**
     * @test
     */
    public function it_can_convert_an_indexed_array_to_an_associative_array()
    {
        $params = [
            'taxonomyIdColumn' => 0,
            'taxonomyNameColumn' => 1,
            'parentIdColumn' => 2,
            'rootTaxonomy' => Taxonomy::category()
        ];
        $associativeRecords = (new ImportTaxonomiesCommand($params))->mapToUuidKeys($this->taxonomyRecords);

        foreach ($associativeRecords as $uuid => $record) {
            $recordKey = array_search($record['name'], array_column($this->taxonomyRecords, 1));
            $parentKey = array_search($this->taxonomyRecords[$recordKey][2], array_column($this->taxonomyRecords, 0));
            $parentUuid = $this->taxonomyRecords[$recordKey][2] === Taxonomy::category()->id? Taxonomy::category()->id : array_search($this->taxonomyRecords[$parentKey][1], array_column($associativeRecords, 'name', 'id'));
            $this->assertEquals(
                [
                    'id' => $uuid,
                    'name' => $this->taxonomyRecords[$recordKey][1],
                    'parent_id' => $parentUuid,
                    'order' => 0,
                    'depth' => 1,
                    'created_at' => $record['created_at'],
                    'updated_at' => $record['updated_at'],
                ],
                $record
            );
        }
    }

    /**
     * @test
     */
    public function it_can_calculate_taxonomy_depth()
    {
        $records = [
            'level-0-0' => [
                'id' => 'level-0-0',
                'name' => 'Level 0 0',
                'parent_id' => Taxonomy::category()->id,
                'order' => 0,
                'depth' => 1,
            ],
            'level-1-0' => [
                'id' => 'level-1-0',
                'name' => 'Level 1 0',
                'parent_id' => 'level-0-0',
                'order' => 0,
                'depth' => 1,
            ],
            'level-2-0' => [
                'id' => 'level-2-0',
                'name' => 'Level 2 0',
                'parent_id' => 'level-1-0',
                'order' => 0,
                'depth' => 1,
            ],
            'level-3-0' => [
                'id' => 'level-3-0',
                'name' => 'Level 3 0',
                'parent_id' => 'level-2-0',
                'order' => 0,
                'depth' => 1,
            ],
            'level-1-1' => [
                'id' => 'level-1-1',
                'name' => 'Level 1 1',
                'parent_id' => 'level-0-0',
                'order' => 0,
                'depth' => 1,
            ],
            'level-2-1' => [
                'id' => 'level-2-1',
                'name' => 'Level 2-1',
                'parent_id' => 'level-1-0',
                'order' => 0,
                'depth' => 1,
            ],
        ];

        $cmd = new ImportTaxonomiesCommand();

        $recordsWithDepth = $cmd->calculateTaxonomyDepth(['level-0-0'], $records);

        $this->assertEquals(1, $recordsWithDepth['level-0-0']['depth']);
        $this->assertEquals(2, $recordsWithDepth['level-1-0']['depth']);
        $this->assertEquals(2, $recordsWithDepth['level-1-1']['depth']);
        $this->assertEquals(3, $recordsWithDepth['level-2-0']['depth']);
        $this->assertEquals(3, $recordsWithDepth['level-2-1']['depth']);
        $this->assertEquals(4, $recordsWithDepth['level-3-0']['depth']);
    }

    /**
     * @test
     */
    public function it_builds_a_taxonomy_tree_from_a_flat_array()
    {
        $params = [
            'taxonomyIdColumn' => 0,
            'taxonomyNameColumn' => 1,
            'parentIdColumn' => 2,
            'rootTaxonomy' => Taxonomy::category()
        ];

        $cmd = new ImportTaxonomiesCommand($params);
        $taxonomyRecords = $cmd->mapToUuidKeys($this->taxonomyRecords);

        $taxonomyRecords = $cmd->mapTaxonomyDepth($taxonomyRecords);

        $this->assertEquals(1, $taxonomyRecords[array_search('Level 0 0', array_column($taxonomyRecords, 'name', 'id'))]['depth']);
        $this->assertEquals(2, $taxonomyRecords[array_search('Level 1 6', array_column($taxonomyRecords, 'name', 'id'))]['depth']);
        $this->assertEquals(3, $taxonomyRecords[array_search('Level 2 4', array_column($taxonomyRecords, 'name', 'id'))]['depth']);
        $this->assertEquals(4, $taxonomyRecords[array_search('Level 3 9', array_column($taxonomyRecords, 'name', 'id'))]['depth']);
    }

    /**
     * @test
     */
    public function it_can_delete_all_taxonomies()
    {
        $cmd = new ImportTaxonomiesCommand();

        $currentTaxonomyCount = count($cmd->getDescendantTaxonomyIds([Taxonomy::category()->id]));

        $rootTaxonomies = factory(Taxonomy::class, 3)->create();

        $rootTaxonomies->each(function ($taxonomy) {
            factory(Taxonomy::class, 5)->create([
                'parent_id' => $taxonomy->id,
                'depth' => 2,
            ]);
        });

        $this->assertCount($currentTaxonomyCount + 18, $cmd->getDescendantTaxonomyIds([Taxonomy::category()->id]));

        $cmd->deleteAllTaxonomies();

        $this->assertCount(0, $cmd->getDescendantTaxonomyIds([Taxonomy::category()->id]));
    }

    /**
     * @test
     */
    public function it_can_import_an_array_of_taxonomy_data()
    {
        $faker = Faker::create('en_GB');
        $params = [
            'taxonomyIdColumn' => 0,
            'taxonomyNameColumn' => 1,
            'parentIdColumn' => 2,
            'firstRowLabels' => true,
            'rootTaxonomy' => Taxonomy::category()
        ];
        $cmd = new ImportTaxonomiesCommand($params);
        $currentTaxonomyCount = count($cmd->getDescendantTaxonomyIds([Taxonomy::category()->id]));

        $taxonomyRecords = [
            [uuid(), $faker->words(3, true), null],
        ];
        for ($i = 0; $i < 10; $i++) {
            $taxonomyRecords[] = [uuid(), $faker->words(3, true), $taxonomyRecords[0][0]];
        }
        for ($i = 0; $i < 10; $i++) {
            $taxonomyRecords[] = [uuid(), $faker->words(3, true), $taxonomyRecords[2][0]];
            $taxonomyRecords[] = [uuid(), $faker->words(3, true), $taxonomyRecords[4][0]];
            $taxonomyRecords[] = [uuid(), $faker->words(3, true), $taxonomyRecords[6][0]];
            $taxonomyRecords[] = [uuid(), $faker->words(3, true), $taxonomyRecords[8][0]];
        }

        array_splice($taxonomyRecords, 0, 0, [['ID', 'Name', 'Parent ID']]);

        $cmd->importTaxonomyRecords($taxonomyRecords, false, false);

        $this->assertDatabaseHas((new Taxonomy())->getTable(), [
            'name' => $taxonomyRecords[10][1],
        ]);

        $this->assertCount(($currentTaxonomyCount + count($taxonomyRecords) - 1), $cmd->getDescendantTaxonomyIds([Taxonomy::category()->id]));
    }

    /**
     * @test
     */
    public function it_can_delete_existing_taxonomies_and_import_an_array_of_taxonomy_data()
    {
        $faker = Faker::create('en_GB');
        $params = [
            'taxonomyIdColumn' => 0,
            'taxonomyNameColumn' => 1,
            'parentIdColumn' => 2,
            'firstRowLabels' => true,
            'rootTaxonomy' => Taxonomy::category()
        ];
        $cmd = new ImportTaxonomiesCommand($params);

        $taxonomyRecords = [
            [uuid(), $faker->words(3, true), null],
        ];
        for ($i = 0; $i < 10; $i++) {
            $taxonomyRecords[] = [uuid(), $faker->words(3, true), $taxonomyRecords[0][0]];
        }
        for ($i = 0; $i < 10; $i++) {
            $taxonomyRecords[] = [uuid(), $faker->words(3, true), $taxonomyRecords[2][0]];
            $taxonomyRecords[] = [uuid(), $faker->words(3, true), $taxonomyRecords[4][0]];
            $taxonomyRecords[] = [uuid(), $faker->words(3, true), $taxonomyRecords[6][0]];
            $taxonomyRecords[] = [uuid(), $faker->words(3, true), $taxonomyRecords[8][0]];
        }

        array_splice($taxonomyRecords, 0, 0, [['ID', 'Name', 'Parent ID']]);

        $cmd->importTaxonomyRecords($taxonomyRecords, true, false);

        $this->assertDatabaseHas((new Taxonomy())->getTable(), [
            'name' => $taxonomyRecords[10][1],
        ]);

        $this->assertCount(count($taxonomyRecords) - 1, $cmd->getDescendantTaxonomyIds([Taxonomy::category()->id]));
    }
}
