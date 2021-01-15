<?php

namespace App\Http\Controllers;

use App\Cart;
use App\Http\Requests\ProductCart\StoreRequest;
use App\Http\Requests\ProductCart\UpdateRequest;
use App\Product;
use App\ProductCart;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ProductCartController extends Controller
{
    public function index()
    {
        //
    }

    public function store(StoreRequest $request)
    {
        $id = auth()->user()->id;

        try {
            DB::beginTransaction();

            $cart = Cart::firstOrCreate([
                'user_id' => $id,
                'is_closed' => false
            ]);

            $product = Product::firstWhere('id', $request->product_id);

            $product_cart = ProductCart::firstOrNew([
                'cart_id' => $cart->id,
                'product_id' => $request->product_id
            ]);

            if ($product_cart->exists) {
                $cart->decrement('total_price', $product_cart->price * $product_cart->amount);
            }

            $product_cart->fill(array_merge($request->except('cart_id'), [
                'unit_of_measure' => $product->unit_of_measure,
                'price' => $product->price,
            ]));
            $product_cart->save();

            DB::commit();

            return response()->json($product_cart, 201);
        } catch (\Exception $err) {
            DB::rollback();
            return response()->json([
                'message' => $err->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        //
    }


    public function update(UpdateRequest $request, ProductCart $productCart)
    {
        $productCart->amount = $request->input('amount');
        $productCart->save();

        return response()->json($productCart, 200);
    }

    public function destroy(ProductCart $productCart)
    {
        if (Gate::denies('manage-product-cart', $productCart)) {
            return response()->json([
                'message' => 'Você não tem autorização a este recurso',
            ], 401);
        }

        $productCart->delete();

        return response()->json(null, 204);
    }
}
