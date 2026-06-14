<?php

namespace App\Services\Api\User;

use App\Helpers\ApiResponse;
use App\Models\User;
use App\Repositories\Api\User\VendorRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Exceptions\HttpResponseException;

class VendorService
{
    public function __construct(
        protected VendorRepository $repository
    ) {}

    public function topRated(int $perPage): array
    {
        $vendors = $this->repository->getTopRatedPaginated($perPage);

        return $this->formatPaginatedResponse($vendors);
    }

    public function nearby(
        User $user,
        int $perPage,
        ?string $search = null,
        mixed $latitude = null,
        mixed $longitude = null,
        ?int $storeTypeId = null,
        string $sortDirection = 'asc'
    ): array {
        $resolvedLatitude = $latitude !== null ? (float) $latitude : $user->latitude;
        $resolvedLongitude = $longitude !== null ? (float) $longitude : $user->longitude;

        if ($resolvedLatitude === null || $resolvedLongitude === null) {
            throw new HttpResponseException(
                ApiResponse::sendResponse(422, __('vendor.user_location_required'), [])
            );
        }

        $vendors = $this->repository->getNearbyPaginated(
            latitude: (float) $resolvedLatitude,
            longitude: (float) $resolvedLongitude,
            perPage: $perPage,
            search: $search,
            storeTypeId: $storeTypeId,
            sortDirection: $sortDirection
        );

        return $this->formatPaginatedResponse($vendors);
    }

    private function formatPaginatedResponse(LengthAwarePaginator $vendors): array
    {
        return [
            'vendors' => $vendors,
            'pagination' => [
                'total' => $vendors->total(),
                'current_page' => $vendors->currentPage(),
                'last_page' => $vendors->lastPage(),
                'per_page' => $vendors->perPage(),
            ],
        ];
    }
}
