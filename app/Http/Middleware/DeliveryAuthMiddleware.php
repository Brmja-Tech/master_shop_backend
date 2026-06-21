<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use App\Models\DeliveryUser;
use Closure;
use Illuminate\Http\Request;

class DeliveryAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $deliveryUser = auth('sanctum')->user();

        if (! $deliveryUser instanceof DeliveryUser) {
            return ApiResponse::sendResponse(
                401,
                __('validation.unauthenticated'),
                []
            );
        }

        return $next($request);
    }
}
