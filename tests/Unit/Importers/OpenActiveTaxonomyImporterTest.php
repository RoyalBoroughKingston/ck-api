<?php

namespace Tests\Unit\Importers;

use App\BatchImport\OpenActiveTaxonomyImporter;
use App\Models\Taxonomy;
use Tests\TestCase;

class OpenActiveTaxonomyImporterTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->openActiveCategory = Taxonomy::firstOrCreate(
            [
                'parent_id' => Taxonomy::category()->id,
                'name' => 'OpenActive',
            ],
            [
                'order' => 0,
                'depth' => 1,
            ]
        );

        $this->openActiveTaxonomyImport = [
            [
                "id" => "https://openactive.io/activity-list#f5d6e765-28d7-4aff-8bde-74baaa1da1dd",
                "identifier" => "f5d6e765-28d7-4aff-8bde-74baaa1da1dd",
                "type" => "Concept",
                "prefLabel" => "Football",
                "narrower" => [
                    "https://openactive.io/activity-list#6016ce87-d9ed-4bd6-8cc9-5598c2f59f79",
                    "https://openactive.io/activity-list#b8019b67-2ade-406f-a012-91a5c3869652",
                    "https://openactive.io/activity-list#1de4c90e-6a27-4bc4-a2be-437a443c7ded",
                    "https://openactive.io/activity-list#666cf454-4733-4697-89cb-8e28f6e8595b",
                    "https://openactive.io/activity-list#6a553d6a-d2ad-487d-a2b1-51842f2d6254",
                    "https://openactive.io/activity-list#8ea70826-d1c6-461f-97fc-b4195e08994b",
                    "https://openactive.io/activity-list#f6301564-93d5-41ff-91a1-7ac2dd833951",
                ],
                "definition" => "Football is widely considered to be the most popular sport in the world. The beautiful game is England's national sport.",
                "notation" => "football",
                "topConceptOf" => "https://openactive.io/activity-list",
            ],
            [
                "id" => "https://openactive.io/activity-list#8ea70826-d1c6-461f-97fc-b4195e08994b",
                "identifier" => "8ea70826-d1c6-461f-97fc-b4195e08994b",
                "type" => "Concept",
                "prefLabel" => "Small Sided Football",
                "broader" => [
                    "https://openactive.io/activity-list#f5d6e765-28d7-4aff-8bde-74baaa1da1dd",
                ],
                "narrower" => [
                    "https://openactive.io/activity-list#47c5e355-be82-4a44-97f1-858b08e71819",
                    "https://openactive.io/activity-list#f62e8cfe-0514-4f6f-9fb5-211b7fc02744",
                    "https://openactive.io/activity-list#fff1781e-d02c-462b-911d-b6d8e1821f75",
                    "https://openactive.io/activity-list#599d80ba-8d24-4836-a39f-790f731c64ac",
                    "https://openactive.io/activity-list#00750444-c29e-43ab-ab8e-0ef800d8bdeb",
                    "https://openactive.io/activity-list#00750444-c29e-43ab-ab8e-0ef800d8bdeb",
                ],
                "notation" => "small_sided_football",
            ],
            [
                "id" => "https://openactive.io/activity-list#6a553d6a-d2ad-487d-a2b1-51842f2d6254",
                "identifier" => "6a553d6a-d2ad-487d-a2b1-51842f2d6254",
                "type" => "Concept",
                "prefLabel" => "11-a-side",
                "broader" => [
                    "https://openactive.io/activity-list#f5d6e765-28d7-4aff-8bde-74baaa1da1dd",
                ],
                "notation" => "11_a_side",
            ],
            [
                "id" => "https://openactive.io/activity-list#00750444-c29e-43ab-ab8e-0ef800d8bdeb",
                "identifier" => "00750444-c29e-43ab-ab8e-0ef800d8bdeb",
                "type" => "Concept",
                "prefLabel" => "5-a-side",
                "broader" => [
                    "https://openactive.io/activity-list#8ea70826-d1c6-461f-97fc-b4195e08994b",
                ],
                "notation" => "5_a_side",
            ],
            [
                "id" => "https://openactive.io/activity-list#f62e8cfe-0514-4f6f-9fb5-211b7fc02744",
                "identifier" => "f62e8cfe-0514-4f6f-9fb5-211b7fc02744",
                "type" => "Concept",
                "prefLabel" => "6-a-side",
                "broader" => [
                    "https://openactive.io/activity-list#8ea70826-d1c6-461f-97fc-b4195e08994b",
                ],
                "notation" => "6_a_side",
            ],
            [
                "id" => "https://openactive.io/activity-list#fff1781e-d02c-462b-911d-b6d8e1821f75",
                "identifier" => "fff1781e-d02c-462b-911d-b6d8e1821f75",
                "type" => "Concept",
                "prefLabel" => "7-a-side",
                "broader" => [
                    "https://openactive.io/activity-list#8ea70826-d1c6-461f-97fc-b4195e08994b",
                ],
                "notation" => "7_a_side",
            ],
            [
                "id" => "https://openactive.io/activity-list#47c5e355-be82-4a44-97f1-858b08e71819",
                "identifier" => "47c5e355-be82-4a44-97f1-858b08e71819",
                "type" => "Concept",
                "prefLabel" => "8-a-side",
                "broader" => [
                    "https://openactive.io/activity-list#8ea70826-d1c6-461f-97fc-b4195e08994b",
                ],
                "notation" => "8_a_side",
            ],
            [
                "id" => "https://openactive.io/activity-list#599d80ba-8d24-4836-a39f-790f731c64ac",
                "identifier" => "599d80ba-8d24-4836-a39f-790f731c64ac",
                "type" => "Concept",
                "prefLabel" => "9-a-side",
                "broader" => [
                    "https://openactive.io/activity-list#8ea70826-d1c6-461f-97fc-b4195e08994b",
                ],
                "notation" => "9_a_side",
            ],
        ];
    }
    /**
     * @test
     */
    public function it_can_format_open_activity_taxonomies()
    {
        $importer = new OpenActiveTaxonomyImporter();
        $importer->getOpenActiveCategory();

        $importTaxonomies = $importer->mapOpenActiveTaxonomyImport($this->openActiveCategory, $this->openActiveTaxonomyImport);

        foreach ($importTaxonomies as $taxonomy) {
            if ($taxonomy['id'] == 'f5d6e765-28d7-4aff-8bde-74baaa1da1dd') {
                $this->assertJsonStringEqualsJsonString(
                    json_encode([
                        'id' => 'f5d6e765-28d7-4aff-8bde-74baaa1da1dd',
                        'name' => 'Football',
                        'parent_id' => $this->openActiveCategory->id,
                        'order' => 0,
                        'depth' => 2,
                        'created_at' => $taxonomy['created_at'],
                        'updated_at' => $taxonomy['updated_at'],
                    ]),
                    json_encode($taxonomy)
                );
            }

            if ($taxonomy['id'] == '8ea70826-d1c6-461f-97fc-b4195e08994b') {
                $this->assertJsonStringEqualsJsonString(
                    json_encode([
                        'id' => '8ea70826-d1c6-461f-97fc-b4195e08994b',
                        'name' => 'Small Sided Football',
                        'parent_id' => 'f5d6e765-28d7-4aff-8bde-74baaa1da1dd',
                        'order' => 0,
                        'depth' => 2,
                        'created_at' => $taxonomy['created_at'],
                        'updated_at' => $taxonomy['updated_at'],
                    ]),
                    json_encode($taxonomy)
                );
            }

            if ($taxonomy['id'] == '00750444-c29e-43ab-ab8e-0ef800d8bdeb') {
                $this->assertJsonStringEqualsJsonString(
                    json_encode([
                        'id' => '00750444-c29e-43ab-ab8e-0ef800d8bdeb',
                        'name' => '5-a-side',
                        'parent_id' => '8ea70826-d1c6-461f-97fc-b4195e08994b',
                        'order' => 0,
                        'depth' => 2,
                        'created_at' => $taxonomy['created_at'],
                        'updated_at' => $taxonomy['updated_at'],
                    ]),
                    json_encode($taxonomy)
                );
            }
        }
    }

    /**
     * @test
     */
    public function it_can_import_formatted_taxonomies_into_the_database()
    {
        $taxonomyTable = (new Taxonomy())->getTable();
        $importer = new OpenActiveTaxonomyImporter();
        $importer->getOpenActiveCategory();

        $importTaxonomies = $importer->mapOpenActiveTaxonomyImport($this->openActiveCategory, $this->openActiveTaxonomyImport);

        $importer->importTaxonomies($this->openActiveCategory, $importTaxonomies);

        $this->assertDatabaseHas($taxonomyTable, [
            'id' => 'f5d6e765-28d7-4aff-8bde-74baaa1da1dd',
            'name' => 'Football',
            'parent_id' => $this->openActiveCategory->id,
            'order' => 0,
            'depth' => 2,
        ]);
        $this->assertDatabaseHas($taxonomyTable, [
            'id' => '8ea70826-d1c6-461f-97fc-b4195e08994b',
            'name' => 'Small Sided Football',
            'parent_id' => 'f5d6e765-28d7-4aff-8bde-74baaa1da1dd',
            'order' => 0,
            'depth' => 3,
        ]);

        $this->assertDatabaseHas($taxonomyTable, [
            'id' => '00750444-c29e-43ab-ab8e-0ef800d8bdeb',
            'name' => '5-a-side',
            'parent_id' => '8ea70826-d1c6-461f-97fc-b4195e08994b',
            'order' => 0,
            'depth' => 4,
        ]);
    }
}
