<?php

namespace App\Http\Controllers;

use App\Cart;
use App\Http\Requests\ProductCart\StoreRequest;
use App\Product;
use App\ProductCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductCartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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

            $product_cart = new ProductCart();
            $product_cart->fill(array_merge($request->all(), [
                'cart_id' => $cart->id,
                'unit_of_measure' => $product->unit_of_measure,
                'price' => $product->price,
                'delivered_at' => date('Y-m-d H:i:s', strtotime("+$product->estimated_delivery_time day"))
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
