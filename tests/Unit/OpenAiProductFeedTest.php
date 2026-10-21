<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Support\OpenAiProductFeed;
use Tests\TestCase;

class OpenAiProductFeedTest extends TestCase
{
    public function testProductRowContainsRequiredOpenAiAdsFields()
    {
        $product = new Product([
            'title' => 'Двері вхідні Тест',
            'alias' => 'test-door',
            'description' => '<p>Надійні двері для квартири.</p>',
            'price' => 24500,
            'extra_fields' => [
                'brand' => 'Метр на Метр',
                'availability' => 'InStock',
            ],
        ]);
        $product->id = 41;
        $product->setRelation('images', collect());

        $this->assertSame([
            'item_id' => 'metrnametr-41',
            'title' => 'Двері вхідні Тест',
            'description' => 'Надійні двері для квартири.',
            'url' => 'https://metrnametr.com.ua/product/test-door?utm_source=chatgpt&utm_medium=product_feed&utm_campaign=doors_catalog',
            'brand' => 'Метр на Метр',
            'image_url' => 'https://metrnametr.com.ua/images/placeholder-product.png',
            'price' => '24500.00 UAH',
            'availability' => 'in_stock',
            'seller_name' => 'Метр на Метр',
            'seller_url' => 'https://metrnametr.com.ua',
            'target_countries' => 'UA',
            'store_country' => 'UA',
            'is_eligible_search' => 'true',
            'is_eligible_checkout' => 'false',
            'is_ads_eligible' => 'true',
        ], OpenAiProductFeed::row($product));
    }

    public function testProductRowUsesSafeFallbacksForMissingOptionalCatalogData()
    {
        $product = new Product([
            'title' => 'Міжкімнатні двері Лайн',
            'alias' => 'line',
            'description' => '',
            'text' => '',
            'price' => 4750,
            'extra_fields' => [],
        ]);
        $product->id = 52;
        $product->setRelation('images', collect());

        $row = OpenAiProductFeed::row($product);

        $this->assertSame('Модель дверей «Міжкімнатні двері Лайн» у каталозі Метр на Метр.', $row['description']);
        $this->assertSame('Метр на Метр', $row['brand']);
        $this->assertSame('unknown', $row['availability']);
    }

    public function testCsvEscapesUkrainianProductDataAndUsesOfficialHeaderOrder()
    {
        $product = new Product([
            'title' => 'Двері «Тест», білі',
            'alias' => 'white-test',
            'description' => 'Опис, із комою',
            'price' => 10000,
            'extra_fields' => ['availability' => 'в наявності'],
        ]);
        $product->id = 7;
        $product->setRelation('images', collect());

        $csv = OpenAiProductFeed::csv(collect([$product]));
        $lines = preg_split('/\r\n|\r|\n/', trim($csv));

        $this->assertSame(
            'item_id,title,description,url,brand,image_url,price,availability,seller_name,seller_url,target_countries,store_country,is_eligible_search,is_eligible_checkout,is_ads_eligible',
            $lines[0]
        );
        $this->assertStringContainsString('"Двері «Тест», білі"', $lines[1]);
        $this->assertStringContainsString('"Опис, із комою"', $lines[1]);
        $this->assertStringContainsString(',in_stock,', $lines[1]);
    }

    public function testDescriptionPreservesWordBoundariesBetweenHtmlBlocks()
    {
        $product = new Product([
            'title' => 'Тестові двері',
            'alias' => 'html-description',
            'description' => '<p>Перша характеристика.</p><p>Друга характеристика.</p>',
            'price' => 12000,
            'extra_fields' => [],
        ]);
        $product->id = 8;
        $product->setRelation('images', collect());

        $this->assertSame(
            'Перша характеристика. Друга характеристика.',
            OpenAiProductFeed::row($product)['description']
        );
    }
}
