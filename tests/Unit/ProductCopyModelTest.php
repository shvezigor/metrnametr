<?php

namespace Tests\Unit;

use App\Models\Product;
use Tests\TestCase;

class ProductCopyModelTest extends TestCase
{
    public function testDirtyValuesAreNormalizedBeforeSaving()
    {
        $product = new Product();
        $product->title = 'Двері вхідіні Престиж';
        $product->text = '<p>Накладки МДФ с двох сторін</p>';
        $product->seo_description = 'В комплект входить поріг';

        $this->assertSame('Двері вхідні Престиж', $product->getAttributes()['title']);
        $this->assertSame('<p>Накладки МДФ з двох сторін</p>', $product->getAttributes()['text']);
        $this->assertSame('До комплекту входить поріг', $product->getAttributes()['seo_description']);
    }

    public function testLegacyRawValuesAreNormalizedWhenRead()
    {
        $product = new Product();
        $product->setRawAttributes([
            'title' => 'Двері вхідін Оберіг',
            'description' => 'Двері Підїздні',
        ], true);

        $this->assertSame('Двері вхідні Оберіг', $product->title);
        $this->assertSame('Двері Під’їздні', $product->description);
    }
}
