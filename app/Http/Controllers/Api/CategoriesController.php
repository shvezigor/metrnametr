<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $list = Category::with(
            [
                'user',
                'catalog',
                'type',
            ]
        );

        if ($request->has('search')) {
            $search = sprintf('%%%s%%', $request->get('search'));
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
                'title' => 'required'
            ]
        );

        $record = Category::create(
            [
                'title' => $request->get('title'),
                'seo_title' => $request->get('seo_title'),
                'seo_description' => $request->get('seo_description'),
                'canonical_url' => $request->get('canonical_url'),
                'faq' => $request->get('faq'),
                'user_id' => $request->has('user_id') ? (int)$request->get('user_id') : auth()->user()->id,
                'catalog_id' => $request->get('catalog_id', null),
                'published' => (int) $request->get('published', 0),
                'type_id' => $request->get('type', null),
            ]
        );

        $record->load(
            [
                'user',
                'catalog',
                'type',
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
            $user = Category::with(
                [
                    'user',
                    'catalog',
                    'type',
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

            $record = Category::findOrFail($id);

            $record->fill(
                $request->only(
                    [
                        'title',
                        'seo_title',
                        'seo_description',
                        'canonical_url',
                        'faq',
                        'published',
                        'user_id',
                        'catalog_id',
                        'type_id',
                    ]
                )
            );

            $record->save();

            $record->load(
                [
                    'user',
                    'catalog',
                    'type',
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
            $response = Category::findOrFail($id)->delete();

        } catch (Exception $e) {

            $response = $e->getMessage();
            $statusCode = 400;

        } finally {

            return response()->json($response, $statusCode);
        }
    }

    public function list()
    {
        return Category::pluck('title', 'id');
    }

    public function listOfArray()
    {
        return Category::select('id', 'title')->get();
    }
}
