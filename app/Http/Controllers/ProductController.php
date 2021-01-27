<?php

namespace App\Http\Controllers;

use App\Components\Upload\UploadHandler;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\Product\UpdateRequest;
use App\Product;

class ProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/products",
     *     operationId="index",
     *     summary="Retornar uma lista de produtos",
     *     tags={"Produtos"},
     *
     *     @OA\Parameter(
     *         name="cooperative",
     *         description="Id da cooperativa",
     *         required=false,
     *         in="query",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         description="Nome do produto",
     *         required=false,
     *         in="query",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         description="Id da categoria dos produtos",
     *         required=false,
     *         in="query",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="max_price",
     *         description="Valor máximo dos produtos",
     *         required=false,
     *         in="query",
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="min_price",
     *         description="Valor mínimo dos produtos",
     *         required=false,
     *         in="query",
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="order",
     *         description="Ordenação dos produtos desc ou asc",
     *         required=false,
     *         in="query",
     *         @OA\Schema(type="string", format="desc|asc")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Product")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=500, description="Server Error")
     * )
     */
    public function index()
    {
        $products = Product::query()
            ->when(request()->cooperative, function ($query, $cooperative) {
                $query->where('cooperative_id', '=', $cooperative);
            })->when(request()->name, function ($query, $name) {
                $query->where('name', 'like', "%$name%");
            })->when(request()->category, function ($query, $category) {
                $query->where('category_id', '=', $category);
            })->when((request()->max_price || request()->min_price), function ($query, $value) {
                $max = request()->input('max_price', PHP_INT_MAX);
                $min = request()->input('min_price', 0);
                $query->whereBetween('price', [$min, $max]);
            })->when(request()->order, function ($query, $order) {
                $order = $order == 'asc' || $order == 'desc' ? $order : 'asc';
                $query->reorder('price', $order);
            })->paginate(100);

        return response()->json($products);
    }

    /**
     * @OA\Post(
     *     path="/products",
     *     operationId="store",
     *     summary="Registra um novo produto",
     *     tags={"Produtos"},
     *
     *     @OA\RequestBody(
     *         request="Produto",
     *         description="Objeto de produto",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(response=422, description="Unprocess Entity"),
     *     @OA\Response(response=500, description="Server Error")
     * )
     */
    public function store(StoreRequest $request, UploadHandler $uploader)
    {
        $user = $request->user();
        $validatedData = $request->validated();

        $product = new Product();
        $product->cooperative_id = $user->cooperative_id;
        $product->fill($validatedData);
        $product->photo_path = $uploader->upload($validatedData['photo_path']);

        $product->save();

        $product->load('cooperative');

        return response()->json($product, 201);
    }

    /**
     * @OA\Get(
     *     path="/products/{productId}",
     *     operationId="show",
     *     summary="Retorna um produto pelo ID",
     *     tags={"Produtos"},
     *
     *     @OA\Parameter(
     *         name="productId",
     *         description="Id do produto",
     *         required=true,
     *         in="path",
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(response=500, description="Server Error")
     * )
     */
    public function show(Product $product)
    {
        $product->load('category');
        return response()->json($product);
    }

    /**
     * @OA\Put(
     *     path="/products/{productId}",
     *     operationId="update",
     *     summary="Atualiza os dados do produto",
     *     tags={"Produtos"},
     *
     *     @OA\Parameter(
     *         name="productId",
     *         description="Id do produto",
     *         required=true,
     *         in="path",
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         request="Produto",
     *         description="Objeto de produto",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(response=422, description="Unprocessable Entity"),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=500, description="Server Error")
     * )
     */
    public function update(UpdateRequest $request, Product $product, UploadHandler $uploader)
    {
        $validatedData = $request->validated();
        $product->fill($validatedData);
        if ($request->hasFile('photo_path')) {
            $product->photo_path = $uploader->upload($validatedData['photo_path']);
        }
        $product->save();

        return response()->json($product);
    }

    /**
     * @OA\Delete(
     *     path="/products/{productId}",
     *     operationId="destroy",
     *     summary="Exclui os dados do produto",
     *     tags={"Produtos"},
     *
     *     @OA\Parameter(
     *         name="productId",
     *         description="Id do produto",
     *         required=true,
     *         in="path",
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(response=204, description="No content"),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=401, description="Unathorized"),
     *     @OA\Response(response=500, description="Server Error")
     * )
     */
    public function destroy(Product $product)
    {
        if (Gate::denies('manage-product', $product)) {
            return response()->json([
                'message' => 'Você não tem autorização a este recurso',
            ], 401);
        }

        $product->delete();

        return response()->json(null, 204);
    }
}
