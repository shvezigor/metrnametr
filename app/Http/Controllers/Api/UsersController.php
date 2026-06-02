<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $list = User::with(
            [
                'articles',
                'categories.products',
                'vacancies',
            ]
        );

        if ($request->has('text')) {
            $search = sprintf('%%%s%%', $request->get('text'));
            $list
                ->where('name', 'LIKE', $search)
                ->orWhere('email', 'LIKE', $search);
        }

        if ($request->has('field')) {
            $list->orderBy($request->get('field', 'id'), $request->get('order', 'DESC'));
        }

        return $list
            ->paginate(10);
    }

    public function list()
    {
        return User::pluck('name', 'id');
    }

    public function listOfArray()
    {
        return User::select('id', 'name')->get();
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
                'email' => 'required|email|unique:users',
                'name' => 'required',
                'password' => 'required',
            ]
        );

        $record = User::create(
            [
                'email' => $request->get('email'),
                'name' => $request->get('name'),
                'password' => Hash::make($request->get('password'))
            ]
        );

        $record->load(
            [
                'articles',
                'categories.products',
                'vacancies'
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
            $user = User::with(
                [
                    'articles',
                    'categories.products',
                    'vacancies'
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

            $record = User::findOrFail($id);

            $record->fill(
                $request->only(
                    [
                        'name',
                        'email',
                    ]
                )
            );

            if ($request->has('password') && !empty($request->get('password'))) {
                $record->password = Hash::make($request->get('password'));
            }

            $record->save();

            $record->load(
                [
                    'articles',
                    'categories.products',
                    'vacancies'
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
            $response = User::findOrFail($id)->delete();

        } catch (Exception $e) {

            $response = $e->getMessage();
            $statusCode = 400;

        } finally {

            return response()->json($response, $statusCode);
        }
    }
}
