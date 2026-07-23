<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Product;
use App\Models\Type;
use App\Support\CommercialLanding;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Tests\TestCase;

class CommercialLandingTest extends TestCase
{
    public function testCardUsesConfirmedValuesAndOmitsMissingSpecifications()
    {
        $product = $this->product([
            'id' => 41,
            'title' => 'Тестові двері',
            'alias' => 'test-door',
            'price' => 0,
            'extra_fields' => [
                'locks' => 'два підтверджені замки',
                'finish' => '',
                'availability' => '',
            ],
        ], 'Вхідні двері', 'Для квартири');

        $card = CommercialLanding::card($product);

        $this->assertSame('Тестові двері', $card['title']);
        $this->assertSame('Ціну уточнюйте після підбору комплектації', $card['price']);
        $this->assertSame(['locks' => 'два підтверджені замки'], $card['specifications']);
        $this->assertNull($card['availability']);
        $this->assertSame('/product/test-door', parse_url($card['url'], PHP_URL_PATH));
    }

    public function testCardFormatsOnlyAConfirmedPositivePrice()
    {
        $product = $this->product([
            'id' => 42,
            'title' => 'Модель із ціною',
            'alias' => 'priced-door',
            'price' => 24500,
        ], 'Вхідні двері', 'Металеві');

        $this->assertSame('24 500 грн', CommercialLanding::card($product)['price']);
    }

    public function testFilterReturnsUniquePublishedMatchingProductsInDeterministicOrder()
    {
        $preferred = $this->product([
            'id' => 1,
            'title' => 'Перша',
            'alias' => 'first',
            'published' => 1,
            'label' => Product::LABEL_TOP_SALE,
            'updated_at' => Carbon::parse('2026-07-20'),
        ], 'Вхідні двері', 'Для квартири');

        $newer = $this->product([
            'id' => 2,
            'title' => 'Друга',
            'alias' => 'second',
            'published' => 1,
            'label' => Product::LABEL_EMPTY,
            'updated_at' => Carbon::parse('2026-07-22'),
        ], 'Вхідні двері', 'Для квартири');

        $unpublished = $this->product([
            'id' => 3,
            'title' => 'Неопублікована',
            'alias' => 'hidden',
            'published' => 0,
        ], 'Вхідні двері', 'Для квартири');

        $unrelated = $this->product([
            'id' => 4,
            'title' => 'Інша категорія',
            'alias' => 'unrelated',
            'published' => 1,
        ], 'Міжкімнатні двері', 'Фарбовані');

        $filtered = CommercialLanding::filter(
            new Collection([$newer, $preferred, $preferred, $unpublished, $unrelated]),
            ['type_terms' => ['вхідні'], 'category_terms' => ['квартир']],
            12
        );

        $this->assertSame([1, 2], $filtered->pluck('id')->all());
    }

    public function testFilterDoesNotUseUnrelatedProductsAsFallback()
    {
        $product = $this->product([
            'id' => 9,
            'title' => 'Міжкімнатні',
            'alias' => 'interior',
            'published' => 1,
        ], 'Міжкімнатні двері', 'Шпоновані');

        $filtered = CommercialLanding::filter(
            collect([$product]),
            ['type_terms' => ['вхідні'], 'category_terms' => ['броньован']],
            12
        );

        $this->assertTrue($filtered->isEmpty());
    }

    private function product(array $attributes, string $typeTitle, string $categoryTitle): Product
    {
        $product = new Product(array_merge([
            'price' => null,
            'published' => 1,
            'label' => Product::LABEL_EMPTY,
            'extra_fields' => [],
        ], $attributes));

        if (isset($attributes['id'])) {
            $product->id = $attributes['id'];
        }

        if (isset($attributes['updated_at'])) {
            $product->updated_at = $attributes['updated_at'];
        }

        $type = new Type(['title' => $typeTitle]);
        $category = new Category(['title' => $categoryTitle]);
        $category->setRelation('type', $type);
        $product->setRelation('categories', collect([$category]));
        $product->setRelation('images', collect());

        return $product;
    }
}
