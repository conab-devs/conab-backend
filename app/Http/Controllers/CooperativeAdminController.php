<?php

namespace App\Http\Controllers;

use App\Components\Upload\UploadHandler;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Cooperative;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;

class CooperativeAdminController extends Controller
{
    /**
     * @OA\Get(
     *     path="/cooperatives/{cooperativeId}/admins",
     *     operationId="index",
     *     summary="Retorna uma lista de administradores da cooperativa",
     *     tags={"Administradores da Cooperativa"},
     *
     *     @OA\Parameter(
     *         name="cooperativeId",
     *         description="Id da cooperativa",
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
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(
     *                         allOf={
     *                             @OA\Schema(ref="#/components/schemas/User"),
     *                             @OA\Schema(
     *                                 @OA\Property(
     *                                     property="phones",
     *                                     type="array",
     *                                     @OA\Items(ref="#/components/schemas/Phone")
     *                                 )
     *                             )
     *                         }
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unathorized"),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=500, description="Server Error")
     * )
     */
    public function index(Cooperative $cooperative)
    {
        if (Gate::denies('manage-cooperative-admin')) {
            return response()->json([
                'message' => 'Você não tem autorização a este recurso',
            ], 401);
        }
        return response()->json(
            $cooperative->admins()->with('phones')->paginate(5),
            200
        );
    }

    /**
     * @OA\Get(
     *     path="/cooperatives/{cooperativeId}/admins/{userId}",
     *     operationId="show",
     *     summary="Retorna um administrador da cooperativa",
     *     tags={"Administradores da Cooperativa"},
     *
     *     @OA\Parameter(
     *         name="cooperativeId",
     *         description="Id da cooperativa",
     *         required=true,
     *         in="path",
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="userId",
     *         description="Id do administrador",
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
     *                 schema="UserResponse",
     *                 allOf={
     *                     @OA\Schema(ref="#/components/schemas/User"),
     *                     @OA\Schema(
     *                         @OA\Property(
     *                             property="phones",
     *                             type="array",
     *                             @OA\Items(ref="#/components/schemas/Phone")
     *                         )
     *                     )
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unathorized"),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=500, description="Server Error")
     * )
     */
    public function show(Cooperative $cooperative, $id)
    {
        $admin = $cooperative->admins()
            ->with('phones')
            ->where('id', $id)
            ->firstOrFail();

        if (Gate::denies('manage-cooperative-admin', $admin)) {
            return response()->json([
                'message' => 'Você não tem autorização a este recurso',
            ], 401);
        }

        return response()->json($admin);
    }

    /**
     * @OA\Post(
     *     path="/cooperatives/{cooperative}/admins",
     *     operationId="store",
     *     summary="Registra um novo administrador",
     *     description="Retorna os dados do administrador registrado",
     *     tags={"Administradores da Cooperativa"},
     *
     *     @OA\Parameter(
     *         name="cooperativeId",
     *         description="Id da cooperativa",
     *         required=true,
     *         in="path",
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         request="Administrador",
     *         description="Objeto de usuário",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 schema="UserRequest",
     *                 allOf={
     *                     @OA\Schema(ref="#/components/schemas/UserStoreRequest"),
     *                     @OA\Schema(
     *                         @OA\Property(
     *                             property="phones",
     *                             type="array",
     *                             @OA\Items(ref="#/components/schemas/Phone")
     *                         )
     *                     )
     *                 }
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
     *                 schema="UserResponse",
     *                 allOf={
     *                     @OA\Schema(ref="#/components/schemas/User"),
     *                     @OA\Schema(
     *                         @OA\Property(
     *                             property="phones",
     *                             type="array",
     *                             @OA\Items(ref="#/components/schemas/Phone")
     *                         )
     *                     )
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Unprocessable Entity"),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=401, description="Unathorized"),
     *     @OA\Response(response=500, description="Server Error")
     * )
     */
    public function store(StoreRequest $request, Cooperative $cooperative, UploadHandler $uploader)
    {
        if (Gate::denies('manage-cooperative-admin')) {
            return response()->json([
                'message' => 'Você não tem autorização a este recurso',
            ], 401);
        }

        $coopAdminInformation = array_merge($request->validated(), [
            'password' => $request->cpf,
            'user_type' => 'ADMIN_COOP',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create($coopAdminInformation);
            $user->profile_picture = $uploader->upload($coopAdminInformation['avatar']);
            $cooperative->admins()->save($user);

            $user->phones()->createMany($coopAdminInformation['phones']);
            $user->load(['phones']);

            DB::commit();

            return response()->json($user, 201);
        } catch (\Exception $err) {
            DB::rollback();
            return response()->json([
                'message' => 'Algo deu errado, tente novamente em alguns instantes',
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/cooperatives/{cooperative}/admins/{userId}",
     *     operationId="update",
     *     summary="Atualiza os dados do administrador da cooperativa",
     *     description="Retorna os dados do administrador atualizado",
     *     tags={"Administradores da Cooperativa"},
     *
     *     @OA\Parameter(
     *         name="cooperativeId",
     *         description="Id da cooperativa",
     *         required=true,
     *         in="path",
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="userId",
     *         description="Id do administrador",
     *         required=true,
     *         in="path",
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         request="Administrador",
     *         description="Objeto de usuário",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 schema="UserRequest",
     *                 allOf={
     *                     @OA\Schema(ref="#/components/schemas/UserUpdateRequest"),
     *                     @OA\Schema(
     *                         @OA\Property(
     *                             property="phones",
     *                             type="array",
     *                             @OA\Items(ref="#/components/schemas/Phone")
     *                         )
     *                     )
     *                 }
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
     *                 schema="UserResponse",
     *                 allOf={
     *                     @OA\Schema(ref="#/components/schemas/User"),
     *                     @OA\Schema(
     *                         @OA\Property(
     *                             property="phones",
     *                             type="array",
     *                             @OA\Items(ref="#/components/schemas/Phone")
     *                         )
     *                     )
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(response=400, description="Bad Request"),
     *     @OA\Response(response=401, description="Unathorized"),
     *     @OA\Response(response=422, description="Unprocessable Entity"),
     *     @OA\Response(response=500, description="Server Error")
     * )
     */
    public function update(UpdateRequest $request)
    {
        $admin = $request->user();
        $validatedData = $request->validated();

        try {
            DB::beginTransaction();

            $admin->fill($validatedData);

            if (!empty($validatedData['password'])) {
                if (!Hash::check($validatedData['password'], $admin->password)) {
                    return response()->json('Senha inválida', 400);
                }
                $admin->password = $validatedData['new_password'];
            }

            if (!empty($validatedData['phones'])) {
                $admin->phones()->delete();
                $admin->phones()->createMany($validatedData['phones']);
            }

            $admin->save();

            DB::commit();

            return response()->json($admin);
        } catch (\Exception $error) {
            DB::rollback();
            return response()->json([
                'message' => 'Algo deu errado, tente novamente em alguns instantes'
            ], 500);
        }
    }
}
