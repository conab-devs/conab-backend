<?php


namespace App\Components\Traits;


use App\Components\FirebaseStorageAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

trait UploadFirebase
{
    function uploadFileOnFirebase(UploadedFile $file): ?string
    {
        $localFolder = public_path('storage/uploads') . '/';
        $filename = Str::random(80) . "." . $file->getClientOriginalExtension();
        $file = $file->move($localFolder, $filename);

        /* @var $firebaseStorageAdapter FirebaseStorageAdapter */
        $firebaseStorageAdapter = resolve(FirebaseStorageAdapter::class);

        if ($firebaseStorageAdapter->uploadFile($file->getRealPath(), $filename)
            && $file_url = $firebaseStorageAdapter->getUrl($filename)) {
            return $file_url;
        }

        return null;
    }
}
