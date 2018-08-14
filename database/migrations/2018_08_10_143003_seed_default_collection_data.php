<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SeedDefaultCollectionData extends Migration
{
    /**
     * @var \Illuminate\Support\Carbon
     */
    protected $now;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->now = now();
        $this->categoryTaxonomy = DB::table('taxonomies')
            ->whereNull('parent_id')
            ->where('name', 'Category')
            ->first();

        $this->seedCategoryCollections();
        $this->seedPersonaCollections();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('collection_taxonomies')->truncate();
        DB::table('collections')->truncate();
    }

    /**
     * Seed the category collections.
     */
    protected function seedCategoryCollections()
    {
        $this->seedLeisureCategory();
        $this->seedSelfHelpCategory();
        $this->seedAdviceCategory();
    }

    /**
     * Seed the Leisure and Social Activities category.
     */
    protected function seedLeisureCategory()
    {
        $uuid = uuid();
        DB::table('collections')->insert([
            'id' => $uuid,
            'type' => 'category',
            'name' => 'Leisure and Social Activities',
            'meta' => json_encode([
                'intro' => 'Lorem ipsum',
                'icon' => 'coffee',
            ]),
            'order' => 1,
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);

        $this->linkToCategoryTaxonomy($uuid, ['Care', 'Support Network', 'Support Groups']);
        $this->linkToCategoryTaxonomy($uuid, ['Care', 'Support Network', 'One-on-One Support']);
        $this->linkToCategoryTaxonomy($uuid, ['Education', 'Skills & Training', 'Daily Life Skills']);
        $this->linkToCategoryTaxonomy($uuid, ['Education', 'More Education', 'Health Education', 'Nutrition Education']);
        $this->linkToCategoryTaxonomy($uuid, ['Food', 'Community Gardens']);
    }

    /**
     * Seed the Self Help category.
     */
    protected function seedSelfHelpCategory()
    {
        $uuid = uuid();
        DB::table('collections')->insert([
            'id' => $uuid,
            'type' => 'category',
            'name' => 'Self Help',
            'meta' => json_encode([
                'intro' => 'Lorem ipsum',
                'icon' => 'life-ring',
            ]),
            'order' => 2,
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);

        $this->linkToCategoryTaxonomy($uuid, ['Care', 'Support Network', 'Counseling']);
        $this->linkToCategoryTaxonomy($uuid, ['Education', 'Skills & Training', 'Resume Development']);
        $this->linkToCategoryTaxonomy($uuid, ['Education', 'Skills & Training', 'Computer Class']);
        $this->linkToCategoryTaxonomy($uuid, ['Education', 'Skills & Training', 'Basic Literacy']);
        $this->linkToCategoryTaxonomy($uuid, ['Education', 'Skills & Training', 'Daily Life Skills']);
        $this->linkToCategoryTaxonomy($uuid, ['Education', 'Skills & Training', 'Specialized Training']);
        $this->linkToCategoryTaxonomy($uuid, ['Education', 'Skills & Training', 'Skills Assessment']);
        $this->linkToCategoryTaxonomy($uuid, ['Education', 'Skills & Training', 'Interview Training']);
        $this->linkToCategoryTaxonomy($uuid, ['Education', 'More Education', 'Financial Education', 'Credit Counseling']);
        $this->linkToCategoryTaxonomy($uuid, ['Education', 'More Education', 'Financial Education', 'Homebuyer Education']);
    }

    /**
     * Seed the Advice and Support Services category.
     */
    protected function seedAdviceCategory()
    {
        $uuid = uuid();
        DB::table('collections')->insert([
            'id' => $uuid,
            'type' => 'category',
            'name' => 'Advice and Support Services',
            'meta' => json_encode([
                'intro' => 'Lorem ipsum',
                'icon' => 'info-circle',
            ]),
            'order' => 3,
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);

        $this->linkToCategoryTaxonomy($uuid, ['Care', 'Support Network', 'Support Groups', 'Bereavement']);
        $this->linkToCategoryTaxonomy($uuid, ['Care', 'Support Network', 'Support Groups', 'Parenting Education']);
        $this->linkToCategoryTaxonomy($uuid, ['Care', 'Support Network', 'Counseling']);
        $this->linkToCategoryTaxonomy($uuid, ['Care', 'Support Network', 'Spiritual Support']);
        $this->linkToCategoryTaxonomy($uuid, ['Care', 'Support Network', 'Help Hotlines']);
        $this->linkToCategoryTaxonomy($uuid, ['Care', 'Support Network', 'One-on-One Support']);
        $this->linkToCategoryTaxonomy($uuid, ['Care', 'Support Network', 'Peer Support']);
        $this->linkToCategoryTaxonomy($uuid, ['Care', 'Navigating the System', 'Help Find Childcare']);
        $this->linkToCategoryTaxonomy($uuid, ['Care', 'Navigating the System', 'Help Fill out Forms']);
        $this->linkToCategoryTaxonomy($uuid, ['Care', 'Navigating the System', 'Help Find Work']);
        $this->linkToCategoryTaxonomy($uuid, ['Care', 'Navigating the System', 'Help Find School']);
        $this->linkToCategoryTaxonomy($uuid, ['Care', 'Navigating the System', 'Help Find Housing']);
        $this->linkToCategoryTaxonomy($uuid, ['Education']);
        $this->linkToCategoryTaxonomy($uuid, ['Work', 'Help Find Work']);
        $this->linkToCategoryTaxonomy($uuid, ['Work', 'Help Pay for Work Expenses']);
        $this->linkToCategoryTaxonomy($uuid, ['Work', 'Workplace Rights']);
        $this->linkToCategoryTaxonomy($uuid, ['Emergency']);
        $this->linkToCategoryTaxonomy($uuid, ['Legal', 'Advocacy & Legal Aid']);
        $this->linkToCategoryTaxonomy($uuid, ['Health', 'Health Education']);
        $this->linkToCategoryTaxonomy($uuid, ['Housing', 'Help Find Housing']);
        $this->linkToCategoryTaxonomy($uuid, ['Money', 'Financial Assistance']);
    }

    /**
     * Seed the persona collections.
     */
    protected function seedPersonaCollections()
    {
        $this->seedHomelessPersona();
        $this->seedRefugeesPersona();
        $this->seedSocialIsolationPersona();
    }

    /**
     * Seed the Homeless persona.
     */
    protected function seedHomelessPersona()
    {
        $uuid = uuid();
        DB::table('collections')->insert([
            'id' => $uuid,
            'type' => 'persona',
            'name' => 'Homeless',
            'meta' => json_encode([
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Or at risk of homelessness',
                'image_file_id' => null,
            ]),
            'order' => 1,
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);

        $this->linkToCategoryTaxonomy($uuid, ['Housing', 'Help Find Housing']);
        $this->linkToCategoryTaxonomy($uuid, ['Housing', 'Emergency Shelter']);
        $this->linkToCategoryTaxonomy($uuid, ['Housing', 'Help Pay for Housing']);
        $this->linkToCategoryTaxonomy($uuid, ['Money', 'Financial Assistance', 'Help Pay for Housing']);
    }

    /**
     * Seed the Refugees persona.
     */
    protected function seedRefugeesPersona()
    {
        $uuid = uuid();
        DB::table('collections')->insert([
            'id' => $uuid,
            'type' => 'persona',
            'name' => 'Refugees',
            'meta' => json_encode([
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Lorem ipsum',
                'image_file_id' => null,
            ]),
            'order' => 2,
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);

        $this->linkToCategoryTaxonomy($uuid, ['Care', 'Navigating the System', 'Help Find Childcare']);
        $this->linkToCategoryTaxonomy($uuid, ['Care', 'Navigating the System', 'Help Fill out Forms']);
        $this->linkToCategoryTaxonomy($uuid, ['Care', 'Navigating the System', 'Help Find Work']);
        $this->linkToCategoryTaxonomy($uuid, ['Care', 'Navigating the System', 'Help Find School']);
        $this->linkToCategoryTaxonomy($uuid, ['Care', 'Navigating the System', 'Help Find Housing']);
        $this->linkToCategoryTaxonomy($uuid, ['Education', 'More Education', 'English as a Second Language (ESL)']);
        $this->linkToCategoryTaxonomy($uuid, ['Education', 'Screening & Exams', 'English as a Second Language (ESL)']);
        $this->linkToCategoryTaxonomy($uuid, ['Education', 'Screening & Exams', 'Citizenship & Immigration']);
        $this->linkToCategoryTaxonomy($uuid, ['Work', 'Help Find Work', 'Job Placement']);
        $this->linkToCategoryTaxonomy($uuid, ['Legal', 'Advocacy & Legal Aid', 'Citizenship & Immigration']);
        $this->linkToCategoryTaxonomy($uuid, ['Housing', 'Help Find Housing']);
    }

    /**
     * Seed the Social Isolation persona.
     */
    protected function seedSocialIsolationPersona()
    {
        $uuid = uuid();
        DB::table('collections')->insert([
            'id' => $uuid,
            'type' => 'persona',
            'name' => 'Social Isolation',
            'meta' => json_encode([
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Lorem ipsum',
                'image_file_id' => null,
            ]),
            'order' => 3,
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);

        $this->linkToCategoryTaxonomy($uuid, ['Care', 'Support Network', 'Support Groups']);
        $this->linkToCategoryTaxonomy($uuid, ['Care', 'Support Network', 'Peer Support']);
    }

    /**
     * @param string $collectionId
     * @param array $taxonomyPath
     * @param string|null $parentId
     */
    protected function linkToCategoryTaxonomy(string $collectionId, array $taxonomyPath, string $parentId = null)
    {
        // If the array is empty, then finish the recursion.
        if (count($taxonomyPath) === 0) {
            return;
        }

        // Take and remove the first element of the taxonomy path, as this is all we care about.
        $taxonomyName = array_shift($taxonomyPath);

        // If the $parentId is null, then it's a top level taxonomy (under the Category taxonomy).
        if ($parentId === null) {
            $parentId = $this->categoryTaxonomy->id;
        }

        // Get the ID of the taxonomy by its name.
        $taxonomy = DB::table('taxonomies')
            ->where('parent_id', $parentId)
            ->where('name', $taxonomyName)
            ->first();

        // Check if a record has already been created (such as two multiple taxonomies under the same parent).
        $alreadyExists = DB::table('collection_taxonomies')
            ->where('collection_id', $collectionId)
            ->where('taxonomy_id', $taxonomy->id)
            ->exists();

        // Create the pivot record.
        if (!$alreadyExists) {
            DB::table('collection_taxonomies')->insert([
                'id' => uuid(),
                'collection_id' => $collectionId,
                'taxonomy_id' => $taxonomy->id,
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ]);
        }

        // Use recursion for the remaining array items.
        $this->linkToCategoryTaxonomy($collectionId, $taxonomyPath, $taxonomy->id);
    }
}
