<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\User\VendorListResource;
use App\Services\Api\SearchService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(
        protected SearchService $service
    ) {}

    public function index(Request $request)
    {
        $request->validate([
            'search' => ['required', 'string', 'max:255'],
        ]);

        return ApiResponse::sendResponse(
            200,
            __('product.public_list'),
            $this->service->search($request->string('search')->toString())
        );
    }

    public function vendors(Request $request)
    {
        $request->validate([
            'search' => ['required', 'string', 'max:255'],
        ]);

        return ApiResponse::sendResponse(
            200,
            __('vendor.list_retrieved'),
            VendorListResource::collection(
                $this->service->searchVendors($request->string('search')->toString())
            )
        );
    }
}
