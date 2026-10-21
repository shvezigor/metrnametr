<?php

namespace App\Support;

use App\Models\Product;

class OpenAiProductFeed
{
    private const HEADERS = [
        'item_id',
        'title',
        'description',
        'url',
        'brand',
        'image_url',
        'price',
        'availability',
        'seller_name',
        'seller_url',
        'target_countries',
        'store_country',
        'is_eligible_search',
        'is_eligible_checkout',
        'is_ads_eligible',
    ];

    public static function products()
    {
        return Product::published()
            ->where('price', '>', 0)
            ->with('images')
            ->orderBy('id')
            ->get();
    }

    public static function row(Product $product): array
    {
        $specifications = SeoContent::productSpecifications($product);
        $description = self::plainText($product->description ?: $product->text);
        $sellerName = SeoContent::site('name', 'Метр на Метр');

        if ($description === '') {
            $description = sprintf('Модель дверей «%s» у каталозі %s.', $product->title, $sellerName);
        }

        return [
            'item_id' => 'metrnametr-' . $product->id,
            'title' => $product->title,
            'description' => $description,
            'url' => SeoContent::canonical('/product/' . $product->alias)
                . '?utm_source=chatgpt&utm_medium=product_feed&utm_campaign=doors_catalog',
            'brand' => $specifications['brand'] ?: $sellerName,
            'image_url' => self::imageUrl($product),
            'price' => number_format((float) $product->price, 2, '.', '') . ' UAH',
            'availability' => self::availability($specifications['availability']),
            'seller_name' => $sellerName,
            'seller_url' => rtrim(SeoContent::site('domain'), '/'),
            'target_countries' => 'UA',
            'store_country' => 'UA',
            'is_eligible_search' => 'true',
            'is_eligible_checkout' => 'false',
            'is_ads_eligible' => 'true',
        ];
    }

    public static function csv($products): string
    {
        $stream = fopen('php://temp', 'r+');
        fputcsv($stream, self::HEADERS);

        foreach ($products as $product) {
            fputcsv($stream, array_values(self::row($product)));
        }

        rewind($stream);
        $csv = stream_get_contents($stream);
        fclose($stream);

        return $csv;
    }

    private static function plainText($value): string
    {
        $value = preg_replace('/<[^>]+>/u', ' ', (string) $value);
        $value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');

        return trim(preg_replace('/\s+/u', ' ', $value));
    }

    private static function imageUrl(Product $product): string
    {
        if ($product->relationLoaded('images')) {
            $image = $product->images->first();

            return SeoContent::absoluteUrl($image ? $image->location : '/images/placeholder-product.png');
        }

        return SeoContent::absoluteUrl($product->cover);
    }

    private static function availability($value): string
    {
        $normalized = mb_strtolower((string) $value);
        $normalized = str_replace([' ', '-', '_', 'ʼ', "'"], '', $normalized);

        $values = [
            'instock' => 'in_stock',
            'внаявності' => 'in_stock',
            'наявності' => 'in_stock',
            'outofstock' => 'out_of_stock',
            'немаєвнаявності' => 'out_of_stock',
            'preorder' => 'pre_order',
            'передзамовлення' => 'pre_order',
            'backorder' => 'backorder',
            'підзамовлення' => 'backorder',
        ];

        return $values[$normalized] ?? 'unknown';
    }
}
