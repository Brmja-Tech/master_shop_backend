<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use App\Models\Vendor;

class VendorAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $vendor = auth('sanctum')->user();

        if (!$vendor instanceof Vendor) {
            return ApiResponse::sendResponse(
                401,
                __('validation.unauthenticated'),
                []
            );
        }

        return $next($request);
    }
}
