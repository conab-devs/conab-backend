<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Category;
use App\Http\Requests\Category\StoreRequest;
use App\Http\Requests\Category\UpdateRequest;

class CategoryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/categories",
     *     operationId="index",
     *     summary="Retornar uma lista de categorias",
     *     tags={"Categorias"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Category")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=500, description="Server Error")
     * )
     */
    public function index()
    {
        return response()->json(Category::all(), 200);
    }

    /**
     * @OA\Get(
     *     path="/categories/{categoryId}",
     *     operationId="show",
     *     summary="Retorna um categoria",
     *     tags={"Categorias"},
     *
     *     @OA\Parameter(
     *         name="categoryId",
     *         description="Id da categoria",
     *         required=true,
     *         in="path",
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(ref="#/components/schemas/Category")
     *     ),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=500, description="Server Error")
     * )
     */
    public function show(Category $category)
    {
        return response()->json($category, 200);
    }

    /**
     * @OA\Post(
     *     path="/categories",
     *     operationId="store",
     *     summary="Registra uma nova categoria",
     *     tags={"Categorias"},
     *
     *     @OA\RequestBody(
     *         request="Categoria",
     *         description="Objeto de categoria",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CategoryRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(ref="#/components/schemas/Category")
     *     ),
     *     @OA\Response(response=422, description="Unprocess Entity"),
     *     @OA\Response(response=401, description="Unathorized"),
     *     @OA\Response(response=500, description="Server Error")
     * )
     */
    public function store(StoreRequest $request)
    {
        $validated = $request->validated();

        if (Gate::denies('admin-conab')) {
            return response()->json([
                'message' => 'Você não tem autorização a este recurso',
            ], 401);
        }

        $category = Category::create($validated);
        $category->save();

        return response()->json($category, 201);
    }

    /**
     * @OA\Put(
     *     path="/categories/{categoryId}",
     *     operationId="update",
     *     summary="Atualiza os dados da categoria",
     *     tags={"Categorias"},
     *
     *     @OA\Parameter(
     *         name="categoryId",
     *         description="Id da categoria",
     *         required=true,
     *         in="path",
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         request="Categoria",
     *         description="Objeto de categoria",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CategoryRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(ref="#/components/schemas/Category")
     *     ),
     *     @OA\Response(response=401, description="Unathorized"),
     *     @OA\Response(response=422, description="Unprocessable Entity"),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=500, description="Server Error")
     * )
     */
    public function update(UpdateRequest $request, Category $category)
    {
        $validated = $request->validated();

        if (Gate::denies('admin-conab')) {
            return response()->json([
                'message' => 'Você não tem autorização a este recurso',
            ], 401);
        }

        $category->update($validated);

        return response()->json($category, 200);
    }

    /**
     * @OA\Delete(
     *     path="/categories/{categoryId}",
     *     operationId="destroy",
     *     summary="Exclui os dados da categoria",
     *     tags={"Categorias"},
     *
     *     @OA\Parameter(
     *         name="categoryId",
     *         description="Id da categoria",
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
    public function destroy(Category $category)
    {
        if (Gate::denies('admin-conab')) {
            return response()->json([
                'message' => 'Você não tem autorização a este recurso',
            ], 401);
        }

        $category->delete();
        return response()->json(null, 204);
    }
}
