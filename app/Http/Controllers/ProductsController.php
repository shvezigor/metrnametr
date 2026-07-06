<?php

namespace App\Http\Controllers;

use App\Models\Catalog;
use App\Models\Product;
use App\Models\Category;
use App\Models\Size;
use App\Support\SeoContent;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index(Request $request) {

        $list = Product::published();

        if ($request->has('catalog')) {
            $catalogID = $request->get('catalog', null);

            if ($catalogID) {
                $list->whereHas('categories', function ($q) use ($catalogID) {
                    $q->whereHas('catalog', function ($q) use ($catalogID) {
                        $q->where('id', $catalogID);
                    });
                });
            }
        }

        if ($request->has('categories')) {
            $categories = $request->get('categories', []);

            if (is_array($categories) && count($categories) > 0) {
                $list->whereHas('categories', function ($q) use ($categories) {
                    $q->whereIn('id', $categories);
                });
            }
        }

        $selectedCategory = null;
        $selectedCategories = $request->get('categories', []);
        if (is_array($selectedCategories) && count($selectedCategories) === 1) {
            $selectedCategory = Category::published()->find($selectedCategories[0]);
        }

        if ($request->has('sizes')) {
            $sizes = $request->get('sizes', []);

            if (is_array($sizes) && count($sizes) > 0) {
                $list->whereHas('sizes', function ($q) use ($sizes) {
                    $q->whereIn('id', $sizes);
                });
            }
        }

        if ($request->has('sizes')) {
            $sizes = $request->get('sizes');

            $list->whereHas('sizes', function($q) use ($sizes) {
                $q->whereIn('id', $sizes);
            });
        }

        $startMin = $min = $list->min('price');
        $startMax = $max = $list->max('price');

        $listOfProducts = $list;

        if ($request->has('min')) {
            $minimalPrice = $request->get('min', null);

            if ($minimalPrice) {
                $listOfProducts->where('price', '>=', $minimalPrice);
                $startMin = $minimalPrice;
            }
        }

        if ($request->has('max')) {
            $maximumPrice = $request->get('max', null);

            if ($maximumPrice) {
                $listOfProducts->where('price', '<=', $maximumPrice);
                $startMax = $maximumPrice;
            }
        }

        $listOfProducts = $listOfProducts
            ->orderBy('created_at', 'DESC')
            ->paginate(12);

        $catalog = Catalog::published()->orderBy('title', 'ASC')->get();
        $categories = Category::published()->orderBy('title', 'ASC')->get();
        $sizes = Size::orderBy('title', 'ASC')->get();

        $catalogTitle = $selectedCategory
            ? SeoContent::titleFor($selectedCategory, $selectedCategory->title . ' — двері у Луцьку | Метр на Метр')
            : 'Вхідні двері у Луцьку — купити металеві двері | Метр на Метр';
        $catalogDescription = $selectedCategory
            ? SeoContent::descriptionFor($selectedCategory, 'Категорія ' . $selectedCategory->title . ' у каталозі Метр на Метр: підбір дверей, консультація та монтаж.')
            : 'Каталог дверей Метр на Метр: вхідні та міжкімнатні двері з підбором, консультацією і можливістю монтажу.';
        $catalogFaq = $selectedCategory && !empty(SeoContent::decodedList($selectedCategory->faq))
            ? SeoContent::decodedList($selectedCategory->faq)
            : SeoContent::faq('door_faq');

        return view('client.products.index')
            ->with('list', $listOfProducts)
            ->with('catalogForFilter', $catalog)
            ->with('categoriesForFilter', $categories)
            ->with('sizesForFilter', $sizes)
            ->with('params', $request->all())
            ->with('breadcrumbs', [
                route('catalog') => 'Каталог',
            ])
            ->with('title', $catalogTitle)
            ->with('description', $catalogDescription)
            ->with('canonical', $selectedCategory ? SeoContent::canonicalFor($selectedCategory, '/catalog?categories[]=' . $selectedCategory->id) : SeoContent::canonical('/catalog'))
            ->with('faq', $catalogFaq)
            ->with('schema', [
                SeoContent::collectionSchema($listOfProducts),
                SeoContent::breadcrumbSchema([
                    '/' => 'Головна',
                    '/catalog' => 'Каталог',
                ]),
                SeoContent::faqSchema($catalogFaq),
            ])
            ->with('min', $min)
            ->with('max', $max)
            ->with('startMin', $startMin)
            ->with('startMax', $startMax);
    }

    public function show($alias) {

        $product = Product::published()->where('alias', $alias)->first();

        if ($product === null) {
            abort(404);
        }

        $list = Product::published()->orderBy('created_at', 'DESC')->limit(8)->get();

        return view('client.products.show')
            ->with('product', $product)
            ->with('description', SeoContent::descriptionFor($product, $product->description))
            ->with('keywords', $product->keywords)
            ->with('list', $list)
            ->with('extra', SeoContent::productExtraFor($product))
            ->with('faq', SeoContent::productFaq($product))
            ->with('ogType', 'product')
            ->with('ogImage', SeoContent::ogImageFor($product, $product->cover))
            ->with('canonical', SeoContent::canonicalFor($product, $product->location))
            ->with('schema', [
                SeoContent::productSchema($product),
                SeoContent::breadcrumbSchema([
                    '/' => 'Головна',
                    '/catalog' => 'Каталог',
                    $product->location => $product->title,
                ]),
                SeoContent::faqSchema(SeoContent::productFaq($product)),
            ])
            ->with('breadcrumbs', [
                route('catalog') => 'Каталог',
                route('product.show', ['alias' => $product->alias]) => $product->title,
            ])
            ->with('title', SeoContent::titleFor($product, $product->title . ' — вхідні двері з монтажем | Метр на Метр'));
    }
}
