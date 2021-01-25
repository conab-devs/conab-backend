<?php

namespace App\Http\Controllers;

use App\Cart;

class CartController extends Controller
{
    /**
     * @OA\Get(
     *     path="/carts",
     *     operationId="index",
     *     summary="Retorna os carrinhos do usuário autenticado",
     *     tags={"Carrinhos"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Cart")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=500, description="Server Error")
     * )
     */
    public function index()
    {
        $user = auth()->user();

        return response()->json($user->carts);
    }

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

        $cart = Cart::with('product_carts.product')->where([
            'user_id' => $user->id,
            'id' => $id
        ])->firstOrFail();

        return response()->json($cart);
    }
}
