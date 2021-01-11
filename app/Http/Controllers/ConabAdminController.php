<?php

namespace App\Http\Controllers;

use App\Components\Upload\UploadHandler;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;

class ConabAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('only-conab-admins');
    }

    /**
     * @OA\Get(
     *     path="/conab/admins",
     *     operationId="index",
     *     summary="Retorna todos os administradores da Conab",
     *     tags={"Administradores da Conab"},
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
     *     @OA\Response(response=500, description="Server Error")
     * )
     */
    public function index()
    {
        $user = Auth::user();
        $admins = User::with('phones')->where([
            ['user_type', '=', 'ADMIN_CONAB'],
            ['id', '<>', $user->id]
        ])->paginate(5);
        return response()->json($admins, 200);
    }

    /**
     * @OA\Get(
     *     path="/conab/admins/{userId}",
     *     operationId="show",
     *     summary="Retorna um administrador pelo ID",
     *     tags={"Administradores da Conab"},
     *
     *     @OA\Parameter(
     *         name="userId",
     *         description="Id do usuário",
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
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=401, description="Unathorized"),
     *     @OA\Response(response=500, description="Server Error")
     * )
     */
    public function show(int $id)
    {
        $admin = User::with('phones')->findOrFail($id);
        return response()->json($admin, 200);
    }

    /**
     * @OA\Post(
     *     path="/conab/admins",
     *     operationId="store",
     *     summary="Registra um novo administrador",
     *     description="Retorna os dados do administrador registrado",
     *     tags={"Administradores da Conab"},
     *
     *     @OA\RequestBody(
     *         request="Administradores",
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
     *     @OA\Response(response=401, description="Unathorized"),
     *     @OA\Response(response=500, description="Server Error")
     * )
     */
    public function store(StoreRequest $request, UploadHandler $uploader)
    {
        $validatedData = $request->validated();
        $userData = array_merge($validatedData, [
            'password' => $validatedData['cpf'],
            'user_type' => 'CONAB_ADMIN'
        ]);

        try {
            DB::beginTransaction();

            $user = new User();
            $user->fill($userData);
            $user->profile_picture = $uploader->upload($validatedData['avatar']);
            $user->save();

            $user->phones()->createMany($validatedData['phones']);
            $user->load(['phones']);

            DB::commit();

            return response()->json($user, 201);
        } catch (\Exception $err) {
            DB::rollBack();
            return response()->json([
                'message' => 'Algo deu errado, tente novamente em alguns instantes'
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/conab/admins",
     *     operationId="update",
     *     summary="Atualiza os dados do administrador autenticado",
     *     description="Retorna os dados do administrador atualizado",
     *     tags={"Administradores da Conab"},
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
        $userData = $request->validated();
        $user = $request->user();

        try {
            DB::beginTransaction();

            $user->fill($userData);

            if (!empty($userData['password'])) {
                if (!Hash::check($userData['password'], $user->password)) {
                    return response()->json('Senha inválida', 400);
                }
                $user->password = $userData['new_password'];
            }

            if (!empty($userData['phones'])) {
                $user->phones()->delete();
                $user->phones()->createMany($userData['phones']);
            }

            $user->save();
            $user->load(['phones']);

            DB::commit();

            return response()->json($user, 200);
        } catch (\Exception $err) {
            DB::rollBack();
            return response()->json([
                'message' => 'Algo deu errado, tente novamente em alguns instantes'
            ], 500);
        }
    }
}
