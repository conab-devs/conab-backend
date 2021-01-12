<?php

namespace App\Http\Controllers;

use App\Cart;
use App\Http\Requests\ProductCart\StoreRequest;
use App\ProductCart;
use Illuminate\Http\Request;

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

            $product_cart = new ProductCart();
            $product_cart->fill($request->all());
            $product_cart->cart_id = $cart->id;
            $product_cart->save();

            DB::commit();

            return response()->json($product_cart, 201);
        } catch (\Exception $err) {
            DB::rollback();
            return response()->json([
                'message' => 'Algo deu errado, tente novamente em alguns instantes'
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
