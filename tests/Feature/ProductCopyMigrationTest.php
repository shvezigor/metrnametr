<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProductCopyMigrationTest extends TestCase
{
    use DatabaseTransactions;

    public function testMigrationPersistsReviewedCorrectionsOnly()
    {
        $product = Product::published()->firstOrFail();

        DB::table('products')->where('id', $product->id)->update([
            'title' => 'Двері вхідіні Тест',
            'text' => '<p>Накладки МДФ с двух сторін 16мм</p>',
            'description' => 'S.A.P., МД 068, 86*203',
        ]);

        require_once database_path('migrations/2026_07_24_090000_normalize_ukrainian_product_copy.php');
        (new \NormalizeUkrainianProductCopy())->up();

        $stored = DB::table('products')->where('id', $product->id)->first();
        $this->assertSame('Двері вхідні Тест', $stored->title);
        $this->assertSame('<p>Накладки МДФ з двох сторін 16мм</p>', $stored->text);
        $this->assertSame('S.A.P., МД 068, 86*203', $stored->description);
    }

    public function testFollowUpMigrationPersistsRemainingMixedLanguageCorrectionsOnly()
    {
        $product = Product::published()->firstOrFail();

        DB::table('products')->where('id', $product->id)->update([
            'title' => 'Накладки МДФ с двох сторін',
            'text' => '<p>Накладки МДФ С двох сторін</p>',
            'description' => 'Накладки МДФ с двох сторін',
            'seo_title' => 'Накладки МДФ С двох сторін',
            'seo_description' => 'Накладки МДФ с двох сторін',
            'alias' => 'copy-migration-alias',
            'price' => 12345,
        ]);

        require_once database_path('migrations/2026_07_24_114800_normalize_remaining_mixed_language_product_copy.php');
        (new \NormalizeRemainingMixedLanguageProductCopy())->up();

        $stored = DB::table('products')->where('id', $product->id)->first();
        $this->assertSame('Накладки МДФ з двох сторін', $stored->title);
        $this->assertSame('<p>Накладки МДФ З двох сторін</p>', $stored->text);
        $this->assertSame('Накладки МДФ з двох сторін', $stored->description);
        $this->assertSame('Накладки МДФ З двох сторін', $stored->seo_title);
        $this->assertSame('Накладки МДФ з двох сторін', $stored->seo_description);
        $this->assertSame('copy-migration-alias', $stored->alias);
        $this->assertSame(12345.0, $stored->price);
    }
}
