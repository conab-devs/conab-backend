<?php

namespace App\Components\Upload;

use App\Components\Traits\UploadFirebase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;

class UploadHandler
{
    use UploadFirebase;

    public function upload(UploadedFile $file)
    {
        return App::environment('production')
            ? $this->uploadFileOnFirebase($file)
            : $file->store('uploads');
    }
}
