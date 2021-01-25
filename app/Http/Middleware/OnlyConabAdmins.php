<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Gate;

class OnlyConabAdmins
{
    public function handle($request, Closure $next)
    {
        if (Gate::denies('admin-conab')) {
            return response()->json([
                'message' => 'Você não tem autorização a este recurso'
            ], 401);
        }

        return $next($request);
    }
}
