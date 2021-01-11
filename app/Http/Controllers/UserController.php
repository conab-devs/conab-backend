<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Components\Upload\UploadHandler;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/users",
     *     operationId="show",
     *     summary="Retorna o usuário autenticado",
     *     tags={"Usuários"},
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
     *     @OA\Response(response=500, description="Server Error")
     * )
     */
    public function show()
    {
        $user = auth()->user();
        return response()->json($user->load('phones'));
    }

    /**
     * @OA\Post(
     *     path="/users",
     *     operationId="store",
     *     summary="Registra um novo usuário",
     *     description="Retorna os dados do usuário registrado",
     *     tags={"Usuários"},
     *
     *     @OA\RequestBody(
     *         request="Usuário",
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
     *     @OA\Response(response=500, description="Server Error")
     * )
     */
    public function store(StoreRequest $request, UploadHandler $uploader)
    {
        $validatedData = $request->validated();

        try {
            DB::beginTransaction();

            $user = new User();
            $user->fill($validatedData);
            $user->profile_picture = $uploader->upload($validatedData['avatar']);
            $user->save();

            $phoneNumber = $validatedData['phones'][0];
            $user->phones()->create($phoneNumber);
            $user->load('phones');

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
     *     path="/users",
     *     operationId="update",
     *     summary="Atualiza os dados do usuário autenticado",
     *     description="Retorna os dados do usuário atualizado",
     *     tags={"Usuários"},
     *
     *     @OA\RequestBody(
     *         request="Usuário",
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
     *     @OA\Response(response=422, description="Unprocessable Entity"),
     *     @OA\Response(response=500, description="Server Error")
     * )
     */
    public function update(UpdateRequest $request, UploadHandler $uploader)
    {
        $validatedData = $request->validated();
        $user = auth()->user();

        try {
            DB::beginTransaction();

            if (!empty($validatedData['password'])) {
                if (!Hash::check($validatedData['password'], $user->password)) {
                    return response()->json('Senha Inválida', 400);
                }
                $user->password = $validatedData['new_password'];
            }

            if ($request->hasFile('avatar') && ($avatar = $request->file('avatar'))->isValid()) {
                $user->profile_picture = $uploader->upload($avatar);
            }

            $user->fill($request->except('password', 'new_password'));
            $user->save();

            $user->phones()->delete();
            $user->phones()->createMany($request->input('phones'));

            $user->load('phones');

            DB::commit();

            return response()->json($user, 200);
        } catch (\Exception $err) {
            DB::rollback();
            return response()->json([
                'message' => 'Algo deu errado, tente novamente em alguns instantes',
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/users/{userId}",
     *     operationId="destroy",
     *     summary="Excluir o usuário pelo ID",
     *     tags={"Usuários"},
     *
     *     @OA\Parameter(
     *          name="userId",
     *          description="Id do usuário",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *
     *     @OA\Response(response=204, description="No Content"),
     *     @OA\Response(response=401, description="Unathorizated"),
     *     @OA\Response(response=404, description="Not found"),
     *     @OA\Response(response=500, description="Server Error")
     * )
     */
    public function destroy(User $user)
    {
        if (Gate::denies('destroy-user', $user)) {
            return response()->json('Você não tem autorização a este recurso', 401);
        }

        $user->phones()->delete();
        $user->addresses()->delete();

        $user->delete();

        return response()->json(null, 204);
    }
}
