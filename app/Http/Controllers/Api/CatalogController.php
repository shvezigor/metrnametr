<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Catalog;
use Illuminate\Support\Str;

class CatalogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $list = Catalog::with(
            [
                'user',
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
                'title' => 'required',
                'alias' => 'required|unique:catalog',
            ]
        );

        $record = Catalog::create(
            [
                'title' => $request->get('title'),
                'alias' => $request->has('alias') ? $request->get('alias') : Str::slug($request->get('title')),
                'user_id' => $request->has('user_id') ? (int)$request->get('user_id') : auth()->user()->id,
                'published' => (int) $request->get('published', 0),
            ]
        );

        $record->load(
            [
                'user',
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
            $user = Catalog::with(
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

            $record = Catalog::findOrFail($id);

            $record->fill(
                $request->only(
                    [
                        'title',
                        'alias',
                        'published',
                    ]
                )
            );

            if ($request->has('user_id')) {
                $record->user_id = $request->get('user_id');
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
            $response = Catalog::findOrFail($id)->delete();

        } catch (Exception $e) {

            $response = $e->getMessage();
            $statusCode = 400;

        } finally {

            return response()->json($response, $statusCode);
        }
    }

    public function list()
    {
        return Catalog::pluck('title', 'id');
    }

    public function listOfArray()
    {
        return Catalog::select('id', 'title')->get();
    }
}
