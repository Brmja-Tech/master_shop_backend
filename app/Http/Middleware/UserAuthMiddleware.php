<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class UserAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth('sanctum')->user();

        if (! $user instanceof User) {
            return ApiResponse::sendResponse(
                401,
                __('validation.unauthenticated'),
                []
            );
        }

        return $next($request);
    }
}
