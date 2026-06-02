<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $list = Order::with(
            [
                'product',
            ]
        );

        if ($request->has('text')) {
            $search = sprintf('%%%s%%', $request->get('text'));
            $list
                ->where('name', 'LIKE', $search)
                ->orWhere('phone', 'LIKE', $search);
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
                'phone' => 'required',
            ]
        );

        $record = Order::create(
            [
                'name' => $request->get('name'),
                'phone' => $request->get('phone'),
            ]
        );

        if ($request->has('product')) {
            $record->update([
                'product_id' => $request->get('product'),
            ]);
        }

        $record->load(
            [
                'product',
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
            $response = Order::findOrFail($id)->delete();

        } catch (Exception $e) {

            $response = $e->getMessage();
            $statusCode = 400;

        } finally {

            return response()->json($response, $statusCode);
        }
    }
}
