<?php

namespace App\Http\Controllers;

use App\Models\Catalog;
use App\Models\Product;
use App\Models\Category;
use App\Models\Size;
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

        return view('client.products.index')
            ->with('list', $listOfProducts)
            ->with('catalogForFilter', $catalog)
            ->with('categoriesForFilter', $categories)
            ->with('sizesForFilter', $sizes)
            ->with('params', $request->all())
            ->with('breadcrumbs', [
                route('catalog') => 'Каталог',
            ])
            ->with('title', 'Продукти')
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
            ->with('description', $product->description)
            ->with('keywords', $product->keywords)
            ->with('list', $list)
            ->with('breadcrumbs', [
                route('catalog') => 'Каталог',
                route('product.show', ['alias' => $product->alias]) => $product->title,
            ])
            ->with('title', $product->title);
    }
}
