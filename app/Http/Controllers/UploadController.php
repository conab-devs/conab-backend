<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Components\Upload\UploadHandler;
use App\User;

class UploadController extends Controller
{
    /**
     * @OA\Post(
     *     path="/uploads",
     *     operationId="store",
     *     summary="Faz o upload do avatar do usuário",
     *     description="Retorna a url do arquivo",
     *     tags={"Upload"},
     *
     *     @OA\RequestBody(
     *         request="Upload",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="avatar",
     *                     type="string",
     *                     format="base64"
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
     *                 schema="UploadResponse",
     *                 @OA\Property(
     *                     property="url",
     *                     type="string"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=400, description="Bad Request"),
     *     @OA\Response(response=500, description="Server Error")
     * )
     */
    public function store(Request $request, UploadHandler $uploader)
    {
        /* @var $user User */
        $user = Auth::user();

        if ($request->hasFile('avatar') && ($avatar = $request->file('avatar'))->isValid()) {
            $user->profile_picture = $uploader->upload($avatar);
            $user->save();

            return response()->json(['url' => $user->profile_picture]);
        }

        return response()->json([
            'message' => 'Avatar é obrigatório e deve ser um image válida'
        ], 400);
    }
}
