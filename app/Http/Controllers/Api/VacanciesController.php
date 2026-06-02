<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vacancy;
use Illuminate\Support\Str;

class VacanciesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $list = Vacancy::with(
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
                'alias' => 'required|unique:vacancies',
            ]
        );

        $record = Vacancy::create(
            [
                'title' => $request->get('title'),
                'alias' => $request->has('alias') ? $request->get('alias') : Str::slug($request->get('title')),
                'salary' => $request->get('salary'),
                'text' => $request->get('text'),
                'contacts' => $request->get('contacts'),
                'user_id' => $request->has('user_id') ? $request->get('user_id') : auth()->user()->id,
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
            $user = Vacancy::with(
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

            $record = Vacancy::findOrFail($id);

            $record->fill(
                $request->only(
                    [
                        'title',
                        'alias',
                        'salary',
                        'text',
                        'contacts',
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
            $response = Vacancy::findOrFail($id)->delete();

        } catch (Exception $e) {

            $response = $e->getMessage();
            $statusCode = 400;

        } finally {

            return response()->json($response, $statusCode);
        }
    }
}
