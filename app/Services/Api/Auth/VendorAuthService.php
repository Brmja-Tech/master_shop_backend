<?php

namespace App\Services\Api\Auth;

use App\Models\Vendor;
use App\Repositories\Api\Auth\VendorAuthRepository;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\Api\Vendor\Auth\VendorResource;

class VendorAuthService
{
    public function __construct(
        protected VendorAuthRepository $repository
    ) {}

    public function register(array $data): Vendor
    {
        $data['password'] = Hash::make($data['password']);

        $data['is_active'] = false;

        $data['is_verified'] = false;

        return $this->repository->create($data);
    }
        public function login(array $data): array
    {
        $vendor = $this->repository->findByPhone($data['phone']);

        if (! $vendor || ! Hash::check($data['password'], $vendor->password)) {
            return [
                'status'  => 401,
                'message' => 'auth.invalid-credentials',
                'data'    => [],
            ];
        }

        if (! $vendor->is_verified) {
            return [
                'status'  => 403,
                'message' => 'vendor.not-verified',
                'data'    => [],
            ];
        }

        $vendor->load('storeType');

        $token = $vendor->createToken('vendor-token')->plainTextToken;

        return [
            'status'  => 200,
            'message' => 'vendor.login-successfully',
            'data'    => [
                'vendor'     => new VendorResource($vendor),
                'token'      => $token,
            ],
        ];
    }
}
