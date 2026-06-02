<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $list = Product::with(
            [
                'user',
                'categories',
                'images',
                'sizes',
            ]
        );

        if ($request->has('text')) {
            $search = sprintf('%%%s%%', $request->get('text'));
            $list
                ->where('title', 'LIKE', $search);
        }

        if ($request->has('field')) {
            $list->orderBy($request->get('field', 'id'), $request->get('order', 'DESC'));
        }

        return $list
            ->paginate(10);
    }

    public function labels() {
        return Product::getLabels();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate(
            $request,
            [
                'title' => 'required',
                'alias' => 'required|unique:products',
                'price' => 'numeric|nullable',
            ]
        );

        $record = Product::create(
            [
                'title' => $request->get('title'),
                'alias' => $request->has('alias') ? $request->get('alias') : Str::slug($request->get('title')),
                'price' => $request->get('price', null),
                'text' => $request->get('text'),
                'description' => $request->get('description'),
                'keywords' => $request->get('keywords'),
                'user_id' => $request->has('user_id') ? $request->get('user_id') : auth()->user()->id,
                'label' => $request->get('label', 0),
                'size' => $request->get('size'),
                'published' => (int) $request->get('published', 0),
                'slider' => (int) $request->get('slider', 0),
            ]
        );

        if ($request->has('categories')) {
            $categories = $request->get('categories');
            if (is_array($categories)) {
                $record->categories()->attach($categories);
            }
        }

        if ($request->has('sizes')) {
            $sizes = $request->get('sizes');
            if (is_array($sizes)) {
                $record->sizes()->attach($sizes);
            }
        }

        if ($request->hasFile('images')) {
            $files = $request->file('images');
            foreach($files as $image) {

                $extension = $image->getClientOriginalExtension();
                $filename = $record->id.'-'.Str::random(12) . '.' . $extension;

                $storage = Storage::disk('products');

                $fImage = Image::make($image->getRealPath())->resize(400, 800, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

                $storage->put($filename, (string)$fImage->encode(), 'public');
                $url = $storage->url($filename);

                $record->images()->create([
                    'location' => $url
                ]);
            }
        }

        $record->load(
            [
                'user',
                'categories',
                'images',
                'sizes',
            ]
        );

        return $record;
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $statusCode = 200;
            $user = Product::with(
                [
                    'user',
                    'categories',
                    'images',
                    'sizes',
                ]
            )->findOrFail($id);

            $response = $user->toArray();
        } catch (Exception $e) {
            $statusCode = 400;
            $response = ['error' => $e->getMessage()];
        } finally {
            return response()->json($response, $statusCode);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $statusCode = 200;

            $record = Product::findOrFail($id);

            $record->fill(
                $request->only(
                    [
                        'title',
                        'alias',
                        'price',
                        'text',
                        'description',
                        'keywords',
                        'label',
                        'published',
                        'slider',
                    ]
                )
            );

            if ($request->has('user_id')) {
                $record->user_id = $request->get('user_id');
            }

            $record->save();

            if ($request->has('categories')) {
                $categories = $request->get('categories');
                if (is_array($categories)) {
                    $record->categories()->sync($categories);
                }
            }

            if ($request->has('sizes')) {
                $sizes = $request->get('sizes');
                if (is_array($sizes)) {
                    $record->sizes()->sync($sizes);
                }
            }

            $record->load(
                [
                    'user',
                    'categories',
                    'images',
                    'sizes',
                ]
            );

            $response = $record->toArray();
        } catch (Exception $e) {
            $statusCode = 400;
            $response = $e->getMessage();
        } finally {
            return response()->json($response, $statusCode);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {

            $statusCode = 200;
            $product = Product::findOrFail($id);

            $listOfImages = $product->images()->pluck('location');

            $response = Product::findOrFail($id)->delete();

            if (count($listOfImages) > 0) {
                $storage = Storage::disk('products');

                foreach ($listOfImages as $image) {
                    $filename = basename($image);
                    if ($storage->exists($filename)) {
                        $storage->delete($filename);
                    }
                }
            }

        } catch (Exception $e) {

            $response = $e->getMessage();
            $statusCode = 400;

        } finally {

            return response()->json($response, $statusCode);
        }
    }

    public function upload(Request $request, $id) {

        $record = Product::findOrFail($id);

        $this->validate($request, [
            'images.*' => 'image|mimes:jpeg,png',
        ]);

        if ($request->hasFile('images')) {
            $files = $request->file('images');
            foreach($files as $image) {

                $extension = $image->getClientOriginalExtension();
                $filename = Str::random(12) . '.' . $extension;

                $storage = Storage::disk('products');

                $fImage = Image::make($image->getRealPath())->resize(400, 800, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

                $storage->put($filename, (string)$fImage->encode(), 'public');
                $url = $storage->url($filename);

                $record->images()->create([
                    'location' => $url
                ]);
            }
        }

        $record->load([
            'user',
            'categories',
            'images',
        ]);

        return $record;
    }
}
