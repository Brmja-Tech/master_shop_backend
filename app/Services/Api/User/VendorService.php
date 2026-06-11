<?php

namespace App\Services\Api\User;

use App\Repositories\Api\User\VendorRepository;

class VendorService
{
    public function __construct(
        protected VendorRepository $repository
    ) {}

    public function topRated(int $perPage): array
    {
        $vendors = $this->repository->getTopRatedPaginated($perPage);

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
