<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Components\Upload\UploadHandler;
use App\User;

class UploadController extends Controller
{
    public function store(Request $request, UploadHandler $uploader)
    {
        /* @var $user User */
        $user = Auth::user();

        if ($request->hasFile('avatar') && ($avatar = $request->file('avatar'))->isValid()) {
            $user->profile_picture = $uploader->upload($avatar);
            $user->save();

            return response()->json(['url' => $user->profile_picture]);
        }

        return response()->json(['error' => 'Avatar is required and should be a valid file'], 400);
    }
}
