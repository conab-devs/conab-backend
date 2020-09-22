<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($request->hasFile('avatar')) {
            if (Storage::exists($user->profile_picture)) {
                Storage::delete($user->profile_picture);
            }

            $avatar = $request->file('avatar');
            if ($avatar->isValid()) {
                $path = $avatar->store('uploads');
                $user->profile_picture = $path;
                $user->save();
                return response(['url' => Storage::url($path)], 200);
            }
        }

        return response(['error' => 'Avatar is required and should be a valid file'], 400);
    }
}
