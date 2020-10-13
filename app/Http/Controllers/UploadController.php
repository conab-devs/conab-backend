<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use \Kreait\Firebase\Storage;
#use Illuminate\Support\Facades\Storage;

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
            $localFolder =  public_path('uploads') . '/';
            $name = Str::random(80);
            $extension = $avatar->getClientOriginalExtension();
            $fileName = "$name.$extension";
            if ($file = $avatar->move($localFolder, $fileName)) {
                $firebaseName = "uploads/$name";
                $uploadedFile = fopen($file->getRealPath(), 'r');

                $firebaseStorage = app('firebase.storage');
                $firebaseStorage->getBucket()->upload($uploadedFile, [
                    'name' => $firebaseName
                ]);

                $expiresAt = new \DateTime('tomorrow');
                $imageReference = $firebaseStorage->getBucket()->object($firebaseName);
                if($imageReference->exists())
                    return response(['url' => $imageReference->signedUrl($expiresAt)], 200);
            }

           /* if (Storage::exists($user->profile_picture)) {
                Storage::delete($user->profile_picture);
            }

            $avatar = $request->file('avatar');
            if ($avatar->isValid()) {
                $path = $avatar->store('uploads');
                $user->profile_picture = $path;
                $user->save();
                return response(['url' => Storage::url($path)], 200);
            }*/
        }

        return response(['error' => 'Avatar is required and should be a valid file'], 400);
    }
}
