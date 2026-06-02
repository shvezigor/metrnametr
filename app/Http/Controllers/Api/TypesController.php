<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Type;

class TypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $list = Type::with(
            [
                'categories',
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate(
            $request,
            [
                'title' => 'required|unique:types',
            ]
        );

        $record = Type::create(
            [
                'title' => $request->get('title'),
            ]
        );

        $record->load(
            [
                'categories',
            ]
        );

        return $record;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $statusCode = 200;
            $user = Type::with(
                [
                    'categories',
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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $statusCode = 200;

            $record = Type::findOrFail($id);

            $record->fill(
                $request->only(
                    [
                        'title',
                    ]
                )
            );

            $record->save();

            $record->load(
                [
                    'categories',
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $statusCode = 200;
            $response = Type::findOrFail($id)->delete();
        } catch (Exception $e) {
            $response = $e->getMessage();
            $statusCode = 400;
        } finally {
            return response()->json($response, $statusCode);
        }
    }

    public function list()
    {
        return Type::pluck('title', 'id');
    }

    public function listOfArray()
    {
        return Type::select('id', 'title')->get();
    }
}
