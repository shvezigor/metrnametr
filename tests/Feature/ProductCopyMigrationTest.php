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
}
