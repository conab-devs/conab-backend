<?php

namespace App\Http\Controllers;

use App\Cart;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();

        $orders = $user->orders()
            ->with('carts.product_carts.product')
            ->get();

        return response()->json($orders);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = auth()->user();

        $order = $user->orders()
            ->with('carts.product_carts.product')
            ->where('id', $id)
            ->firstOrFail();

        return response()->json($order);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = auth()->user();

        $order = $user->orders()->where('id', $id)->firstOrFail();
        $carts = $order->carts()
            ->Where('carts.status', Cart::STATUS_COMPLETED)
            ->orWhere('carts.status', Cart::STATUS_PENDING)
            ->exists();

        if ($carts) {
            return response()->json([
                'message' => 'Não é possível excluir pedido.'
            ], 400);
        }

        $order->delete();
        return response()->json('', 204);
    }
}
