<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ArticlesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $list = Article::with(
            [
                'user',
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
                'title' => 'required|max:255|min:2',
                'alias' => 'required|unique:articles',
                'image' => 'required|image|mimes:jpeg,png',
            ]
        );

        $record = Article::create(
            [
                'title' => $request->get('title'),
                'alias' => $request->has('alias') ? $request->get('alias') : Str::slug($request->get('title')),
                'text' => $request->get('text'),
                'keywords' => $request->get('keywords'),
                'description' => $request->get('description'),
                'seo_title' => $request->get('seo_title'),
                'seo_description' => $request->get('seo_description'),
                'canonical_url' => $request->get('canonical_url'),
                'og_image' => $request->get('og_image'),
                'faq' => $request->get('faq'),
                'user_id' => $request->has('user_id') ? $request->get('user_id') : auth()->user()->id,
                'published' => (int) $request->get('published', 0),
            ]
        );

        if ($request->hasFile('image')) {
            $record->update(
                [
                    'cover' => $this->image($request->file('image'), $record->id),
                ]
            );
        }

        $record->load(
            [
                'user',
            ]
        );

        return $record;
    }

    private function image($image, $id) {

        $storage = Storage::disk('articles');
        $extension = $image->getClientOriginalExtension();

        $filename = sprintf('%d-%s.%s', $id, Str::random('12'), $extension);

        $fImage = Image::make($image->getRealPath())->fit(760, 500);

        $storage->put($filename, (string)$fImage->encode(), 'public');
        return $storage->url($filename);
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
            $user = Article::with(
                [
                    'user',
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

            $record = Article::findOrFail($id);

            $record->fill(
                $request->only(
                    [
                        'title',
                        'alias',
                        'text',
                        'description',
                        'keywords',
                        'seo_title',
                        'seo_description',
                        'canonical_url',
                        'og_image',
                        'faq',
                        'published',
                    ]
                )
            );

            if ($request->has('user_id')) {
                $record->user_id = $request->get('user_id');
            }

            if ($request->has('imageForRemove')) {

            }

            $record->save();

            $record->load(
                [
                    'user',
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
            $response = Article::findOrFail($id)->delete();
        } catch (Exception $e) {
            $response = $e->getMessage();
            $statusCode = 400;
        } finally {
            return response()->json($response, $statusCode);
        }
    }

    public function upload(Request $request, $id) {

        $record = Article::findOrFail($id);

        $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,png',
        ]);

        if ($request->hasFile('image')) {

            // Get previous image URL
            $url = $record->cover;

            // Start upload
            $record->update(
                [
                    'cover' => $this->image($request->file('image'), $record->id),
                ]
            );

            // Remove old image
            $filename = basename($url);
            $storage = Storage::disk('articles');

            if ($storage->exists($filename)) {
                $storage->delete($filename);
            }
        }

        $record->load(
            [
                'user',
            ]
        );

        return $record;
    }
}
