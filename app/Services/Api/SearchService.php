<?php

namespace App\Services\Api;

use App\Http\Resources\Api\SearchProductResource;
use App\Repositories\Api\User\VendorRepository;
use App\Repositories\Api\Vendor\ProductRepository;

class SearchService
{
    public function __construct(
        protected ProductRepository $repository,
        protected VendorRepository $vendorRepository
    ) {}

    public function search(string $search)
    {
        return SearchProductResource::collection(
            $this->repository->search($search)
        );
    }

    public function searchVendors(string $search)
    {
        return $this->vendorRepository->searchByName($search);
    }
}
