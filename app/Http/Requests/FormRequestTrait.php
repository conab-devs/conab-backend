<?php

namespace App\Http\Requests;

use App\Exceptions\UnauthorizedException;

trait FormRequestTrait
{
    protected function failedAuthorization()
    {
        throw new UnauthorizedException('Você não tem autorização a este recurso');
    }
}
