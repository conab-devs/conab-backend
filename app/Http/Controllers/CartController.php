<?php

namespace App\Http\Controllers;

use App\Cart;
use App\Order;

class CartController extends Controller
{
    /**
     * @OA\Get(
     *     path="/carts/{id}",
     *     operationId="show",
     *     summary="Retorna um carrinho pelo ID",
     *     tags={"Carrinhos"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         description="Id do Carrinho",
     *         required=true,
     *         in="path",
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 allOf={
     *                     @OA\Schema(ref="#/components/schemas/Cart"),
     *                     @OA\Schema(
     *                         @OA\Property(
     *                             property="product_carts",
     *                             type="array",
     *                             @OA\Items(ref="#/components/schemas/ProductCart")
     *                         )
     *                     )
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=500, description="Server Error")
     * )
     */
    public function show($id)
    {
        $user = auth()->user();

        $cart = $user->hasManyThrough(Cart::class, Order::class)
            ->where('carts.id', $id)
            ->with('product_carts.product')
            ->firstOrFail();

        return response()->json($cart);
    }
}
