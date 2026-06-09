<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;

class VendorAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $vendor = auth('vendor')->user();

        if (!$vendor) {
            return ApiResponse::sendResponse(
                401,
                __('validation.unauthenticated'),
                []
            );
        }

        return $next($request);
    }
}
