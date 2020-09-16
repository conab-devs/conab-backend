<?php

namespace App\Components\Traits;

use App\Components\Errors\CustomException;

trait HttpResponse {
    
    private $status = 500;

    public function respondWithError($error)
    {
        if ($error instanceof CustomException) {
            $this->status = $error->status;
        }

        return response()->json([
            'message' => $error->getMessage()
        ], $this->status);
    }
}