<?php

namespace App\Http\Controllers;

use App\Cart;

class OrderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/order",
     *     operationId="index",
     *     summary="Retorna lista de pedido do usuário logado",
     *     tags={"Pedidos"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 allOf={
     *                     @OA\Schema(ref="#/components/schemas/Order"),
     *                     @OA\Schema(
     *                         @OA\Property(
     *                             property="carts",
     *                             type="array",
     *                             @OA\Items(ref="#/components/schemas/Cart")
     *                         )
     *                     )
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(response=500, description="Server Error")
     * )
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
     * @OA\Get(
     *     path="/order/{id}",
     *     operationId="show",
     *     summary="Retorna um pedido pelo ID",
     *     tags={"Pedidos"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         description="Id do Pedido",
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
     *                     @OA\Schema(ref="#/components/schemas/Order"),
     *                     @OA\Schema(
     *                         @OA\Property(
     *                             property="carts",
     *                             type="array",
     *                             @OA\Items(ref="#/components/schemas/Cart")
     *                         )
     *                     )
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Ok"),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=500, description="Server Error")
     * )
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
     * @OA\Delete(
     *     path="/order/{id}",
     *     operationId="destroy",
     *     summary="Exclui o pedido",
     *     tags={"Pedidos"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         description="Id do pedido",
     *         required=true,
     *         in="path",
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(response=204, description="No Content"),
     *     @OA\Response(response=400, description="Bad Request"),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=500, description="Server Error")
     * )
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
