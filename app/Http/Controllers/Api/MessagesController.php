<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class MessagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $list = Message::query();

        if ($request->has('text')) {
            $search = sprintf('%%%s%%', $request->get('text'));
            $list
                ->where('name', 'LIKE', $search)
                ->orWhere('email', 'LIKE', $search)
                ->orWhere('title', 'LIKE', $search)
                ->orWhere('text', 'LIKE', $search);
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
                'text' => 'required',
                'email' => 'required|email',
            ]
        );

        $record = Message::create(
            [
                'name' => $request->get('name'),
                'title' => $request->get('title'),
                'text' => $request->get('text'),
                'email' => $request->get('email'),
            ]
        );

        return $record;
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
            $response = Message::findOrFail($id)->delete();

        } catch (Exception $e) {

            $response = $e->getMessage();
            $statusCode = 400;

        } finally {

            return response()->json($response, $statusCode);
        }
    }
}
