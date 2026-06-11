<?php

namespace App\Services\Api;

use App\Http\Resources\Api\SearchProductResource;
use App\Repositories\Api\Vendor\ProductRepository;

class SearchService
{
    public function __construct(
        protected ProductRepository $repository
    ) {}

    public function search(string $search)
    {
        return SearchProductResource::collection(
            $this->repository->search($search)
        );
    }
}
