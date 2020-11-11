<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Components\Traits\UploadFirebase;

class UploadController extends Controller
{
    use UploadFirebase;

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /* @var $user User */
        $user = Auth::user();

        if ($request->hasFile('avatar') && ($avatar = $request->file('avatar'))->isValid()) {
            $user->profile_picture = App::environment('production')
                ? $this->uploadFileOnFirebase($avatar)
                : $avatar->store('uploads');
            $user->save();

            return response()->json(['url' => $user->profile_picture]);
        }

        return response()->json(['error' => 'Avatar is required and should be a valid file'], 400);
    }
}
