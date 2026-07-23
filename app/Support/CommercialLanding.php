<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Support\Collection;

class CommercialLanding
{
    public static function products(array $landing, int $limit = 12): Collection
    {
        $match = $landing['catalog_match'] ?? [];

        if (empty($match)) {
            return collect();
        }

        $products = Product::published()
            ->with(['categories.type', 'images'])
            ->get();

        return self::filter($products, $match, $limit);
    }

    public static function filter(Collection $products, array $match, int $limit = 12): Collection
    {
        return $products
            ->filter(function (Product $product) use ($match) {
                return (bool) $product->published && self::matches($product, $match);
            })
            ->unique(function (Product $product) {
                return $product->id;
            })
            ->sort(function (Product $left, Product $right) {
                $labelOrder = ((int) $right->label) <=> ((int) $left->label);

                if ($labelOrder !== 0) {
                    return $labelOrder;
                }

                $leftUpdated = $left->updated_at ? $left->updated_at->getTimestamp() : 0;
                $rightUpdated = $right->updated_at ? $right->updated_at->getTimestamp() : 0;
                $updatedOrder = $rightUpdated <=> $leftUpdated;

                return $updatedOrder !== 0
                    ? $updatedOrder
                    : ((int) $left->id <=> (int) $right->id);
            })
            ->take(max(0, $limit))
            ->values();
    }

    public static function card(Product $product): array
    {
        $specifications = SeoContent::productSpecifications($product);
        $category = $product->categories->first();
        $type = $category ? $category->type : null;

        return [
            'id' => (int) $product->id,
            'title' => $product->title,
            'image' => $product->cover,
            'image_alt' => $product->title . ' — двері з каталогу Метр на Метр',
            'type' => $type ? $type->title : ($category ? $category->title : null),
            'specifications' => array_filter([
                'leaf_thickness' => $specifications['leaf_thickness'],
                'insulation' => $specifications['insulation'],
                'locks' => $specifications['locks'],
                'finish' => $specifications['finish'],
                'thermal_break' => $specifications['thermal_break'],
            ], function ($value) {
                return $value !== null && $value !== '';
            }),
            'price' => (float) $product->price > 0
                ? number_format((float) $product->price, 0, ',', ' ') . ' грн'
                : 'Ціну уточнюйте після підбору комплектації',
            'availability' => $specifications['availability'],
            'url' => $product->location,
        ];
    }

    private static function matches(Product $product, array $match): bool
    {
        $categories = $product->categories;
        $typeText = self::normalize($categories
            ->map(function ($category) {
                return $category->type ? $category->type->title : '';
            })
            ->implode(' '));
        $categoryText = self::normalize($categories->pluck('title')->implode(' '));

        return self::matchesTerms($typeText, $match['type_terms'] ?? [])
            && self::matchesTerms($categoryText, $match['category_terms'] ?? []);
    }

    private static function matchesTerms(string $haystack, array $terms): bool
    {
        if (empty($terms)) {
            return true;
        }

        foreach ($terms as $term) {
            $term = self::normalize($term);

            if ($term !== '' && mb_strpos($haystack, $term) !== false) {
                return true;
            }
        }

        return false;
    }

    private static function normalize($value): string
    {
        return mb_strtolower(trim((string) $value), 'UTF-8');
    }
}
