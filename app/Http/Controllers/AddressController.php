<?php

namespace App\Http\Controllers;

use App\Http\Requests\Address\StoreRequest;
use App\Http\Requests\Address\UpdateRequest;

class AddressController extends Controller
{
    /**
     * @OA\Get(
     *     path="/addresses",
     *     operationId="index",
     *     summary="Retorna os endereços do usuário autenticado",
     *     tags={"Endereços"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Address")
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
        return response()->json($user->addresses()->get());
    }

    /**
     * @OA\Post(
     *     path="/addresses",
     *     operationId="store",
     *     summary="Registra um ou vários endereços",
     *     description="Retorna os dados do(s) endereço(s) registrado(s)",
     *     tags={"Endereços"},
     *
     *     @OA\RequestBody(
     *         request="Endereço",
     *         description="Array de objetos de endereço",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 schema="AddressRequest",
     *                 @OA\Property(
     *                     property="addresses",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Address")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 schema="AddressResponse",
     *                 @OA\Property(
     *                     property="addresses",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Address")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Unprocessable Entity"),
     *     @OA\Response(response=500, description="Server Error")
     * )
     */
    public function store(StoreRequest $request)
    {
        $validatedData = $request->validated();

        $user = auth()->user();
        $addresses = $user->addresses()
            ->createMany($validatedData['addresses']);

        return response()->json($addresses, 201);
    }

    /**
     * @OA\Put(
     *     path="/addresses",
     *     operationId="update",
     *     summary="Atualiza os dados dos endereços do usuário autenticado",
     *     description="Retorna os dados do(s) endereço(s) atualizado(s)",
     *     tags={"Endereços"},
     *
     *     @OA\RequestBody(
     *         request="Endereço",
     *         description="Array de objetos de endereço",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 schema="AddressRequest",
     *                 @OA\Property(
     *                     property="addresses",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Address")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 schema="AddressResponse",
     *                 @OA\Property(
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Address")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=400, description="Bad Request"),
     *     @OA\Response(response=422, description="Unprocessable Entity"),
     *     @OA\Response(response=500, description="Server Error")
     * )
     */
    public function update(UpdateRequest $request)
    {
        $validated = $request->validated();

        $user = auth()->user();
        $user->addresses()->delete();

        $addresses = $user->addresses()
            ->createMany([$validated['addresses']]);

        return response()->json($addresses);
    }
}
