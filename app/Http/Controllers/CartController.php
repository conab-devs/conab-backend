<?php

namespace App\Http\Controllers;

use App\Cart;

class CartController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return response()->json($user->carts);
    }

    public function show($id)
    {
        $user = auth()->user();

        $cart = Cart::with('product_carts.product')->where([
            'user_id' => $user->id,
            'id' => $id
        ])->firstOrFail();

        return response()->json($cart);
    }
}
