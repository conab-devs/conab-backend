<?php

namespace App\Http\Controllers;

use App\Components\Auth\AuthHandler;
use App\Components\Auth\ForgotPasswordHandler;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\SendResetPasswordRequest;
use App\User;

/**
 * @OA\Schema(
 *     schema="LoginRequest",
 *     type="object",
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         description="Email do usuário"
 *     ),
 *     @OA\Property(
 *         property="password",
 *         type="string",
 *         description="Senha do usuário"
 *     ),
 *     @OA\Property(
 *         property="user_type",
 *         type="string",
 *         description="ADMIN_COOP | ADMIN_CONAB | CUSTOMER"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="ResetPasswordRequest",
 *     type="object",
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         description="Email do usuário"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="ResetRequest",
 *     type="object",
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         description="Email do usuário"
 *     ),
 *     @OA\Property(
 *         property="password",
 *         type="string",
 *         description="A nova senha do usuário"
 *     ),
 *     @OA\Property(
 *         property="password_confirmation",
 *         type="string",
 *         description="Confirmação da senha"
 *     ),
 *     @OA\Property(
 *         property="code",
 *         type="integer",
 *         description="Email do usuário"
 *     )
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/login",
     *     operationId="login",
     *     summary="Efetuar o login na API",
     *     tags={"Autenticação"},
     *
     *     @OA\RequestBody(
     *         request="Login",
     *         description="Credencias do usuário",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/LoginRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 schema="LoginResponse",
     *                 @OA\Property(property="token", type="string"),
     *                 @OA\Property(property="user", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Unprocessable Entity"),
     *     @OA\Response(response=401, description="Unathorized"),
     *     @OA\Response(response=500, description="Server Error")
     * )
     */
    public function login(LoginRequest $request, AuthHandler $handler)
    {
        $user = User::where('email', $request->input('email'))->first();
        $responseContent = $handler->authenticate($request->only([
            'email', 'password'
        ]));

        return response()->json([
            'token' => $responseContent['token'],
            'user' => $user->load('phones'),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/password/reset/request",
     *     operationId="sendResetPasswordRequest",
     *     summary="Requisição de mudança de email",
     *     tags={"Autenticação"},
     *
     *     @OA\RequestBody(
     *         request="ResetPasswordRequest",
     *         description="Email para a recuperação de senha",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ResetPasswordRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 schema="ResetPasswordResponse",
     *                 @OA\Property(property="message", type="string"),
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Unprocessable Entity"),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=500, description="Server Error")
     * )
     */
    public function sendResetPasswordRequest(SendResetPasswordRequest $request, ForgotPasswordHandler $handler)
    {
        User::where('email', $request->input('email'))->firstOrFail();
        $handler->sendResetRequest($request->input('email'));
        return response()->json([
            'message' => 'The reset token was sent to your email',
        ]);
    }

    /**
     * @OA\Post(
     *     path="/password/reset",
     *     operationId="resetPassword",
     *     summary="Requisição para envia os dados da nova senha",
     *     tags={"Autenticação"},
     *
     *     @OA\RequestBody(
     *         request="ResetRequest",
     *         description="Dados da nova senha",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ResetRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 schema="ResetResponse",
     *                 @OA\Property(property="message", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Unprocessable Entity"),
     *     @OA\Response(response=401, description="Unathorizated"),
     *     @OA\Response(response=500, description="Server Error")
     * )
     */
    public function resetPassword(ResetPasswordRequest $request, ForgotPasswordHandler $handler)
    {
        $handler->resetPassword($request->all());
        return response()->json(['message' => 'The password was reset sucessfully']);
    }
}
