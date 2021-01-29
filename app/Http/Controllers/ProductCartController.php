<?php

namespace App\Http\Controllers;

use App\Order;
use App\Http\Requests\ProductCart\StoreRequest;
use App\Http\Requests\ProductCart\UpdateRequest;
use App\Product;
use App\ProductCart;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ProductCartController extends Controller
{
    /**
     * @OA\Post(
     *     path="/product-carts",
     *     operationId="store",
     *     summary="Registra um novo produto no carrinho",
     *     tags={"Carrinhos"},
     *
     *     @OA\RequestBody(
     *         request="Produto Carrinho",
     *         description="Objeto de produto carrinho",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ProductCart")
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(ref="#/components/schemas/ProductCart")
     *     ),
     *     @OA\Response(response=409, description="Conflict"),
     *     @OA\Response(response=422, description="Unprocess Entity"),
     *     @OA\Response(response=500, description="Server Error")
     * )
     */
    public function store(StoreRequest $request)
    {
        $id = auth()->user()->id;

        $order = Order::firstOrCreate([
            'user_id' => $id,
            'is_closed' => false
        ]);
        $product = Product::where('id', $request->product_id)->firstOrFail();

        if($order->product_carts->where('product_id', $product->id)->first() !== null) {
            return response()->json([
                'message' => 'Você já adicionou esse produto no carrinho',
            ], 409);
        }

        try {
            DB::beginTransaction();

            $product_cart = ProductCart::firstOrNew([
                'product_id' => $request->product_id
            ]);

            $product_cart->order_id = $order->id;
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
                'message' => 'Algo deu errado, tente novamente em alguns instantes',
            ], 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="/product-carts/{productCart}",
     *     operationId="update",
     *     summary="Atualiza quantidade do produto no carrinho",
     *     tags={"Carrinhos"},
     *
     *     @OA\Parameter(
     *         name="ProductCart",
     *         description="Id do produto no carrinho",
     *         required=true,
     *         in="path",
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         request="Quantidade do produto no carrinho",
     *         description="Quantidade do produto no carrinho",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="amount",
     *                     type="number",
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(ref="#/components/schemas/ProductCart")
     *     ),
     *     @OA\Response(response=422, description="Unprocessable Entity"),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=500, description="Server Error")
     * )
     */
    public function update(UpdateRequest $request, ProductCart $productCart)
    {
        $productCart->amount = $request->input('amount');
        $productCart->save();

        return response()->json('');
    }

    /**
     * @OA\Delete(
     *     path="/product-carts/{productCart}",
     *     operationId="destroy",
     *     summary="Exclui os dados do produto no carrinho",
     *     tags={"Carrinhos"},
     *
     *     @OA\Parameter(
     *         name="productCart",
     *         description="Id do produto no carrinho",
     *         required=true,
     *         in="path",
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(response=204, description="No content"),
     *     @OA\Response(response=401, description="Unathorized"),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=500, description="Server Error")
     * )
     */
    public function destroy(ProductCart $productCart)
    {
        if (Gate::denies('consumer-manage-order', $productCart->cart->order)) {
            return response()->json([
                'message' => 'Você não tem autorização a este recurso',
            ], 401);
        }

        $productCart->delete();

        return response()->json(null, 204);
    }
}
