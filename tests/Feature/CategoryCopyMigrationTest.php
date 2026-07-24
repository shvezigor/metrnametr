<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CategoryCopyMigrationTest extends TestCase
{
    use DatabaseTransactions;

    public function testMigrationPersistsReviewedCategoryCorrectionsOnly()
    {
        $category = DB::table('categories')->where('published', 1)->first();
        $catalogId = $category->catalog_id;
        $typeId = $category->type_id;

        DB::table('categories')->where('id', $category->id)->update([
            'title' => 'Підїздні двері',
            'seo_title' => 'Підїздні двері у Луцьку',
            'seo_description' => 'Підїздні двері для будинку',
        ]);

        require_once database_path('migrations/2026_07_24_115100_normalize_category_copy.php');
        (new \NormalizeCategoryCopy())->up();

        $stored = DB::table('categories')->where('id', $category->id)->first();
        $this->assertSame('Під’їзні двері', $stored->title);
        $this->assertSame('Під’їзні двері у Луцьку', $stored->seo_title);
        $this->assertSame('Під’їзні двері для будинку', $stored->seo_description);
        $this->assertSame($catalogId, $stored->catalog_id);
        $this->assertSame($typeId, $stored->type_id);
    }
}
