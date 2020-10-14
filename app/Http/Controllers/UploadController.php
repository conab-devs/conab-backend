<?php

namespace App\Http\Controllers;

use App\Components\FirebaseStorageAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

            $avatar = $request->file('avatar');

            if (App::environment('production')) {
                $name = Str::random(80);
                $fileName = $name . "." . $avatar->getClientOriginalExtension();
                $localFolder =  public_path('storage/uploads') . '/';
                if ($file = $avatar->move($localFolder, $fileName)) {
                    /* @var $firebaseStorageAdapter FirebaseStorageAdapter */
                    $firebaseStorageAdapter = resolve(FirebaseStorageAdapter::class);
                    $firebaseObjectName = "uploads/$name";
                    $firebaseStorageAdapter->uploadFile($file->getRealPath(), $firebaseObjectName);
                    $user->profile_picture = $firebaseObjectName;
                    return response([ 'url' => $firebaseStorageAdapter->getUrl($firebaseObjectName)]);
                }
            }

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
