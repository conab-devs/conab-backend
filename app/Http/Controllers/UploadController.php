<?php

namespace App\Http\Controllers;

use App\Components\FirebaseStorageAdapter;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
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
        /* @var $user User */
        $user = Auth::user();

        if (
            $request->hasFile('avatar')
            && ($avatar = $request->file('avatar'))->isValid()
        ) {
            if (
                App::environment('production')
                && $file_url = $this->uploadOnFirebase($avatar)
            ) {
                $user->profile_picture = $file_url;
                $user->save();

                return response(['url' => $file_url], 200);
            } else {
                $this->deleteProfilePictureIfExists($user->profile_picture);
                $path = $avatar->store('uploads');
                $user->profile_picture = $path;
                $user->save();

                return response(['url' => Storage::url($path)], 200);
            }
        }

        return response(['error' => 'Avatar is required and should be a valid file'], 400);
    }

    private function uploadOnFirebase(UploadedFile $avatar) : ?string
    {
        $localFolder =  public_path('storage/uploads') . '/';
        $filename = Str::random(80) . "." . $avatar->getClientOriginalExtension();
        $file = $avatar->move($localFolder, $filename);

        /* @var $firebaseStorageAdapter FirebaseStorageAdapter */
        $firebaseStorageAdapter = resolve(FirebaseStorageAdapter::class);
        $firebaseObjectName = "uploads/$filename";

        if ($firebaseStorageAdapter->uploadFile($file->getRealPath(), $firebaseObjectName)
            && $file_url = $firebaseStorageAdapter->getUrl($firebaseObjectName)) {
            return $file_url;
        }

        return null;
    }

    private function deleteProfilePictureIfExists(string $profilePicture) : void
    {
        if (Storage::exists($profilePicture)) {
            Storage::delete($profilePicture);
        }
    }
}
